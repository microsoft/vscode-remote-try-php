<?php
/**
 * UAGB Twenty  Twenty Two Compatibility.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UAGB_Twenty_Twenty_Two_Compatibility' ) ) {

	/**
	 * Class UAGB_Twenty_Twenty_Two_Compatibility.
	 */
	final class UAGB_Twenty_Twenty_Two_Compatibility {

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance;

		/**
		 *  Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
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
		 * @since 2.0
		 */
		public function generate_stylesheet() {

			$query_args = array(
				'post_type' => 'wp_template',
			);

			$query = new WP_Query( $query_args );

			foreach ( $query->posts as $key => $post ) {
				$post_id             = $post->ID;
				$current_post_assets = new UAGB_Post_Assets( intval( $post_id ) );
				$current_post_assets->enqueue_scripts();
			}

		}
	}
}
UAGB_Twenty_Twenty_Two_Compatibility::get_instance();
