<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.2.0
 *
 * @package uagb
 */

$block_slug = 'uagb/modal';
$block_data = array(
	'doc'                 => 'modal',
	'slug'                => '',
	'admin_categories'    => array( 'content', 'post' ),
	'link'                => 'modal',
	'title'               => __( 'Modal', 'ultimate-addons-for-gutenberg' ),
	'description'         => __( 'This block allows you to add modal popup.', 'ultimate-addons-for-gutenberg' ),
	'default'             => true,
	'extension'           => false,
	'priority'            => Spectra_Block_Prioritization::get_block_priority( 'modal' ),
	'static_dependencies' => array(
		'uagb-modal-js' => array(
			'src'  => UAGB_Scripts_Utils::get_js_url( 'modal' ),
			'dep'  => array(),
			'type' => 'js',
		),
	),
	'dynamic_assets'      => array(
		'dir' => 'modal',
	),
);
