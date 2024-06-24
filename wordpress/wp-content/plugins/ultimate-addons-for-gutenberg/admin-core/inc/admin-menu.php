<?php
/**
 * Uag Admin Menu.
 *
 * @package Uag
 */

namespace UagAdmin\Inc;

use UagAdmin\Inc\Admin_Helper;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \ZipAI\Classes\Helper as Zip_Ai_Helper;
use \ZipAI\Classes\Module as Zip_Ai_Module;


/**
 * Class Admin_Menu.
 */
class Admin_Menu {

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
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
	 * Instance
	 *
	 * @access private
	 * @var string Class object.
	 * @since 1.0.0
	 */
	private $menu_slug = 'spectra';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->initialize_hooks();
	}

	/**
	 * Init Hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function initialize_hooks() {

		/* Setup the Admin Menu */
		add_action( 'admin_menu', array( $this, 'setup_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_admin_scripts' ) );

		/* Add the Action Links */
		add_filter( 'plugin_action_links_' . UAGB_BASE, array( $this, 'add_action_links' ) );

		/* Render admin content view */
		add_action( 'uag_render_admin_page_content', array( $this, 'render_content' ), 10, 2 );
	}

	/**
	 * Show action on plugin page.
	 *
	 * @param  array $links links.
	 * @return array
	 */
	public function add_action_links( $links ) {

		$default_url = admin_url( 'admin.php?page=' . $this->menu_slug );

		$mylinks = array(
			'<a href="' . $default_url . '">' . __( 'Settings', 'ultimate-addons-for-gutenberg' ) . '</a>',
		);

		return array_merge( $mylinks, $links );
	}

	/**
	 *  Initialize after Spectra gets loaded.
	 */
	public function settings_admin_scripts() {

		// Enqueue admin scripts.
		if ( ! empty( $_GET['page'] ) && ( $this->menu_slug === $_GET['page'] || false !== strpos( sanitize_text_field( $_GET['page'] ), $this->menu_slug . '_' ) ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended

			add_action( 'admin_enqueue_scripts', array( $this, 'styles_scripts' ) );

			add_filter( 'admin_footer_text', array( $this, 'add_footer_link' ), 99 );
		}

	}

	/**
	 * Add submenu to admin menu.
	 *
	 * @since 1.0.0
	 */
	public function setup_menu() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$menu_slug  = $this->menu_slug;
		$capability = 'manage_options';

		$icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDcwIDcwIiBmaWxsPSJub25lIiBjbGFzcz0ic3BlY3RyYS1wYWdlLXNldHRpbmdzLWJ1dHRvbiIgYXJpYS1oaWRkZW49InRydWUiIGZvY3VzYWJsZT0iZmFsc2UiPiA8cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCIgZD0iTTM1IDcwQzU0LjMzIDcwIDcwIDU0LjMzIDcwIDM1QzcwIDE1LjY3IDU0LjMzIDAgMzUgMEMxNS42NyAwIDAgMTUuNjcgMCAzNUMwIDU0LjMzIDE1LjY3IDcwIDM1IDcwWk0yNC40NDcxIDIzLjUxMTJDMTguOTcyMiAyNi43NDAzIDIwLjI4NTIgMzUuMzc1OSAyNi41MDMyIDM3LjAzNTFMMzYuODg3NSAzOS44MDZDMzcuNzUzMyA0MC4wMzcgMzcuOTEgNDEuMjI0IDM3LjEzNSA0MS42ODExTDI3LjA5NzIgNDcuNTc5OUwyNi4wMzYgNThMNDUuNTUyOSA0Ni40ODg4QzUxLjAyNzggNDMuMjU5NyA0OS43MTQ4IDM0LjYyNDEgNDMuNDk2OCAzMi45NjQ5TDMzLjExMjUgMzAuMTk0MUMzMi4yNDY3IDI5Ljk2MyAzMi4wOSAyOC43NzYgMzIuODY1IDI4LjMxODlMNDIuOTAyOCAyMi40MjAyTDQzLjk2NCAxMkwyNC40NDcxIDIzLjUxMTJaIj48L3BhdGg+IDwvc3ZnPg==';

		// Add the Spectra Menu.
		add_menu_page(
			__( 'Spectra', 'ultimate-addons-for-gutenberg' ),
			__( 'Spectra', 'ultimate-addons-for-gutenberg' ),
			$capability,
			$menu_slug,
			array( $this, 'render' ),
			$icon,
			30
		);

		// Add the Dashboard Submenu.
		add_submenu_page(
			$menu_slug,
			__( 'Spectra', 'ultimate-addons-for-gutenberg' ),
			__( 'Dashboard', 'ultimate-addons-for-gutenberg' ),
			$capability,
			$menu_slug,
			array( $this, 'render' )
		);

		// Add the Blocks / Extensions Submenu.
		add_submenu_page(
			$menu_slug,
			__( 'Spectra', 'ultimate-addons-for-gutenberg' ),
			__( 'Blocks', 'ultimate-addons-for-gutenberg' ),
			$capability,
			$menu_slug . '&path=blocks',
			array( $this, 'render' )
		);

		// Use this action hook to add sub menu to above menu.
		do_action( 'spectra_after_menu_register' );

		// Add the AI Features Submenu if Zip AI Library is loaded.
		if ( defined( 'ZIP_AI_VERSION' ) ) {
			add_submenu_page(
				$menu_slug,
				__( 'Spectra', 'ultimate-addons-for-gutenberg' ),
				__( 'AI Features', 'ultimate-addons-for-gutenberg' ),
				$capability,
				$menu_slug . '&path=ai-features',
				array( $this, 'render' )
			);
		}

		// Finally, add the Settings Submenu.
		add_submenu_page(
			$menu_slug,
			__( 'Spectra', 'ultimate-addons-for-gutenberg' ),
			__( 'Settings', 'ultimate-addons-for-gutenberg' ),
			$capability,
			$menu_slug . '&path=settings',
			array( $this, 'render' )
		);
	}

	/**
	 * Renders the admin settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render() {

		$menu_page_slug = ( ! empty( $_GET['page'] ) ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : $this->menu_slug; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$page_action    = '';

		if ( isset( $_GET['action'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$page_action = sanitize_text_field( wp_unslash( $_GET['action'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$page_action = str_replace( '_', '-', $page_action );
		}

		include_once UAG_ADMIN_DIR . 'views/admin-base.php';
	}

	/**
	 * Renders the admin settings content.
	 *
	 * @since 1.0.0
	 * @param sting $menu_page_slug current page name.
	 * @param sting $page_action current page action.
	 *
	 * @return void
	 */
	public function render_content( $menu_page_slug, $page_action ) {

		if ( $this->menu_slug === $menu_page_slug ) {
			include_once UAG_ADMIN_DIR . 'views/dashboard-app.php';
		}
	}

	/**
	 * Enqueues the needed CSS/JS for the builder's admin settings page.
	 *
	 * @since 1.0.0
	 */
	public function styles_scripts() {

		$admin_slug  = 'uag-admin';
		$blocks_info = $this->get_blocks_info_for_activation_deactivation();
		wp_enqueue_style( $admin_slug . '-font', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap', array(), UAGB_VER );
		// Styles.

		wp_enqueue_style( 'wp-components' );

		$theme = wp_get_theme();

		$theme_data          = \WP_Theme_JSON_Resolver::get_theme_data();
		$theme_settings      = $theme_data->get_settings();
		$theme_font_families = isset( $theme_settings['typography']['fontFamilies']['theme'] ) && is_array( $theme_settings['typography']['fontFamilies']['theme'] ) ? $theme_settings['typography']['fontFamilies']['theme'] : array();

		$localize = apply_filters(
			'uag_react_admin_localize',
			array(
				'current_user'             => ! empty( wp_get_current_user()->user_firstname ) ? wp_get_current_user()->user_firstname : wp_get_current_user()->display_name,
				'admin_base_url'           => admin_url(),
				'uag_base_url'             => admin_url( 'admin.php?page=' . $this->menu_slug ),
				'plugin_dir'               => UAGB_URL,
				'plugin_ver'               => UAGB_VER,
				'admin_url'                => admin_url( 'admin.php' ),
				'ajax_url'                 => admin_url( 'admin-ajax.php' ),
				'wp_pages_url'             => admin_url( 'post-new.php?post_type=page' ),
				'home_slug'                => $this->menu_slug,
				'rollback_url'             => esc_url( add_query_arg( 'version', 'VERSION', wp_nonce_url( admin_url( 'admin-post.php?action=uag_rollback' ), 'uag_rollback' ) ) ),
				'blocks_info'              => $blocks_info,
				'reusable_url'             => esc_url( admin_url( 'edit.php?post_type=wp_block' ) ),
				'global_data'              => Admin_Helper::get_options(),
				'uag_content_width_set_by' => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_content_width_set_by', __( 'Spectra', 'ultimate-addons-for-gutenberg' ) ),
				'spectra_pro_installed'    => file_exists( UAGB_DIR . '../spectra-pro/spectra-pro.php' ),
				'spectra_pro_licensing'    => file_exists( UAGB_DIR . '../spectra-pro/admin/license-handler.php' ),
				'spectra_pro_status'       => is_plugin_active( 'spectra-pro/spectra-pro.php' ),
				'spectra_pro_ver'          => defined( 'SPECTRA_PRO_VER' ) ? SPECTRA_PRO_VER : null,
				'spectra_custom_fonts'     => apply_filters( 'spectra_system_fonts', array() ),
				'spectra_admin_video'      => apply_filters( 'spectra_display_admin_video', true ),
				'is_allow_registration'    => (bool) get_option( 'users_can_register' ),
				'theme_fonts'              => $theme_font_families,
				'is_block_theme'           => \UAGB_Admin_Helper::is_block_theme(),
				'spectra_pro_url'          => \UAGB_Admin_Helper::get_spectra_pro_url(),
			)
		);

		// If the Zip AI Assets is available, add the Zip AI localizations.
		if ( is_array( $localize )
			&& class_exists( '\ZipAI\Classes\Helper' )
			&& class_exists( '\ZipAI\Classes\Module' )
			&& defined( 'ZIP_AI_CREDIT_TOPUP_URL' )
		) {

			$localize = array_merge(
				$localize,
				array(
					'zip_ai_auth_middleware'  => Zip_Ai_Helper::get_auth_middleware_url( array( 'plugin' => 'spectra' ) ),
					'zip_ai_auth_revoke_url'  => Zip_Ai_Helper::get_auth_revoke_url(),
					'zip_ai_credit_topup_url' => ZIP_AI_CREDIT_TOPUP_URL,
					'zip_ai_is_authorized'    => Zip_Ai_Helper::is_authorized(),
					'zip_ai_is_chat_enabled'  => Zip_Ai_Module::is_enabled( 'ai_assistant' ),
					'zip_ai_admin_nonce'      => wp_create_nonce( 'zip_ai_admin_nonce' ),
					'zip_ai_credit_details'   => Zip_Ai_Helper::get_credit_details(),
				)
			);

			// In Zip AI version 1.1.2, the ZIPWP API constant was added - if this is available, get the current plan details.
			if ( defined( 'ZIP_AI_ZIPWP_API' ) ) {
				$response_zipwp_plan = Zip_Ai_Helper::get_current_plan_details();

				// If the response is not an error, then proceed to localize the required details.
				if ( is_array( $response_zipwp_plan ) && 'error' !== $response_zipwp_plan['status'] ) {
					// Create the base array to be localized.
					$current_zipwp_plan = array();

					// Add the team name if it exists.
					if ( ! empty( $response_zipwp_plan['team']['name'] ) ) {
						$current_zipwp_plan['team_name'] = $response_zipwp_plan['team']['name'];
					}

					// If the final array is not empty, localize it.
					if ( ! empty( $current_zipwp_plan ) ) {
						$localize['zip_ai_current_plan'] = $current_zipwp_plan;
					}
				}
			}
		}

		$this->settings_app_scripts( $localize );
	}


	/**
	 * Create an Array of Blocks info which we need to show in Admin dashboard.
	 */
	public function get_blocks_info_for_activation_deactivation() {

		$blocks = \UAGB_Admin_Helper::get_block_options();

		array_multisort(
			array_map(
				function( $element ) {
					if ( isset( $element['priority'] ) ) {
						return $element['priority'];
					}
					return;
				},
				$blocks
			),
			SORT_ASC,
			$blocks
		);

		$cf7_status = $this->get_plugin_status( 'contact-form-7/wp-contact-form-7.php' );
		$gf_status  = $this->get_plugin_status( 'gravityforms/gravityforms.php' );

		if ( is_array( $blocks ) && ! empty( $blocks ) ) {

			$blocks_names = array();

			foreach ( $blocks as $addon => $info ) {

				$addon = str_replace( 'uagb/', '', $addon );

				$exclude_blocks = array(
					'column',
					'icon-list-child',
					'social-share-child',
					'buttons-child',
					'faq-child',
					'forms-name',
					'forms-email',
					'forms-hidden',
					'forms-phone',
					'forms-textarea',
					'forms-url',
					'forms-select',
					'forms-radio',
					'forms-checkbox',
					'forms-upload',
					'forms-toggle',
					'forms-date',
					'forms-accept',
					'post-title',
					'post-image',
					'post-button',
					'post-excerpt',
					'post-taxonomy',
					'post-meta',
					'restaurant-menu-child',
					'content-timeline-child',
					'tabs-child',
					'how-to-step',
					'slider-child',
					'slider-pro',
					'image-gallery-pro',
					'loop-wrapper',
				);

				if ( ( 'cf7-styler' === $addon && 'active' !== $cf7_status ) || ( 'gf-styler' === $addon && 'active' !== $gf_status ) ) {
					$exclude_blocks[] = $addon;
				}

				$enable_legacy_blocks = \UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_legacy_blocks', ( 'yes' === get_option( 'uagb-old-user-less-than-2' ) ) ? 'yes' : 'no' );

				if ( 'yes' !== $enable_legacy_blocks ) {
					$exclude_blocks[] = 'wp-search';
					$exclude_blocks[] = 'columns';
					$exclude_blocks[] = 'section';
					$exclude_blocks[] = 'cf7-styler';
					$exclude_blocks[] = 'gf-styler';
					$exclude_blocks[] = 'post-masonry';
				}

				if ( array_key_exists( 'extension', $info ) && $info['extension'] ) {
					continue;
				}

				if ( in_array( $addon, $exclude_blocks, true ) ) {
					continue;
				}
				$info['slug']   = $addon;
				$blocks_names[] = $info;

			}

			return $blocks_names;
		}

		return array();

	}

	/**
	 * Get plugin status
	 *
	 * @since 2.0.0
	 *
	 * @param  string $plugin_init_file Plguin init file.
	 * @return mixed
	 */
	public function get_plugin_status( $plugin_init_file ) {

		$installed_plugins = get_plugins();

		if ( ! isset( $installed_plugins[ $plugin_init_file ] ) ) {
			return 'not-installed';
		} elseif ( is_plugin_active( $plugin_init_file ) ) {
			return 'active';
		} else {
			return 'inactive';
		}
	}

	/**
	 * Settings app scripts
	 *
	 * @param array $localize Variable names.
	 */
	public function settings_app_scripts( $localize ) {
		$handle            = 'uag-admin-settings';
		$build_path        = UAG_ADMIN_DIR . 'assets/build/';
		$build_url         = UAG_ADMIN_URL . 'assets/build/';
		$script_asset_path = $build_path . 'dashboard-app.asset.php';
		$script_info       = file_exists( $script_asset_path )
			? include $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => UAGB_VER,
			);

		$script_dep = array_merge( $script_info['dependencies'], array( 'updates' ) );

		wp_register_script(
			$handle,
			$build_url . 'dashboard-app.js',
			$script_dep,
			$script_info['version'],
			true
		);

		wp_register_style(
			$handle,
			$build_url . 'dashboard-app.css',
			array(),
			UAGB_VER
		);

		wp_register_style(
			'uag-admin-google-fonts',
			'https://fonts.googleapis.com/css2?family=Inter:wght@200&display=swap',
			array(),
			UAGB_VER
		);

		wp_enqueue_script( $handle );

		wp_set_script_translations( $handle, 'ultimate-addons-for-gutenberg' );
		wp_enqueue_style( 'uag-admin-google-fonts' );
		wp_enqueue_style( $handle );
		wp_style_add_data( $handle, 'rtl', 'replace' );
		wp_localize_script( $handle, 'uag_admin_react', $localize );
		wp_localize_script( $handle, 'uag_react', $localize );

	}

	/**
	 *  Add footer link.
	 */
	public function add_footer_link() {
		return '<span id="spectra-footer-thankyou" style="font-family: Inter, sans-serif;">' . sprintf(
			// translators: %1$s: Opening Strong Tag, %2$s: Closing Strong Tag, %3$s Anchor Tag with Star Symbol Codes.
			__(
				'Enjoyed %1$sSpectra%2$s? Please leave us a %3$s rating. We really appreciate your support!',
				'ultimate-addons-for-gutenberg'
			),
			'<strong>',
			'</strong>',
			'<a href="https://wordpress.org/support/plugin/ultimate-addons-for-gutenberg/reviews/?rate=5#new-post" target="_blank" style="color: #6104ff; text-decoration: none;" onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
		) . '</span>';

	}

}

Admin_Menu::get_instance();
