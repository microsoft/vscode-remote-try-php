<?php
/**
 * Plugin Name: ZipWP Images
 * Description: It is a free image library.
 * Author: Brainstorm Force
 * Version: 1.0.7
 * License: GPL v2
 * Text Domain: zipwp-images
 *
 * @package {{package}}
 */

if ( defined( 'ZIPWP_IMAGES_FILE' ) ) {
	return;
}

/**
 * Set constants
 */
define( 'ZIPWP_IMAGES_FILE', __FILE__ );
define( 'ZIPWP_IMAGES_BASE', plugin_basename( ZIPWP_IMAGES_FILE ) );
define( 'ZIPWP_IMAGES_DIR', plugin_dir_path( ZIPWP_IMAGES_FILE ) );
define( 'ZIPWP_IMAGES_URL', plugins_url( '/', ZIPWP_IMAGES_FILE ) );
define( 'ZIPWP_IMAGES_VER', '1.0.7' );

require_once 'zipwp-images-loader.php';
