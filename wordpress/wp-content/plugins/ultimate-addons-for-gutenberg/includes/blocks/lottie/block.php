<?php
/**
 * Block Information.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/lottie';
$block_data = array(
	'doc'                 => 'lottie',
	'slug'                => '',
	'admin_categories'    => array( 'creative' ),
	'link'                => 'lottie',
	'title'               => __( 'Lottie Animation', 'ultimate-addons-for-gutenberg' ),
	'description'         => __( 'Add customizable lottie animation on your page.', 'ultimate-addons-for-gutenberg' ),
	'default'             => true,
	'priority'            => Spectra_Block_Prioritization::get_block_priority( 'lottie' ),
	'deprecated'          => false,
	'static_dependencies' => array(
		'uagb-lottie-js'    => array(
			'src'        => UAGB_Scripts_Utils::get_js_url( 'lottie' ),
			'dep'        => array( 'uagb-bodymovin-js' ),
			'skipEditor' => true,
			'type'       => 'js',
		),
		'uagb-bodymovin-js' => array(
			'type' => 'js',
		),
	),
	'dynamic_assets'      => array(
		'dir' => 'lottie',
	),
);
