<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/how-to';
$block_data = array(
	'doc'              => 'how-to-schema',
	'slug'             => '',
	'admin_categories' => array( 'seo' ),
	'link'             => 'how-to-schema',
	'title'            => __( 'How To', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Add instructions/steps on processes using how to block.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'how-to' ),
	'deprecated'       => false,
	'dynamic_assets'   => array(
		'dir' => 'how-to',
	),
);
