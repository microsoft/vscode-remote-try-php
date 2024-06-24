<?php
/**
 * Block Information.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/content-timeline';
$block_data = array(
	'doc'                 => 'content-timeline',
	'slug'                => '',
	'admin_categories'    => array( 'content' ),
	'link'                => 'content-timeline',
	'title'               => __( 'Content Timeline', 'ultimate-addons-for-gutenberg' ),
	'description'         => __( 'Create a timeline displaying contents of your site.', 'ultimate-addons-for-gutenberg' ),
	'default'             => true,
	'extension'           => false,
	'priority'            => Spectra_Block_Prioritization::get_block_priority( 'content-timeline' ),
	'deprecated'          => false,
	'static_dependencies' => array(
		'uagb-timeline-js' => array(
			'src'  => UAGB_Scripts_Utils::get_js_url( 'timeline' ),
			'dep'  => array(),
			'type' => 'js',
		),
	),
	'static_css'          => 'timeline',
	'dynamic_assets'      => array(
		'dir' => 'content-timeline',
	),
);
