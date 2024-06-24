<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.4.0
 *
 * @package uagb
 */

$block_slug = 'uagb/countdown';
$block_data = array(
	'doc'                 => 'countdown',
	'slug'                => '',
	'admin_categories'    => array( 'creative', 'core' ),
	'link'                => 'countdown',
	'title'               => __( 'Countdown', 'ultimate-addons-for-gutenberg' ),
	'description'         => __( 'This block allows you to add countdown timers.', 'ultimate-addons-for-gutenberg' ),
	'default'             => true,
	'extension'           => false,
	'priority'            => Spectra_Block_Prioritization::get_block_priority( 'countdown' ),
	'static_dependencies' => array(
		'uagb-countdown-js' => array(
			'src'  => UAGB_Scripts_Utils::get_js_url( 'uagb-countdown' ),
			'dep'  => array(),
			'type' => 'js',
		),
	),
	'dynamic_assets'      => array(
		'dir' => 'countdown',
	),
);
