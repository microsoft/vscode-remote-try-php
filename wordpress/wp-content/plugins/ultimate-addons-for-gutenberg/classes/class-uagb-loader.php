<?php
/**
 * UAGB Loader.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UAGB_Loader' ) ) {

	/**
	 * Class UAGB_Loader.
	 */
	final class UAGB_Loader {

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance;

		/**
		 * Post assets object cache
		 *
		 * @var array
		 */
		public $post_assets_objs = array();

		/**
		 *  Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();

				/**
				 * Spectra loaded.
				 *
				 * Fires when Spectra was fully loaded and instantiated.
				 *
				 * @since 2.1.0
				 */
				do_action( 'spectra_core_loaded' );
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			// Activation hook.
			register_activation_hook( UAGB_FILE, array( $this, 'activation_reset' ) );

			// deActivation hook.
			register_deactivation_hook( UAGB_FILE, array( $this, 'deactivation_reset' ) );

			if ( ! $this->is_gutenberg_active() ) {
				/* TO DO */
				add_action( 'admin_notices', array( $this, 'uagb_fails_to_load' ) );
				return;
			}

			$this->define_constants();

			$this->loader();

			add_action( 'after_setup_theme', array( $this, 'load_compatibility' ) );

			add_action( 'plugins_loaded', array( $this, 'load_plugin' ) );

			add_action( 'init', array( $this, 'init_actions' ) );
		}

		/**
		 * Defines all constants
		 *
		 * @since 1.0.0
		 */
		public function define_constants() {
			define( 'UAGB_BASE', plugin_basename( UAGB_FILE ) );
			define( 'UAGB_DIR', plugin_dir_path( UAGB_FILE ) );
			define( 'UAGB_URL', plugins_url( '/', UAGB_FILE ) );
			define( 'UAGB_VER', '2.13.4' );
			define( 'UAGB_MODULES_DIR', UAGB_DIR . 'modules/' );
			define( 'UAGB_MODULES_URL', UAGB_URL . 'modules/' );
			define( 'UAGB_SLUG', 'spectra' );
			define( 'UAGB_URI', trailingslashit( 'https://wpspectra.com/' ) );

			if ( ! defined( 'UAGB_TABLET_BREAKPOINT' ) ) {
				define( 'UAGB_TABLET_BREAKPOINT', '976' );
			}
			if ( ! defined( 'UAGB_MOBILE_BREAKPOINT' ) ) {
				define( 'UAGB_MOBILE_BREAKPOINT', '767' );
			}

			if ( ! defined( 'UAGB_UPLOAD_DIR_NAME' ) ) {
				define( 'UAGB_UPLOAD_DIR_NAME', 'uag-plugin' );
			}

			$upload_dir = wp_upload_dir( null, false );

			if ( ! defined( 'UAGB_UPLOAD_DIR' ) ) {
				define( 'UAGB_UPLOAD_DIR', $upload_dir['basedir'] . '/' . UAGB_UPLOAD_DIR_NAME . '/' );
			}

			if ( ! defined( 'UAGB_UPLOAD_URL' ) ) {
				define( 'UAGB_UPLOAD_URL', $upload_dir['baseurl'] . '/' . UAGB_UPLOAD_DIR_NAME . '/' );
			}

			define( 'UAGB_ASSET_VER', get_option( '__uagb_asset_version', UAGB_VER ) );
			define( 'UAGB_CSS_EXT', defined( 'WP_DEBUG' ) && WP_DEBUG ? '.css' : '.min.css' );
			define( 'UAGB_JS_EXT', defined( 'WP_DEBUG' ) && WP_DEBUG ? '.js' : '.min.js' );
		}

		/**
		 * Loads Other files.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function loader() {

			require_once UAGB_DIR . 'classes/utils.php';
			require_once UAGB_DIR . 'classes/class-spectra-block-prioritization.php';
			require_once UAGB_DIR . 'classes/class-uagb-install.php';
			require_once UAGB_DIR . 'classes/class-uagb-filesystem.php';
			require_once UAGB_DIR . 'classes/class-uagb-update.php';
			require_once UAGB_DIR . 'classes/class-uagb-block.php';

			if ( is_admin() ) {
				require_once UAGB_DIR . 'classes/class-uagb-beta-updates.php';
				require_once UAGB_DIR . 'classes/class-uagb-rollback.php';
			}
		}

		/**
		 * Loads plugin files.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function load_plugin() {

			$this->load_textdomain();

			require_once UAGB_DIR . 'classes/class-uagb-scripts-utils.php';
			require_once UAGB_DIR . 'classes/class-uagb-block-module.php';
			require_once UAGB_DIR . 'classes/class-uagb-admin-helper.php';
			require_once UAGB_DIR . 'classes/class-uagb-helper.php';
			require_once UAGB_DIR . 'blocks-config/blocks-config.php';
			require_once UAGB_DIR . 'lib/astra-notices/class-astra-notices.php';

			if ( is_admin() ) {
				require_once UAGB_DIR . 'classes/class-uagb-admin.php';
			}

			require_once UAGB_DIR . 'classes/class-uagb-post-assets.php';
			require_once UAGB_DIR . 'classes/class-uagb-front-assets.php';
			require_once UAGB_DIR . 'classes/class-uagb-init-blocks.php';
			require_once UAGB_DIR . 'classes/class-uagb-rest-api.php';
			require_once UAGB_DIR . 'classes/class-uagb-visibility.php';
			require_once UAGB_DIR . 'classes/class-uagb-caching.php';

			if ( 'twentyseventeen' === get_template() ) {
				require_once UAGB_DIR . 'classes/class-uagb-twenty-seventeen-compatibility.php';
			}

			if ( 'twentysixteen' === get_template() ) {
				require_once UAGB_DIR . 'compatibility/class-uagb-twenty-sixteen-compatibility.php';
			}

			require_once UAGB_DIR . 'admin-core/admin-loader.php';

			// Register all UAG Lite Blocks.
			uagb_block()->register_blocks();

			add_filter( 'rest_pre_dispatch', array( $this, 'rest_pre_dispatch' ), 10, 3 );

			$enable_templates_button = UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_templates_button', 'yes' );

			if ( 'yes' === $enable_templates_button ) {
				require_once UAGB_DIR . 'lib/class-uagb-ast-block-templates.php';
			} else {
				add_filter( 'ast_block_templates_disable', '__return_true' );
			}

			// Add the filters for the Zip AI Library and include it.
			add_filter( 'zip_ai_collab_product_details', array( $this, 'add_zip_ai_collab_product_details' ), 20, 1 );
			add_filter( 'zip_ai_modules', array( $this, 'add_zip_ai_modules' ), 20, 1 );
			add_filter( 'zip_ai_auth_redirection_flag', '__return_true', 20, 1 );
			add_filter( 'zip_ai_auth_redirection_url', array( $this, 'add_zip_ai_redirection_url' ), 20, 1 );
			add_filter( 'zip_ai_revoke_redirection_url', array( $this, 'add_zip_ai_redirection_url' ), 20, 1 );

			require_once UAGB_DIR . 'lib/class-uagb-zip-ai.php';
		}

		/**
		 * Loads theme compatibility files.
		 *
		 * @since 2.5.1
		 *
		 * @return void
		 */
		public function load_compatibility() {
			require_once UAGB_DIR . 'classes/class-uagb-fse-fonts-compatibility.php';
		}
		/**
		 * Fix REST API issue with blocks registered via PHP register_block_type.
		 *
		 * @since 1.25.2
		 *
		 * @param mixed  $result  Response to replace the requested version with.
		 * @param object $server  Server instance.
		 * @param object $request Request used to generate the response.
		 *
		 * @return array Returns updated results.
		 */
		public function rest_pre_dispatch( $result, $server, $request ) {

			if ( strpos( $request->get_route(), '/wp/v2/block-renderer' ) !== false && isset( $request['attributes'] ) ) {

					$attributes = $request['attributes'];

				if ( isset( $attributes['UAGUserRole'] ) ) {
					unset( $attributes['UAGUserRole'] );
				}

				if ( isset( $attributes['UAGBrowser'] ) ) {
					unset( $attributes['UAGBrowser'] );
				}

				if ( isset( $attributes['UAGSystem'] ) ) {
					unset( $attributes['UAGSystem'] );
				}

				if ( isset( $attributes['UAGDisplayConditions'] ) ) {
					unset( $attributes['UAGDisplayConditions'] );
				}

				if ( isset( $attributes['UAGHideDesktop'] ) ) {
					unset( $attributes['UAGHideDesktop'] );
				}

				if ( isset( $attributes['UAGHideMob'] ) ) {
					unset( $attributes['UAGHideMob'] );
				}

				if ( isset( $attributes['UAGHideTab'] ) ) {
					unset( $attributes['UAGHideTab'] );
				}

				if ( isset( $attributes['UAGLoggedIn'] ) ) {
					unset( $attributes['UAGLoggedIn'] );
				}

				if ( isset( $attributes['UAGLoggedOut'] ) ) {
					unset( $attributes['UAGLoggedOut'] );
				}

				if ( isset( $attributes['UAGDay'] ) ) {
					unset( $attributes['UAGDay'] );
				}

				if ( isset( $attributes['zIndex'] ) ) {
					unset( $attributes['zIndex'] );
				}

				if ( isset( $attributes['UAGResponsiveConditions'] ) ) {
					unset( $attributes['UAGResponsiveConditions'] );
				}

				if ( isset( $attributes['UAGAnimationType'] ) ) {
					unset( $attributes['UAGAnimationType'] );
				}

				if ( isset( $attributes['UAGAnimationTime'] ) ) {
					unset( $attributes['UAGAnimationTime'] );
				}

				if ( isset( $attributes['UAGAnimationDelay'] ) ) {
					unset( $attributes['UAGAnimationDelay'] );
				}

				if ( isset( $attributes['UAGAnimationEasing'] ) ) {
					unset( $attributes['UAGAnimationEasing'] );
				}

				if ( isset( $attributes['UAGAnimationRepeat'] ) ) {
					unset( $attributes['UAGAnimationRepeat'] );
				}

				if ( isset( $attributes['UAGAnimationDelayInterval'] ) ) {
					unset( $attributes['UAGAnimationDelayInterval'] );
				}

				if ( isset( $attributes['UAGAnimationDoNotApplyToContainer'] ) ) {
					unset( $attributes['UAGAnimationDoNotApplyToContainer'] );
				}

				if ( isset( $attributes['UAGStickyLocation'] ) ) {
					unset( $attributes['UAGStickyLocation'] );
				}

				if ( isset( $attributes['UAGStickyRestricted'] ) ) {
					unset( $attributes['UAGStickyRestricted'] );
				}

				if ( isset( $attributes['UAGStickyOffset'] ) ) {
					unset( $attributes['UAGStickyOffset'] );
				}

				if ( isset( $attributes['UAGPosition'] ) ) {
					unset( $attributes['UAGPosition'] );
				}

					$request['attributes'] = $attributes;

			}

			return $result;
		}

		/**
		 * Check if Gutenberg is active
		 *
		 * @since 1.1.0
		 *
		 * @return boolean
		 */
		public function is_gutenberg_active() {
			return function_exists( 'register_block_type' );
		}

		/**
		 * Load Ultimate Gutenberg Text Domain.
		 * This will load the translation textdomain depending on the file priorities.
		 *      1. Global Languages /wp-content/languages/ultimate-addons-for-gutenberg/ folder
		 *      2. Local directory /wp-content/plugins/ultimate-addons-for-gutenberg/languages/ folder
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function load_textdomain() {

			/**
			 * Filters the languages directory path to use for AffiliateWP.
			 *
			 * @param string $lang_dir The languages directory path.
			 */
			$lang_dir = apply_filters( 'uagb_languages_directory', UAGB_ROOT . '/languages/' );

			load_plugin_textdomain( 'ultimate-addons-for-gutenberg', false, $lang_dir );
		}

		/**
		 * Fires admin notice when Gutenberg is not installed and activated.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function uagb_fails_to_load() {

			if ( ! current_user_can( 'install_plugins' ) ) {
				return;
			}

			$class = 'notice notice-error';
			/* translators: %s: html tags */
			$message = sprintf( __( 'The %1$sSpectra%2$s plugin requires %1$sGutenberg%2$s plugin installed & activated.', 'ultimate-addons-for-gutenberg' ), '<strong>', '</strong>' );

			$action_url   = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=gutenberg' ), 'install-plugin_gutenberg' );
			$button_label = __( 'Install Gutenberg', 'ultimate-addons-for-gutenberg' );

			$button = '<p><a href="' . $action_url . '" class="button-primary">' . $button_label . '</a></p><p></p>';

			printf( '<div class="%1$s"><p>%2$s</p>%3$s</div>', esc_attr( $class ), wp_kses_post( $message ), wp_kses_post( $button ) );
		}

		/**
		 * Activation Reset
		 */
		public function activation_reset() {

			uagb_install()->create_files();

			update_option( '__uagb_do_redirect', true );
			update_option( '__uagb_asset_version', time() );
		}

		/**
		 * Deactivation Reset
		 */
		public function deactivation_reset() {
			update_option( '__uagb_do_redirect', false );
		}

		/**
		 * Init actions
		 *
		 * @since 2.0.0
		 *
		 * @return void
		 */
		public function init_actions() {

			$theme_folder = get_template();

			if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
				if ( 'twentytwentytwo' === $theme_folder ) {
					require_once UAGB_DIR . 'compatibility/class-uagb-twenty-twenty-two-compatibility.php';
				}
			}

			if ( 'astra' === $theme_folder ) {
				require_once UAGB_DIR . 'compatibility/class-uagb-astra-compatibility.php';
			}

				register_meta(
					'post',
					'_uag_custom_page_level_css',
					array(
						'show_in_rest'      => true,
						'type'              => 'string',
						'single'            => true,
						'auth_callback'     => function() {
							return current_user_can( 'edit_posts' );
						},
						'sanitize_callback' => function( $meta_value ) {
							return wp_kses_post( $meta_value );
						},
					)
				);

			// This class is loaded from blocks config.
			UAGB_Popup_Builder::generate_scripts();

			UAGB_Update::migrate_visibility_mode();

			// Adds filters to modify the blocks allowed in excerpts.
			add_filter( 'excerpt_allowed_blocks', array( $this, 'add_blocks_to_excerpt' ), 20 );
			add_filter( 'excerpt_allowed_wrapper_blocks', array( $this, 'add_wrapper_blocks_to_excerpt' ), 20 );
			add_filter( 'uagb_blocks_allowed_in_excerpt', array( $this, 'add_uagb_blocks_to_excerpt' ), 20, 2 );
			$this->get_regenerate_assets_on_migration();
		}

		/**
		 * Adds specified blocks to the list of allowed blocks in excerpts.
		 *
		 * @param array $allowed    List of allowed blocks in excerpts.
		 * @since 2.6.0
		 * @return array            Modified list of allowed blocks in excerpts.
		 */
		public function add_blocks_to_excerpt( $allowed ) {
			return apply_filters( 'uagb_blocks_allowed_in_excerpt', $allowed, array( 'uagb/advanced-heading' ) );
		}

		/**
		 * Adds specified wrapper blocks to the list of allowed blocks in excerpts.
		 *
		 * @param array $allowed    List of allowed blocks in excerpts.
		 * @since 2.6.0
		 * @return array            Modified list of allowed blocks in excerpts.
		 */
		public function add_wrapper_blocks_to_excerpt( $allowed ) {
			return apply_filters(
				'uagb_blocks_allowed_in_excerpt',
				$allowed,
				array(
					'uagb/container',
					'uagb/columns',
					'uagb/column',
				)
			);
		}

		/**
		 * Adds specified UAGB blocks to the list of allowed blocks in excerpts.
		 *
		 * @param array $excerpt_blocks     List of allowed blocks in excerpts.
		 * @param array $blocks_to_add      Blocks to add to the list of allowed blocks in excerpts.
		 * @since 2.6.0
		 * @return array                    The merged excerpt blocks array if both parameters are arrays, or the original excerpt blocks if either parameter is not an array.
		 */
		public function add_uagb_blocks_to_excerpt( $excerpt_blocks, $blocks_to_add ) {
			if ( is_array( $excerpt_blocks ) && is_array( $blocks_to_add ) ) {
				return array_merge( $excerpt_blocks, $blocks_to_add );
			}

			// If either parameter is not an array, return the original excerpt blocks.
			return $excerpt_blocks;
		}

		/**
		 * Generate assets on migration.
		 *
		 * @since 2.7.10
		 * @return void
		 */
		public function get_regenerate_assets_on_migration() {
			// Parse the host (domain/hostname) from the site URL.
			$site_host = wp_parse_url( site_url(), PHP_URL_HOST );

			// Check if $site_host is empty or not a string. If true, return and exit the function.
			if ( empty( $site_host ) || ! is_string( $site_host ) ) {
				return;
			}

			// Remove 'www.' from the domain.
			$domain = str_replace( 'www.', '', $site_host );

			// Replace dots (.) with dashes (-) in the domain to create $site_domain.
			$site_domain = str_replace( '.', '-', $domain );

			// Retrieve the stored domain from admin settings.
			$stored_domain = \UAGB_Admin_Helper::get_admin_settings_option( 'uagb_site_url', '' );

			// If the stored domain is empty, update the 'uagb_site_url' option in admin settings with the modified site domain and return.
			if ( empty( $stored_domain ) ) {
				\UAGB_Admin_Helper::update_admin_settings_option( 'uagb_site_url', $site_domain );
				return;
			}

			// If the stored domain is different from the current site domain, update the '__uagb_asset_version' option with the current timestamp.
			if ( $stored_domain !== $site_domain ) {
				\UAGB_Admin_Helper::update_admin_settings_option( '__uagb_asset_version', time() );
			}
		}

		/**
		 * Add the Zip AI Collab Product Details.
		 *
		 * @param mixed $product_details The previous product details, if any.
		 * @since 2.10.2
		 * @return array The Spectra product details.
		 */
		public function add_zip_ai_collab_product_details( $product_details ) {
			// Overwrite the product details that were of a lower priority, if any.
			$product_details = array(
				'product_name'                          => 'Spectra',
				'product_slug'                          => 'spectra',
				'product_logo'                          => file_get_contents( UAGB_DIR . 'assets/images/logos/spectra.svg' ),
				'product_primary_color'                 => '#5733ff',
				'ai_assistant_learn_more_url'           => admin_url( 'admin.php?page=spectra&path=ai-features' ),
				'ai_assistant_authorized_disable_url'   => admin_url( 'admin.php?page=spectra&path=ai-features&manage-features=yes' ),
				'ai_assistant_unauthorized_disable_url' => admin_url( 'admin.php?page=spectra&path=ai-features&manage-features=yes' ),
			);
			// Return the Spectra product details.
			return $product_details;
		}

		/**
		 * Add the Zip AI Modules that come with Spectra.
		 *
		 * @param mixed $modules The modules for Zip AI, if any.
		 * @since 2.10.2
		 * @return array The Spectra default modules.
		 */
		public function add_zip_ai_modules( $modules ) {
			// If the filtered modules is not an array, make it one.
			$modules = is_array( $modules ) ? $modules : array();

			// List of module names to enable.
			$modules_to_enable = array( 'ai_assistant', 'ai_design_copilot' );

			// Ensure each module in the list is enabled.
			foreach ( $modules_to_enable as $module_name ) {
				// @phpstan-ignore-next-line
				if ( class_exists( '\ZipAI\Classes\Module' ) && method_exists( '\ZipAI\Classes\Module', 'force_enabled' ) ) {
					\ZipAI\Classes\Module::force_enabled( $modules, $module_name );
				}
			}

			// Return the Spectra default modules.
			return $modules;
		}

		/**
		 * Add the Zip AI Authorization/Revoke URL.
		 *
		 * @param mixed $auth_url The previous authorization URL, if any.
		 * @since 2.10.2
		 * @return string The Spectra redirection URL.
		 */
		public function add_zip_ai_redirection_url( $auth_url ) {
			return admin_url( 'admin.php?page=spectra&path=ai-features' );
		}
	}
}

/**
 *  Prepare if class 'UAGB_Loader' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
UAGB_Loader::get_instance();

/**
 * Load main object
 *
 * @since 2.0.0
 *
 * @return object
 */
function uagb() {
	return UAGB_Loader::get_instance();
}
