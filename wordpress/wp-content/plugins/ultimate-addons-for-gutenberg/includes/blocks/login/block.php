<?php
/**
 * Block Information & Attributes File.
 *
 * @since 2.1.0
 *
 * @package uagb
 */

$block_slug = 'uagb/login';
$block_data = array(
	'doc'              => 'login',
	'slug'             => '',
	'admin_categories' => array( 'form', 'pro' ),
	'link'             => 'login',
	'title'            => __( 'Login Form', 'ultimate-addons-for-gutenberg' ),
	'description'      => __( 'This block lets you add a user login form.', 'ultimate-addons-for-gutenberg' ),
	'default'          => true,
	'extension'        => false,
	'priority'         => Spectra_Block_Prioritization::get_block_priority( 'login' ),
	'pro_filler'       => true,
);
