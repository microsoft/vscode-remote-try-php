<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.1.0
 *
 * @package uagb
 */

$block_slug = 'uagb/image-gallery';
$block_data = array(
	'doc'                 => 'image-gallery',
	'slug'                => '',
	'admin_categories'    => array( 'content', 'creative' ),
	'link'                => 'image-gallery',
	'title'               => __( 'Image Gallery', 'ultimate-addons-for-gutenberg' ),
	'description'         => __( 'Create a highly customizable image gallery', 'ultimate-addons-for-gutenberg' ),
	'default'             => true,
	'extension'           => false,
	'priority'            => Spectra_Block_Prioritization::get_block_priority( 'image-gallery' ),
	'static_dependencies' => array(
		'uagb-image-gallery-js' => array(
			'src'  => UAGB_Scripts_Utils::get_js_url( 'image-gallery' ),
			'dep'  => array(),
			'type' => 'js',
		),
		'uagb-masonry'          => array(
			'type' => 'js',
		),
		'uagb-imagesloaded'     => array(
			'type' => 'js',
		),
		'uagb-slick-js'         => array(
			'type' => 'js',
		),
		'uagb-swiper-js'        => array(
			'type' => 'js',
		),
		'uagb-slick-css'        => array(
			'type' => 'css',
		),
		'uagb-swiper-css'       => array(
			'type' => 'css',
		),
	),
	'dynamic_assets'      => array(
		'dir' => 'image-gallery',
	),
);
