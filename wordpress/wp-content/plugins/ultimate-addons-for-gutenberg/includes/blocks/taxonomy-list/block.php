<?php
/**
 * Block Information.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/taxonomy-list';
$block_data = array(
	'doc'              => 'taxonomy-list',
	'slug'             => '',
	'admin_categories' => array( 'content' ),
	'link'             => 'taxonomy',
	'title'            => __( 'Taxonomy List', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Display your content categorized as per post type.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'taxonomy-list' ),
	'deprecated'       => false,
	'dynamic_assets'   => array(
		'dir' => 'taxonomy-list',
	),
);
