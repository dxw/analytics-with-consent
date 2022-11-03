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
			expect('add_action')->toBeCalled()->with('acf/init', [$this->options, 'acfInit']);

			$this->options->register();
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
