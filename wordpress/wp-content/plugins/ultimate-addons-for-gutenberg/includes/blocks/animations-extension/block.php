<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.6.0
 *
 * @package uagb
 */

$block_slug = 'uagb/animations-extension';
$block_data = array(
	'slug'                => '',
	'title'               => __( 'Animations Extension', 'ultimate-addons-for-gutenberg' ),
	'description'         => __( 'Add animations to Spectra blocks.', 'ultimate-addons-for-gutenberg' ),
	'default'             => true,
	'extension'           => true,
	'attributes'          => array(),
	'deprecated'          => false,
	'static_dependencies' => array(
		'uagb-aos-js'       => array(
			'src'  => UAGB_Scripts_Utils::get_js_url( 'aos' ),
			'dep'  => array(),
			'type' => 'js',
		),
		'uagb-aos-css'      => array(
			'type' => 'css',
		),
		'uagb-animation-js' => array(
			'src'        => UAGB_Scripts_Utils::get_js_url( 'spectra-animations' ),
			'dep'        => array( 'uagb-aos-js' ),
			'type'       => 'js',
			'skipEditor' => true,
		),
	),
);
