<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/restaurant-menu';
$block_data = array(
	'doc'              => 'price-list',
	'slug'             => '',
	'admin_categories' => array( 'content' ),
	'link'             => 'price-list',
	'title'            => __( 'Price List', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Create an attractive price list for your products.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'price-list' ),
	'deprecated'       => false,
	'static_css'       => 'price-list',
	'dynamic_assets'   => array(
		'dir' => 'restaurant-menu',
	),
);
