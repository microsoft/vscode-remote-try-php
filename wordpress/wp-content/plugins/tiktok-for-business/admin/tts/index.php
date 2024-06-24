<?php

/**
 * Entry of tts code
 */

namespace tiktok\admin\tts\index;

add_action(
	'plugins_loaded',
	function () {
		// check if woocommerce has installed
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		if ( ! did_action( 'woocommerce_loaded' ) > 0 ) {
			return;
		}

		// pretty permalink to allow woocommerce rest api
		if ( ! get_option( 'permalink_structure' ) ) {
			add_action(
				'admin_enqueue_scripts',
				function () {
					global $wp_rewrite;

					$wp_rewrite->set_permalink_structure( '/%year%/%monthnum%/%day%/%postname%/' );
				}
			);
		}
	}
);
