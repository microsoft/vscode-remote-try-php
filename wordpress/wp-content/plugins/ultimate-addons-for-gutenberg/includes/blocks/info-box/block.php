<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/info-box';
$block_data = array(
	'doc'              => 'infobox',
	'slug'             => '',
	'admin_categories' => array( 'core', 'content' ),
	'link'             => 'info-box',
	'title'            => __( 'Info Box', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Add image/icon, seperator and text description using a single block.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'info-box' ),
	'deprecated'       => false,
	'dynamic_assets'   => array(
		'dir' => 'info-box',
	),
);
