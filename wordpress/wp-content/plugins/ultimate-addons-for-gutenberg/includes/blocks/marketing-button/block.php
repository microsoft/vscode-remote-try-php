<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/marketing-button';
$block_data = array(
	'doc'              => 'marketing-button',
	'slug'             => '',
	'admin_categories' => array( 'creative' ),
	'link'             => 'marketing-button',
	'title'            => __( 'Marketing Button', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Add a marketing call to action button with a short description.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'marketing-button' ),
	'deprecated'       => false,
	'dynamic_assets'   => array(
		'dir' => 'marketing-button',
	),
);
