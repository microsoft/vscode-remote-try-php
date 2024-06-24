<?php
/**
 * Plugin Name: Astra Widgets
 * Plugin URI: https://wpastra.com/
 * Description: The Fastest Way to Add More Widgets into Your WordPress Website.
 * Version: 1.2.13
 * Author: Brainstorm Force
 * Author URI: https://www.brainstormforce.com
 * Text Domain: astra-widgets
 *
 * @package Astra Widgets
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Set constants.
 */
define( 'ASTRA_WIDGETS_FILE', __FILE__ );
define( 'ASTRA_WIDGETS_BASE', plugin_basename( ASTRA_WIDGETS_FILE ) );
define( 'ASTRA_WIDGETS_DIR', plugin_dir_path( ASTRA_WIDGETS_FILE ) );
define( 'ASTRA_WIDGETS_URI', plugins_url( '/', ASTRA_WIDGETS_FILE ) );
define( 'ASTRA_WIDGETS_VER', '1.2.13' );
define( 'ASTRA_WIDGETS_TEMPLATE_DEBUG_MODE', false );

require_once ASTRA_WIDGETS_DIR . 'classes/class-astra-widgets.php';

if ( is_admin() ) {

	/**
	 * Admin Notice Library Settings
	 */
	require_once ASTRA_WIDGETS_DIR . 'lib/astra-notices/class-astra-notices.php';
}

// BSF Analytics library.
if ( ! class_exists( 'BSF_Analytics_Loader' ) ) {
	require_once ASTRA_WIDGETS_DIR . 'admin/bsf-analytics/class-bsf-analytics-loader.php';
}

$bsf_analytics = BSF_Analytics_Loader::get_instance();

$bsf_analytics->set_entity(
	array(
		'bsf' => array(
			'product_name'    => 'Astra Widgets',
			'path'            => ASTRA_WIDGETS_DIR . 'admin/bsf-analytics',
			'author'          => 'Brainstorm Force',
			'time_to_display' => '+24 hours',
		),
	)
);
