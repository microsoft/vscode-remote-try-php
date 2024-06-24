<?php
/**
 * Input.
 *
 * @package Automattic\WooCommerce\Pinterest\View\PHPView
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * PHP View.
 *
 * @var \Automattic\WooCommerce\Pinterest\View\PHPView $this
 */

/**
 * Input.
 *
 * @var array $input
 */
$input = $this->input;

woocommerce_wp_select( $input );
