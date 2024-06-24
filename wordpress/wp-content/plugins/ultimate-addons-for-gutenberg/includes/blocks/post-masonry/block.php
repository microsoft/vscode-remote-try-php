<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/post-masonry';
$block_data = array(
	'doc'                 => 'post-masonry',
	'slug'                => '',
	'admin_categories'    => array( 'content', 'post' ),
	'link'                => 'post-layouts/#post-masonary',
	'title'               => __( 'Post Masonry', 'ultimate-addons-for-gutenberg' ),
	'description'         => __( 'Display your posts in a masonary layout.', 'ultimate-addons-for-gutenberg' ),
	'default'             => true,
	'extension'           => false,
	'priority'            => Spectra_Block_Prioritization::get_block_priority( 'post-masonry' ),
	'deprecated'          => true,
	'static_dependencies' => array(
		'uagb-post-js'      => array(
			'src'  => UAGB_Scripts_Utils::get_js_url( 'post' ),
			'dep'  => array( 'jquery' ),
			'type' => 'js',
		),
		'uagb-masonry'      => array(
			'type' => 'js',
		),
		'uagb-imagesloaded' => array(
			'type' => 'js',
		),
	),
	'dynamic_assets'      => array(
		'dir' => 'post-masonry',
	),
	'static_css'          => 'post',
);
