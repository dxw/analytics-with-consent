<?php

namespace AnalyticsWithConsent;

class Scripts implements \Dxw\Iguana\Registerable
{
    public function register() : void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueStyles']);
    }

    public function enqueueScripts() : void
    {
        $apiKey = get_field('civic_cookie_control_api_key', 'option');
        $productType = get_field('civic_cookie_control_product_type', 'option');
        $googleAnalyticsId = get_field('google_analytics_id', 'option');
        if ($apiKey && $productType) {
            wp_enqueue_script('civicCookieControl', 'https://cc.cdn.civiccomputing.com/9/cookieControl-9.x.min.js');
            wp_enqueue_script('civicCookieControlConfig', plugins_url('/assets/js/config.js', dirname(__FILE__)), ['civicCookieControl']);
            wp_localize_script('civicCookieControlConfig', 'cookieControlConfig', [
                'apiKey' => $apiKey,
                'productType' => $productType,
                'googleAnalyticsId' => $googleAnalyticsId
            ]);
        }
    }

    public function enqueueStyles() : void
    {
        wp_enqueue_style('analytics-with-consent-styles', plugins_url('/assets/css/styles.css', dirname(__FILE__)));
    }
}
