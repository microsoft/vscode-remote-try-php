<?php
/**
 * Plugin Name: WooCommerce Cart Abandonment Recovery
 * Plugin URI: https://cartflows.com/
 * Description: Recover your lost revenue. Capture email address of users on the checkout page and send follow up emails if they don't complete the purchase.
 * Version: 1.2.27
 * Author: CartFlows Inc
 * Author URI: https://cartflows.com/
 * Text Domain: woo-cart-abandonment-recovery
 * WC requires at least: 3.0
 * WC tested up to: 8.6.1
 *
 * @package Woocommerce-Cart-Abandonment-Recovery
 */

/**
 * Set constants.
 */
define( 'CARTFLOWS_CA_FILE', __FILE__ );

/**
 * Loader
 */
require_once 'classes/class-cartflows-ca-loader.php';
