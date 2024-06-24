<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/how-to-step';
$block_data = array(
	'slug'           => '',
	'title'          => __( 'Step', 'ultimate-addons-for-gutenberg' ),
	'description'    => __( 'Add relevant content for this step.', 'ultimate-addons-for-gutenberg' ),
	'default'        => true,
	'is_child'       => true,
	'dynamic_assets' => array(
		'dir' => 'how-to-step',
	),
	'deprecated'     => false,
);
