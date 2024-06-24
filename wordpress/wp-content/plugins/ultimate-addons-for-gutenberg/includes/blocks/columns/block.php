<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/columns';
$block_data = array(
	'admin_categories' => array(),
	'doc'              => 'advanced-columns',
	'slug'             => '',
	'link'             => 'advanced-columns',
	'title'            => __( 'Advanced Columns', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Insert a number of columns within a single row.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'columns' ),
	'deprecated'       => true,
	'dynamic_assets'   => array(
		'dir' => 'columns',
	),
);
