<?php

namespace AnalyticsWithConsent;

class Embeds implements \Dxw\Iguana\Registerable
{
	public function register(): void
	{
		add_filter('embed_oembed_html', [$this, 'embedPlaceholder'], 10, 4);
	}

	public function embedPlaceholder(string $html, string $url, array $attr, int $post_ID): string
	{
		if (!$this->isThirdPartyMediaEmbedConsentEnabled() || is_admin()) {
			return $html;
		}
		return $this->placeholderMarkup($html);
	}

	private function isThirdPartyMediaEmbedConsentEnabled(): bool
	{
		if (!function_exists('get_field')) {
			return false;
		}

		return get_field('third_party_media_embed_consent', 'option') === true;
	}

	private function placeholderMarkup(string $html): string
	{
		$encodedHtml = base64_encode($html);
		return '<div class="awc-embed-placeholder" data-embed="' . $encodedHtml . '">Third party media content is blocked to comply with your cookie consent choices. Please enable third party media embed cookies to view this content</div>';
	}
}
