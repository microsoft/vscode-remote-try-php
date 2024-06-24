<?php
/**
 * Block Information.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/column';
$block_data = array(
	'slug'           => '',
	'link'           => '',
	'title'          => __( 'Column', 'ultimate-addons-for-gutenberg' ),
	'description'    => __( 'Immediate child of Advanced Columns.', 'ultimate-addons-for-gutenberg' ),
	'default'        => true,
	'is_child'       => true,
	'extension'      => false,
	'dynamic_assets' => array(
		'dir' => 'column',
	),
	'deprecated'     => true,
);
