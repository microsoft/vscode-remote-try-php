<?php
/**
 * Block Information.
 *
 * @since 2.6.0
 *
 * @package uagb
 */

$block_slug = 'uagb/loop-builder';
$block_data = array(
	'doc'              => 'loop-builder',
	'slug'             => '',
	'admin_categories' => array( 'content', 'post', 'pro' ),
	'link'             => 'loop-builder',
	'title'            => __( 'Loop Builder', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'This block allows you to generate custom loop from different posts.', 'ultimate-addons-for-gutenberg' ), // Need to be improved.
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'loop-builder' ),
	'pro_filler'       => true,
);
