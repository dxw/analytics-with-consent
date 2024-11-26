<?php

namespace AnalyticsWithConsent;

describe(Options::class, function () {
	beforeEach(function () {
		$this->options = new Options();
	});

	it('is registerable', function () {
		expect($this->options)->toBeAnInstanceOf(\Dxw\Iguana\Registerable::class);
	});

	describe('->register()', function () {
		it('adds actions', function () {
			allow('add_action')->toBeCalled();
			allow('add_filter')->toBeCalled();

			expect('add_action')->toBeCalled()->with('acf/init', [$this->options, 'acfInit']);
			expect('add_filter')->toBeCalled()->with('acf/validate_value/name=google_analytics_id', [$this->options, 'validateTrimmedText'], 10, 4);
			expect('add_filter')->toBeCalled()->with('acf/validate_value/name=ga_4_id', [$this->options, 'validateTrimmedText'], 10, 4);
			expect('add_filter')->toBeCalled()->with('acf/validate_value/name=google_analytics_gtm', [$this->options, 'validateTrimmedText'], 10, 4);
			expect('add_filter')->toBeCalled()->with('acf/validate_value/name=gtm_marketing_cookies', [$this->options, 'validateTrimmedText'], 10, 4);
			expect('add_filter')->toBeCalled()->with('acf/validate_value/name=civic_cookie_control_api_key', [$this->options, 'validateTrimmedText'], 10, 4);
			expect('add_filter')->toBeCalled()->with('acf/validate_value/name=hotjar_id', [$this->options, 'validateTrimmedText'], 10, 4);

			$this->options->register();
		});
	});

	describe('->validateTrimmedText()', function () {
		context('if there is already a validation error', function () {
			it('does nothing', function () {
				$valid = $this->options->validateTrimmedText('bad input', 'abcde', 'myfield', 'original');
				expect($valid)->toEqual('bad input');
			});
		});

		context('if the input is already trimmed', function () {
			it('does nothing', function () {
				$valid = $this->options->validateTrimmedText(true, 'abcde', 'myfield', 'original');
				expect($valid)->toEqual(true);
			});
		});

		context('if the input is not trimmed', function () {
			it('returns a validation error', function () {
				$valid = $this->options->validateTrimmedText(true, ' abcde', 'myfield', 'original');
				expect($valid)->toEqual('Please remove any leading or trailing spaces.');
				$valid = $this->options->validateTrimmedText(true, 'abcde ', 'myfield', 'original');
				expect($valid)->toEqual('Please remove any leading or trailing spaces.');
				$valid = $this->options->validateTrimmedText(true, ' abcde ', 'myfield', 'original');
				expect($valid)->toEqual('Please remove any leading or trailing spaces.');
			});
		});
	});

	describe('->acfInit()', function () {
		it('registers options', function () {
			allow('acf_add_options_sub_page')->toBeCalled();
			expect('acf_add_options_sub_page')->toBeCalled();

			allow('acf_add_local_field_group')->toBeCalled();
			expect('acf_add_local_field_group')->toBeCalled();

			$this->options->acfInit();
		});
	});
});
