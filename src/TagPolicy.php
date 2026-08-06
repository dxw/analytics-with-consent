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

		/** @var mixed $allowlistConfig **/
		$allowlistConfig = $policy['allowlist'] ?? null;

		/** @var mixed $blocklistConfig **/
		$blocklistConfig = $policy['blocklist'] ?? null;

		if (!$allowlistConfig && !$blocklistConfig) {
			return;
		}

		$dataLayerPush = 'window.dataLayer = window.dataLayer || [];';

		$encodedAllowlist = wp_json_encode($allowlistConfig);

		if (!in_array($encodedAllowlist, [false, 'null'], true)) {
			$dataLayerPush .= "window.dataLayer.push({'gtm.allowlist': " . $encodedAllowlist . "});";
		}

		$encodedBlocklist = wp_json_encode($blocklistConfig);

		if (!in_array($encodedBlocklist, [false, 'null'], true)) {
			$dataLayerPush .= "window.dataLayer.push({'gtm.blocklist': " . $encodedBlocklist . "});";
		}

		wp_add_inline_script(
			'civicCookieControlDefaultAnalytics',
			$dataLayerPush,
			'before',
		);
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
}
