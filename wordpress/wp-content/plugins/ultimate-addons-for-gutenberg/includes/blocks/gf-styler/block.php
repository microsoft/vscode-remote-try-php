<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/gf-styler';
$block_data = array(
	'doc'              => 'gravity-form',
	'slug'             => '',
	'admin_categories' => array( 'form' ),
	'link'             => 'gravity-form-styler',
	'title'            => __( 'Gravity Form Designer', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Highly customize and style your forms created by Gravity Forms.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'is_active'        => class_exists( 'GFForms' ),
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'gf-styler' ),
	'deprecated'       => true,
	'dynamic_assets'   => array(
		'dir' => 'gf-styler',
	),
);
