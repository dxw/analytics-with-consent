<?php

namespace AnalyticsWithConsent;

class Scripts implements \Dxw\Iguana\Registerable
{
    public function register() : void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    public function enqueueScripts() : void
    {
        if (get_field('civic_cookie_control_api_key', 'option') && get_field('civic_cookie_control_product_type', 'option')) {
            wp_enqueue_script('civicCookieControl', 'https://cc.cdn.civiccomputing.com/9/cookieControl-9.x.min.js');
            wp_enqueue_script('civicCookieControlConfig', plugins_url('/static/js/config.js', dirname(__FILE__)), ['civicCookieControl']);
        }
    }
}
