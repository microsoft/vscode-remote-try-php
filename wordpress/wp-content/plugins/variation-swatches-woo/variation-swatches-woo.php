<?php
/**
 * Plugin Name: Variation Swatches for WooCommerce
 * Description: Provides super easy shopping experience by displaying beautiful variation swatches on WooCommerce shop and product page.
 * Author: CartFlows
 * Author URI: https://cartflows.com/
 * Version: 1.0.8
 * License: GPL v2
 * Text Domain: variation-swatches-woo
 *
 * @package variation-swatches-woo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Set constants
 */
define( 'CFVSW_FILE', __FILE__ );
define( 'CFVSW_BASE', plugin_basename( CFVSW_FILE ) );
define( 'CFVSW_DIR', plugin_dir_path( CFVSW_FILE ) );
define( 'CFVSW_URL', plugins_url( '/', CFVSW_FILE ) );
define( 'CFVSW_VER', '1.0.8' );
define( 'CFVSW_GLOBAL', 'cfvsw_global' );
define( 'CFVSW_SHOP', 'cfvsw_shop' );
define( 'CFVSW_STYLE', 'cfvsw_style' );
define( 'CFVSW_PRODUCT_ATTR', 'cfvsw_product_attr' );

require_once 'plugin-loader.php';
