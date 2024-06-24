<?php
/**
 * Block Information.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/social-share';
$block_data = array(
	'doc'              => 'social-share',
	'slug'             => '',
	'admin_categories' => array( 'social' ),
	'link'             => 'social-share',
	'title'            => __( 'Social Share', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Share your content on different social media platforms.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'social-share' ),
	'deprecated'       => false,
	'dynamic_assets'   => array(
		'dir' => 'social-share',
	),
);
