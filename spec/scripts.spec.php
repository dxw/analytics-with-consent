<?php

namespace AnalyticsWithConsent;

describe(Scripts::class, function () {
    beforeEach(function () {
        $this->options = new \AnalyticsWithConsent\Options();
        $this->scripts = new Scripts($this->options);
    });

    it('is registerable', function () {
        expect($this->scripts)->toBeAnInstanceOf(\Dxw\Iguana\Registerable::class);
    });

    describe('->register()', function () {
        it('adds actions', function () {
            allow('add_action')->toBeCalled();
            expect('add_action')->toBeCalled()->times(2);
            expect('add_action')->toBeCalled()->with('wp_enqueue_scripts', [$this->scripts, 'enqueueScripts']);
            expect('add_action')->toBeCalled()->with('wp_enqueue_scripts', [$this->scripts, 'enqueueStyles']);
            $this->scripts->register();
        });
    });

    describe('->enqueueScripts()', function () {
        context('Civic Cookie API Key is not set', function () {
            it('does nothing', function () {
                allow('get_field')->toBeCalled()->andReturn('');
                expect('get_field')->toBeCalled()->once()->with('civic_cookie_control_api_key', 'option');
                $this->scripts->enqueueScripts();
            });
        });
        context('Civic Cookie API Key is set', function () {
            context('but Civic Product Type is not set', function () {
                it('does nothing', function () {
                    allow('get_field')->toBeCalled()->andReturn('an_api_key', '');
                    expect('get_field')->toBeCalled()->once()->with('civic_cookie_control_api_key', 'option');
                    expect('get_field')->toBeCalled()->once()->with('civic_cookie_control_product_type', 'option');
                    $this->scripts->enqueueScripts();
                });
            });
            context('and Civic Product Type is set', function () {
                context('and using default customisation options', function () {
                    it('enqueues the Civic Cookie Control script and the config script, and injects our settings', function () {
                        $instance = new \AnalyticsWithConsent\Scripts(new \AnalyticsWithConsent\Options());

                        $customisationDefaults = [
                            'title' => 'This site uses cookies to store information on your computer.',
                            'intro' => 'Some of these cookies are essential, while others help us to improve your experience by providing insights into how the site is being used.',
                            'necessaryDescription' => 'Necessary cookies enable core functionality such as page navigation and access to secure areas. The website cannot function properly without these cookies, and can only be disabled by changing your browser preferences.',
                            'analyticalDescription' => 'Analytical cookies help us to improve our website by collecting and reporting information on its usage.',
                            'closeLabel' => 'Save and Close',
                            'acceptSettings' => 'Accept all cookies',
                            'rejectSettings' => 'Only accept necessary cookies'                        
                        ];

                        allow('get_field')->toBeCalled()->andReturn(
                            'an_api_key', 'a_product_type', 'a_ga_id',null,null,null,null,null,null,null
                        );
                        expect('get_field')->toBeCalled()->once()->with('civic_cookie_control_api_key', 'option');
                        expect('get_field')->toBeCalled()->once()->with('civic_cookie_control_product_type', 'option');
                        expect('get_field')->toBeCalled()->once()->with('google_analytics_id', 'option');
                        $fieldPrefix = 'civic_cookie_control_';
                        expect('get_field')->toBeCalled()->once()->with($fieldPrefix.'title', 'option');
                        expect('get_field')->toBeCalled()->once()->with($fieldPrefix.'intro', 'option');
                        expect('get_field')->toBeCalled()->once()->with($fieldPrefix.'necessaryDescription', 'option');
                        expect('get_field')->toBeCalled()->once()->with($fieldPrefix.'analyticalDescription', 'option');
                        expect('get_field')->toBeCalled()->once()->with($fieldPrefix.'closeLabel', 'option');
                        expect('get_field')->toBeCalled()->once()->with($fieldPrefix.'acceptSettings', 'option');
                        expect('get_field')->toBeCalled()->once()->with($fieldPrefix.'rejectSettings', 'option');

                        allow($instance)->toReceive('resolveOptions')
                            ->with(array_keys($customisationDefaults))
                            ->andReturn($customisationDefaults);
                        allow('wp_enqueue_script')->toBeCalled();
                        expect('wp_enqueue_script')->toBeCalled()->once()->with('civicCookieControl', 'https://cc.cdn.civiccomputing.com/9/cookieControl-9.x.min.js');
                        allow('dirname')->toBeCalled()->andReturn('/path/to/this/plugin');
                        allow('plugins_url')->toBeCalled()->andReturn('http://path/to/this/plugin/assets/js/config.js');
                        expect('plugins_url')->toBeCalled()->once()->with('/assets/js/config.js', '/path/to/this/plugin');
                        expect('wp_enqueue_script')->toBeCalled()->once()->with('civicCookieControlConfig', 'http://path/to/this/plugin/assets/js/config.js', ['civicCookieControl']);
                        allow('wp_localize_script')->toBeCalled();
                        expect('wp_localize_script')->toBeCalled()->once()->with(
                            'civicCookieControlConfig', 
                            'cookieControlConfig', 
                            array_merge(
                                [
                                    'apiKey' => 'an_api_key',
                                    'productType' => 'a_product_type',
                                    'googleAnalyticsId' => 'a_ga_id'
                                ],
                                $customisationDefaults
                            )
                        );
                        $this->scripts->enqueueScripts();
                    });
                });
            });
        });
    });
    

    describe('->enqueueStyles()', function () {
        it('enqueues the plugin stylesheet', function () {
            allow('dirname')->toBeCalled()->andReturn('/path/to/this/plugin');
            allow('plugins_url')->toBeCalled()->andReturn('http://path/to/this/plugin/assets/css/styles.css');
            expect('plugins_url')->toBeCalled()->once()->with('/assets/css/styles.css', '/path/to/this/plugin');
            allow('wp_enqueue_style')->toBeCalled();
            expect('wp_enqueue_style')->toBeCalled()->once()->with('analytics-with-consent-styles', 'http://path/to/this/plugin/assets/css/styles.css');
            $this->scripts->enqueueStyles();
        });
    });
});
