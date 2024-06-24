<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/wp-search';
$block_data = array(
	'doc'              => 'wp-search',
	'slug'             => '',
	'admin_categories' => array( 'content' ),
	'link'             => 'wp-search',
	'title'            => __( 'Search', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Add a search widget to let users search posts from your website.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'wp-search' ),
	'deprecated'       => true,
	'dynamic_assets'   => array(
		'dir' => 'wp-search',
	),
);
