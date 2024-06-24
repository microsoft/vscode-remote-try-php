<?php
/**
 * Astra Widgets - Loader.
 *
 * @package Astra Addon
 * @since 1.0.0
 */

if ( ! class_exists( 'Astra_Widgets_Loader' ) ) {

	/**
	 * Customizer Initialization
	 *
	 * @since 1.0.0
	 */
	class Astra_Widgets_Loader {

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
		 *  Constructor
		 */
		public function __construct() {

			// Helper.
			require_once ASTRA_WIDGETS_DIR . 'classes/class-astra-widgets-helper.php';

			// Add Widget.
			require_once ASTRA_WIDGETS_DIR . 'classes/widgets/class-astra-widget-address.php';
			require_once ASTRA_WIDGETS_DIR . 'classes/widgets/class-astra-widget-list-icons.php';
			require_once ASTRA_WIDGETS_DIR . 'classes/widgets/class-astra-widget-social-profiles.php';

			add_action( 'widgets_init', array( $this, 'register_list_icons_widgets' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_backend_and_frontend' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_backend_and_frontend' ) );
		}

		/**
		 * Regiter List Icons widget
		 *
		 * @return void
		 */
		public function register_list_icons_widgets() {
			register_widget( 'Astra_Widget_Address' );
			register_widget( 'Astra_Widget_List_Icons' );
			register_widget( 'Astra_Widget_Social_Profiles' );
		}

		/**
		 * Regiter Social Icons widget script
		 *
		 * @return void
		 */
		public function enqueue_scripts_backend_and_frontend() {
		}
	}
}

/**
*  Kicking this off by calling 'get_instance()' method
*/
Astra_Widgets_Loader::get_instance();
