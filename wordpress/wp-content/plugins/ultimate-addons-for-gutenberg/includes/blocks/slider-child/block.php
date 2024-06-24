<?php
/**
 * Block Information.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/slider-child';
$block_data = array(
	'slug'           => '',
	'link'           => '',
	'title'          => __( 'Slider', 'ultimate-addons-for-gutenberg' ),
	'description'    => __( 'Customize this slider as per your need.', 'ultimate-addons-for-gutenberg' ),
	'default'        => true,
	'is_child'       => true,
	'extension'      => false,
	'dynamic_assets' => array(
		'dir' => 'slider-child',
	),
	'deprecated'     => false,
);
