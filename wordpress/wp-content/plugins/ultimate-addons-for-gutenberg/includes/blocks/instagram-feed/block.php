<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.4.1
 *
 * @package uagb
 */

$block_slug = 'uagb/instagram-feed';
$block_data = array(
	'doc'              => 'instagram-feed',
	'slug'             => '',
	'admin_categories' => array( 'social', 'pro' ),
	'link'             => 'instagram-feed',
	'title'            => __( 'Instagram Feed', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'This block allows you to add Instagram Feeds.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'instagram-feed' ),
	'pro_filler'       => true,
);
