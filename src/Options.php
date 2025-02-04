<?php

namespace AnalyticsWithConsent;

class Options implements \Dxw\Iguana\Registerable
{
	public function register(): void
	{
		add_action('acf/init', [$this, 'acfInit']);
		add_filter('acf/validate_value/name=google_analytics_id', [$this, 'validateTrimmedText'], 10, 4);
		add_filter('acf/validate_value/name=ga_4_id', [$this, 'validateTrimmedText'], 10, 4);
		add_filter('acf/validate_value/name=google_analytics_gtm', [$this, 'validateTrimmedText'], 10, 4);
		add_filter('acf/validate_value/name=gtm_marketing_cookies', [$this, 'validateTrimmedText'], 10, 4);
		add_filter('acf/validate_value/name=civic_cookie_control_api_key', [$this, 'validateTrimmedText'], 10, 4);
		add_filter('acf/validate_value/name=hotjar_id', [$this, 'validateTrimmedText'], 10, 4);
	}

	/**
	 * @psalm-param boolean | string $valid
	 * @psalm-param mixed $value
	 * @psalm-param array<mixed> $field
	 * @psalm-param string $input_name
	 * @return boolean | string
	 */
	public function validateTrimmedText($valid, $value, $field, $input_name)
	{
		if ($valid !== true) {
			return $valid;
		}
		if (trim($value) !== $value) {
			$valid = 'Please remove any leading or trailing spaces.';
		}
		return $valid;
	}

	public function acfInit(): void
	{
		if (!function_exists('acf_add_options_sub_page')) {
			return;
		}

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
					'label' => 'Google Analytics ID (Universal Analytics)',
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
					'key' => 'field_dc1b03f153de4',
					'label' => 'Google Analytics ID (GA4)',
					'name' => 'ga_4_id',
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
					'placeholder' => 'G-',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				],
				[
					'key' => 'field_5f986ab76a820',
					'label' => 'Google Analytics GTM',
					'name' => 'google_analytics_gtm',
					'type' => 'text',
					'instructions' => 'Note: if you use GTM, it is up to you to configure GTM correctly to ensure it checks for "ad_storage" and/or "analytics_storage" consent status when adding scripts.',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'placeholder' => 'GTM-',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				],
				[
					'key' => 'field_4024de106295e',
					'label' => 'Include GTM marketing consent?',
					'name' => 'gtm_marketing_consent',
					'type' => 'true_false',
					'instructions' => 'If you tick this, then the plugin will control GTM\'s "ad_storage" state, as well as the "analytics_storage" state.',
					'required' => 0,
					'conditional_logic' => [
						[
							[
								'field' => 'field_5f986ab76a820',
								'operator' => '!=empty',
							],
						],
					],
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => ''
				],
				[
					'key' => 'field_634548a37c19d',
					'label' => 'GTM Marketing cookies',
					'name' => 'gtm_marketing_cookies',
					'type' => 'text',
					'instructions' => 'The marketing cookies that will be set by GTM. Should be formatted as a comma-separated list, without quotes, e.g. cookie_1,cookie_2,cookie_3',
					'required' => 0,
					'conditional_logic' => [
						[
							[
								'field' => 'field_4024de106295e',
								'operator' => '==',
								'value' => '1',
							],
						],
					],
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
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
				],
				[
					'key' => 'field_5f986ace6a82b',
					'label' => 'Hotjar ID',
					'name' => 'hotjar_id',
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
					'placeholder' => '0000000',
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
