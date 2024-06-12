<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/icon-list';
$block_data = array(
	'doc'              => 'icon-list',
	'slug'             => '',
	'admin_categories' => array( 'creative' ),
	'link'             => 'icon-list',
	'title'            => __( 'Icon List', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Create a list highlighted with icons/images.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'icon-list' ),
	'deprecated'       => false,
	'dynamic_assets'   => array(
		'dir' => 'icon-list',
	),
);
