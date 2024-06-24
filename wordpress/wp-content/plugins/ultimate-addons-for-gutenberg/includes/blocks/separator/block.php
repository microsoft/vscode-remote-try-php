<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.6.0
 *
 * @package uagb
 */

$block_slug = 'uagb/separator';
$block_data = array(
	'doc'              => 'separator',
	'slug'             => '',
	'admin_categories' => array(),
	'link'             => 'separator',
	'title'            => __( 'Separator', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Add a modern separator to divide your page content with icon/text.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'separator' ),
	'deprecated'       => false,
	'dynamic_assets'   => array(
		'dir' => 'separator',
	),
);
