<?php

namespace AnalyticsWithConsent;

class Scripts implements \Dxw\Iguana\Registerable
{
	public function register(): void
	{
		add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
		add_action('wp_enqueue_scripts', [$this, 'enqueueStyles']);
		add_action('wp_head', [$this, 'addGA4']);
		add_action('wp_head', [$this, 'addGTM']);
		add_filter('plugin_action_links_analytics-with-consent/index.php', [$this, 'addActionLinks']);
	}

	public function addActionLinks(array $links): array
	{
		$settingsLink = '<a href="' . admin_url('options-general.php?page=analytics-with-consent') . '">Settings</a>';
		array_push($links, $settingsLink);
		return $links;
	}

	public function enqueueScripts(): void
	{
		if (!function_exists('get_field')) {
			return;
		}

		$apiKey = trim(get_field('civic_cookie_control_api_key', 'option') ?? '');
		$productType = trim(get_field('civic_cookie_control_product_type', 'option') ?? '');
		$googleAnalyticsId = trim(get_field('google_analytics_id', 'option') ?? '');
		$ga4Id = trim(get_field('ga_4_id', 'option') ?? '');
		$gtmId = trim(get_field('google_analytics_gtm', 'option') ?? '');
		$hotjarId = trim(get_field('hotjar_id', 'option') ?? '');
		if ($apiKey && $productType) {
			wp_enqueue_script('civicCookieControl', 'https://cc.cdn.civiccomputing.com/9/cookieControl-9.x.min.js');
			wp_enqueue_script('civicCookieControlDefaultAnalytics', plugins_url('/assets/js/analytics.js', dirname(__FILE__)), ['civicCookieControl']);
			wp_localize_script('civicCookieControlDefaultAnalytics', 'cookieControlDefaultAnalytics', [
				'googleAnalyticsId' => $googleAnalyticsId,
				'ga4Id' => $ga4Id,
				'gtmId' => $gtmId,
				'hjid' 	=> $hotjarId
			]);
			wp_enqueue_script('civicCookieControlConfig', plugins_url('/assets/js/config.js', dirname(__FILE__)), ['civicCookieControl', 'civicCookieControlDefaultAnalytics']);
			wp_localize_script('civicCookieControlConfig', 'cookieControlConfig', $this->defaultConfig());
		}
	}

	public function enqueueStyles(): void
	{
		wp_enqueue_style('analytics-with-consent-styles', plugins_url('/assets/css/styles.css', dirname(__FILE__)));
	}

	public function addGA4(): void
	{
		if (!function_exists('get_field')) {
			return;
		}

		$apiKey = trim(get_field('civic_cookie_control_api_key', 'option') ?? '');
		$productType = trim(get_field('civic_cookie_control_product_type', 'option') ?? '');
		$ga4Id = trim(get_field('ga_4_id', 'option') ?? '');
		if ($apiKey && $productType && $ga4Id) {
			wp_print_script_tag(['async' => true, 'id' => 'awc_gtag', 'src' => esc_url("https://www.googletagmanager.com/gtag/js?id=".$ga4Id)]);
		}
	}

	public function addGTM(): void
	{
		if (!function_exists('get_field')) {
			return;
		}

		$apiKey = trim(get_field('civic_cookie_control_api_key', 'option') ?? '');
		$productType = trim(get_field('civic_cookie_control_product_type', 'option') ?? '');
		$gtmId = trim(get_field('google_analytics_gtm', 'option') ?? '');
		if ($apiKey && $productType && $gtmId) {
			$js_tracking_id = "
window.dataLayer = window.dataLayer || [];

(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','".esc_js($gtmId)."');";
			wp_print_inline_script_tag($js_tracking_id);
		}
	}

	private function defaultConfig(): array
	{
		if (!function_exists('get_field')) {
			return [];
		}

		$optionalCookies = [
			[
				'name' => 'analytics',
				'label' => 'Analytical Cookies',
				'description' => 'Analytical cookies help us to improve our website by collecting and reporting information on its usage.',
				'cookies' => ['_ga', '_gid', '_gat', '__utma', '__utmt', '__utmb', '__utmc', '__utmz', '__utmv'],
				'onAccept' => "analyticsWithConsent.gaAccept",
				'onRevoke' => "analyticsWithConsent.gaRevoke"
			]
		];
		if (get_field('gtm_marketing_consent', 'option')) {
			$optionalCookies[] = [
				'name' => 'marketing',
				'label' => 'Marketing Cookies',
				'description' => 'Marketing cookies help us to improve the relevancy of advertising campaigns you receive from us.',
				'cookies' => (explode(',', trim(esc_js(get_field('gtm_marketing_cookies', 'option'))))),
				'onAccept' => "analyticsWithConsent.marketingAccept",
				'onRevoke' => "analyticsWithConsent.marketingRevoke"
			];
		}
		return apply_filters('awc_civic_cookie_control_config', [
			'apiKey' => trim(get_field('civic_cookie_control_api_key', 'option') ?? ''),
			'product' => trim(get_field('civic_cookie_control_product_type', 'option') ?? ''),
			'closeStyle' => 'button',
			'initialState' => 'open',
			'text' => [
				'closeLabel' => 'Save and Close',
				'acceptSettings' => 'Accept all cookies',
				'rejectSettings' => 'Only accept necessary cookies'
			],
			'branding' => [
				'removeAbout' => true
			],
			'position' => 'LEFT',
			'theme' => 'DARK',
			'subDomains' => false,
			'toggleType' => 'checkbox',
			'optionalCookies' => $optionalCookies,
			'necessaryCookies' => ['wp-postpass_*']
		]);
	}
}
