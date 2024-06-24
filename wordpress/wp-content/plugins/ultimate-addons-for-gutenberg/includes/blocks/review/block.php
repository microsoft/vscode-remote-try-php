<?php
/**
 * Block Information.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/review';
$block_data = array(
	'doc'              => 'review-schema',
	'slug'             => '',
	'admin_categories' => array( 'seo' ),
	'link'             => 'review-schema',
	'title'            => __( 'Review', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Add reviews to items with Schema support.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'review' ),
	'deprecated'       => false,
	'dynamic_assets'   => array(
		'dir' => 'review',
	),
);
