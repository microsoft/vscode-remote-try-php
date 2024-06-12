<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/post-carousel';
$block_data = array(
	'doc'                 => 'post-carousel',
	'slug'                => '',
	'admin_categories'    => array( 'content', 'post' ),
	'link'                => 'post-carousel',
	'title'               => __( 'Post Carousel', 'ultimate-addons-for-gutenberg' ),
	'description'         => __( 'Display your posts in a sliding carousel layout.', 'ultimate-addons-for-gutenberg' ),
	'default'             => true,
	'extension'           => false,
	'priority'            => Spectra_Block_Prioritization::get_block_priority( 'post-carousel' ),
	'deprecated'          => false,
	'dynamic_assets'      => array(
		'dir' => 'post-carousel',
	),
	'static_dependencies' => array(
		'uagb-post-js'      => array(
			'src'  => UAGB_Scripts_Utils::get_js_url( 'post' ),
			'dep'  => array( 'jquery', 'uagb-slick-js' ),
			'type' => 'js',
		),
		'uagb-imagesloaded' => array(
			'type' => 'js',
		),
		'uagb-slick-js'     => array(
			'type' => 'js',
		),
		'uagb-slick-css'    => array(
			'type' => 'css',
		),
	),
	'static_css'          => 'post',
);
