<?php

namespace AnalyticsWithConsent;

class Embeds implements \Dxw\Iguana\Registerable
{
	public function register(): void
	{
		add_filter('embed_oembed_html', [$this, 'embedPlaceholder'], 10, 4);
		add_filter('render_block', [$this, 'filterBlock'], 10, 2);
	}

	public function filterBlock(string $blockContent, array $block): string
	{
		if (!$this->isThirdPartyMediaEmbedConsentEnabled() || is_admin()) {
			return $blockContent;
		}
		if (strpos($block['blockName'], 'core-embed/') === 0 || $block['blockName'] === 'core/embed') {
			$url = '';
			if (array_key_exists('attrs', $block) && array_key_exists('url', $block['attrs'])) {
				$url = $block['attrs']['url'];
			}
			return $this->placeholderMarkup($blockContent, $url);
		}
		return $blockContent;
	}

	public function embedPlaceholder(string $html, string $url): string
	{
		if (!$this->isThirdPartyMediaEmbedConsentEnabled() || is_admin()) {
			return $html;
		}
		if (str_contains($html, 'awc-embed-placeholder')) {
			return $html;
		}
		return $this->placeholderMarkup($html, $url);
	}

	private function isThirdPartyMediaEmbedConsentEnabled(): bool
	{
		if (!function_exists('get_field')) {
			return false;
		}

		return get_field('third_party_media_embed_consent', 'option') === true;
	}

	private function placeholderMarkup(string $html, string $url): string
	{
		$encodedHtml = base64_encode($html);
		$output = '<div class="awc-embed-placeholder" data-embed="' . $encodedHtml . '"><p>Third party media content is blocked to comply with your cookie consent choices. Please enable third party media embed cookies to view this content.</p>';
		if (!empty($url)) {
			$output .= '<p>You can view the content on the external site here: <a href="' . esc_url($url) . '">' . esc_url($url) . '</a></p>';
		}
		return $output . '</div>';
	}
}
