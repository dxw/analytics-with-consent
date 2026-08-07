<?php

namespace AnalyticsWithConsent;

class TagPolicy implements \Dxw\Iguana\Registerable
{
	public function register(): void
	{
		add_action('wp_enqueue_scripts', [$this, 'addPolicy'], 10, 0);
	}

	/**
	 * Push the tag policy to the GTM dataLayer before GTM initializes
	 *
	 * _All_ tags are allowed by default. This method applies a filter
	 * so that themes and plugins can specify a blocklist and/or allowlist.
	 * Note that blocklists override allowlists e.g. if the same tag exists
	 * in both lists, it is the blocklist that takes precedence.
	 *
	 * If populated, the tag policy is printed within the <head> element of
	 * each web page, prior to any other scripts from this plugin. This
	 * ensures that the policy exists in the dataLayer array, before GTM
	 * is initialized via `/assets/js/analytics.js`.
	 *
	 * @link https://developers.google.com/tag-platform/tag-manager/restrict
	 */
	public function addPolicy(): void
	{
		if (!$this->isGtmConfigured()) {
			return;
		}

		/** @var array{allowlist?: mixed, blocklist?: mixed} $policy **/
		$policy = apply_filters('awc_gtm_tag_policy', []);

		$allowlistConfig = $this->sanitizeConfig($policy['allowlist'] ?? null);
		$blocklistConfig = $this->sanitizeConfig($policy['blocklist'] ?? null);

		if (!$allowlistConfig && !$blocklistConfig) {
			return;
		}

		wp_add_inline_script(
			'civicCookieControlDefaultAnalytics',
			'window.dataLayer = window.dataLayer || [];'
				. $this->pushEncodedList('gtm.allowlist', $allowlistConfig)
				. $this->pushEncodedList('gtm.blocklist', $blocklistConfig),
			'before',
		);
	}

	private function sanitizeConfig(mixed $listConfig): array
	{
		if (!is_array($listConfig)) {
			return [];
		}

		$sanitizedConfig = array_values(array_filter($listConfig, function ($item) {
			return is_string($item) && $item !== '';
		}));

		if (count($sanitizedConfig) !== count($listConfig)) {
			$this->logMalformedPolicy();
		}

		return $sanitizedConfig;
	}

	private function pushEncodedList(string $key, mixed $listConfig): string
	{
		if (empty($listConfig)) {
			return '';
		}

		$encodedList = wp_json_encode($listConfig);

		if ($encodedList === false) {
			return '';
		}

		return "window.dataLayer.push({'{$key}': {$encodedList}});";
	}

	private function isGtmConfigured(): bool
	{
		if (!function_exists('get_field')) {
			return false;
		}

		$civicApiKey = trim((string) get_field('civic_cookie_control_api_key', 'option'));
		$civicProductType = trim((string) get_field('civic_cookie_control_product_type', 'option'));
		$gtmId = trim((string) get_field('google_analytics_gtm', 'option'));

		if (!($civicApiKey && $civicProductType && $gtmId)) {
			return false;
		}

		return true;
	}

	private function logMalformedPolicy(): void
	{
		_doing_it_wrong(
			'awc_gtm_tag_policy',
			'The awc_gtm_tag_policy filter must return an array of non-empty strings for "blocklist" and "allowlist". Malformed entries have been ignored.',
			''
		);

		error_log('[Plugin: Analytics with Consent]: The awc_gtm_tag_policy filter must return an array of non-empty strings for "blocklist" and "allowlist". Malformed entries have been ignored.');
	}
}
