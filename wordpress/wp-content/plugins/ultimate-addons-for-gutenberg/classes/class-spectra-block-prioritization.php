<?php
/**
 * Spectra Block Prioritization.
 *
 * @package UAGB
 * @since 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Spectra_Block_Prioritization.
 */
class Spectra_Block_Prioritization {

	/**
	 * Array of all blocks in order.
	 * This array should be in the same order as: /src/blocks.js.
	 *
	 * @var array
	 */
	private static $blocks = array(
		// Core Spectra Blocks.
		'container',
		'advanced-heading',
		'image',
		'icon',
		'buttons',
		'info-box',
		'call-to-action',
		'countdown',
		// Alphabetically Ordered Blocks.
		'blockquote',
		'content-timeline',
		'counter',
		'faq',
		'forms',
		'google-map',
		'how-to',
		'icon-list',
		'image-gallery',
		'inline-notice',
		'instagram-feed',
		'login',
		'loop-builder',
		'lottie',
		'marketing-button',
		'modal',
		'post-carousel',
		'post-grid',
		'post-timeline',
		'price-list',
		'register',
		'review',
		'separator',
		'slider',
		'social-share',
		'star-rating',
		'table-of-contents',
		'tabs',
		'taxonomy-list',
		'team',
		'testimonial',
		// Legacy Blocks.
		'columns',
		'section',
		'cf7-styler',
		'gf-styler',
		'post-masonry',
		'wp-search',
		// Extensions.
		'popup-builder',
	);

	/**
	 * Member Variable.
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 *  Initiator.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get the Block Priority of a Specific Block.
	 *
	 * @since 2.1.0
	 * @param string $block_name The slug of the required block.
	 */
	public static function get_block_priority( $block_name ) {
		return ( array_search( $block_name, self::$blocks, true ) + 1 );
	}
}

/**
 *  Prepare if class 'Spectra_Block_Prioritization' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
Spectra_Block_Prioritization::get_instance();
