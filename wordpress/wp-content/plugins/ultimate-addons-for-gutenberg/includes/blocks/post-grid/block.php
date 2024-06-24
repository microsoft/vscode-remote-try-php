<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/post-grid';
$block_data = array(
	'doc'                 => 'post-grid',
	'slug'                => '',
	'admin_categories'    => array( 'content', 'post' ),
	'link'                => 'post-grid',
	'title'               => __( 'Post Grid', 'ultimate-addons-for-gutenberg' ),
	'description'         => __( 'Display your posts in a grid layout.', 'ultimate-addons-for-gutenberg' ),
	'default'             => true,
	'extension'           => false,
	'priority'            => Spectra_Block_Prioritization::get_block_priority( 'post-grid' ),
	'deprecated'          => false,
	'static_css'          => 'post',
	'dynamic_assets'      => array(
		'dir' => 'post-grid',
	),
	'static_dependencies' => array(
		'uagb-post-js' => array(
			'src'  => UAGB_Scripts_Utils::get_js_url( 'post' ),
			'type' => 'js',
		),
	),
);
