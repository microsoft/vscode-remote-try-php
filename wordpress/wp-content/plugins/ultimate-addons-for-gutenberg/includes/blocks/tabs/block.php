<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/tabs';
$block_data = array(
	'slug'                => '',
	'admin_categories'    => array( 'content' ),
	'link'                => 'tabs',
	'doc'                 => 'tabs-block',
	'title'               => __( 'Tabs', 'ultimate-addons-for-gutenberg' ),
	'description'         => __( 'Display your content under different tabs.', 'ultimate-addons-for-gutenberg' ),
	'default'             => true,
	'extension'           => false,
	'priority'            => Spectra_Block_Prioritization::get_block_priority( 'tabs' ),
	'deprecated'          => false,
	'static_dependencies' => array(
		'uagb-tabs-js' => array(
			'src'  => UAGB_Scripts_Utils::get_js_url( 'tabs' ),
			'dep'  => array(),
			'type' => 'js',
		),
	),
	'dynamic_assets'      => array(
		'dir' => 'tabs',
	),
);
