<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.1.0
 *
 * @package uagb
 */

$block_slug = 'uagb/register';
$block_data = array(
	'doc'              => 'register',
	'slug'             => '',
	'admin_categories' => array( 'form', 'pro' ),
	'link'             => 'register',
	'title'            => __( 'Registration Form', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'This block lets you add a user register form.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'register' ),
	'deprecated'       => false,
	'pro_filler'       => true,
);
