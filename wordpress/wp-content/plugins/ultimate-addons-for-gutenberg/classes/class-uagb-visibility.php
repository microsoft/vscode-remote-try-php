<?php
/**
 * UAGB Visibility.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class UAGB_Visibility.
 */
class UAGB_Visibility {

	/**
	 * Member Variable
	 *
	 * @since 2.8.0
	 * @var UAGB_Visibility|null
	 */
	private static $instance;

	/**
	 *  Initiator
	 *
	 * @since 2.8.0
	 * @return UAGB_Visibility
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) || null === self::$instance ) {
			self::$instance = new self();

		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$visibility         = UAGB_Admin_Helper::get_admin_settings_option( 'uag_visibility_mode', 'disabled' );
		$visibility_page_id = UAGB_Admin_Helper::get_admin_settings_option( 'uag_visibility_page', false );

		if ( 'disabled' !== $visibility && ! is_user_logged_in() && false !== $visibility_page_id && isset( $visibility_page_id ) && ! empty( $visibility_page_id ) ) {
			add_action( 'template_redirect', array( $this, 'set_visibility_page' ), 99 );
			add_filter( 'template_include', array( $this, 'set_visibility_template' ), 99 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_asset_files' ) );
		}
	}

	/**
	 * Set Visibility Template.
	 * 
	 * @since 2.8.0
	 * 
	 * @return string Template file path.
	 */
	public function set_visibility_template() {
		return UAGB_DIR . 'templates/visibility-template.php';
	}

	/**
	 * Set Visibility Page.
	 *
	 * @since 2.8.0
	 * 
	 * @return void
	 */
	public function set_visibility_page() {
		$visibility_page_id = intval( UAGB_Admin_Helper::get_admin_settings_option( 'uag_visibility_page', false ) );

		$current_page_id = get_the_ID();

		if ( $visibility_page_id !== $current_page_id && 'publish' === get_post_status( $visibility_page_id ) ) {
			$maintenance = UAGB_Admin_Helper::get_admin_settings_option( 'uag_visibility_mode', 'disabled' );
			if ( 'maintenance' === $maintenance ) {
				status_header( 503 );
			}

			// Output JavaScript for redirection.
			echo '<script type="text/javascript">window.location.href = "' . esc_url( get_page_link( $visibility_page_id ) ) . '";</script>';

			// Exit to prevent further processing.
			exit();
		}
	}

	/**
	 * Enqueue asset files.
	 *
	 * @since 2.8.0
	 */
	public function enqueue_asset_files() {

		$current_page_id    = get_the_ID();
		$visibility_page_id = intval( UAGB_Admin_Helper::get_admin_settings_option( 'uag_visibility_page', false ) );

		if ( $visibility_page_id === $current_page_id ) {
			wp_enqueue_style(
				'uagb-style-visibility', // Handle.
				UAGB_URL . 'assets/css/visibility.min.css',
				array(),
				UAGB_VER
			);
		}
	}
}

/**
 *  Prepare if class 'UAGB_Visibility' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
UAGB_Visibility::get_instance();
