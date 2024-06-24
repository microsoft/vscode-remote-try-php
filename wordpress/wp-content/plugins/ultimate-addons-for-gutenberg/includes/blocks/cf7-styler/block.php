<?php
/**
 * Block Information.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$block_slug = 'uagb/cf7-styler';
$block_data = array(
	'doc'              => 'contact-form-7-styler',
	'slug'             => '',
	'admin_categories' => array( 'form' ),
	'link'             => 'contact-form-7-styler',
	'title'            => __( 'Contact Form 7 Designer', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'Highly customize and style your Contact Form 7 forms.', 'ultimate-addons-for-gutenberg' ),
	'is_active'        => class_exists( 'WPCF7_ContactForm' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'cf7-styler' ),
	'deprecated'       => true,
	'dynamic_assets'   => array(
		'dir' => 'cf7-styler',
	),
);
