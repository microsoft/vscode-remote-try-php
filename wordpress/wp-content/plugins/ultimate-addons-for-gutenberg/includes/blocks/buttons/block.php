<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/buttons';
$block_data = array(
	'doc'              => 'multi-buttons',
	'slug'             => '',
	'admin_categories' => array( 'creative', 'core' ),
	'link'             => 'buttons',
	'title'            => __( 'Buttons', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Add multiple buttons to redirect user to different webpages.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'buttons' ),
	'deprecated'       => false,
	'dynamic_assets'   => array(
		'dir' => 'buttons',
	),
);
