<?php
/**
 * UAGB Caching.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use SiteGround_Optimizer\Options\Options;
use SiteGround_Optimizer\File_Cacher\File_Cacher;

/**
 * Class UAGB_Caching.
 *
 * @since 2.10.1
 */
class UAGB_Caching {

	/**
	 * Member Variable
	 *
	 * @since 2.10.1
	 * @var UAGB_Caching|null
	 */
	private static $instance;

	/**
	 *  Initiator
	 *
	 * @since 2.10.1
	 * @return UAGB_Caching
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) || null === self::$instance ) {
			self::$instance = new self();

		}
		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 2.10.1
	 */
	public function __construct() {
		add_action( 'uagb_delete_uag_asset_dir', array( $this, 'clear_cache' ) );
		add_action( 'uagb_delete_page_assets', array( $this, 'clear_cache' ) );
	}

	/**
	 * Clears the cache.
	 *
	 * @since 2.10.1
	 * @return void
	 */
	public function clear_cache() {
		self::clear_siteground_cache();
		self::clear_cloudways_cache();
	}

	/**
	 * Clears the SiteGround cache.
	 *
	 * @since 2.10.1
	 * @return void
	 */
	public static function clear_siteground_cache() {
		if ( ! class_exists( 'SiteGround_Optimizer\Options\Options' ) || ! class_exists( 'SiteGround_Optimizer\File_Cacher\File_Cacher' ) ) {
			return;
		}

		if ( Options::is_enabled( 'siteground_optimizer_file_caching' ) ) {
			File_Cacher::get_instance()->purge_everything();
		}
	}

	/**
	 * This function helps to purge all cache in clodways envirnoment.
	 * In presence of Breeze plugin (https://wordpress.org/plugins/breeze/)
	 *
	 * @since 2.11.0
	 * @return void
	 */
	public static function clear_cloudways_cache() {
		if ( ! class_exists( 'Breeze_Configuration' ) || ! class_exists( 'Breeze_CloudFlare_Helper' ) || ! class_exists( 'Breeze_Admin' ) ) {
			return;
		}

		// clear varnish cache.
		$admin = new Breeze_Admin();
		$admin->breeze_clear_varnish();

		// clear static cache.
		Breeze_Configuration::breeze_clean_cache();
		Breeze_CloudFlare_Helper::reset_all_cache();
	}
}

/**
 *  Prepare if class 'UAGB_Caching' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
UAGB_Caching::get_instance();
