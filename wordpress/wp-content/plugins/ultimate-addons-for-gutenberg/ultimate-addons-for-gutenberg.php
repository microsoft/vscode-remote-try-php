<?php
/**
 * Plugin Name: Spectra
 * Plugin URI: https://www.brainstormforce.com
 * Author: Brainstorm Force
 * Author URI: https://www.brainstormforce.com
 * Version: 2.13.4
 * Description: The Spectra extends the Gutenberg functionality with several unique and feature-rich blocks that help build websites faster.
 * Text Domain: ultimate-addons-for-gutenberg
 *
 * @package UAGB
 */

define( 'UAGB_FILE', __FILE__ );
define( 'UAGB_ROOT', dirname( plugin_basename( UAGB_FILE ) ) );
define( 'UAGB_PLUGIN_NAME', 'Spectra' );
define( 'UAGB_PLUGIN_SHORT_NAME', 'Spectra' );
define( 'SPECTRA_PRO_PLUGIN_URL', 'https://wpspectra.com/pro' );

if ( ! version_compare( PHP_VERSION, '5.6', '>=' ) ) {
	add_action( 'admin_notices', 'uagb_fail_php_version' );
} elseif ( ! version_compare( get_bloginfo( 'version' ), '4.7', '>=' ) ) {
	add_action( 'admin_notices', 'uagb_fail_wp_version' );
} else {
	require_once 'classes/class-uagb-loader.php';
}

/**
 * Ultimate Addons for Gutenberg admin notice for minimum PHP version.
 *
 * Warning when the site doesn't have the minimum required PHP version.
 *
 * @since 1.8.1
 *
 * @return void
 */
function uagb_fail_php_version() {
	/* translators: %s: PHP version */
	$message      = sprintf( esc_html__( 'Spectra requires PHP version %s+, plugin is currently NOT RUNNING.', 'ultimate-addons-for-gutenberg' ), '5.6' );
	$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}


/**
 * Ultimate Addons for Gutenberg admin notice for minimum WordPress version.
 *
 * Warning when the site doesn't have the minimum required WordPress version.
 *
 * @since 1.8.1
 *
 * @return void
 */
function uagb_fail_wp_version() {
	/* translators: %s: WordPress version */
	$message      = sprintf( esc_html__( 'Spectra requires WordPress version %s+. Because you are using an earlier version, the plugin is currently NOT RUNNING.', 'ultimate-addons-for-gutenberg' ), '4.7' );
	$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}
