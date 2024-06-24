<?php
/**
 * Initialize Astra Widgets
 *
 * @package Astra Widgets
 * @since 1.0.0
 */

if ( ! class_exists( 'Astra_Widgets' ) ) :
	/**
	 * Astra Widgets
	 *
	 * @since 1.0.0
	 */
	class Astra_Widgets {
		/**
		 * Instance
		 *
		 * @access private
		 * @var object Class Instance.
		 * @since 1.0.0
		 */
		private static $instance;
		/**
		 * Initiator
		 *
		 * @since 1.0.0
		 * @return object initialized object of class.
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
			// Init.
			require_once ASTRA_WIDGETS_DIR . 'classes/class-astra-widgets-loader.php';
		}
	}
	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Astra_Widgets::get_instance();
endif;
