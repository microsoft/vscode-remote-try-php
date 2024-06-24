<?php
/**
 * Block Information.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/forms';
$block_data = array(
	'slug'                => '',
	'admin_categories'    => array( 'form' ),
	'link'                => 'forms',
	'doc'                 => 'uag-forms-block',
	'title'               => __( 'Form', 'ultimate-addons-for-gutenberg' ),
	'description'         => __( 'Add easily customizable forms to gather information.', 'ultimate-addons-for-gutenberg' ),
	'default'             => true,
	'priority'            => Spectra_Block_Prioritization::get_block_priority( 'forms' ),
	'deprecated'          => false,
	'static_dependencies' => array(
		'uagb-forms-js' => array(
			'src'  => UAGB_Scripts_Utils::get_js_url( 'forms' ),
			'dep'  => array(),
			'type' => 'js',
		),
	),
	'dynamic_assets'      => array(
		'dir' => 'forms',
	),
);
