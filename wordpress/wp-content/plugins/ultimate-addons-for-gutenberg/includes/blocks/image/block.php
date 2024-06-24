<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/image';
$block_data = array(
	'slug'             => '',
	'admin_categories' => array( 'content', 'core' ),
	'link'             => 'image-block',
	'doc'              => 'image',
	'title'            => __( 'Image', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Add images on your webpage with multiple customization options.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'image' ),
	'deprecated'       => false,
	'dynamic_assets'   => array(
		'dir' => 'image',
	),
);
