<?php

namespace AnalyticsWithConsent;

describe(Scripts::class, function () {
    beforeEach(function () {
        $this->scripts = new Scripts();
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
            // mock get_site_url() for tests
            function get_site_url()
            {
                return "https://www.example.com'";
            }
            context('and Civic Product Type is set', function () {
                it('enqueues the Civic Cookie Control script and the config and analytics scripts, and injects our settings, with the option to filter them', function () {
                    allow('get_field')->toBeCalled()->andReturn('an_api_key', 'a_product_type', 'a_ga_id', true, 'https://www.example.com');
                    expect('get_field')->toBeCalled()->times(2)->with('civic_cookie_control_api_key', 'option');
                    expect('get_field')->toBeCalled()->times(2)->with('civic_cookie_control_product_type', 'option');
                    expect('get_field')->toBeCalled()->once()->with('google_analytics_id', 'option');
                    expect('get_field')->toBeCalled()->once()->with('awc_track_events', 'option');
                    allow('wp_enqueue_script')->toBeCalled();
                    expect('wp_enqueue_script')->toBeCalled()->once()->with('civicCookieControl', 'https://cc.cdn.civiccomputing.com/9/cookieControl-9.x.min.js');
                    allow('dirname')->toBeCalled()->andReturn('/path/to/this/plugin');
                    allow('plugins_url')->toBeCalled()->andReturn('http://path/to/this/plugin/assets/js/analytics.js', 'http://path/to/this/plugin/assets/js/config.js');
                    expect('plugins_url')->toBeCalled()->once()->with('/assets/js/analytics.js', '/path/to/this/plugin');
                    expect('wp_enqueue_script')->toBeCalled()->once()->with('civicCookieControlDefaultAnalytics', 'http://path/to/this/plugin/assets/js/analytics.js', ['civicCookieControl']);
                    expect('plugins_url')->toBeCalled()->once()->with('/assets/js/config.js', '/path/to/this/plugin');
                    expect('wp_enqueue_script')->toBeCalled()->once()->with('civicCookieControlConfig', 'http://path/to/this/plugin/assets/js/config.js', ['civicCookieControl', 'civicCookieControlDefaultAnalytics']);
                    allow('wp_localize_script')->toBeCalled();
                    expect('wp_localize_script')->toBeCalled()->times(2)->with(
                        [
                        0 => [
                            0 => "civicCookieControlDefaultAnalytics",
                            1 => "cookieControlDefaultAnalytics",
                            2 => [
                                "googleAnalyticsId" => "a_ga_id",
                                "track_events" => true,
                                "siteurl" => "https://www.example.com'"
                            ]
                        ],
                        1 => [
                            0 => "civicCookieControlConfig",
                            1 => "cookieControlConfig",
                            2 => [
                                "apiKey" => "https://www.example.com",
                                "product" => "https://www.example.com",
                                "closeStyle" => "button",
                                "initialState" => "open",
                                "text" => [
                                    "closeLabel" => "Save and Close",
                                    "acceptSettings" => "Accept all cookies",
                                    "rejectSettings" => "Only accept necessary cookies"
                                ],
                                "branding" => [
                                    "removeAbout" => true
                                ],
                                "position" => "LEFT",
                                "theme" => "DARK",
                                "subDomains" => false,
                                "toggleType" => "checkbox",
                                "optionalCookies" => [
                                    0 => [
                                        "name" => "analytics",
                                        "label" => "Analytical Cookies",
                                        "description" => "Analytical cookies help us to improve our website by collecting and reporting information on its usage.",
                                        "cookies" => [
                                            0 => "_ga",
                                            1 => "_gid",
                                            2 => "_gat",
                                            3 => "__utma",
                                            4 => "__utmt",
                                            5 => "__utmb",
                                            6 => "__utmc",
                                            7 => "__utmz",
                                            8 => "__utmv"
                                        ],
                                        "onAccept" => "analyticsWithConsent.gaAccept",
                                        "onRevoke" => "analyticsWithConsent.gaRevoke"
                                    ]
                                ]
                            ]
                        ]
                    ]
                    );
                    // expect('wp_localize_script')->toBeCalled()->once()->with('civicCookieControlDefaultAnalytics', 'cookieControlDefaultAnalytics', [
                    //     'googleAnalyticsId' => 'a_ga_id',
                    //     'track_events' => 'true',
                    //     'siteurl' => 'https://www.example.com'
                    // ]);
                    allow('apply_filters')->toBeCalled()->andRun(function ($filterName, $filteredData) {
                        return $filteredData;
                    });
                    expect('apply_filters')->toBeCalled()->once()->with('awc_civic_cookie_control_config', \Kahlan\Arg::toBeAn('array'));
                    //expect('wp_localize_script')->toBeCalled()->once()->with('civicCookieControlConfig', 'cookieControlConfig', \Kahlan\Arg::toBeAn('array'));
                    $this->scripts->enqueueScripts();
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
