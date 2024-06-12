<?php
/**
 * Block Information.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/star-rating';
$block_data = array(
	'slug'             => '',
	'doc'              => 'star-rating-block',
	'admin_categories' => array( 'creative' ),
	'link'             => 'star-rating',
	'title'            => __( 'Star Ratings', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Display customizable star ratings on your page.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'star-rating' ),
	'deprecated'       => false,
	'dynamic_assets'   => array(
		'dir' => 'star-rating',
	),
);
