<?php
/**
 * Yoast SEO Compatibility File.
 *
 * @package Astra
 */

/**
 * Astra Yoast SEO Compatibility
 *
 * @since 2.1.2
 */
class Astra_Yoast_SEO {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'wpseo_sitemap_exclude_post_type', array( $this, 'sitemap_exclude_post_type' ), 10, 2 );
	}

	/**
	 * Exclude One Content Type From Yoast SEO Sitemap
	 *
	 * @param  string $value value.
	 * @param  string $post_type Post Type.
	 * @since 2.1.2
	 */
	public function sitemap_exclude_post_type( $value, $post_type ) {
		return 'astra-advanced-hook' === $post_type;
	}

}

/**
 * Kicking this off by object
 */
new Astra_Yoast_SEO();
