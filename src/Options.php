<?php

namespace AnalyticsWithConsent;

class Options implements \Dxw\Iguana\Registerable
{
    /** @var array */
    private $defaults;
    /** @var string */
    private $fieldPrefix = 'civic_cookie_control_'; // for customisation options field names

    public function __construct()
    {
        $this->defaults = [
            'title' => 'This site uses cookies to store information on your computer.',
            'intro' => 'Some of these cookies are essential, while others help us to improve your experience by providing insights into how the site is being used.',
            'necessaryDescription' => 'Necessary cookies enable core functionality such as page navigation and access to secure areas. The website cannot function properly without these cookies, and can only be disabled by changing your browser preferences.',
            'analyticalDescription' => 'Analytical cookies help us to improve our website by collecting and reporting information on its usage.',
            'closeLabel' => 'Save and Close',
            'acceptSettings' => 'Accept all cookies',
            'rejectSettings' => 'Only accept necessary cookies'
        ];
    }

    public function getDefault(string $key): ?string
    {
        return $this->defaults[$key] ?? null;
    }

    // get customisation option handling field prefixes
    public function getCustomisationOption(string $key): ?string
    {
        if (strpos($key, $this->fieldPrefix) === false) {
            $key = ($this->fieldPrefix) . $key;
        }
        return get_field($key, 'option');
    }

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
            'title' => 'Analytics with Consent: Main settings',
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

        acf_add_local_field_group([
            'key' => 'group_5f986aaed3445',
            'title' => 'Analytics with Consent: Customisation',
            'fields' => [
                [
                    'key' => 'field_5f986ab76a820',
                    'label' => 'Title',
                    'name' => $this->fieldPrefix.'title',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => $this->getDefault('title'),
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],
                [
                    'key' => 'field_5f986ab76a822',
                    'label' => 'Intro',
                    'name' => $this->fieldPrefix.'intro',
                    'type' => 'textarea',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => $this->getDefault('intro'),
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],
                [
                    'key' => 'field_5f986ab76a824',
                    'label' => 'Necessary cookies decription',
                    'name' => $this->fieldPrefix.'necessaryDescription',
                    'type' => 'textarea',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => $this->getDefault('necessaryDescription'),
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],
                [
                    'key' => 'field_5f986ab76a826',
                    'label' => 'Analytical cookies description',
                    'name' => $this->fieldPrefix.'analyticalDescription',
                    'type' => 'textarea',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => $this->getDefault('analyticalDescription'),
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],
                [
                    'key' => 'field_5f986ab76a828',
                    'label' => 'Close button text',
                    'name' => $this->fieldPrefix.'closeLabel',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => $this->getDefault('closeLabel'),
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],
                [
                    'key' => 'field_5f986ab76a830',
                    'label' => 'Accept all button text',
                    'name' => $this->fieldPrefix.'acceptSettings',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => $this->getDefault('acceptSettings'),
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],
                [
                    'key' => 'field_5f986ab76a832',
                    'label' => 'Accept only necessary cookies button text',
                    'name' => $this->fieldPrefix.'rejectSettings',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => $this->getDefault('rejectSettings'),
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],
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
            'menu_order' => 1,
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
