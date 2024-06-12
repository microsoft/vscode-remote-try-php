<?php
/**
 * Block Information.
 *
 * @since 2.6.0
 *
 * @package uagb
 */

$block_slug = 'uagb/popup-builder';
$block_data = array(
	'doc'              => 'popup-builder',
	'slug'             => '',
	'admin_categories' => array( 'content', 'creative', 'pro' ),
	'link'             => 'popup-builder',
	'title'            => __( 'Popup Builder', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Create eye-catching popups that can be reused sitewide!', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => true,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'popup-builder' ),
	'dynamic_assets'   => array(
		'dir' => 'popup-builder',
	),
);
