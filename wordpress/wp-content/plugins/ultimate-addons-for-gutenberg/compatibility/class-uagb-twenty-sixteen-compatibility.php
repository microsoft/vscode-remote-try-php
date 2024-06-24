<?php
/**
 * UAGB Twenty Sixteen Compatibility.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UAGB_Twenty_Sixteen_Compatibility' ) ) {

	/**
	 * Class UAGB_Twenty_Sixteen_Compatibility.
	 */
	final class UAGB_Twenty_Sixteen_Compatibility {

		/**
		 * Member Variable
		 *
		 * @var UAGB_Twenty_Sixteen_Compatibility
		 */
		private static $instance;

		/**
		 *  Initiator
		 *
		 * @since 2.11.4
		 * @return UAGB_Twenty_Sixteen_Compatibility
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
		
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'wp', array( $this, 'generate_stylesheet' ), 101 );
		}
		/**
		 * Generates stylesheet and appends in head tag.
		 *
		 * @since 2.11.4
		 * @return void
		 */
		public function generate_stylesheet() {

			if ( is_home() ) {
				$post_id             = get_the_ID();
				$current_post_assets = new UAGB_Post_Assets( intval( $post_id ) );
				
				if ( is_object( $current_post_assets ) ) {
					$current_post_assets->enqueue_scripts();
				}
			}

		}
	}
}
UAGB_Twenty_Sixteen_Compatibility::get_instance();
