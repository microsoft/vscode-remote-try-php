<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/team';
$block_data = array(
	'doc'              => 'team',
	'slug'             => '',
	'admin_categories' => array( 'social' ),
	'link'             => 'team',
	'title'            => __( 'Team', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Showcase your team by displaying info and social media profiles.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'team' ),
	'deprecated'       => false,
	'dynamic_assets'   => array(
		'dir' => 'team',
	),
);
