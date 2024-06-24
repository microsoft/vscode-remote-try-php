<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/container';
$block_data = array(
	'slug'                => '',
	'admin_categories'    => array( 'content', 'core' ),
	'link'                => 'container-layout',
	'doc'                 => 'container',
	'title'               => __( 'Container', 'ultimate-addons-for-gutenberg' ),
	'description'         => __( 'Create beautiful layouts with flexbox powered container block.', 'ultimate-addons-for-gutenberg' ),
	'default'             => true,
	'extension'           => false,
	'priority'            => Spectra_Block_Prioritization::get_block_priority( 'container' ),
	'deprecated'          => false,
	'dynamic_assets'      => array(
		'dir' => 'container',
	),
	'static_dependencies' => array(
		'uagb-block-positioning-js'  => array(
			'type' => 'js',
		),
		'uagb-block-positioning-css' => array(
			'type' => 'css',
		),
	),
);
