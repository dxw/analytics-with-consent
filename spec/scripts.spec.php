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
            expect('add_action')->toBeCalled()->times(4);
            expect('add_action')->toBeCalled()->with('wp_enqueue_scripts', [$this->scripts, 'enqueueScripts']);
            expect('add_action')->toBeCalled()->with('wp_enqueue_scripts', [$this->scripts, 'enqueueStyles']);
            expect('add_action')->toBeCalled()->with('wp_head', [$this->scripts, 'addGA4']);
            expect('add_action')->toBeCalled()->with('wp_head', [$this->scripts, 'addGTM']);
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
                context('but marketing scripts are not on', function () {
                    it('enqueues the Civic Cookie Control script and the config and analytics scripts, and injects our settings, with the option to filter them', function () {
                        allow('get_field')->toBeCalled()->andReturn('an_api_key', 'a_product_type', 'a_ga_id', 'a_ga4_id', 'a_gtm_id', false, 'an_api_key', 'a_product_type');
                        expect('get_field')->toBeCalled()->times(2)->with('civic_cookie_control_api_key', 'option');
                        expect('get_field')->toBeCalled()->times(2)->with('civic_cookie_control_product_type', 'option');
                        expect('get_field')->toBeCalled()->once()->with('google_analytics_id', 'option');
                        expect('get_field')->toBeCalled()->once()->with('ga_4_id', 'option');
                        expect('get_field')->toBeCalled()->once()->with('google_analytics_gtm', 'option');
                        allow('wp_enqueue_script')->toBeCalled();
                        expect('wp_enqueue_script')->toBeCalled()->once()->with('civicCookieControl', 'https://cc.cdn.civiccomputing.com/9/cookieControl-9.x.min.js');
                        allow('dirname')->toBeCalled()->andReturn('/path/to/this/plugin');
                        allow('plugins_url')->toBeCalled()->andReturn('http://path/to/this/plugin/assets/js/analytics.js', 'http://path/to/this/plugin/assets/js/config.js');
                        expect('plugins_url')->toBeCalled()->once()->with('/assets/js/analytics.js', '/path/to/this/plugin');
                        expect('wp_enqueue_script')->toBeCalled()->once()->with('civicCookieControlDefaultAnalytics', 'http://path/to/this/plugin/assets/js/analytics.js', ['civicCookieControl']);
                        expect('plugins_url')->toBeCalled()->once()->with('/assets/js/config.js', '/path/to/this/plugin');
                        expect('wp_enqueue_script')->toBeCalled()->once()->with('civicCookieControlConfig', 'http://path/to/this/plugin/assets/js/config.js', ['civicCookieControl', 'civicCookieControlDefaultAnalytics']);
                        allow('wp_localize_script')->toBeCalled();
                        expect('wp_localize_script')->toBeCalled()->once()->with('civicCookieControlDefaultAnalytics', 'cookieControlDefaultAnalytics', [
                            'googleAnalyticsId' => 'a_ga_id',
                            'ga4Id' => 'a_ga4_id',
                            'gtmId' => 'a_gtm_id'
                        ]);
                        allow('apply_filters')->toBeCalled()->andRun(function ($filterName, $filteredData) {
                            return $filteredData;
                        });
                        expect('apply_filters')->toBeCalled()->once()->with('awc_civic_cookie_control_config', \Kahlan\Arg::toBeAn('array'));
                        expect('wp_localize_script')->toBeCalled()->once()->with('civicCookieControlConfig', 'cookieControlConfig', \Kahlan\Arg::toBeAn('array'));
                        $this->scripts->enqueueScripts();
                    });
                });

                context('and marketing scripts are on', function () {
                    it('enqueues the Civic Cookie Control script and the config and analytics scripts, and injects our settings including the additional optional cookies, with the option to filter them', function () {
                        allow('get_field')->toBeCalled()->andReturn('an_api_key', 'a_product_type', 'a_ga_id', 'a_ga4_id', 'a_gtm_id', true, 'a list of marketing cookies', 'an_api_key', 'a_product_type');
                        allow('esc_js')->toBeCalled()->andRun(function ($input) {
                            return $input;
                        });
                        expect('get_field')->toBeCalled()->times(2)->with('civic_cookie_control_api_key', 'option');
                        expect('get_field')->toBeCalled()->times(2)->with('civic_cookie_control_product_type', 'option');
                        expect('get_field')->toBeCalled()->once()->with('google_analytics_id', 'option');
                        expect('get_field')->toBeCalled()->once()->with('ga_4_id', 'option');
                        expect('get_field')->toBeCalled()->once()->with('google_analytics_gtm', 'option');
                        expect('get_field')->toBeCalled()->once()->with('gtm_marketing_consent', 'option');
                        expect('get_field')->toBeCalled()->once()->with('gtm_marketing_cookies', 'option');
                        allow('wp_enqueue_script')->toBeCalled();
                        expect('wp_enqueue_script')->toBeCalled()->once()->with('civicCookieControl', 'https://cc.cdn.civiccomputing.com/9/cookieControl-9.x.min.js');
                        allow('dirname')->toBeCalled()->andReturn('/path/to/this/plugin');
                        allow('plugins_url')->toBeCalled()->andReturn('http://path/to/this/plugin/assets/js/analytics.js', 'http://path/to/this/plugin/assets/js/config.js');
                        expect('plugins_url')->toBeCalled()->once()->with('/assets/js/analytics.js', '/path/to/this/plugin');
                        expect('wp_enqueue_script')->toBeCalled()->once()->with('civicCookieControlDefaultAnalytics', 'http://path/to/this/plugin/assets/js/analytics.js', ['civicCookieControl']);
                        expect('plugins_url')->toBeCalled()->once()->with('/assets/js/config.js', '/path/to/this/plugin');
                        expect('wp_enqueue_script')->toBeCalled()->once()->with('civicCookieControlConfig', 'http://path/to/this/plugin/assets/js/config.js', ['civicCookieControl', 'civicCookieControlDefaultAnalytics']);
                        allow('wp_localize_script')->toBeCalled();
                        expect('wp_localize_script')->toBeCalled()->once()->with('civicCookieControlDefaultAnalytics', 'cookieControlDefaultAnalytics', [
                            'googleAnalyticsId' => 'a_ga_id',
                            'ga4Id' => 'a_ga4_id',
                            'gtmId' => 'a_gtm_id'
                        ]);
                        allow('apply_filters')->toBeCalled()->andRun(function ($filterName, $filteredData) {
                            return $filteredData;
                        });
                        expect('apply_filters')->toBeCalled()->once()->with('awc_civic_cookie_control_config', \Kahlan\Arg::toBeAn('array'));
                        expect('wp_localize_script')->toBeCalled()->once()->with('civicCookieControlConfig', 'cookieControlConfig', \Kahlan\Arg::toBeAn('array'));

                        $result = $this->scripts->enqueueScripts();
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

    describe('->addGA4()', function () {
        context('API Key is not set', function () {
            it('does nothing', function () {
                allow('get_field')->toBeCalled()->andReturn(null, 'a_product_type', 'a_ga4_id');

                ob_start();
                $this->scripts->addGA4();
                $result = ob_get_clean();

                expect($result)->toEqual('');
            });
        });
        context('product type is not set', function () {
            it('does nothing', function () {
                allow('get_field')->toBeCalled()->andReturn('an_api_key', null, 'a_ga4_id');

                ob_start();
                $this->scripts->addGA4();
                $result = ob_get_clean();

                expect($result)->toEqual('');
            });
        });
        context('GA4 ID is not set', function () {
            it('does nothing', function () {
                allow('get_field')->toBeCalled()->andReturn('an_api_key', 'a_product_type', null);

                ob_start();
                $this->scripts->addGA4();
                $result = ob_get_clean();

                expect($result)->toEqual('');
            });
        });
        context('API Key, product type and GA4 ID are set', function () {
            it('outputs the GA4 script tag', function () {
                allow('get_field')->toBeCalled()->andReturn('an_api_key', 'a_product_type', '123456');
                allow('esc_attr')->toBeCalled()->andRun(function ($input) {
                    return $input;
                });

                ob_start();
                $this->scripts->addGA4();
                $result = ob_get_clean();

                expect($result)->toEqual('<script async id="awc_gtag" src="https://www.googletagmanager.com/gtag/js?id=123456"></script>');
            });
        });
    });

    describe('->addGTM()', function () {
        context('API Key is not set', function () {
            it('does nothing', function () {
                allow('get_field')->toBeCalled()->andReturn(null, 'a_product_type', 'a_gtm_id');

                ob_start();
                $this->scripts->addGTM();
                $result = ob_get_clean();

                expect($result)->toEqual('');
            });
        });
        context('product type is not set', function () {
            it('does nothing', function () {
                allow('get_field')->toBeCalled()->andReturn('an_api_key', null, 'a_gtm_id');

                ob_start();
                $this->scripts->addGTM();
                $result = ob_get_clean();

                expect($result)->toEqual('');
            });
        });
        context('GTM ID is not set', function () {
            it('does nothing', function () {
                allow('get_field')->toBeCalled()->andReturn('an_api_key', 'a_product_type', null);

                ob_start();
                $this->scripts->addGTM();
                $result = ob_get_clean();

                expect($result)->toEqual('');
            });
        });
        context('API Key, product type and GTM ID are set', function () {
            it('outputs the GTM script tag', function () {
                allow('get_field')->toBeCalled()->andReturn('an_api_key', 'a_product_type', '123456');
                allow('esc_js')->toBeCalled()->andRun(function ($input) {
                    return $input;
                });

                ob_start();
                $this->scripts->addGTM();
                $result = ob_get_clean();

                expect($result)->toContain("window,document,'script','dataLayer','123456'");
            });
        });
    });
});
