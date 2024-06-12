<?php
/**
 * Handle Admin init.
 *
 * @package     Pinterest/Admin
 * @version     1.0.0
 */

use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Admin\Features\Navigation\Menu;
use Automattic\WooCommerce\Admin\Features\Navigation\Screen;
use Automattic\WooCommerce\Admin\Loader;
use Automattic\WooCommerce\Admin\PageController;
use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry;
use Automattic\WooCommerce\Pinterest\Compat;
use Automattic\WooCommerce\Pinterest\Tracking;
use Automattic\WooCommerce\Pinterest\PinterestSyncSettings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Pinterest_For_Woocommerce_Admin' ) ) :

	/**
	 * Class handling the settings page and onboarding Wizard registration and rendering.
	 */
	class Pinterest_For_Woocommerce_Admin {

		/**
		 * Initialize class
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_assets' ) );
			add_action( 'admin_init', array( $this, 'maybe_redirect_to_middleware' ) );
			add_filter( 'woocommerce_get_registered_extended_tasks', array( $this, 'register_task_list_item' ), 10, 1 );
			add_filter( 'admin_footer', array( $this, 'load_settings' ) );
			add_filter( 'woocommerce_marketing_menu_items', array( $this, 'add_menu_items' ) );
			add_action( 'admin_menu', array( $this, 'fix_menu_paths' ) );
			add_action( 'admin_menu', array( $this, 'register_wc_admin_pages' ) );
		}


		/**
		 * Handle registration of all needed pages, depending on setup_complete status,
		 * and wether we have the new WC nav enabled or not.
		 *
		 * @return void
		 */
		public function register_wc_admin_pages() {

			$new_nav        = $this->is_new_nav_enabled();
			$setup_complete = Pinterest_For_Woocommerce()::is_setup_complete();

			if ( $new_nav && $setup_complete ) {

				// If setup is complete, add the base menu item as a category, and the settings as the main item.
				// Connection & catalog are added later on, for both new and old nav.

				wc_admin_register_page(
					array(
						'id'       => 'pinterest-for-woocommerce-category',
						'title'    => esc_html__( 'Pinterest', 'pinterest-for-woocommerce' ),
						'parent'   => 'woocommerce',
						'path'     => '/pinterest/settings',
						'nav_args' => array(
							'title'        => esc_html__( 'Pinterest', 'pinterest-for-woocommerce' ),
							'is_category'  => true,
							'menuId'       => 'plugins',
							'is_top_level' => true,
						),
					)
				);

				wc_admin_register_page(
					array(
						'id'       => 'pinterest-for-woocommerce-catalog',
						'title'    => esc_html__( 'Catalog', 'pinterest-for-woocommerce' ),
						'parent'   => 'pinterest-for-woocommerce-category',
						'path'     => '/pinterest/catalog',
						'nav_args' => array(
							'order'  => 10,
							'parent' => 'pinterest-for-woocommerce-category',
						),
					)
				);

			} elseif ( $new_nav ) {

				// Setup not complete. Add the Landing page as the main menu item.
				wc_admin_register_page(
					array(
						'id'       => 'pinterest-for-woocommerce-landing-page',
						'title'    => esc_html__( 'Pinterest', 'pinterest-for-woocommerce' ),
						'parent'   => 'woocommerce',
						'path'     => '/pinterest/landing',
						'nav_args' => array(
							'title'        => esc_html__( 'Pinterest', 'pinterest-for-woocommerce' ),
							'menuId'       => 'plugins',
							'is_top_level' => true,
						),
					)
				);

				// Allow rendering of the onboarding guide on a page refresh.
				wc_admin_register_page(
					array(
						'id'     => 'pinterest-for-woocommerce-setup-guide',
						'title'  => esc_html__( 'Setup Pinterest', 'pinterest-for-woocommerce' ),
						'parent' => '',
						'path'   => '/pinterest/onboarding',
					)
				);

			}

			$menu_items_parent = $new_nav ? 'pinterest-for-woocommerce-category' : 'toplevel_page_woocommerce-marketing';

			if ( $setup_complete ) {

				// The connection & settings pages are registered for both old & new nav, if setup is complete.

				wc_admin_register_page(
					array(
						'id'       => 'pinterest-for-woocommerce-connection',
						'title'    => esc_html__( 'Connection', 'pinterest-for-woocommerce' ),
						'parent'   => $menu_items_parent,
						'path'     => '/pinterest/connection',
						'nav_args' => array(
							'order'  => 50,
							'parent' => $menu_items_parent,
						),
					)
				);

				wc_admin_register_page(
					array(
						'id'       => 'pinterest-for-woocommerce-settings',
						'title'    => esc_html__( 'Settings', 'pinterest-for-woocommerce' ),
						'parent'   => $menu_items_parent,
						'path'     => '/pinterest/settings',
						'nav_args' => array(
							'order'  => 40,
							'parent' => $menu_items_parent,
						),
					)
				);

			}

			if ( ! $new_nav ) {

				// Allow rendering of the onboarding guide on a page refresh.
				wc_admin_register_page(
					array(
						'id'     => 'pinterest-for-woocommerce-setup-guide',
						'title'  => esc_html__( 'Setup Pinterest', 'pinterest-for-woocommerce' ),
						'parent' => 'toplevel_page_woocommerce-marketing',
						'path'   => '/pinterest/onboarding',
					)
				);
			}

			if ( $setup_complete ) {
				// Allow rendering of the landing page on a page refresh for both old & new nav, when setup is complete.
				wc_admin_register_page(
					array(
						'id'     => 'pinterest-for-woocommerce-landing-page',
						'title'  => esc_html__( 'Landing page', 'pinterest-for-woocommerce' ),
						'parent' => '',
						'path'   => '/pinterest/landing',
					)
				);
			}
		}


		/**
		 * Fix sub-menu paths. wc_admin_register_page() gets it wrong.
		 *
		 * @return void
		 */
		public function fix_menu_paths() {
			global $submenu;

			if ( ! isset( $submenu['woocommerce-marketing'] ) || $this->is_new_nav_enabled() ) {
				return;
			}

			foreach ( $submenu['woocommerce-marketing'] as &$item ) {
				// The "slug" (aka the path) is the third item in the array.
				if ( 0 === strpos( $item[2], 'wc-admin' ) ) {
					$item[2] = 'admin.php?page=' . $item[2];
				}
			}
		}


		/**
		 * Add the base menu item using the woocommerce_marketing_menu_items filter,
		 * Depending on status of setup_complete.
		 *
		 * @param array $items The array of items to be filtered.
		 *
		 * @return array
		 */
		public function add_menu_items( $items ) {

			if ( $this->is_new_nav_enabled() ) {
				return $items;
			}

			if ( Pinterest_For_Woocommerce()::is_setup_complete() ) {
				$items[] = array(
					'id'         => 'pinterest-for-woocommerce-catalog',
					'title'      => esc_html__( 'Pinterest', 'pinterest-for-woocommerce' ),
					'path'       => '/pinterest/catalog',
					'capability' => 'manage_woocommerce',
				);
			} else {
				$items[] = array(
					'id'         => 'pinterest-for-woocommerce-landing-page',
					'title'      => esc_html__( 'Pinterest', 'pinterest-for-woocommerce' ),
					'path'       => '/pinterest/landing',
					'capability' => 'manage_woocommerce',
				);
			}

			return $items;
		}


		/**
		 * Checks if the new WC navigation is enabled.
		 *
		 * @return boolean
		 */
		public function is_new_nav_enabled() {
			return method_exists( Screen::class, 'register_post_type' ) &&
				method_exists( Menu::class, 'add_plugin_item' ) &&
				method_exists( Menu::class, 'add_plugin_category' ) &&
				method_exists( Features::class, 'is_enabled' ) &&
				Features::is_enabled( 'navigation' );
		}

		/**
		 * Compatibility layer for is_admin_page function.
		 * Needed since WC 6.5.
		 */
		private function is_admin_page(): bool {
			if ( method_exists( PageController::class, 'is_admin_page' ) ) {
				return PageController::is_admin_page();
			} else {
				return class_exists( Loader::class ) && Loader::is_admin_page();
			}
		}

		/**
		 * Load the scripts needed for the setup guide / settings page.
		 */
		public function load_setup_guide_scripts() {

			if ( ! $this->is_admin_page() ) {
				return;
			}

			$build_path = '/assets/build';

			$handle            = PINTEREST_FOR_WOOCOMMERCE_PREFIX . '-setup-guide';
			$script_asset_path = Pinterest_For_Woocommerce()->plugin_path() . $build_path . '/setup-guide.asset.php';
			$script_info       = file_exists( $script_asset_path )
				? include $script_asset_path
				: array(
					'dependencies' => array(),
					'version'      => PINTEREST_FOR_WOOCOMMERCE_VERSION,
				);

			$script_info['dependencies'][] = 'wc-settings';

			wp_register_script(
				$handle,
				Pinterest_For_Woocommerce()->plugin_url() . $build_path . '/setup-guide.js',
				$script_info['dependencies'],
				$script_info['version'],
				true
			);

			wp_enqueue_script( $handle );

			wp_register_style(
				$handle,
				Pinterest_For_Woocommerce()->plugin_url() . $build_path . '/style-setup-guide.css',
				array( 'wc-admin-app' ),
				PINTEREST_FOR_WOOCOMMERCE_VERSION
			);

			wp_enqueue_style( $handle );
		}

		/**
		 * Enqueues admin related scripts & styles
		 *
		 * @return void
		 */
		public function load_admin_assets() {

			$assets_path_url = str_replace( array( 'http:', 'https:' ), '', Pinterest_For_Woocommerce()->plugin_url() ) . '/assets/';
			$ext             = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			wp_enqueue_script( 'pinterest-for-woocommerce-admin', $assets_path_url . 'js/admin/pinterest-for-woocommerce-admin' . $ext . '.js', array( 'jquery' ), PINTEREST_FOR_WOOCOMMERCE_VERSION, true );

			$this->load_setup_guide_scripts();
		}

		/**
		 * Register the Task List item for WC-Admin.
		 *
		 * @param array $registered_tasks_list_items the list of tasks to be filtered.
		 */
		public function register_task_list_item( $registered_tasks_list_items ) {

			if (
				! $this->is_admin_page() ||
				! Compat::should_show_tasks()
			) {
				return $registered_tasks_list_items;
			}

			$new_task_name = 'woocommerce_admin_add_task_pinterest_setup';

			if ( ! in_array( $new_task_name, $registered_tasks_list_items, true ) ) {
				array_push( $registered_tasks_list_items, $new_task_name );
			}

			return $registered_tasks_list_items;
		}


		/**
		 * Load all plugin frontend data using the AssetDataRegistry class.
		 * Hooked to admin_footer as AssetDataRegistry prints at wp_print_footer_scripts.
		 *
		 * @return void
		 */
		public function load_settings() {
			if ( ! $this->is_admin_page() || ! class_exists( AssetDataRegistry::class ) ) {
				return;
			}

			Package::container()->get( AssetDataRegistry::class )->add( 'pinterest_for_woocommerce', $this->get_component_settings() );
		}


		/**
		 * Initialize asset data and registering it with
		 * the internal WC data registry.
		 *
		 * @return array
		 */
		private function get_component_settings() {
			$store_country = Pinterest_For_Woocommerce()::get_base_country() ?? 'US';

			return array(
				'pluginVersion'            => PINTEREST_FOR_WOOCOMMERCE_VERSION,
				'pluginUrl'                => Pinterest_For_Woocommerce()->plugin_url(),
				'serviceLoginUrl'          => $this->get_service_login_url(),
				'createBusinessAccountUrl' => $this->get_create_business_account_url(),
				'switchBusinessAccountUrl' => $this->get_switch_business_account_url(),
				'homeUrlToVerify'          => get_home_url(),
				'storeCountry'             => $store_country,
				'isAdsSupportedCountry'    => Pinterest_For_Woocommerce_Ads_Supported_Countries::is_ads_supported_country(),
				'isConnected'              => ! empty( Pinterest_For_Woocommerce()::is_connected() ),
				'isBusinessConnected'      => ! empty( Pinterest_For_Woocommerce()::is_business_connected() ),
				'businessAccounts'         => Pinterest_For_Woocommerce()::get_linked_businesses(),
				'apiRoute'                 => PINTEREST_FOR_WOOCOMMERCE_API_NAMESPACE . '/v' . PINTEREST_FOR_WOOCOMMERCE_API_VERSION,
				'optionsName'              => PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME,
				'syncedSettings'           => PinterestSyncSettings::get_synced_settings(),
				'error'                    => isset( $_GET['error'] ) ? sanitize_text_field( wp_unslash( $_GET['error'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended --- not needed
				'pinterestLinks'           => array(
					'newAccount'             => 'https://business.pinterest.com/',
					'claimWebsite'           => 'https://help.pinterest.com/en/business/article/claim-your-website',
					'richPins'               => 'https://help.pinterest.com/en/business/article/rich-pins',
					'enhancedMatch'          => 'https://help.pinterest.com/en/business/article/enhanced-match',
					'createAdvertiser'       => 'https://help.pinterest.com/en/business/article/create-an-advertiser-account',
					'adGuidelines'           => 'https://policy.pinterest.com/en/advertising-guidelines',
					'adDataTerms'            => 'https://policy.pinterest.com/en/ad-data-terms',
					'merchantGuidelines'     => 'https://policy.pinterest.com/en/merchant-guidelines',
					'convertToBusinessAcct'  => 'https://help.pinterest.com/en/business/article/get-a-business-account#section-15096',
					'appealDeclinedMerchant' => 'https://www.pinterest.com/product-catalogs/data-source/?showModal=true',
					'installTag'             => 'https://help.pinterest.com/en/business/article/install-the-pinterest-tag',
					'adsManager'             => 'https://ads.pinterest.com/',
					'preLaunchNotice'        => 'https://help.pinterest.com/en-gb/business/article/get-a-business-profile/',
					'adsAvailability'        => 'https://help.pinterest.com/en/business/availability/ads-availability',
					'automaticEnhancedMatch' => 'https://www.pinterest.com/_/_/help/business/article/automatic-enhanced-match',
					'tagManager'             => $this->get_tag_manager_link(),
				),
				'isSetupComplete'          => Pinterest_For_Woocommerce()::is_setup_complete(),
				'countryTos'               => Pinterest_For_Woocommerce()::get_applicable_tos(),
				'claimWebsiteErrorStatus'  => array(
					401 => 'token',
					403 => 'connection',
					406 => 'domain verification',
					409 => 'meta-tag',
					),
				'conflictingTagsWarning'   => Tracking::get_third_party_tags_warning_message(),
			);
		}


		/**
		 * Return the serviceLoginUrl
		 *
		 * @return string
		 */
		private function get_service_login_url() {
			return add_query_arg(
				array(
					'page' => 'wc-admin',
					'view' => 'wizard',
					PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_go_to_service_login' => '1',
					PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_nonce' => $this->get_middleware_url_nonce(),
				),
				admin_url( 'admin.php' )
			);
		}


		/**
		 * Return the createBusinessAccountUrl
		 *
		 * @return string
		 */
		private function get_create_business_account_url() {
			return add_query_arg(
				array(
					'page' => 'wc-admin',
					'view' => 'wizard',
					PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_go_to_create_account' => '1',
					PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_nonce' => $this->get_middleware_url_nonce(),
				),
				admin_url( 'admin.php' )
			);
		}


		/**
		 * Return the switchBusinessAccountUrl
		 *
		 * @return string
		 */
		private function get_switch_business_account_url() {
			return add_query_arg(
				array(
					'page' => 'wc-admin',
					'view' => 'wizard',
					PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_go_to_switch_account' => '1',
					PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_nonce' => $this->get_middleware_url_nonce(),
				),
				admin_url( 'admin.php' )
			);
		}


		/**
		 * Create & return a nonce for the service URL.
		 * This nonce is runtime cached.
		 *
		 * @return string
		 */
		private function get_middleware_url_nonce() {
			static $nonce;

			return null === $nonce ? wp_create_nonce( 'go_to_middleware_url' ) : $nonce;
		}


		/**
		 * Handles redirection to the Middleware App (Woo Connect Bridge).
		 */
		public function maybe_redirect_to_middleware() {

			if ( ! isset( $_GET[ PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_go_to_service_login' ] ) && ! isset( $_GET[ PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_go_to_create_account' ] ) && ! isset( $_GET[ PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_go_to_switch_account' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended --- not needed
				return;
			}

			if ( ! isset( $_GET[ PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_nonce' ] ) || ! wp_verify_nonce( sanitize_key( $_GET[ PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_nonce' ] ), 'go_to_middleware_url' ) || ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( esc_html__( "Cheatin' huh?", 'pinterest-for-woocommerce' ) );
			}

			$context = 'login';

			$args = array( 'view' => ! empty( $_REQUEST['view'] ) ? sanitize_key( $_REQUEST['view'] ) : null );

			if ( isset( $_GET[ PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_go_to_create_account' ] ) ) {
				$context = 'create_business';
			} elseif ( isset( $_GET[ PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_go_to_switch_account' ] ) && ! empty( $_GET['business_id'] ) ) {
				$context             = 'switch_business';
				$args['business_id'] = sanitize_key( $_GET['business_id'] );
			}

			add_filter( 'allowed_redirect_hosts', array( $this, 'allow_service_login' ) );

			wp_safe_redirect( Pinterest_For_Woocommerce()::get_middleware_url( $context, $args ) );
			exit;

		}

		/**
		 * Add the domain of API/Bridge service to the list of allowed redirect hosts.
		 *
		 * @param array $allowed_hosts the array of allowed hosts.
		 *
		 * @return array
		 */
		public function allow_service_login( $allowed_hosts ) {

			$service_domain  = Pinterest_For_Woocommerce()::get_connection_proxy_url();
			$allowed_hosts[] = wp_parse_url( $service_domain, PHP_URL_HOST );

			return $allowed_hosts;
		}

		/**
		 * Get tags manager link if there is a connected advertiser or the ads manager link if not.
		 *
		 * @since 1.2.18
		 *
		 * @return string
		 */
		protected function get_tag_manager_link() {
			$tag_manager_link = 'https://ads.pinterest.com/advertiser/';

			$advertiser_id = Pinterest_For_Woocommerce()->get_setting( 'tracking_advertiser' );

			if ( ! $advertiser_id ) {
				return $tag_manager_link;
			}

			return "{$tag_manager_link}{$advertiser_id}/conversions/tag";
		}
	}

endif;

return new Pinterest_For_Woocommerce_Admin();
