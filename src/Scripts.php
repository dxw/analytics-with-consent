<?php

namespace AnalyticsWithConsent;

class Scripts implements \Dxw\Iguana\Registerable
{
    private $options;

    public function __construct(\AnalyticsWithConsent\Options $options)
    {
        $this->options = $options;
    }

    public function register() : void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueStyles']);
    }
    
    /**
     * Resolves options to saved value or plugin defaults
     *
     * @param string[] $options
     */
    private function resolveOptions(array $options) : array
    {
        $hash = [];
        foreach ($options as $option) {
            $hash[$option] =
                $this->options->getCustomisationOption($option)
                ?? $this->options->getDefault($option)
                ?? null;
        }
        return $hash;
    }

    public function enqueueScripts() : void
    {
        // main required options
        $options = [];
        $options['apiKey'] = get_field('civic_cookie_control_api_key', 'option');
        $options['productType'] = get_field('civic_cookie_control_product_type', 'option');
        $options['googleAnalyticsId'] = get_field('google_analytics_id', 'option');

        if ($options['apiKey'] && $options['productType']) {
            // add customisation options
            $customisations = $this->resolveOptions(
                [
                    'title',
                    'intro',
                    'necessaryDescription',
                    'analyticalDescription',
                    'closeLabel',
                    'acceptSettings',
                    'rejectSettings'
                ]
            );
            $options = array_merge($options, $customisations);

            // enqueue scripts with options
            wp_enqueue_script('civicCookieControl', 'https://cc.cdn.civiccomputing.com/9/cookieControl-9.x.min.js');
            wp_enqueue_script('civicCookieControlConfig', plugins_url('/assets/js/config.js', dirname(__FILE__)), ['civicCookieControl']);
            wp_localize_script('civicCookieControlConfig', 'cookieControlConfig', $options);
        }
    }

    public function enqueueStyles() : void
    {
        wp_enqueue_style('analytics-with-consent-styles', plugins_url('/assets/css/styles.css', dirname(__FILE__)));
    }
}
