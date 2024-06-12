<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/testimonial';
$block_data = array(
	'doc'                 => 'testimonial',
	'slug'                => '',
	'admin_categories'    => array( 'social' ),
	'link'                => 'testimonials',
	'title'               => __( 'Testimonials', 'ultimate-addons-for-gutenberg' ),
	'description'         => __( 'Display customer testimonials with customizable layouts.', 'ultimate-addons-for-gutenberg' ),
	'default'             => true,
	'extension'           => false,
	'priority'            => Spectra_Block_Prioritization::get_block_priority( 'testimonial' ),
	'deprecated'          => false,
	'static_dependencies' => array(
		'uagb-testimonial-js' => array(
			'src'  => UAGB_Scripts_Utils::get_js_url( 'testimonial' ),
			'dep'  => array(),
			'type' => 'js',
		),
		'uagb-imagesloaded'   => array(
			'type' => 'js',
		),
		'uagb-slick-js'       => array(
			'type' => 'js',
		),
		'uagb-slick-css'      => array(
			'type' => 'css',
		),
	),
	'dynamic_assets'      => array(
		'dir' => 'testimonial',
	),
);
