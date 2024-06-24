<?php
/**
 * Block Information.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/google-map';
$block_data = array(
	'doc'              => 'google-map',
	'slug'             => '',
	'admin_categories' => array( 'content' ),
	'link'             => 'google-maps',
	'title'            => __( 'Google Maps', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Show a Google Map location on your website.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'google-map' ),
	'deprecated'       => false,
	'dynamic_assets'   => array(
		'dir' => 'google-map',
	),
);
