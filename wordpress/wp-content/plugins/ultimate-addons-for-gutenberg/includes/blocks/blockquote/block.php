<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/blockquote';
$block_data = array(
	'doc'              => 'blockquote',
	'slug'             => '',
	'admin_categories' => array( 'social' ),
	'link'             => 'blockquote',
	'title'            => __( 'Blockquote', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Display qoutes/quoted texts using blockquote.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'blockquote' ),
	'deprecated'       => false,
	'dynamic_assets'   => array(
		'dir' => 'blockquote',
	),
);
