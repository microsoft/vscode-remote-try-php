<?php
/**
 * Installation related functions and actions.
 *
 * @package  Pinterest_For_Woocommerce
 * @version  1.0.0
 */

use Automattic\WooCommerce\Pinterest as Pinterest;
use Automattic\WooCommerce\Pinterest\AdCredits;
use Automattic\WooCommerce\Pinterest\AdCreditsCoupons;
use Automattic\WooCommerce\Pinterest\AdsCreditCurrency;
use Automattic\WooCommerce\Pinterest\Billing;
use Automattic\WooCommerce\Pinterest\Heartbeat;
use Automattic\WooCommerce\Pinterest\Notes\MarketingNotifications;
use Automattic\WooCommerce\Pinterest\PinterestApiException;
use Automattic\WooCommerce\Pinterest\Utilities\Tracks;
use Automattic\WooCommerce\Pinterest\API\UserInteraction;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists;
use Automattic\WooCommerce\Pinterest\Admin\Tasks\Onboarding;

if ( ! class_exists( 'Pinterest_For_Woocommerce' ) ) :

	/**
	 * Base Plugin class holding generic functionality
	 */
	final class Pinterest_For_Woocommerce {

		use Tracks;

		/**
		 * Tos IDs and URLs per country.
		 */
		const TOS_PER_COUNTRY = array(
			'US' => array(
				'tos_id'    => 8,
				'terms_url' => 'https://business.pinterest.com/en/pinterest-advertising-services-agreement',
			),
			'CA' => array(
				'tos_id'    => 8,
				'terms_url' => 'https://business.pinterest.com/en/pinterest-advertising-services-agreement',
			),
			'FR' => array(
				'tos_id'    => 11,
				'terms_url' => 'https://business.pinterest.com/fr/pinterest-advertising-services-agreement',
			),
			'BR' => array(
				'tos_id'    => 15,
				'terms_url' => 'https://business.pinterest.com/pt-br/pinterest-advertising-services-agreement/',
			),
			'MX' => array(
				'tos_id'    => 16,
				'terms_url' => 'https://business.pinterest.com/es/pinterest-advertising-services-agreement/mexico/',
			),
			'*'  => array(
				'tos_id'    => 9,
				'terms_url' => 'https://business.pinterest.com/en-gb/pinterest-advertising-services-agreement/',
			),
		);

		/**
		 * Set the minimum required versions for the plugin.
		 */
		const PLUGIN_REQUIREMENTS = array(
			'php_version'      => '7.4',
			'wp_version'       => '5.6',
			'wc_version'       => '5.3',
			'action_scheduler' => '3.3.0',
		);

		/**
		 * Pinterest_For_Woocommerce version.
		 *
		 * @var string
		 */
		public $version = PINTEREST_FOR_WOOCOMMERCE_VERSION;

		/**
		 * The single instance of the class.
		 *
		 * @var Pinterest_For_Woocommerce
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/**
		 * The initialized state of the class.
		 *
		 * @var Pinterest_For_Woocommerce
		 * @since 1.0.0
		 */
		protected static $initialized = false;

		/**
		 * Heartbeat instance.
		 *
		 * @var Heartbeat
		 * @since 1.1.0
		 */
		protected $heartbeat = null;

		/**
		 * When set to true, the settings have been
		 * changed and the runtime cached must be flushed
		 *
		 * @var Pinterest_For_Woocommerce
		 * @since 1.0.0
		 */
		protected static $dirty_settings = array();

		/**
		 * The default settings that will be created
		 * with the given values, if they don't exist.
		 *
		 * @var Pinterest_For_Woocommerce
		 * @since 1.0.0
		 */
		protected static $default_settings = array(
			'track_conversions'                => true,
			'enhanced_match_support'           => true,
			'automatic_enhanced_match_support' => true,
			'save_to_pinterest'                => true,
			'rich_pins_on_posts'               => true,
			'rich_pins_on_products'            => true,
			'product_sync_enabled'             => true,
			'enable_debug_logging'             => false,
			'erase_plugin_data'                => false,
		);

		/**
		 * Main Pinterest_For_Woocommerce Instance.
		 *
		 * Ensures only one instance of Pinterest_For_Woocommerce is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 * @static
		 * @see Pinterest_For_Woocommerce()
		 * @return Pinterest_For_Woocommerce - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
				self::$instance->maybe_init_plugin();
			}
			return self::$instance;
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning this class is forbidden.', 'pinterest-for-woocommerce' ), '1.0.0' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is forbidden.', 'pinterest-for-woocommerce' ), '1.0.0' );
		}

		/**
		 * Pinterest_For_Woocommerce Initializer.
		 */
		public function maybe_init_plugin() {
			if ( self::$initialized ) {
				_doing_it_wrong( __FUNCTION__, esc_html__( 'Only a single instance of this class is allowed. Use singleton.', 'pinterest-for-woocommerce' ), '1.0.0' );
				return;
			}

			self::$initialized = true;

			$this->define_constants();

			add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );

			/**
			 * Plugin loaded action.
			 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
			 */
			do_action( 'pinterest_for_woocommerce_loaded' );
		}


		/**
		 * Define Pinterest_For_Woocommerce Constants.
		 */
		private function define_constants() {
			define( 'PINTEREST_FOR_WOOCOMMERCE_PREFIX', 'pinterest-for-woocommerce' );
			define( 'PINTEREST_FOR_WOOCOMMERCE_PLUGIN_BASENAME', plugin_basename( PINTEREST_FOR_WOOCOMMERCE_PLUGIN_FILE ) );
			define( 'PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME', 'pinterest_for_woocommerce' );
			define( 'PINTEREST_FOR_WOOCOMMERCE_DATA_NAME', 'pinterest_for_woocommerce_data' );
			define( 'PINTEREST_FOR_WOOCOMMERCE_LOG_PREFIX', 'pinterest-for-woocommerce' );
			define( 'PINTEREST_FOR_WOOCOMMERCE_WOO_CONNECT_URL', 'https://connect.woocommerce.com/' );
			define( 'PINTEREST_FOR_WOOCOMMERCE_WOO_CONNECT_SERVICE', 'pinterestv3native' );
			define( 'PINTEREST_FOR_WOOCOMMERCE_API_NAMESPACE', 'pinterest' );
			define( 'PINTEREST_FOR_WOOCOMMERCE_API_VERSION', '1' );
			define( 'PINTEREST_FOR_WOOCOMMERCE_API_AUTH_ENDPOINT', 'oauth/callback' );
			define( 'PINTEREST_FOR_WOOCOMMERCE_AUTH', PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_auth_key' );
			define( 'PINTEREST_FOR_WOOCOMMERCE_TRACKER_PREFIX', 'pfw' );
		}


		/**
		 * What type of request is this?
		 *
		 * @param  string $type admin, ajax, cron or frontend.
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin':
					return is_admin();
				case 'ajax':
					return defined( 'DOING_AJAX' );
				case 'cron':
					return defined( 'DOING_CRON' );
				case 'frontend':
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		private function includes() {

			include_once 'includes/class-pinterest-for-woocommerce-ads-supported-countries.php';

			if ( $this->is_request( 'admin' ) ) {
				include_once 'includes/admin/class-pinterest-for-woocommerce-admin.php';
			}

			if ( $this->is_request( 'frontend' ) ) {
				include_once 'includes/class-pinterest-for-woocommerce-frontend-assets.php';
			}
		}

		/**
		 * Include plugins files and hook into actions and filters.
		 *
		 * @since  1.0.0
		 */
		public function init_plugin() {

			if ( ! $this->check_plugin_requirements() ) {
				return;
			}

			$this->includes();

			// Start the heartbeat.
			$this->heartbeat = new Heartbeat( WC()->queue() );
			$this->heartbeat->init();

			add_action( 'admin_init', array( $this, 'admin_init' ), 0 );
			add_action( 'rest_api_init', array( $this, 'init_api_endpoints' ) );
			add_action( 'wp_head', array( $this, 'maybe_inject_verification_code' ) );
			add_action( 'wp_head', array( Pinterest\RichPins::class, 'maybe_inject_rich_pins_opengraph_tags' ) );
			add_action( 'wp', array( Pinterest\SaveToPinterest::class, 'maybe_init' ) );

			add_action( 'init', array( $this, 'init' ), 0 );

			// ActionScheduler is activated on init 1 so lets make sure we are updating after that.
			add_action( 'init', array( $this, 'maybe_update_plugin' ), 5 );
			add_action( 'init', array( Pinterest\Tracking::class, 'maybe_init' ) );
			add_action( 'init', array( Pinterest\ProductSync::class, 'maybe_init' ) );
			add_action( 'init', array( Pinterest\TrackerSnapshot::class, 'maybe_init' ) );
			add_action( 'init', array( Pinterest\Billing::class, 'schedule_event' ) );
			add_action( 'init', array( Pinterest\AdCredits::class, 'schedule_event' ) );

			// Register the marketing channel if the feature is included.
			if ( defined( 'WC_MCM_EXISTS' ) ) {
				add_action(
					'init',
					array( Pinterest\MultichannelMarketing\MarketingChannelRegistrar::class, 'register' )
				);
			}

			// Verify that the ads_campaign is active or not.
			add_action( 'admin_init', array( Pinterest\AdCredits::class, 'check_if_ads_campaign_is_active' ) );

			// Append credits info to account data.
			add_action( 'init', array( $this, 'add_currency_credits_info_to_account_data' ) );

			add_action( 'pinterest_for_woocommerce_token_saved', array( $this, 'set_default_settings' ) );
			add_action( 'pinterest_for_woocommerce_token_saved', array( $this, 'update_account_data' ) );

			// Handle the Pinterest verification URL.
			add_action( 'parse_request', array( $this, 'verification_request' ) );

			// Disconnect advertiser if advertiser or tag change.
			add_action( 'update_option_pinterest_for_woocommerce', array( $this, 'maybe_disconnect_advertiser' ), 10, 2 );

			// Init marketing notifications.
			add_action( Heartbeat::DAILY, array( $this, 'init_marketing_notifications' ) );

			// Check available coupons and credits.
			add_action( Heartbeat::HOURLY, array( $this, 'check_available_coupons_and_credits' ) );

			// Hook the setup task. The hook admin_init is not triggered when the WC fetches the tasks using the endpoint: wp-json/wc-admin/onboarding/tasks and hence hooking into init.
			add_action( 'init', array( $this, 'add_onboarding_task' ), 20 );
		}


		/**
		 * Init Pinterest_For_Woocommerce when WordPress Initialises.
		 */
		public function init() {
			/**
			 * Before init action.
			 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
			 */
			do_action( 'before_pinterest_for_woocommerce_init' );

			// Set up localisation.
			$this->load_plugin_textdomain();

			/**
			 * Init action.
			 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
			 */
			do_action( 'pinterest_for_woocommerce_init' );
		}

		/**
		 * Init classes for admin interface.
		 */
		public function admin_init() {
			$view_factory         = new Pinterest\View\PHPViewFactory();
			$admin                = new Pinterest\Admin\Admin( $view_factory );
			$attributes_tab       = new Pinterest\Admin\Product\Attributes\AttributesTab( $admin );
			$activation_redirect  = new Pinterest\Admin\ActivationRedirect();
			$variation_attributes = new Pinterest\Admin\Product\Attributes\VariationsAttributes( $admin );

			$admin->register();
			$attributes_tab->register();
			$activation_redirect->register();
			$variation_attributes->register();
		}

		/**
		 * Init marketing notifications.
		 *
		 * @since 1.1.0
		 */
		public function init_marketing_notifications() {
			$notifications = new MarketingNotifications();
			$notifications->init_notifications();
		}

		/**
		 * Checks all plugin requirements. If run in admin context also adds a notice.
		 *
		 * @return boolean
		 */
		public function check_plugin_requirements() {

			$errors = array();
			global $wp_version;

			if ( ! version_compare( PHP_VERSION, self::PLUGIN_REQUIREMENTS['php_version'], '>=' ) ) {
				/* Translators: The minimum PHP version */
				$errors[] = sprintf( esc_html__( 'Pinterest for WooCommerce requires a minimum PHP version of %s.', 'pinterest-for-woocommerce' ), self::PLUGIN_REQUIREMENTS['php_version'] );
			}

			if ( ! version_compare( $wp_version, self::PLUGIN_REQUIREMENTS['wp_version'], '>=' ) ) {
				/* Translators: The minimum WP version */
				$errors[] = sprintf( esc_html__( 'Pinterest for WooCommerce requires a minimum WordPress version of %s.', 'pinterest-for-woocommerce' ), self::PLUGIN_REQUIREMENTS['wp_version'] );
			}

			if ( ! defined( 'WC_VERSION' ) || ! version_compare( WC_VERSION, self::PLUGIN_REQUIREMENTS['wc_version'], '>=' ) ) {
				/* Translators: The minimum WC version */
				$errors[] = sprintf( esc_html__( 'Pinterest for WooCommerce requires a minimum WooCommerce version of %s.', 'pinterest-for-woocommerce' ), self::PLUGIN_REQUIREMENTS['wc_version'] );
			}

			/**
			 * Check if WooCommerce Admin is enabled.
			 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
			 */
			if ( apply_filters( 'woocommerce_admin_disabled', false ) ) {
				$errors[] = esc_html__( 'Pinterest for WooCommerce requires WooCommerce Admin to be enabled.', 'pinterest-for-woocommerce' );
			}

			if ( ! function_exists( 'as_has_scheduled_action' ) ) {
				/* Translators: The minimum Action Scheduler version */
				$errors[] = sprintf( esc_html__( 'Pinterest for WooCommerce requires a minimum Action Scheduler package of %s. It can be caused by old version of the WooCommerce extensions.', 'pinterest-for-woocommerce' ), self::PLUGIN_REQUIREMENTS['action_scheduler'] );
			}

			if ( empty( $errors ) ) {
				return true;
			}

			if ( $this->is_request( 'admin' ) ) {
				add_action(
					'admin_notices',
					function() use ( $errors ) {
						?>
						<div class="notice notice-error">
							<?php
							foreach ( $errors as $error ) {
								echo '<p>' . esc_html( $error ) . '</p>';
							}
							?>
						</div>
						<?php
					}
				);
				return;
			}

			return false;
		}

		/**
		 * Plugin update entry point.
		 *
		 * @since 1.0.9
		 * @return void
		 */
		public function maybe_update_plugin() {
			$plugin_update = new Pinterest\PluginUpdate();
			$plugin_update->maybe_update();
		}

		/**
		 * Load Localisation files.
		 *
		 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
		 *
		 * Locales found in:
		 *      - WP_LANG_DIR/pinterest-for-woocommerce/pinterest-for-woocommerce-LOCALE.mo
		 *      - WP_LANG_DIR/plugins/pinterest-for-woocommerce-LOCALE.mo
		 */
		private function load_plugin_textdomain() {
			/**
			 * Get plugin locale.
			 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
			 */
			$locale = apply_filters( 'plugin_locale', get_locale(), 'pinterest-for-woocommerce' );

			load_textdomain( 'pinterest-for-woocommerce', WP_LANG_DIR . '/pinterest-for-woocommerce/pinterest-for-woocommerce-' . $locale . '.mo' );
			load_plugin_textdomain( 'pinterest-for-woocommerce', false, plugin_basename( dirname( __FILE__ ) ) . '/i18n/languages' );
		}

		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			/**
			 * Returns template path.
			 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
			 */
			return apply_filters( 'pinterest_for_woocommerce_template_path', 'pinterest-for-woocommerce/' );
		}

		/**
		 * Get Ajax URL.
		 *
		 * @return string
		 */
		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}


		/**
		 * Return APP Settings
		 *
		 * @since 1.0.0
		 *
		 * @param boolean $force  Controls whether to force getting a fresh value instead of one from the runtime cache.
		 * @param string  $option Controls which option to read/write to.
		 *
		 * @return array
		 */
		public static function get_settings( $force = false, $option = PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME ) {

			static $settings;

			if ( $force || is_null( $settings ) || ! isset( $settings[ $option ] ) || ( isset( self::$dirty_settings[ $option ] ) && self::$dirty_settings[ $option ] ) ) {
				$settings[ $option ] = get_option( $option );
			}

			return $settings[ $option ];
		}


		/**
		 * Return APP Setting based on its key
		 *
		 * @since 1.0.0
		 *
		 * @param string  $key The key of specific option to retrieve.
		 * @param boolean $force Controls whether to force getting a fresh value instead of one from the runtime cache.
		 *
		 * @return mixed
		 */
		public static function get_setting( $key, $force = false ) {

			$settings = self::get_settings( $force );

			return empty( $settings[ $key ] ) ? false : $settings[ $key ];
		}


		/**
		 * Save APP Setting
		 *
		 * @since 1.0.0
		 *
		 * @param string $key The key of specific option to retrieve.
		 * @param mixed  $data The data to save for this option key.
		 *
		 * @return boolean
		 */
		public static function save_setting( $key, $data ) {

			$settings = self::get_settings( true );
			// Handle possible false value.
			if ( ! is_array( $settings ) ) {
				$settings = array();
			}
			$settings[ $key ] = $data;

			return self::save_settings( $settings );
		}


		/**
		 * Save APP Settings
		 *
		 * @since 1.0.0
		 *
		 * @param array  $settings The array of settings to save.
		 * @param string $option Controls which option to read/write to.
		 *
		 * @return boolean
		 */
		public static function save_settings( $settings, $option = PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME ) {
			self::$dirty_settings[ $option ] = true;
			return update_option( $option, $settings );
		}


		/**
		 * Return APP Data based on its key
		 *
		 * @since 1.0.0
		 *
		 * @param string  $key The key of specific data to retrieve.
		 * @param boolean $force Controls whether to force getting a fresh value instead of one from the runtime cache.
		 *
		 * @return mixed
		 */
		public static function get_data( $key, $force = false ) {

			$settings = self::get_settings( $force, PINTEREST_FOR_WOOCOMMERCE_DATA_NAME );

			return $settings[ $key ] ?? null;
		}


		/**
		 * Save APP Data
		 *
		 * @since 1.0.0
		 *
		 * @param string $key The key of specific data to retrieve.
		 * @param mixed  $data The data to save for this option key.
		 *
		 * @return boolean
		 */
		public static function save_data( $key, $data ) {

			$settings = self::get_settings( true, PINTEREST_FOR_WOOCOMMERCE_DATA_NAME );
			// Handle possible false value.
			if ( ! is_array( $settings ) ) {
				$settings = array();
			}
			$settings[ $key ] = $data;

			return self::save_settings( $settings, PINTEREST_FOR_WOOCOMMERCE_DATA_NAME );
		}

		/**
		 * Remove APP Data key.
		 *
		 * @param string $key - The key of specific data to retrieve.
		 *
		 * @since 1.3.1
		 *
		 * @return bool - True if the data was removed, false otherwise.
		 */
		public static function remove_data( string $key ) {
			$settings = self::get_settings( true, PINTEREST_FOR_WOOCOMMERCE_DATA_NAME );
			unset( $settings[ $key ] );
			return self::save_settings( $settings, PINTEREST_FOR_WOOCOMMERCE_DATA_NAME );
		}

		/**
		 * Add API endpoints
		 *
		 * @since 1.0.0
		 */
		public function init_api_endpoints() {
			new Pinterest\API\Advertisers();
			new Pinterest\API\AdvertiserConnect();
			new Pinterest\API\Auth();
			new Pinterest\API\AuthDisconnect();
			new Pinterest\API\Businesses();
			new Pinterest\API\DomainVerification();
			new Pinterest\API\FeedState();
			new Pinterest\API\FeedIssues();
			new Pinterest\API\Tags();
			new Pinterest\API\HealthCheck();
			new Pinterest\API\Options();
			new Pinterest\API\SyncSettings();
			new Pinterest\API\UserInteraction();
		}

		/**
		 * Get decrypted token data.
		 *
		 * The Access token and Crypto key live in the data option in the following form:
		 * data: {
		 *   ...
		 *   token: {
		 *     access_token: ${encrypted_token},
		 *   },
		 *   crypto_encoded_key: ${encryption_key},
		 *   ...
		 * }
		 *
		 * @since 1.0.0
		 *
		 * @return array
		 */
		public static function get_token() {

			$token = self::get_data( 'token', true );

			try {
				$token['access_token'] = empty( $token['access_token'] ) ? '' : Pinterest\Crypto::decrypt( $token['access_token'] );
			} catch ( \Exception $th ) {
				/* Translators: The error description */
				Pinterest\Logger::log( sprintf( esc_html__( 'Could not decrypt the Pinterest API access token. Try reconnecting to Pinterest. [%s]', 'pinterest-for-woocommerce' ), $th->getMessage() ), 'error' );
				$token = array();
			}

			return $token;
		}


		/**
		 * Save encrypted token data. See the documentation of the get_token() method for the expected format of the related data variables.
		 *
		 * @since 1.0.0
		 *
		 * @param array $token The array containing the token values to save.
		 *
		 * @return boolean
		 */
		public static function save_token( $token ) {

			$token['access_token'] = empty( $token['access_token'] ) ? '' : Pinterest\Crypto::encrypt( $token['access_token'] );
			return self::save_data( 'token', $token );
		}


		/**
		 * Disconnect by clearing the Token and any other data that we should gather from scratch.
		 *
		 * @since 1.0.0
		 *
		 * @return boolean True if disconnection was successful.
		 *
		 * @throws \Exception PHP Exception.
		 */
		public static function disconnect() {
			/*
			 * If there is no business connected, disconnecting merchant will throw error.
			 * Just need to clean account data in these cases.
			 */
			if ( ! self::is_business_connected() ) {

				self::flush_options();

				// At this point we're disconnected.
				return true;
			}

			try {
				// Disconnect merchant from Pinterest.
				$result = Pinterest\API\Base::disconnect_merchant();

				if ( 'success' !== $result['status'] ) {
					throw new \Exception( esc_html__( 'Response error on disconnect merchant.', 'pinterest-for-woocommerce' ), 400 );
				}

				// Disconnect the advertiser from Pinterest.
				$connected_advertiser = self::get_setting( 'tracking_advertiser', null );
				$connected_tag        = self::get_setting( 'tracking_tag', null );

				if ( $connected_advertiser && $connected_tag ) {

					try {

						Pinterest\API\AdvertiserConnect::disconnect_advertiser( $connected_advertiser, $connected_tag );

					} catch ( \Exception $th ) {

						Pinterest\Logger::log( esc_html__( 'There was an error disconnecting the Advertiser.', 'pinterest-for-woocommerce' ) );
						self::flush_options();
						throw new \Exception( esc_html__( 'There was an error disconnecting the Advertiser. Please try again.', 'pinterest-for-woocommerce' ), 400 );
					}
				}

				self::flush_options();

				// At this point we're disconnected.
				return true;
			} catch ( PinterestApiException $e ) {
				$code = $e->get_pinterest_code();

				if ( PinterestApiException::MERCHANT_NOT_FOUND === $code ) {
					Pinterest\Logger::log( esc_html__( 'Trying to disconnect while the merchant (id) was not found.', 'pinterest-for-woocommerce' ) );

					/*
					 * This is an abnormal state of the application. Caused probably by issues during the connection process.
					 * It looks like the best course of actions is to flush the options and assume that we are disconnected.
					 * This way we restore UI connect functionality and allow merchant to retry.
					 */
					self::flush_options();
					return true;
				}

				return false;

			} catch ( \Exception $th ) {
				// There was an error disconnecting merchant.
				return false;
			}
		}


		/**
		 * Flush data option and remove settings.
		 *
		 * @return void
		 */
		private static function flush_options() {

			// Flush the whole data option.
			delete_option( PINTEREST_FOR_WOOCOMMERCE_DATA_NAME );
			UserInteraction::flush_options();

			// Remove settings that may cause issues if stale on disconnect.
			self::save_setting( 'account_data', null );
			self::save_setting( 'tracking_advertiser', null );
			self::save_setting( 'tracking_tag', null );

			// Cancel scheduled jobs.
			Pinterest\ProductSync::cancel_jobs();
		}


		/**
		 * Disconnect advertiser from the platform if advertiser or tag change.
		 *
		 * @param array $old_value The old value of the option.
		 * @param array $new_value The new value of the option.
		 */
		public static function maybe_disconnect_advertiser( $old_value, $new_value ) {

			if ( ! is_array( $old_value ) || ! is_array( $new_value ) ) {
				return;
			}

			if (
				! isset( $old_value['tracking_advertiser'] ) ||
				! isset( $old_value['tracking_tag'] ) ||
				! isset( $new_value['tracking_advertiser'] ) ||
				! isset( $new_value['tracking_tag'] )
			) {
				return;
			}

			// Disconnect merchant if old values are different than new ones.
			if ( $old_value['tracking_advertiser'] !== $new_value['tracking_advertiser'] || $old_value['tracking_tag'] !== $new_value['tracking_tag'] ) {

				try {

					Pinterest\API\AdvertiserConnect::disconnect_advertiser( $old_value['tracking_advertiser'], $old_value['tracking_tag'] );

				} catch ( \Exception $th ) {

					Pinterest\Logger::log( esc_html__( 'There was an error disconnecting the Advertiser. Please try again.', 'pinterest-for-woocommerce' ) );
				}
			}
		}

		/**
		 * Return WooConnect Bridge URL
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public static function get_connection_proxy_url() {

			/**
			 * Filters the proxy URL.
			 *
			 * @since 1.0.0
			 *
			 * @param string $proxy_url the connection proxy URL
			 */
			return (string) trailingslashit( apply_filters( 'pinterest_for_woocommerce_connection_proxy_url', PINTEREST_FOR_WOOCOMMERCE_WOO_CONNECT_URL ) );
		}


		/**
		 * Return The Middleware URL based on the given context
		 *
		 * @since 1.0.0
		 *
		 * @param string $context The context parameter.
		 * @param string $args    Additional arguments like 'view' or 'business_id'.
		 *
		 * @return string
		 */
		public static function get_middleware_url( $context = 'login', $args = array() ) {

			$control_key = uniqid();
			$view        = is_null( $args['view'] ) ? 'settings' : $args['view'];
			$rest_url    = get_rest_url( null, PINTEREST_FOR_WOOCOMMERCE_API_NAMESPACE . '/v' . PINTEREST_FOR_WOOCOMMERCE_API_VERSION . '/' . PINTEREST_FOR_WOOCOMMERCE_API_AUTH_ENDPOINT );

			$state_params = array(
				'redirect' => add_query_arg(
					array(
						'control' => $control_key,
						'view'    => $view,
					),
					$rest_url
				),
			);

			switch ( $context ) {
				case 'create_business':
					$state_params['create-business'] = true;
					break;
				case 'switch_business':
					$state_params['switch-to-business'] = $args['business_id'];
					break;
			}

			$state = http_build_query( $state_params );

			set_transient( PINTEREST_FOR_WOOCOMMERCE_AUTH, $control_key, MINUTE_IN_SECONDS * 5 );

			// phpcs:ignore Squiz.Commenting.InlineComment.InvalidEndChar
			// nosemgrep: audit.php.wp.security.xss.query-arg
			return self::get_connection_proxy_url() . 'login/' . PINTEREST_FOR_WOOCOMMERCE_WOO_CONNECT_SERVICE . '?' . $state;
		}


		/**
		 * Injects needed meta tags to the site's header
		 *
		 * @since 1.0.0
		 */
		public function maybe_inject_verification_code() {

			$verification_data = self::get_data( 'verification_data' );

			if ( $verification_data ) {
				printf( '<meta name="p:domain_verify" content="%s"/>', esc_attr( $verification_data['verification_code'] ) );
			}
		}


		/**
		 * Fetches the account_data parameters from Pinterest's API
		 * Saves it to the plugin options and returns it.
		 *
		 * @since 1.0.0
		 *
		 * @return array Account data from Pinterest.
		 *
		 * @throws Exception PHP Exception.
		 */
		public static function update_account_data() {

			try {

				$account_data = Pinterest\API\Base::get_account_info();

				if ( 'success' === $account_data['status'] ) {

					$data = array_intersect_key(
						(array) $account_data['data'],
						array(
							'verified_user_websites'  => '',
							'is_any_website_verified' => '',
							'username'                => '',
							'full_name'               => '',
							'id'                      => '',
							'image_medium_url'        => '',
							'is_partner'              => '',
						)
					);

					/*
					 * For now we assume that the billing is not setup and credits are not redeemed.
					 * We will be able to check that only when the advertiser will be connected.
					 * The billing is tied to advertiser.
					 */
					$data['is_billing_setup']     = false;
					$data['coupon_redeem_info']   = array( 'redeem_status' => false );
					$data['currency_credit_info'] = AdsCreditCurrency::get_currency_credits();

					Pinterest_For_Woocommerce()::save_setting( 'account_data', $data );
					return $data;
				}

				self::get_linked_businesses( true );

			} catch ( Throwable $th ) {

				self::disconnect();

				throw new Exception( esc_html__( 'There was an error getting the account data.', 'pinterest-for-woocommerce' ) );
			}

			return array();

		}

		/**
		 * Add billing setup information to the account data option.
		 * Using this function makes sense only when we have a connected advertiser.
		 *
		 * @since 1.2.5
		 *
		 * @return bool Wether billing is set up or not.
		 */
		public static function add_billing_setup_info_to_account_data() {
			$account_data                     = self::get_setting( 'account_data' );
			$account_data['is_billing_setup'] = Billing::has_billing_set_up();
			self::save_setting( 'account_data', $account_data );
			Billing::mark_billing_setup_checked();
			return $account_data['is_billing_setup'];
		}

		/**
		 *
		 * @since 1.2.5
		 *
		 * @return void
		 */
		public static function maybe_check_billing_setup() {
			$account_data          = Pinterest_For_Woocommerce()::get_setting( 'account_data' );
			$has_billing_setup_old = is_array( $account_data ) && ( $account_data['is_billing_setup'] ?? false );
			if ( Billing::should_check_billing_setup_often() ) {
				$has_billing_setup_new = self::add_billing_setup_info_to_account_data();
				// Detect change in billing setup to true and try to redeem.
				if ( $has_billing_setup_new && ! $has_billing_setup_old ) {
					AdCredits::handle_redeem_credit();
				}
			}
		}

		/**
		 * Get billing setup information from the account data option.
		 *
		 * @since 1.2.5
		 *
		 * @return bool
		 */
		public static function get_billing_setup_info_from_account_data() {
			$account_data = self::get_setting( 'account_data' );

			return (bool) $account_data['is_billing_setup'];
		}

		/**
		 * Add redeem credits information to the account data option.
		 * Using this function makes sense only when we have a connected advertiser and the billing data is set up.
		 *
		 * @since 1.2.5
		 *
		 * @return void
		 */
		public static function add_redeem_credits_info_to_account_data() {
			$account_data = self::get_setting( 'account_data' );
			$offer_code   = AdCreditsCoupons::get_coupon_for_merchant();

			// Redeem the coupon.
			$error_code    = false;
			$error_message = '';
			$redeem_status = AdCredits::redeem_credits( $offer_code, $error_code, $error_message );

			$redeem_information = array(
				'redeem_status' => $redeem_status,
				'offer_code'    => $offer_code,
				'advertiser_id' => Pinterest_For_Woocommerce()::get_setting( 'tracking_advertiser' ),
				'username'      => $account_data['username'],
				'id'            => $account_data['id'],
				'error_id'      => $error_code,
				'error_message' => $error_message,
			);

			/*
			 * Track the redeemed offer code.
			 */
			self::record_event(
				'pfw_ads_redeem_credits',
				array(
					'redeem_status' => $redeem_information['redeem_status'],
					'offer_code'    => $redeem_information['offer_code'],
					'error_id'      => $redeem_information['error_id'],
				)
			);

			$account_data['coupon_redeem_info'] = $redeem_information;

			self::save_setting( 'account_data', $account_data );
		}

		/**
		 * Add currency_credit_info information to the account data option.
		 *
		 * @since 1.3.9
		 *
		 * @return void
		 */
		public static function add_currency_credits_info_to_account_data() {
			$account_data = self::get_setting( 'account_data' );
			if ( ! isset( $account_data['currency_credit_info'] ) ) {
				// Handle possible false value.
				if ( ! is_array( $account_data ) ) {
					$account_data = array();
				}
				$account_data['currency_credit_info'] = AdsCreditCurrency::get_currency_credits();
				self::save_setting( 'account_data', $account_data );
			}
		}

		/**
		 * Add available credits information to the account data option.
		 *
		 * @since 1.2.5
		 *
		 * @return void
		 */
		public static function add_available_credits_info_to_account_data() {
			$account_data = self::get_setting( 'account_data' );

			try {
				// Check for available discounts.
				$account_data['available_discounts'] = AdCredits::process_available_discounts();
				self::save_setting( 'account_data', $account_data );
			} catch ( Exception $e ) {
				return;
			}
		}

		/**
		 * Check if coupon was redeemed. We can redeem only once.
		 *
		 * @since 1.2.5
		 *
		 * @return bool
		 */
		public static function check_if_coupon_was_redeemed() {
			$account_data = self::get_setting( 'account_data' );

			$redeem_status = is_array( $account_data['coupon_redeem_info'] ) ? $account_data['coupon_redeem_info']['redeem_status'] : false;
			$error         = $account_data['coupon_redeem_info']['error_id'];
			if ( 2322 === $error || 2318 === $error ) {
				/*
				 * Advertiser has already redeemed the coupon or
				 * the coupon was redeemed by a different advertiser of the same user.
				 * In both cases another redeem is not possible.
				 */
				return true;
			}

			if ( false === $redeem_status ) {
				return false;
			}

			return true;
		}

		/**
		 * Fetches a fresh copy (if needed or explicitly requested), of the authenticated user's linked business accounts.
		 *
		 * @param boolean $force_refresh Wether to refresh the data from the API.
		 *
		 * @return array
		 */
		public static function get_linked_businesses( $force_refresh = false ) {

			$linked_businesses = ! $force_refresh ? Pinterest_For_Woocommerce()::get_data( 'linked_businesses' ) : null;

			if ( null === $linked_businesses ) {
				$linked_businesses = self::update_linked_businesses();
			}

			$linked_businesses = array_map(
				function ( $business ) {
					return array(
						'value' => $business->id,
						'label' => $business->full_name . ' [' . $business->id . ']',
					);
				},
				$linked_businesses
			);

			return $linked_businesses;
		}


		/**
		 * Grabs a fresh copy of businesses from the API saves & returns them.
		 *
		 * @return array
		 */
		public static function update_linked_businesses() {

			$account_data            = Pinterest_For_Woocommerce()::get_setting( 'account_data' );
			$fetch_linked_businesses =
				! empty( $account_data ) &&
				array_key_exists( 'is_partner', $account_data ) &&
				! $account_data['is_partner'];

			try {
				$fetched_businesses = $fetch_linked_businesses ? Pinterest\API\Base::get_linked_businesses() : array();
			} catch ( Exception $e ) {
				$fetched_businesses = array();
			}

			if ( ! empty( $fetched_businesses ) && 'success' === $fetched_businesses['status'] ) {
				$linked_businesses = $fetched_businesses['data'];
			}

			$linked_businesses = $linked_businesses ?? array();

			self::save_data( 'linked_businesses', $linked_businesses );

			return $linked_businesses;
		}

		/**
		 * Returns the Pinterest AccountID from the database.
		 *
		 * @return string|false
		 */
		public static function get_account_id() {
			$account_data = Pinterest_For_Woocommerce()::get_setting( 'account_data' );
			return isset( $account_data['id'] ) ? $account_data['id'] : false;
		}

		/**
		 * Sets the default settings based on the
		 * given values in self::$default_settings
		 *
		 * @return boolean
		 */
		public static function set_default_settings() {

			$settings = self::get_settings( true );
			$settings = wp_parse_args( $settings, self::$default_settings );

			return self::save_settings( $settings );

		}

		/**
		 * Hook the parse_request action and serve the html
		 *
		 * @param WP $wp Current WordPress environment instance.
		 */
		public function verification_request( $wp ) {
			$verification_data = self::get_data( 'verification_data' );
			if ( ! $verification_data || ! array_key_exists( 'filename', $verification_data ) ) {
				return;
			}

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$request = trim( $wp->request ?? $_SERVER['PHP_SELF'] ?? '', '/' );
			if ( $verification_data['filename'] === $request ) {
				wc_nocache_headers();
				header( 'Content-Type: text/html' );
				?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="p:domain_verify" content="<?php echo esc_attr( $verification_data['verification_code'] ); ?>"/>
	<title></title>
</head>
<body><?php esc_html_e( 'Pinterest for WooCommerce verification page', 'pinterest-for-woocommerce' ); ?></body>
</html>
				<?php
				exit;
			}
		}

		/**
		 * Trigger coupons check.
		 *
		 * @since 1.2.5
		 *
		 * @return void
		 */
		public function check_available_coupons_and_credits() {
			Pinterest_For_Woocommerce()::add_available_credits_info_to_account_data();
		}

		/**
		 * Checks if setup is completed and all requirements are set.
		 *
		 * @return boolean
		 */
		public static function is_setup_complete() {
			return self::is_business_connected() && self::is_domain_verified() && self::is_tracking_configured();
		}


		/**
		 * Checks if connected by checking if we got a token in the db.
		 *
		 * @return boolean
		 */
		public static function is_connected() {
			$token = self::get_token();
			return $token && ! empty( $token['access_token'] );
		}


		/**
		 * Checks if connected and on a Business account.
		 *
		 * @return boolean
		 */
		public static function is_business_connected() {
			if ( ! self::is_connected() ) {
				return false;
			}

			$account_data = self::get_setting( 'account_data' );

			return isset( $account_data['is_partner'] ) ? (bool) $account_data['is_partner'] : false;
		}



		/**
		 * Checks whether we have verified our domain, by checking account_data as
		 * returned by Pinterest.
		 *
		 * @return boolean
		 */
		public static function is_domain_verified() {
			$account_data = self::get_setting( 'account_data' );
			return isset( $account_data['is_any_website_verified'] ) ? (bool) $account_data['is_any_website_verified'] : false;
		}

		/**
		 * Checks if tracking is configured properly and enabled.
		 *
		 * @return boolean
		 */
		public static function is_tracking_configured() {
			return false !== Pinterest\Tracking::get_active_tag();
		}


		/**
		 * Returns the Terms object for the currently configured base country.
		 *
		 * @return array
		 */
		public static function get_applicable_tos() {

			$base_country = self::get_base_country();

			return $base_country && isset( self::TOS_PER_COUNTRY[ $base_country ] ) ? self::TOS_PER_COUNTRY[ $base_country ] : self::TOS_PER_COUNTRY['*'];
		}

		/**
		 * Helper function to return the country set in WC's settings using wc_get_base_location().
		 *
		 * @return string|null
		 */
		public static function get_base_country() {
			if ( ! function_exists( 'wc_get_base_location' ) ) {
				return null;
			}

			$base_location = wc_get_base_location();

			return ! empty( $base_location['country'] ) ? $base_location['country'] : null;
		}

		/**
		 * Adds the onboarding task to the Tasklists.
		 *
		 * @since 1.2.11
		 */
		public function add_onboarding_task() {
			if ( class_exists( TaskLists::class ) ) { // compatibility-code "< WC 5.9". This is added for backward compatibility.
				TaskLists::add_task(
					'extended',
					new Onboarding(
						TaskLists::get_list( 'extended' )
					)
				);
			}
		}
	}

endif;
