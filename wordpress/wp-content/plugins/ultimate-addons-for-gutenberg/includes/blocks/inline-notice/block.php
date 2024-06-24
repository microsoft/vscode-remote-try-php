<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/inline-notice';
$block_data = array(
	'doc'                 => 'inline-notice',
	'slug'                => '',
	'admin_categories'    => array( 'content' ),
	'link'                => 'inline-notice',
	'title'               => __( 'Inline Notice', 'ultimate-addons-for-gutenberg' ),
	'description'         => __( 'Highlight important information using inline notice block.', 'ultimate-addons-for-gutenberg' ),
	'default'             => true,
	'extension'           => false,
	'priority'            => Spectra_Block_Prioritization::get_block_priority( 'inline-notice' ),
	'deprecated'          => false,
	'static_dependencies' => array(
		'uagb-inline-notice-js' => array(
			'src'        => UAGB_Scripts_Utils::get_js_url( 'inline-notice' ),
			'dep'        => array( 'uagb-cookie-lib' ),
			'skipEditor' => true,
			'type'       => 'js',
		),
		'uagb-cookie-lib'       => array(
			'type' => 'js',
		),
	),
	'dynamic_assets'      => array(
		'dir' => 'inline-notice',
	),
);
