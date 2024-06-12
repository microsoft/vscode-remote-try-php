<?php
/**
 * Site Builder Free Version Preview.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Astra_Theme_Builder_Free' ) ) {

	define( 'ASTRA_THEME_BUILDER_FREE_DIR', ASTRA_THEME_DIR . 'admin/assets/theme-builder/' );
	define( 'ASTRA_THEME_BUILDER_FREE_URI', ASTRA_THEME_URI . 'admin/assets/theme-builder/' );

	/**
	 * Site Builder initial setup.
	 *
	 * @since 4.5.0
	 */
	class Astra_Theme_Builder_Free {

		/**
		 * Member Variable
		 *
		 * @var null $instance
		 */
		private static $instance;

		/**
		 *  Initiator
		 * 
		 * @since 4.5.0
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				/** @psalm-suppress InvalidPropertyAssignmentValue */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				self::$instance = new self();
				/** @psalm-suppress InvalidPropertyAssignmentValue */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			}
			return self::$instance;
		}

		/**
		 *  Constructor
		 * 
		 * @since 4.5.0
		 * @return void
		 */
		public function __construct() {
			$is_astra_addon_active = ( defined( 'ASTRA_EXT_VER' ) );
			if ( ! $is_astra_addon_active ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'theme_builder_admin_enqueue_scripts' ) );
				add_action( 'admin_body_class', array( $this, 'admin_body_class' ) );
				add_action( 'admin_menu', array( $this, 'setup_menu' ) );
				add_action( 'admin_init', array( $this, 'astra_theme_builder_disable_notices' ) );
			}
			add_action( 'admin_page_access_denied', array( $this, 'astra_theme_builder_access_denied_redirect' ) );
		}

		/**
		 *  Enqueue scripts and styles.
		 * 
		 * @since 4.5.0
		 * @return void
		 */
		public function theme_builder_admin_enqueue_scripts() {
			$file_prefix = '';
			if ( is_rtl() ) {
				$file_prefix .= '.rtl';
			}

			wp_enqueue_style( 'astra-theme-builder-style', ASTRA_THEME_BUILDER_FREE_URI . 'build/index' . $file_prefix . '.css', array(), ASTRA_THEME_VERSION );

			wp_enqueue_script( 'astra-theme-builder-script', ASTRA_THEME_BUILDER_FREE_URI . 'build/index.js', array( 'wp-element' ), ASTRA_THEME_VERSION, true );

			wp_enqueue_style( 'dashicons' );

			$localized_data = array(
				'title'                      => esc_html__( 'Site Builder', 'astra' ),
				'rest_url'                   => '/wp-json/astra-addon/v1/custom-layouts/',
				'new_custom_layout_base_url' => admin_url( 'post-new.php?post_type=astra-advanced-hook' ),
				'astra_pricing_page_url'     => 'https://wpastra.com/pricing/',
				'astra_docs_page_url'        => 'https://wpastra.com/docs/custom-layouts-pro/',
				'admin_url'                  => admin_url(),
			);

			wp_localize_script( 'astra-theme-builder-script', 'astra_theme_builder', $localized_data );
		}

		/**
		 * Admin Body Classes
		 *
		 * @since 4.5.0
		 * @param string $classes Space separated class string.
		 */
		public function admin_body_class( $classes = '' ) {
			$theme_builder_class = isset( $_GET['page'] ) && 'theme-builder-free' === $_GET['page'] ? 'ast-theme-builder' : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Fetching a $_GET value, no nonce available to validate.
			$classes            .= ' ' . $theme_builder_class . ' ';

			return $classes;

		}

		/**
		 * Renders the admin settings.
		 *
		 * @since 4.5.0
		 * @return void
		 */
		public function render_theme_builder() {
			?>
				<div class="ast-tb-menu-page-wrapper">
					<div id="ast-tb-menu-page">
						<div class="ast-tb-menu-page-content">
							<div id="ast-tb-app-root" class="ast-tb-app-root"></div>
						</div>
					</div>
				</div>
			<?php
		}

		/**
		 * Setup menu.
		 *
		 * @since 4.5.0
		 * @return void
		 */
		public function setup_menu() {
			add_submenu_page( // phpcs:ignore WPThemeReview.PluginTerritory.NoAddAdminPages.add_menu_pages_add_submenu_page -- Taken the menu on top level
				'astra',
				__( 'Site Builder', 'astra' ),
				__( 'Site Builder', 'astra' ),
				'manage_options',
				'theme-builder-free',
				array( $this, 'render_theme_builder' ),
				2
			);
		}

		/**
		 * Disable notices for Site Builder page.
		 *
		 * @since 4.5.0
		 * @return void
		 */
		public function astra_theme_builder_disable_notices() {

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Fetching a $_GET value, no nonce available to validate.
			if ( isset( $_GET['page'] ) && 'theme-builder-free' === $_GET['page'] ) {
				remove_all_actions( 'admin_notices' );
				remove_all_actions( 'all_admin_notices' ); // For older versions of WordPress
			}
		}

		/**
		 * Redirect to Site Builder pro from free preview if pro module is active.
		 *
		 * @since 4.5.0
		 * @return void
		 */
		public function astra_theme_builder_access_denied_redirect() {

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Fetching a $_GET value, no nonce available to validate.
			if ( isset( $_GET['page'] ) && 'theme-builder-free' === $_GET['page'] ) {
				/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$is_astra_addon_active = ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'advanced-hooks' ) );
				/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				if ( $is_astra_addon_active ) {
					wp_redirect( admin_url( 'admin.php?page=theme-builder' ) );
					exit;
				}
			}
		}
	}

	/**
	 *  Kicking this off by calling 'get_instance()' method
	 */
	Astra_Theme_Builder_Free::get_instance();

}
