<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/table-of-contents';
$block_data = array(
	'doc'                 => 'table-of-contents',
	'slug'                => '',
	'admin_categories'    => array( 'seo' ),
	'link'                => 'table-of-contents',
	'title'               => __( 'Table of Contents', 'ultimate-addons-for-gutenberg' ),
	'description'         => __( 'Add a table of contents to allow page navigation.', 'ultimate-addons-for-gutenberg' ),
	'default'             => true,
	'extension'           => false,
	'priority'            => Spectra_Block_Prioritization::get_block_priority( 'table-of-contents' ),
	'deprecated'          => false,
	'static_dependencies' => array(
		'uagb-table-of-contents' => array(
			'src'  => UAGB_Scripts_Utils::get_js_url( 'table-of-contents' ),
			'dep'  => array(),
			'type' => 'js',
		),
	),
	'dynamic_assets'      => array(
		'dir' => 'table-of-contents',
	),
);
