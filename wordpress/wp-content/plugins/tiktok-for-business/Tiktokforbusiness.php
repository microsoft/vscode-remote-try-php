<?php

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

require_once __DIR__ . '/mapi/Tt4b_Mapi_Class.php';
require_once __DIR__ . '/logging/Logger.php';
require_once __DIR__ . '/catalog/Tt4b_Catalog_Class.php';
require_once __DIR__ . '/pixel/Tt4b_Pixel_Class.php';
require_once __DIR__ . '/admin/tts/common.php';

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists;

/**
 * The plugin loader class
 */
class Tiktokforbusiness {


	/**
	 * The version of TikTok for WooCommerce
	 *
	 * @var string[]
	 */
	private static $current_tiktok_for_woocommerce_version = '1.2.6';

	/**
	 * Whether WooCommerce has been loaded.
	 *
	 * @var bool
	 */
	private static $woocommerce_loaded = false;

	/**
	 * The TT4B instance.
	 */
	private static $instance = null;

	/**
	 * The constructor.
	 */
	private function __construct() {
		$this->initialize_hooks();
	}

	/**
	 * Initializes hooks.
	 *
	 * This should be hooked to the 'woocommerce_loaded' action.
	 *
	 * @return void
	 */
	public function initialize_hooks() {

		include_once __DIR__ . '/admin/tts/order_list.php';
		include_once __DIR__ . '/admin/tts/order_detail.php';
		include_once __DIR__ . '/admin/tt4b_menu.php';
		include_once __DIR__ . '/pixel/tt4b_pixel.php';

		$this->init();
	}

	/**
	 * Initialize most of the plugin logic.
	 *
	 * @return void
	 */
	private function init() {
		if ( get_option( 'tt4b_version' ) !== self::$current_tiktok_for_woocommerce_version ) {
			update_option( 'tt4b_version', self::$current_tiktok_for_woocommerce_version );
		}

		$logger  = new Logger();
		$mapi    = new Tt4b_Mapi_Class( $logger );
		$catalog = new Tt4b_Catalog_Class( $mapi, $logger );
		$mapi->init();
		$catalog->init();

		// Hook the onboarding task. The hook admin_init is not triggered when the WC fetches the tasks using the endpoint: wp-json/wc-admin/onboarding/tasks and hence hooking into init.
		if ( did_action( 'woocommerce_loaded' ) > 0 ) {
			add_action( 'init', [ $this, 'add_onboarding_task' ], 20 );
		}

		add_filter( 'plugin_action_links_' . plugin_basename( $this->get_plugin_file() ), array( $this, 'plugin_action_links' ) );
	}

	/**
	 * Get the plugin file name.
	 */
	public function get_plugin_file() {
		$slug = dirname( plugin_basename( __FILE__ ) );
		return trailingslashit( $slug ) . $slug . '.php';
	}

	/**
	 * Adds plugin action links.
	 */
	public function plugin_action_links( $actions ) {
		$custom_actions = [];

		// settings url(s).
		$custom_actions['configure'] = $this->get_settings_link();

		// add the links to the front of the actions list.
		return array_merge( $custom_actions, $actions );
	}

	/**
	 * Gets the configuration link to direct to the plugin set-up.
	 */
	public function get_settings_link() {
		$settings_url = get_admin_url() . 'admin.php?page=tiktok';
		if ( $settings_url ) {
			return sprintf( '<a href="%s">%s</a>', $settings_url, esc_html__( 'Configure', 'tiktok-for-woocommerce' ) );
		}
		// no settings.
		return '';
	}

	/**
	 * Adds the onboarding task to the Tasklists.
	 *
	 * @since x.x.x
	 */
	public function add_onboarding_task() {
		include_once __DIR__ . '/admin/Tasks/Onboarding.php';
		if ( ! class_exists( TaskLists::class ) ) {
			// WC 5.9 backward compatibility.
			return;
		}

		TaskLists::add_task(
			'extended',
			new Onboarding(
				TaskLists::get_list( 'extended' )
			)
		);
	}

	/**
	 * Deactivates plugin.
	 *
	 * @return void
	 */
	public static function tt_plugin_deactivate() {
		$external_business_id = get_option( 'tt4b_external_business_id' );

		// delete scheduled TikTok-WooCommerce related actions
		if ( self::$woocommerce_loaded ) {
			as_unschedule_all_actions( 'tt4b_trust_signal_collection' );
			as_unschedule_all_actions( 'tt4b_trust_signal_helper' );
			as_unschedule_all_actions( 'tt4b_catalog_sync' );
			as_unschedule_all_actions( 'tt4b_catalog_sync_helper' );
		}
		$logger = new Logger();
		$mapi          = new Tt4b_Mapi_Class( $logger );
		$external_data = get_option( 'tt4b_external_data' );
		$params = array(
			'business_platform'    => 'WOO_COMMERCE',
			'external_business_id' => $external_business_id,
		);

		// call disconnect API
		$mapi->tbp_post( $external_data, 'business_profile/disconnect', 'v2.0', $params );

		// delete tiktok credentials
		delete_option( 'tt4b_app_id' );
		delete_option( 'tt4b_secret' );
		delete_option( 'tt4b_access_token' );
		delete_option( 'tt4b_external_data_key' );
		delete_option( 'tt4b_user_country' );

		// call tts disconnect
		$mapi->tts_shop_disconnect( $external_data );

		delete_option( 'tt4b_external_data' );
		delete_option( 'tt4b_eligibility_page_total' );
		delete_option( 'tt4b_version' );
		delete_option( 'tt4b_mapi_total_gmv' );
		delete_option( 'tt4b_mapi_total_orders' );
		delete_option( 'tt4b_mapi_tenure' );
		delete_option( 'tt4b_should_send_s2s_events' );
		delete_option( 'tt4b_product_delete_queue' );
		delete_option( 'tt4b_last_product_sync_time' );
	}

	/**
	 * Generates app credentials.
	 *
	 * @return void
	 */
	public static function tt_plugin_activate() {
		$logger               = new Logger();
		$mapi                 = new Tt4b_Mapi_Class( $logger );
		$external_business_id = get_option( 'tt4b_external_business_id' );
		if ( false === $external_business_id ) {
			$external_business_id = uniqid( 'tt4b_woocommerce_' );
			update_option( 'tt4b_external_business_id', $external_business_id );
		}
		add_option( 'tt4b_eligibility_page_total', 0 );
		add_option( 'tt4b_version', self::$current_tiktok_for_woocommerce_version );
		add_option( 'tt4b_mapi_total_gmv', 0 );
		add_option( 'tt4b_mapi_total_orders', 0 );
		add_option( 'tt4b_mapi_tenure', 0 );
		add_option( 'tt4b_product_delete_queue', array() );
		add_option( 'tt4b_last_product_sync_time', 0 );
		$cleaned_redirect = preg_replace( '/[^A-Za-z0-9\-]/', '', admin_url() );
		$smb_id           = $external_business_id . $cleaned_redirect;
		$app_rsp          = $mapi->create_open_source_app( $smb_id, 'PROD', admin_url() );
		if ( false !== $app_rsp ) {
			$open_source_app_rsp = json_decode( $app_rsp, true );
			$secret              = $open_source_app_rsp['data']['app_secret'];
			$app_id              = $open_source_app_rsp['data']['app_id'];
			$external_data_key   = $open_source_app_rsp['data']['external_data_key'];
			update_option( 'tt4b_app_id', $app_id );
			update_option( 'tt4b_secret', $secret );
			update_option( 'tt4b_external_data_key', $external_data_key );
		}
	}

	/**
	 * Get the instance of the Tiktokforbusiness class.
	 *
	 * @return Tiktokforbusiness
	 */
	public static function tiktok_for_business_get_instance() {
		static $instance = null;
		if ( null === $instance ) {
			$instance = new Tiktokforbusiness();
		}

		return $instance;
	}
}
