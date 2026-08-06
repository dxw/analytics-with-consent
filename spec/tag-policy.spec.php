<?php

namespace AnalyticsWithConsent;

describe(TagPolicy::class, function () {
	beforeEach(function () {
		$this->class = new TagPolicy();
	});

	it('is registerable', function () {
		expect($this->class)->toBeAnInstanceOf(\Dxw\Iguana\Registerable::class);
	});

	describe('->register()', function () {
		it('registers the callbacks', function () {
			allow('add_action')->toBeCalled();

			expect('add_action')->toBeCalled()->once()->with('wp_enqueue_scripts', [$this->class, 'addPolicy']);

			$this->class->register();
		});
	});

	describe('->addPolicy', function () {
		beforeEach(function () {
			$this->civicApiKey = 'CIVIC123';
			$this->civicProductType = 'PRO';
			$this->gtmId = 'GTM123';
		});

		context('when get_field() does not exist', function () {
			it('does nothing', function () {
				allow('function_exists')->toBeCalled()->with('get_field')->andReturn(false);

				expect($this->class->addPolicy())->toBeNull();
			});
		});

		context('when Civic is not configured', function () {
			it('does nothing', function () {
				$civicApiKey = '';
				$civicProductType = '';

				allow('function_exists')->toBeCalled()->with('get_field')->andReturn(true);

				allow('get_field')->toBeCalled()->andReturn(
					$civicApiKey,
					$civicProductType,
				);

				expect($this->class->addPolicy())->toBeNull();
			});
		});

		context('when GTM is not configured', function () {
			it('does nothing', function () {
				$gtmId = '';

				allow('function_exists')->toBeCalled()->with('get_field')->andReturn(true);

				allow('get_field')->toBeCalled()->andReturn(
					$this->civicApiKey,
					$this->civicProductType,
					$gtmId,
				);

				expect($this->class->addPolicy())->toBeNull();
			});
		});

		context('when GTM and Civic are configured', function () {
			beforeEach(function () {
				allow('function_exists')->toBeCalled()->with('get_field')->andReturn(true);

				allow('get_field')->toBeCalled()->andReturn(
					$this->civicApiKey,
					$this->civicProductType,
					$this->gtmId,
				);
			});

			context('and both a blocklist and allowlist is configured in a theme/plugin', function () {
				it('pushes the policy to the GTM dataLayer before GTM initializes', function () {

					allow('apply_filters')->toBeCalled()->andReturn([
						'blocklist' => ['html'],
						'allowlist' => ['img'],
					]);

					allow('wp_json_encode')->toBeCalled()->andRun(function ($data) {
						return json_encode($data);
					});

					allow('wp_add_inline_script')->toBeCalled()->andRun(function ($handle, $data) {
						echo $data;
					});

					expect('wp_add_inline_script')->toBeCalled()->with(
						'civicCookieControlDefaultAnalytics',
						\Kahlan\Arg::toBeA('string'),
						'before',
					);

					ob_start();
					$this->class->addPolicy();
					$result = ob_get_clean();

					expect($result)->toContain("'gtm.blocklist': [\"html\"]");
					expect($result)->toContain("'gtm.allowlist': [\"img\"]");
				});
			});

			context('and only a blocklist is configured in a theme/plugin', function () {
				it('pushes the policy to the GTM dataLayer, without any reference to the allowlist', function () {

					allow('apply_filters')->toBeCalled()->andReturn([
						'blocklist' => ['html'],
					]);

					allow('wp_json_encode')->toBeCalled()->andRun(function ($data) {
						return json_encode($data);
					});

					allow('wp_add_inline_script')->toBeCalled()->andRun(function ($handle, $data) {
						echo $data;
					});

					ob_start();
					$this->class->addPolicy();
					$result = ob_get_clean();

					expect($result)->toContain("'gtm.blocklist': [\"html\"]");
					expect($result)->not->toContain("'gtm.allowlist'");
				});
			});

			context('and only an allowlist is configured in a theme/plugin', function () {
				it('pushes the policy to the GTM dataLayer, without any reference to the blocklist', function () {

					allow('apply_filters')->toBeCalled()->andReturn([
						'allowlist' => ['img'],
					]);

					allow('wp_json_encode')->toBeCalled()->andRun(function ($data) {
						return json_encode($data);
					});

					allow('wp_add_inline_script')->toBeCalled()->andRun(function ($handle, $data) {
						echo $data;
					});

					ob_start();
					$this->class->addPolicy();
					$result = ob_get_clean();

					expect($result)->toContain("'gtm.allowlist': [\"img\"]");
					expect($result)->not->toContain("'gtm.blocklist'");
				});
			});

			context('and neither a blocklist or allowlist is configured in a theme/plugin', function () {
				it('does nothing', function () {

					allow('apply_filters')->toBeCalled()->andReturn([]);

					expect('wp_add_inline_script')->not->toBeCalled();

					$this->class->addPolicy();
				});
			});
		});
	});
});
