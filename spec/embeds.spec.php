<?php

namespace AnalyticsWithConsent;

describe(Embeds::class, function () {
	beforeEach(function () {
		$this->embeds = new Embeds();
	});

	it('is registerable', function () {
		expect($this->embeds)->toBeAnInstanceOf(\Dxw\Iguana\Registerable::class);
	});

	describe('->register()', function () {
		it('adds the embed filter', function () {
			allow('add_filter')->toBeCalled();
			expect('add_filter')->toBeCalled()->once()->with('embed_oembed_html', [$this->embeds, 'embedPlaceholder'], 10, 4);
			expect('add_filter')->toBeCalled()->once()->with('render_block', [$this->embeds, 'filterBlock']);

			$this->embeds->register();
		});
	});

	describe('->filterBlock()', function () {
		context('ACF is disabled', function () {
			it('returns the block content', function () {
				allow('function_exists')->toBeCalled()->andReturn(false);

				expect($this->embeds->filterBlock('<iframe>embed</iframe>', ['blockName' => 'core/embed']))->toEqual('<iframe>embed</iframe>');
			});
		});
		context('third party media consent is disabled', function () {
			it('returns the block content', function () {
				allow('function_exists')->toBeCalled()->andReturn(true);
				allow('get_field')->toBeCalled()->andReturn(false);

				expect($this->embeds->filterBlock('<iframe>embed</iframe>', ['blockName' => 'core/embed']))->toEqual('<iframe>embed</iframe>');
			});
		});
		context('third party media consent is enabled', function () {
			context('but we are in the admin panel', function () {
				it('returns the block content', function () {
					allow('function_exists')->toBeCalled()->andReturn(true);
					allow('get_field')->toBeCalled()->andReturn(true);
					allow('is_admin')->toBeCalled()->andReturn(true);

					expect($this->embeds->filterBlock('<iframe>embed</iframe>', ['blockName' => 'core/embed']))->toEqual('<iframe>embed</iframe>');
				});
			});
			context('and we are in the front end', function () {
				context('but the placeholder is already there', function () {
					it('returns the existing html', function () {
						allow('function_exists')->toBeCalled()->andReturn(true);
						allow('get_field')->toBeCalled()->andReturn(true);
						allow('is_admin')->toBeCalled()->andReturn(false);
						expect('esc_url')->not->toBeCalled();

						expect($this->embeds->filterBlock('<div class="awc-embed-placeholder">This is some text</div>', ['blockName' => 'core/embed']))->toEqual('<div class="awc-embed-placeholder">This is some text</div>');
					});
				});
				it('returns the placeholder', function () {
					allow('function_exists')->toBeCalled()->andReturn(true);
					allow('get_field')->toBeCalled()->andReturn(true);
					allow('is_admin')->toBeCalled()->andReturn(false);
					expect('esc_url')->not->toBeCalled();

					expect($this->embeds->filterBlock('<iframe>embed</iframe>', ['blockName' => 'core/embed']))->toContain('awc-embed-placeholder');
				});
				it('includes the media URL in the placeholder if provided', function () {
					allow('function_exists')->toBeCalled()->andReturn(true);
					allow('get_field')->toBeCalled()->andReturn(true);
					allow('is_admin')->toBeCalled()->andReturn(false);
					allow('esc_url')->toBeCalled()->andRun(function ($input) {
						return '_' . $input . '_';
					});

					expect($this->embeds->filterBlock('<iframe>embed</iframe>', ['blockName' => 'core/embed', 'attrs' => ['url' => 'http://bbc.co.uk']]))->toContain('_http://bbc.co.uk_');
				});
			});
		});
	});

	describe('->embedPlaceholder()', function () {
		context('third party embed consent setting is disabled', function () {
			it('returns the original embed html', function () {
				allow('function_exists')->toBeCalled()->andReturn(true);
				allow('get_field')->toBeCalled()->with('third_party_media_embed_consent', 'option')->andReturn(false);

				$result = $this->embeds->embedPlaceholder('<iframe>embed</iframe>', 'https://example.com/video', [], 123);

				expect($result)->toEqual('<iframe>embed</iframe>');
			});
		});

		context('inside admin interface', function () {
			it('returns the original embed html', function () {
				allow('function_exists')->toBeCalled()->andReturn(true);
				allow('get_field')->toBeCalled()->with('third_party_media_embed_consent', 'option')->andReturn(true);
				allow('is_admin')->toBeCalled()->andReturn(true);

				$result = $this->embeds->embedPlaceholder('<iframe>embed</iframe>', 'https://example.com/video', [], 123);

				expect($result)->toEqual('<iframe>embed</iframe>');
			});
		});

		context('third party embed consent setting is enabled and not within the admin panel', function () {
			context('but the placeholder is already in place', function () {
				it('returns the input', function () {
					allow('function_exists')->toBeCalled()->andReturn(true);
					allow('get_field')->toBeCalled()->with('third_party_media_embed_consent', 'option')->andReturn(true);
					allow('is_admin')->toBeCalled()->andReturn(false);

					$result = $this->embeds->embedPlaceholder('<div class="awc-embed-placeholder">Hello</div>', 'https://example.com/video', [], 123);

					expect($result)->toEqual('<div class="awc-embed-placeholder">Hello</div>');
				});
			});
			it('returns the placeholder html', function () {
				allow('function_exists')->toBeCalled()->andReturn(true);
				allow('get_field')->toBeCalled()->with('third_party_media_embed_consent', 'option')->andReturn(true);
				allow('is_admin')->toBeCalled()->andReturn(false);
				allow('esc_url')->toBeCalled()->andRun(function ($input) {
					return '_' . $input . '_';
				});

				$result = $this->embeds->embedPlaceholder('<iframe>embed</iframe>', 'https://example.com/video', [], 123);

				expect($result)->toContain('class="awc-embed-placeholder"');
				expect($result)->toContain('_https://example.com/video_');
			});
		});
	});
});
