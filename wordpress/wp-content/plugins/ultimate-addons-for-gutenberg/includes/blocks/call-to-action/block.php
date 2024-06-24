<?php
/**
 * Block Information.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/call-to-action';
$block_data = array(
	'slug'             => '',
	'doc'              => 'call-to-action-2',
	'admin_categories' => array( 'core', 'content' ),
	'link'             => 'call-to-action',
	'title'            => __( 'Call To Action', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Add a button along with heading and description.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'call-to-action' ),
	'deprecated'       => false,
	'dynamic_assets'   => array(
		'dir' => 'call-to-action',
	),
);
