<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.1.0
 *
 * @package uagb
 */

$block_slug = 'uagb/counter';
$block_data = array(
	'doc'                 => 'counter',
	'slug'                => '',
	'admin_categories'    => array( 'content', 'post' ),
	'link'                => 'counter',
	'title'               => __( 'Counter', 'ultimate-addons-for-gutenberg' ),
	'description'         => __( 'This block allows you to add number counter.', 'ultimate-addons-for-gutenberg' ),
	'default'             => true,
	'extension'           => false,
	'priority'            => Spectra_Block_Prioritization::get_block_priority( 'counter' ),
	'static_dependencies' => array(
		'uagb-counter-js' => array(
			'src'  => UAGB_Scripts_Utils::get_js_url( 'spectra-counter' ),
			'dep'  => array(),
			'type' => 'js',
		),
		'uagb-countUp-js' => array(
			'type' => 'js',
		),
	),
	'dynamic_assets'      => array(
		'dir' => 'counter',
	),
);
