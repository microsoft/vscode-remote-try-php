<?php
/**
 * CartFlows Loader.
 *
 * @package Woocommerce-Cart-Abandonment-Recovery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'CARTFLOWS_CA_Loader' ) ) {

	/**
	 * Class CARTFLOWS_CA_Loader.
	 */
	final class CARTFLOWS_CA_Loader {


		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance = null;

		/**
		 * Member Variable
		 *
		 * @var utils
		 */
		public $utils = null;


		/**
		 *  Initiator
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self();

				/**
				 * CartFlows CA loaded.
				 *
				 * Fires when Cartflows CA was fully loaded and instantiated.
				 *
				 * @since 1.0.0
				 */
				do_action( 'cartflows_ca_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			$this->define_constants();

			// Activation hook.
			register_activation_hook( CARTFLOWS_CA_FILE, array( $this, 'activation_reset' ) );

			// deActivation hook.
			register_deactivation_hook( CARTFLOWS_CA_FILE, array( $this, 'deactivation_reset' ) );

			add_action( 'plugins_loaded', array( $this, 'load_plugin' ), 99 );

			add_action( 'plugins_loaded', array( $this, 'load_cf_textdomain' ) );

			// Let WooCommerce know, Plugin is compatible with HPOS.
			add_action( 'before_woocommerce_init', array( $this, 'declare_woo_hpos_compatibility' ) );
		}

		/**
		 * Defines all constants
		 *
		 * @since 1.0.0
		 */
		public function define_constants() {
			define( 'CARTFLOWS_CA_BASE', plugin_basename( CARTFLOWS_CA_FILE ) );
			define( 'CARTFLOWS_CA_DIR', plugin_dir_path( CARTFLOWS_CA_FILE ) );
			define( 'CARTFLOWS_CA_URL', plugins_url( '/', CARTFLOWS_CA_FILE ) );
			define( 'CARTFLOWS_CA_VER', '1.2.27' );

			define( 'CARTFLOWS_CA_SLUG', 'cartflows_ca' );

			define( 'CARTFLOWS_CA_CART_ABANDONMENT_TABLE', 'cartflows_ca_cart_abandonment' );
			define( 'CARTFLOWS_CA_EMAIL_TEMPLATE_TABLE', 'cartflows_ca_email_templates' );
			define( 'CARTFLOWS_CA_EMAIL_HISTORY_TABLE', 'cartflows_ca_email_history' );
			define( 'CARTFLOWS_CA_EMAIL_TEMPLATE_META_TABLE', 'cartflows_ca_email_templates_meta' );
		}

		/**
		 * Loads plugin files.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function load_plugin() {

			if ( ! function_exists( 'WC' ) ) {
				add_action( 'admin_notices', array( $this, 'fails_to_load' ) );
				return;
			}

			if ( 'no' === get_option( 'wcf_ca_all_db_tables_created', false ) ) {
				add_action( 'admin_notices', array( $this, 'fails_to_create_table' ) );
				return;
			}

			$this->load_helper_files_components();
			$this->load_core_files();
			$this->load_core_components();

			/**
			 * CartFlows Init.
			 *
			 * Fires when Cartflows is instantiated.
			 *
			 * @since 1.0.0
			 */
			do_action( 'cartflows_ca_init' );
		}


		/**
		 * Show error notice when all of the required database tables are not created.
		 *
		 * @since 1.2.15
		 *
		 * @return void
		 */
		public function fails_to_create_table() {

			$class = 'notice notice-error';
			/* translators: %s: html tags */
			$message = sprintf( __( 'Required database tables are not created for %1$sWooCommerce Cart Abandonment Recovery%2$s plugin. Please make sure that the database user has the REFERENCES privilege to create tables.', 'woo-cart-abandonment-recovery' ), '<strong>', '</strong>' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), wp_kses_post( $message ) );
		}


		/**
		 * Fires admin notice when Elementor is not installed and activated.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function fails_to_load() {

			$screen = get_current_screen();

			if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
				return;
			}

			$class = 'notice notice-error';
			/* translators: %s: html tags */
			$message = sprintf( __( 'The %1$sWooCommerce Cart Abandonment Recovery%2$s plugin requires %1$sWooCommerce%2$s plugin installed & activated.', 'woo-cart-abandonment-recovery' ), '<strong>', '</strong>' );
			$plugin  = 'woocommerce/woocommerce.php';

			if ( $this->is_woo_installed() ) {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}

				$action_url   = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
				$button_label = __( 'Activate WooCommerce', 'woo-cart-abandonment-recovery' );

			} else {
				if ( ! current_user_can( 'install_plugins' ) ) {
					return;
				}

				$action_url   = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' );
				$button_label = __( 'Install WooCommerce', 'woo-cart-abandonment-recovery' );
			}

			$button = '<p><a href="' . $action_url . '" class="button-primary">' . $button_label . '</a></p><p></p>';

			printf( '<div class="%1$s"><p>%2$s</p>%3$s</div>', esc_attr( $class ), wp_kses_post( $message ), wp_kses_post( $button ) );
		}


		/**
		 * Is woocommerce plugin installed.
		 *
		 * @since 1.0.0
		 *
		 * @access public
		 */
		public function is_woo_installed() {

			$path    = 'woocommerce/woocommerce.php';
			$plugins = get_plugins();

			return isset( $plugins[ $path ] );
		}

		/**
		 * Create new database tables for plugin updates.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function initialize_cart_abandonment_tables() {

			include_once CARTFLOWS_CA_DIR . 'modules/cart-abandonment/classes/class-cartflows-ca-database.php';
			$db = Cartflows_Ca_Database::get_instance();
			$db->create_tables();
			$db->template_table_seeder();
		}


		/**
		 * Load Helper Files and Components.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function load_helper_files_components() {

			include_once CARTFLOWS_CA_DIR . 'classes/class-cartflows-ca-utils.php';
			$this->utils = Cartflows_Ca_Utils::get_instance();
		}

		/**
		 * Load core files.
		 */
		public function load_core_files() {
			/* Update compatibility. */
			require_once CARTFLOWS_CA_DIR . 'classes/class-cartflows-ca-update.php';

			include_once CARTFLOWS_CA_DIR . 'classes/class-cartflows-ca-settings.php';

			include_once CARTFLOWS_CA_DIR . 'classes/class-cartflows-ca-tabs.php';

			if ( is_admin() ) {
				require_once CARTFLOWS_CA_DIR . 'lib/astra-notices/class-astra-notices.php';
			}

			if ( ! class_exists( 'BSF_Analytics_Loader' ) ) {
				require_once CARTFLOWS_CA_DIR . '/admin/bsf-analytics/class-bsf-analytics-loader.php';
			}

			$bsf_analytics = BSF_Analytics_Loader::get_instance();

			$bsf_analytics->set_entity(
				array(
					'cf' => array(
						'product_name'   => 'Woocommerce Cart Abandonment Recovery',
						'usage_doc_link' => 'https://my.cartflows.com/usage-tracking/',
						'path'           => CARTFLOWS_CA_DIR . 'admin/bsf-analytics',
						'author'         => 'CartFlows Inc',
					),
				)
			);

			include_once CARTFLOWS_CA_DIR . 'classes/class-cartflows-ca-admin-notices.php';
		}

		/**
		 * Load CartFlows Ca Text Domain.
		 * This will load the translation textdomain depending on the file priorities.
		 *      1. Global Languages /wp-content/languages/%plugin-folder-name%/ folder
		 *      2. Local dorectory /wp-content/plugins/%plugin-folder-name%/languages/ folder
		 *
		 * @since  1.0.3
		 * @return void
		 */
		public function load_cf_textdomain() {

			// Default languages directory for CartFlows Ca.
			$lang_dir = CARTFLOWS_CA_DIR . 'languages/';

			/**
			 * Filters the languages directory path to use for CartFlows Ca.
			 *
			 * @param string $lang_dir The languages directory path.
			 */
			$lang_dir = apply_filters( 'carflows_ca_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter.
			global $wp_version;

			$get_locale = get_locale();

			if ( $wp_version >= 4.7 ) {
				$get_locale = get_user_locale();
			}

			/**
			 * Language Locale for CartFlows Ca
			 *
			 * @var $get_locale The locale to use.
			 * Uses get_user_locale()` in WordPress 4.7 or greater,
			 * otherwise uses `get_locale()`.
			 */
			$locale = apply_filters( 'plugin_locale', $get_locale, 'woo-cart-abandonment-recovery' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'woo-cart-abandonment-recovery', $locale );

			// Setup paths to current locale file.
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/%plugin-folder-name%/ folder.
				load_textdomain( 'woo-cart-abandonment-recovery', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/%plugin-folder-name%/languages/ folder.
				load_textdomain( 'woo-cart-abandonment-recovery', $mofile_local );
			} else {
				// Load the default language files.
				load_plugin_textdomain( 'woo-cart-abandonment-recovery', false, $lang_dir );
			}
		}
		/**
		 * Load Core Components.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function load_core_components() {

			/* Cart abandonment templates class */
			include_once CARTFLOWS_CA_DIR . 'modules/cart-abandonment/classes/class-cartflows-ca-module-loader.php';

			include_once CARTFLOWS_CA_DIR . 'modules/weekly-email-report/class-cartflows-ca-admin-report-emails.php';

		}


		/**
		 * Activation Reset
		 */
		public function activation_reset() {
			$this->update_default_settings();
			$this->initialize_cart_abandonment_tables();
		}


		/**
		 *  Set the default cart abandonment settings.
		 */
		public function update_default_settings() {

			$current_user     = wp_get_current_user();
			$email_from       = ( isset( $current_user->user_firstname ) && ! empty( $current_user->user_firstname ) ) ? $current_user->user_firstname . ' ' . $current_user->user_lastname : 'Admin';
			$default_settings = array(
				'wcf_ca_status'                        => 'on',
				'wcf_ca_gdpr_status'                   => 'off',
				'wcf_ca_coupon_code_status'            => 'off',
				'wcf_ca_zapier_tracking_status'        => 'off',
				'wcf_ca_delete_plugin_data'            => 'off',
				'wcf_ca_cut_off_time'                  => 15,
				'wcf_ca_from_name'                     => $email_from,
				'wcf_ca_from_email'                    => $current_user->user_email,
				'wcf_ca_reply_email'                   => $current_user->user_email,
				'wcf_ca_discount_type'                 => 'percent',
				'wcf_ca_coupon_amount'                 => 10,
				'wcf_ca_zapier_cart_abandoned_webhook' => '',
				'wcf_ca_gdpr_message'                  => 'Your email & cart are saved so we can send email reminders about this order.',
				'wcf_ca_coupon_expiry'                 => 0,
				'wcf_ca_coupon_expiry_unit'            => 'hours',
				'wcf_ca_excludes_orders'               => array( 'processing', 'completed' ),

			);

			foreach ( $default_settings as $option_key => $option_value ) {
				if ( ! get_option( $option_key ) ) {
					update_option( $option_key, $option_value );
				}
			}
		}

		/**
		 * Deactivation Reset
		 */
		public function deactivation_reset() {
			wp_clear_scheduled_hook( 'cartflows_ca_update_order_status_action' );
		}

		/**
		 *  Declare the woo HPOS compatibility.
		 */
		public function declare_woo_hpos_compatibility() {

			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', CARTFLOWS_CA_FILE, true );
			}
		}
	}

	/**
	 *  Prepare if class 'CARTFLOWS_CA_Loader' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	CARTFLOWS_CA_Loader::get_instance();
}


if ( ! function_exists( 'wcf_ca' ) ) {
	/**
	 * Get global class.
	 *
	 * @return object
	 */
	function wcf_ca() {
		return CARTFLOWS_CA_Loader::get_instance();
	}
}

