<?php
/**
 * Helper functions that are useful throughout the plugin.
 *
 * @package Automattic\WooCommerce\Pinterest
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest;

/**
 * Trait PluginHelper
 */
trait PluginHelper {

	/**
	 * Get the plugin slug.
	 *
	 * @return string
	 */
	protected function get_slug(): string {
		return 'pinterest';
	}

	/**
	 * Get the prefix used for plugin's metadata keys in the database.
	 *
	 * @return string
	 */
	protected function get_meta_key_prefix(): string {
		return "_wc_{$this->get_slug()}";
	}

	/**
	 * Prefix a meta data key with the plugin prefix.
	 *
	 * @param string $key Meta key name.
	 *
	 * @return string
	 */
	protected function prefix_meta_key( string $key ): string {
		$prefix = $this->get_meta_key_prefix();

		return "{$prefix}_{$key}";
	}

	/**
	 * Check whether debugging mode is enabled.
	 *
	 * @return bool Whether debugging mode is enabled.
	 */
	protected function is_debug_mode(): bool {
		return defined( 'WP_DEBUG' ) && WP_DEBUG;
	}

	/**
	 * Helper method to return the onboarding page parameters.
	 *
	 * @return array The onboarding page parameters.
	 */
	protected function onboarding_page_parameters(): array {

		return array(
			'page' => 'wc-admin',
			'path' => '/pinterest/onboarding',
		);
	}

	/**
	 * Check wether if the current page is the Get Started page.
	 *
	 * @return bool Wether the current page is the Get Started page.
	 */
	protected function is_onboarding_page(): bool {

		$page_parameters = $this->onboarding_page_parameters();

		return count( $page_parameters ) === count( array_intersect_assoc( $_GET, $page_parameters ) ); // phpcs:disable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Strip HTML tags from the given string.
	 * This is intended to be used with the description field on the feed and the Rich Pins.
	 *
	 * @since 1.2.3
	 *
	 * @param string $string           String with HTML tags.
	 * @param bool   $apply_shortcodes Whether to apply shortcodes or not.
	 *
	 * @return string
	 */
	protected static function strip_tags_from_string( $string, $apply_shortcodes = false ) {

		if ( $apply_shortcodes ) {
			// Apply active shortcodes.
			$string = do_shortcode( $string );
		} else {
			// Strip out active shortcodes.
			$string = strip_shortcodes( $string );
		}

		// Strip HTML tags from description.
		$string = wp_strip_all_tags( $string );

		// Strip [&hellip] character from description.
		$string = str_replace( '[&hellip;]', '...', $string );

		return $string;
	}
}
