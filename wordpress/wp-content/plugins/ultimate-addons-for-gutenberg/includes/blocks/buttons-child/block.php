<?php
/**
 * Block Information.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/buttons-child';
$block_data = array(
	'slug'                => '',
	'link'                => '',
	'title'               => __( 'Button', 'ultimate-addons-for-gutenberg' ),
	'description'         => __( 'Customize this button as per your need.', 'ultimate-addons-for-gutenberg' ),
	'default'             => true,
	'is_child'            => true,
	'extension'           => false,
	'dynamic_assets'      => array(
		'dir' => 'buttons-child',
	),
	'deprecated'          => false,
	'static_dependencies' => array(
		'uagb-button-child-js' => array(
			'src'  => UAGB_Scripts_Utils::get_js_url( 'uagb-button-child' ),
			'dep'  => array(),
			'type' => 'js',
		),
	),
);
