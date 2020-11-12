<?php

namespace AnalyticsWithConsent;

class Options implements \Dxw\Iguana\Registerable
{
    public function register() : void
    {
        add_action('acf/init', [$this, 'acfInit']);
    }

    public function acfInit() : void
    {
        acf_add_options_sub_page([
            'page_title' => 'Analytics with Consent',
            'menu_slug' => 'analytics-with-consent',
            'parent_slug' => 'options-general.php',
        ]);

        acf_add_local_field_group([
            'key' => 'group_5f986aaed3444',
            'title' => 'Analytics with Consent',
            'fields' => [
                [
                    'key' => 'field_5f986ab76a819',
                    'label' => 'Google Analytics ID',
                    'name' => 'google_analytics_id',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => '',
                    'placeholder' => 'UA-',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],
                [
                    'key' => 'field_5f986ace6a81a',
                    'label' => 'CIVIC Cookie Control API key',
                    'name' => 'civic_cookie_control_api_key',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],
                [
                    'key' => 'field_5fad0790934c6',
                    'label' => 'Civic Cookie Control Product Type',
                    'name' => 'civic_cookie_control_product_type',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'choices' => [
                        'COMMUNITY' => 'Community',
                        'PRO' => 'Pro',
                        'PRO_MULTISITE' => 'Pro Multisite',
                    ],
                    'default_value' => false,
                    'allow_null' => 0,
                    'multiple' => 0,
                    'ui' => 0,
                    'return_format' => 'value',
                    'ajax' => 0,
                    'placeholder' => '',
                ]
            ],
            'location' => [
                [
                    [
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'analytics-with-consent',
                    ],
                ],
            ],
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => 1,
            'description' => '',
        ]);
    }
}
