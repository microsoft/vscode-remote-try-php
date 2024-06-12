<?php
/**
 * Admin settings helper
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Admin_Settings' ) ) {

	/**
	 * Astra Admin Settings
	 */
	class Astra_Admin_Settings {

		/**
		 * Current Slug
		 *
		 * @since 1.0
		 * @var array $current_slug
		 */
		public static $current_slug = 'general';

		/**
		 * Starter Templates Slug
		 *
		 * @since 2.3.2
		 * @var array $starter_templates_slug
		 */
		public static $starter_templates_slug = 'astra-sites';

		/**
		 * Astra Addon supported versions map array.
		 *
		 * @var array
		 * @since 4.3.0
		 */
		private static $astra_addon_supported_version_map = array(
			'4.1.6' => '4.1.0',
			'4.0.2' => '4.0.0',
			'3.9.4' => '3.9.2',
			'3.9.1' => '3.9.0',
			'3.8.5' => '3.6.11',
			'3.8.4' => '3.6.10',
			'3.8.2' => '3.6.3',
			'3.7.4' => '3.6.2',
			'3.7.3' => '3.6.0',
			'3.6.9' => '3.5.8',
			'3.6.7' => '3.5.5',
			'3.6.4' => '3.5.0',
			'3.4.8' => '3.4.2',
			'3.4.2' => '3.4.0',
			'3.3.3' => '3.3.2',
			'3.3.2' => '3.3.1',
			'3.3.1' => '3.3.0',
			'3.2.0' => '3.1.0',
			'3.0.3' => '3.0.0',
		);

		/**
		 * Constructor
		 */
		public function __construct() {

			if ( ! is_admin() ) {
				return;
			}

			self::get_starter_templates_slug();

			add_action( 'after_setup_theme', __CLASS__ . '::init_admin_settings', 99 );
		}

		/**
		 * Admin settings init
		 */
		public static function init_admin_settings() {

			add_action( 'admin_enqueue_scripts', __CLASS__ . '::register_scripts' );

			add_action( 'customize_controls_enqueue_scripts', __CLASS__ . '::customizer_scripts' );

			add_action( 'astra_notice_before_markup_astra-sites-on-active', __CLASS__ . '::load_astra_admin_script' );

			add_action( 'admin_init', __CLASS__ . '::register_notices' );
			add_action( 'astra_notice_before_markup', __CLASS__ . '::notice_assets' );

			add_action( 'admin_init', __CLASS__ . '::minimum_addon_version_notice' );
			add_action( 'admin_init', __CLASS__ . '::minimum_addon_supported_version_notice' );

			if ( astra_showcase_upgrade_notices() ) {
				add_action( 'admin_init', __CLASS__ . '::upgrade_to_pro_wc_notice' );
				add_action( 'wp_nav_menu_item_custom_fields', __CLASS__ . '::add_custom_fields', 10, 4 );
			}
		}

		/**
		 * Add custom megamenu fields data to the menu.
		 *
		 * @access public
		 * @param int    $id menu item id.
		 * @param object $item A single menu item.
		 * @param int    $depth menu item depth.
		 * @param array  $args menu item arguments.
		 * @return void
		 *
		 * @since 3.9.4
		 */
		public static function add_custom_fields( $id, $item, $depth, $args ) {
			?>
				<p class="description description-wide">
					<button class="button button-secondary button-large astra-megamenu-opts-btn" style="margin: 8px 8px 8px 0;" disabled>
						<?php echo esc_html__( 'Astra Menu Settings', 'astra' ); ?>
						<svg width="17" height="16" style="vertical-align: sub; opacity: 0.5;" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M12.5002 7.2001H11.7002V4.8001C11.7002 3.0401 10.2602 1.6001 8.5002 1.6001C6.7402 1.6001 5.3002 3.0401 5.3002 4.8001V7.2001H4.5002C4.1002 7.2001 3.7002 7.6001 3.7002 8.0001V13.6001C3.7002 14.0001 4.1002 14.4001 4.5002 14.4001H12.5002C12.9002 14.4001 13.3002 14.0001 13.3002 13.6001V8.0001C13.3002 7.6001 12.9002 7.2001 12.5002 7.2001ZM9.3002 12.8001H7.7002L8.0202 11.0401C7.6202 10.8801 7.3002 10.4001 7.3002 10.0001C7.3002 9.3601 7.8602 8.8001 8.5002 8.8001C9.1402 8.8001 9.7002 9.3601 9.7002 10.0001C9.7002 10.4801 9.4602 10.8801 8.9802 11.0401L9.3002 12.8001ZM10.1002 7.2001H6.9002V4.8001C6.9002 3.9201 7.6202 3.2001 8.5002 3.2001C9.3802 3.2001 10.1002 3.9201 10.1002 4.8001V7.2001Z" fill="#0284C7"></path> </svg>
					</button>
					<a href="<?php echo esc_url( ASTRA_PRO_UPGRADE_URL ); ?>" target="_blank" title="<?php echo esc_attr__( 'Unlock with Astra Pro', 'astra' ); ?>">
						<?php echo esc_html__( 'Unlock', 'astra' ); ?>
					</a>
				</p>
			<?php
		}

		/**
		 * Get register & enqueue astra-admin scripts.
		 *
		 * @since 3.6.6
		 */
		public static function load_astra_admin_script() {

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			wp_register_script( 'astra-admin-settings', ASTRA_THEME_URI . 'inc/assets/js/astra-admin-menu-settings.js', array( 'jquery', 'wp-util', 'updates' ), ASTRA_THEME_VERSION, false );

			$localize = array(
				'ajaxUrl'                            => admin_url( 'admin-ajax.php' ),
				'astraSitesLink'                     => admin_url( 'themes.php?page=starter-templates' ),
				'recommendedPluiginActivatingText'   => __( 'Activating', 'astra' ) . '&hellip;',
				'recommendedPluiginDeactivatingText' => __( 'Deactivating', 'astra' ) . '&hellip;',
				'recommendedPluiginActivateText'     => __( 'Activate', 'astra' ),
				'recommendedPluiginDeactivateText'   => __( 'Deactivate', 'astra' ),
				'recommendedPluiginSettingsText'     => __( 'Settings', 'astra' ),
				'astraPluginManagerNonce'            => wp_create_nonce( 'astra_plugin_manager_nonce' ),
			);
			wp_localize_script( 'astra-admin-settings', 'astra', apply_filters( 'astra_theme_js_localize', $localize ) );

			// Script.
			wp_enqueue_script( 'astra-admin-settings' );
		}

		/**
		 * Ask Theme Rating
		 *
		 * @since 1.4.0
		 */
		public static function register_notices() {
			// Return if white labeled.
			if ( astra_is_white_labelled() || false === apply_filters( 'astra_showcase_starter_templates_notice', true ) ) {
				return;
			}

			/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$current_slug = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			// Force Astra welcome notice on theme activation.
			if ( current_user_can( 'install_plugins' ) && ! defined( 'ASTRA_SITES_NAME' ) && '1' == get_option( 'fresh_site' ) && ! in_array( $current_slug, array( 'astra-advanced-hook', 'astra_adv_header' ), true ) ) {

				// Do not display admin welcome banner notice on theme upload page.
				/** @psalm-suppress InvalidGlobal */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				global $pagenow;
				/** @psalm-suppress InvalidGlobal */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

				if ( isset( $pagenow ) && 'update.php' === $pagenow ) {
					return;
				}

				$image_path                          = ASTRA_THEME_URI . 'inc/assets/images/astra-banner.png';
				$gb_image_path                       = ASTRA_THEME_URI . 'inc/assets/images/gb-logo.svg';
				$ele_image_path                      = ASTRA_THEME_URI . 'inc/assets/images/ele-logo.svg';
				$bb_image_path                       = ASTRA_THEME_URI . 'inc/assets/images/bb-logo.jpg';
				$ai_image_path                       = ASTRA_THEME_URI . 'inc/assets/images/ai-logo.svg';
				$ast_sites_notice_btn                = self::astra_sites_notice_button();
				$ast_sites_notice_btn['button_text'] = __( 'Let’s Get Started with Starter Templates', 'astra' );

				if ( file_exists( WP_PLUGIN_DIR . '/astra-sites/astra-sites.php' ) && is_plugin_inactive( 'astra-sites/astra-sites.php' ) && is_plugin_inactive( 'astra-pro-sites/astra-pro-sites.php' ) ) {
					$ast_sites_notice_btn['class'] .= ' button button-primary';
				} elseif ( ! file_exists( WP_PLUGIN_DIR . '/astra-sites/astra-sites.php' ) && is_plugin_inactive( 'astra-pro-sites/astra-pro-sites.php' ) ) {
					$ast_sites_notice_btn['class'] .= ' button button-primary';
					// Astra Premium Sites - Active.
				} else {
					$ast_sites_notice_btn['class'] = ' button button-primary astra-notice-close';
				}

				$astra_sites_notice_args = array(
					'id'                         => 'astra-sites-on-active',
					'type'                       => 'info',
					'message'                    => sprintf(
						'<div class="ast-welcome-banner">
								<div class="ast-col-left">
									<p class="sub-notice-title">%1$s</p>
									<h2 class="notice-title">%2$s</h2>
									<p class="description">%3$s</p>
									<div class="notice-actions">
										<button class="%4$s" %5$s %6$s %7$s %8$s %9$s %10$s> %11$s </button>
									</div>
									<p class="sub-notice-description astra-notice-close">%13$s</p>
								</div>
								<div class="ast-col-right">
									<img src="%12$s" alt="Starter Templates" />
									<div class="ast-st-sites-cta">
										<span>%14$s</span>
										<img src="%15$s" class="ast-page-builder-ico" />
										<img src="%16$s" class="ast-page-builder-ico" />
										<img src="%17$s" class="ast-page-builder-ico" />
										<img src="%18$s" class="ast-page-builder-ico" />
									</div>
								</div>
							</div>',
						__( 'Thank you for choosing the Astra theme!', 'astra' ),
						__( 'Build Your Dream Site in Minutes With AI 🚀', 'astra' ),
						__( 'Say goodbye to the days of spending weeks designing and building your website. With Astra and our Starter Templates plugin, you can now create professional-grade websites in minutes.', 'astra' ),
						esc_attr( $ast_sites_notice_btn['class'] ),
						'href="' . astra_get_prop( $ast_sites_notice_btn, 'link', '' ) . '"',
						'data-slug="' . astra_get_prop( $ast_sites_notice_btn, 'data_slug', '' ) . '"',
						'data-init="' . astra_get_prop( $ast_sites_notice_btn, 'data_init', '' ) . '"',
						'data-settings-link-text="' . astra_get_prop( $ast_sites_notice_btn, 'data_settings_link_text', '' ) . '"',
						'data-settings-link="' . astra_get_prop( $ast_sites_notice_btn, 'data_settings_link', '' ) . '"',
						'data-activating-text="' . astra_get_prop( $ast_sites_notice_btn, 'activating_text', '' ) . '"',
						esc_html( $ast_sites_notice_btn['button_text'] ),
						$image_path,
						__( 'I want to build this website from scratch', 'astra' ),
						__( '280+ Templates', 'astra' ),
						$gb_image_path,
						$ele_image_path,
						$bb_image_path,
						$ai_image_path,
					),
					'priority'                   => 5,
					'display-with-other-notices' => false,
					'show_if'                    => class_exists( 'Astra_Ext_White_Label_Markup' ) ? Astra_Ext_White_Label_Markup::show_branding() : true,
				);

				Astra_Notices::add_notice(
					$astra_sites_notice_args
				);
			}
		}

		/**
		 * Upgrade to Pro notice for Astra on WooCommerce pages.
		 *
		 * @since 3.9.4
		 */
		public static function upgrade_to_pro_wc_notice() {
			/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$current_slug = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			if ( '' !== $current_slug && function_exists( 'WC' ) && in_array( $current_slug, array( 'wc-admin', 'wc-reports', 'wc-status', 'wc-addons', 'wc-settings' ), true ) ) {

				$image_path = ASTRA_THEME_URI . 'inc/assets/images/astra-logo.svg';

				$astra_sites_notice_args = array(
					'id'                         => 'astra-upgrade-pro-wc',
					'type'                       => 'info',
					'message'                    => sprintf(
						'<div class="notice-image">
							<img src="%1$s" class="custom-logo" alt="Astra" itemprop="logo"></div>
							<div class="notice-content">
								<h2 class="notice-heading">
									%2$s
								</h2>
								<p>%3$s</p>
								<div class="astra-review-notice-container">
									<a class="%4$s" %5$s> %6$s </a>
								</div>
							</div>',
						$image_path,
						__( 'Astra Works Seamlessly with WooCommerce!', 'astra' ),
						__( 'Use every tool at your disposal to optimize your online store for conversion. All the advantages you need to make more profit!', 'astra' ),
						esc_attr( 'button button-primary' ),
						'href="' . ASTRA_PRO_UPGRADE_URL . '" target="_blank"',
						__( 'Upgrade Now', 'astra' )
					),
					'priority'                   => 5,
					'show_if'                    => is_admin() ? true : false,
					'display-with-other-notices' => false,
				);

				Astra_Notices::add_notice(
					$astra_sites_notice_args
				);
			}
		}

		/**
		 * Display notice for minimun version for Astra addon.
		 *
		 * @since 2.0.0
		 */
		public static function minimum_addon_version_notice() {

			if ( ! defined( 'ASTRA_EXT_VER' ) ) {
				return;
			}

			if ( version_compare( ASTRA_EXT_VER, ASTRA_EXT_MIN_VER ) < 0 ) {

				$message = sprintf(
					/* translators: %1$1s: Theme Name, %2$2s: Minimum Required version of the addon */
					__( 'Please update the %1$1s to version %2$2s or higher. Ignore if already updated.', 'astra' ),
					astra_get_addon_name(),
					ASTRA_EXT_MIN_VER
				);

				$min_version = get_user_meta( get_current_user_id(), 'ast-minimum-addon-version-notice-min-ver', true );

				if ( ! $min_version ) {
					update_user_meta( get_current_user_id(), 'ast-minimum-addon-version-notice-min-ver', ASTRA_EXT_MIN_VER );
				}

				if ( version_compare( $min_version, ASTRA_EXT_MIN_VER, '!=' ) ) {
					delete_user_meta( get_current_user_id(), 'ast-minimum-addon-version-notice' );
					update_user_meta( get_current_user_id(), 'ast-minimum-addon-version-notice-min-ver', ASTRA_EXT_MIN_VER );
				}

				$notice_args = array(
					'id'                         => 'ast-minimum-addon-version-notice',
					'type'                       => 'warning',
					'message'                    => $message,
					'show_if'                    => true,
					'repeat-notice-after'        => false,
					'priority'                   => 18,
					'display-with-other-notices' => true,
				);

				Astra_Notices::add_notice( $notice_args );
			}
		}

		/**
		 * Get minimum supported version for Astra addon.
		 * This function will be used to inform the user about incompatible version of Astra addon.
		 *
		 * @param string $input_version Input version of the addon.
		 *
		 * @since 4.3.0
		 */
		public static function get_astra_addon_min_supported_version( $input_version ) {
			if ( defined( 'ASTRA_EXT_VER' ) && version_compare( ASTRA_EXT_VER, ASTRA_EXT_MIN_VER ) < 0 ) {
				return ASTRA_EXT_MIN_VER;
			}

			$supported_version = '';

			// First, check if the exact version is supported
			if ( isset( self::$astra_addon_supported_version_map[ $input_version ] ) ) {
				$supported_version = self::$astra_addon_supported_version_map[ $input_version ];
			} else {
				foreach ( self::$astra_addon_supported_version_map as $index => $supported ) {
					/** @psalm-suppress TypeDoesNotContainType */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
					if ( '' !== $supported_version || version_compare( $input_version, $index ) > 0 ) {
						/** @psalm-suppress TypeDoesNotContainType */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
						$supported_version = $supported;
						break;
					}
				}
			}

			return $supported_version;
		}

		/**
		 * This constant will be used to inform the user about incompatible version of Astra addon.
		 *
		 * @since 4.3.0
		 */
		public static function minimum_addon_supported_version_notice() {

			if ( ! defined( 'ASTRA_EXT_VER' ) ) {
				return;
			}

			// ASTRA_EXT_MIN_VER < ASTRA_EXT_VER && ASTRA_EXT_VER < 4.0.0.
			if ( version_compare( ASTRA_EXT_VER, ASTRA_EXT_MIN_VER ) >= 0 || version_compare( '4.0.0', ASTRA_EXT_VER ) < 0 ) {
				return;
			}

			$astra_addon_supported_version = self::get_astra_addon_min_supported_version( ASTRA_EXT_VER );
			$message                       = sprintf(
				/* translators: %1$s: Plugin Name, %2$s: Theme name, %3$s: Supported required version of the addon */
				'Your current version of %1$s plugin is incompatible with %2$s theme. Please update to at least version %3$s for optimal functionality.',
				astra_get_addon_name(),
				astra_get_theme_name(),
				$astra_addon_supported_version
			);

			$ext_min_supported_version = get_user_meta( get_current_user_id(), 'ast-addon-supported-version-notice', true );

			if ( ! $ext_min_supported_version ) {
				update_user_meta( get_current_user_id(), 'ast-addon-supported-version-notice', $astra_addon_supported_version );
			}

			if ( version_compare( $ext_min_supported_version, $astra_addon_supported_version, '!=' ) ) {
				delete_user_meta( get_current_user_id(), 'ast-addon-minimum-supported-version-notice' );
				update_user_meta( get_current_user_id(), 'ast-addon-supported-version-notice', $astra_addon_supported_version );
			}

			$notice_args = array(
				'id'                         => 'ast-addon-minimum-supported-version-notice',
				'type'                       => 'warning',
				'message'                    => $message,
				'show_if'                    => true,
				'repeat-notice-after'        => false,
				'priority'                   => 20,
				'display-with-other-notices' => false,
			);

			Astra_Notices::add_notice( $notice_args );
		}

		/**
		 * Enqueue Astra Notices CSS.
		 *
		 * @since 2.0.0
		 *
		 * @return void
		 */
		public static function notice_assets() {
			if ( is_rtl() ) {
				wp_enqueue_style( 'astra-custom-notices-rtl', ASTRA_THEME_URI . 'inc/assets/css/astra-notices-rtl.css', array(), ASTRA_THEME_VERSION );
			} else {
				wp_enqueue_style( 'astra-custom-notices', ASTRA_THEME_URI . 'inc/assets/css/astra-notices.css', array(), ASTRA_THEME_VERSION );
			}
		}

		/**
		 * Render button for Astra Site notices
		 *
		 * @since 1.6.5
		 * @return array $ast_sites_notice_btn Rendered button
		 */
		public static function astra_sites_notice_button() {

			$ast_sites_notice_btn = array();

			// Any of the Starter Templtes plugin - Active.
			if ( is_plugin_active( 'astra-pro-sites/astra-pro-sites.php' ) || is_plugin_active( 'astra-sites/astra-sites.php' ) ) {
				$ast_sites_notice_btn['class'] = 'active';
				$ast_sites_notice_btn['link']  = admin_url( 'themes.php?page=' . self::$starter_templates_slug );

				return $ast_sites_notice_btn;
			}

			// Starter Templates PRO Plugin - Installed but Inactive.
			if ( file_exists( WP_PLUGIN_DIR . '/astra-pro-sites/astra-pro-sites.php' ) && is_plugin_inactive( 'astra-pro-sites/astra-pro-sites.php' ) ) {
				$ast_sites_notice_btn['class']                   = 'astra-activate-recommended-plugin';
				$ast_sites_notice_btn['data_slug']               = 'astra-pro-sites';
				$ast_sites_notice_btn['data_init']               = '/astra-pro-sites/astra-pro-sites.php';
				$ast_sites_notice_btn['data_settings_link']      = admin_url( 'themes.php?page=' . self::$starter_templates_slug );
				$ast_sites_notice_btn['data_settings_link_text'] = __( 'See Library &#187;', 'astra' );
				$ast_sites_notice_btn['activating_text']         = __( 'Activating Importer Plugin ', 'astra' ) . '&hellip;';

				return $ast_sites_notice_btn;
			}

			// Starter Templates FREE Plugin - Installed but Inactive.
			if ( file_exists( WP_PLUGIN_DIR . '/astra-sites/astra-sites.php' ) && is_plugin_inactive( 'astra-sites/astra-sites.php' ) ) {
				$ast_sites_notice_btn['class']                   = 'astra-activate-recommended-plugin';
				$ast_sites_notice_btn['data_slug']               = 'astra-sites';
				$ast_sites_notice_btn['data_init']               = '/astra-sites/astra-sites.php';
				$ast_sites_notice_btn['data_settings_link']      = admin_url( 'themes.php?page=' . self::$starter_templates_slug );
				$ast_sites_notice_btn['data_settings_link_text'] = __( 'See Library &#187;', 'astra' );
				$ast_sites_notice_btn['activating_text']         = __( 'Activating Importer Plugin ', 'astra' ) . '&hellip;';

				return $ast_sites_notice_btn;
			}

			// Any of the Starter Templates plugin not available.
			if ( ! file_exists( WP_PLUGIN_DIR . '/astra-sites/astra-sites.php' ) || ! file_exists( WP_PLUGIN_DIR . '/astra-pro-sites/astra-pro-sites.php' ) ) {
				$ast_sites_notice_btn['class']                   = 'astra-install-recommended-plugin';
				$ast_sites_notice_btn['data_slug']               = 'astra-sites';
				$ast_sites_notice_btn['data_init']               = '/astra-sites/astra-sites.php';
				$ast_sites_notice_btn['data_settings_link']      = admin_url( 'themes.php?page=' . self::$starter_templates_slug );
				$ast_sites_notice_btn['data_settings_link_text'] = __( 'See Library &#187;', 'astra' );
				$ast_sites_notice_btn['detail_link_class']       = 'plugin-detail thickbox open-plugin-details-modal astra-starter-sites-detail-link';
				$ast_sites_notice_btn['detail_link']             = network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=astra-sites&TB_iframe=true&width=772&height=400' );
				$ast_sites_notice_btn['detail_link_text']        = __( 'Details &#187;', 'astra' );

				return $ast_sites_notice_btn;
			}

			$ast_sites_notice_btn['class'] = 'active';
			$ast_sites_notice_btn['link']  = admin_url( 'themes.php?page=' . self::$starter_templates_slug );

			return $ast_sites_notice_btn;
		}

		/**
		 * Check if installed Starter Sites plugin is new.
		 *
		 * @since 2.3.2
		 */
		public static function get_starter_templates_slug() {

			if ( defined( 'ASTRA_PRO_SITES_VER' ) && version_compare( ASTRA_PRO_SITES_VER, '2.0.0', '>=' ) ) {
				self::$starter_templates_slug = 'starter-templates';
			}

			if ( defined( 'ASTRA_SITES_VER' ) && version_compare( ASTRA_SITES_VER, '2.0.0', '>=' ) ) {
				self::$starter_templates_slug = 'starter-templates';
			}
		}

		/**
		 * Load the scripts and styles in the customizer controls.
		 *
		 * @since 1.2.1
		 */
		public static function customizer_scripts() {
			$color_palettes = wp_json_encode( astra_color_palette() );
			wp_add_inline_script( 'wp-color-picker', 'jQuery.wp.wpColorPicker.prototype.options.palettes = ' . $color_palettes . ';' );
		}

		/**
		 * Register admin scripts.
		 *
		 * @param String $hook Screen name where the hook is fired.
		 * @return void
		 */
		public static function register_scripts( $hook ) {

			if ( in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
				$post_types = get_post_types( array( 'public' => true ) );
				$screen     = get_current_screen();
				$post_type  = $screen->id;

				if ( in_array( $post_type, (array) $post_types ) ) {

					echo '<style class="astra-meta-box-style">
						.block-editor-page #side-sortables #astra_settings_meta_box select { min-width: 84%; padding: 3px 24px 3px 8px; height: 20px; }
						.block-editor-page #normal-sortables #astra_settings_meta_box select { min-width: 200px; }
						.block-editor-page .edit-post-meta-boxes-area #poststuff #astra_settings_meta_box h2.hndle { border-bottom: 0; }
						.block-editor-page #astra_settings_meta_box .components-base-control__field, .block-editor-page #astra_settings_meta_box .block-editor-page .transparent-header-wrapper, .block-editor-page #astra_settings_meta_box .adv-header-wrapper, .block-editor-page #astra_settings_meta_box .stick-header-wrapper, .block-editor-page #astra_settings_meta_box .disable-section-meta div { margin-bottom: 8px; }
						.block-editor-page #astra_settings_meta_box .disable-section-meta div label { vertical-align: inherit; }
						.block-editor-page #astra_settings_meta_box .post-attributes-label-wrapper { margin-bottom: 4px; }
						#side-sortables #astra_settings_meta_box select { min-width: 100%; }
						#normal-sortables #astra_settings_meta_box select { min-width: 200px; }
					</style>';

					/**
					 * Register admin script for missing Layout option from nested Column Blocks inside Group/Cover blocks.
					 *
					 * @see https://github.com/WordPress/gutenberg/issues/33374 & https://gist.github.com/Luehrsen/c4aad3b33435058c19ea80f5f1c268e8 - Remove this once the issue is fixed.
					 *
					 * @since 3.7.9
					 */
					wp_enqueue_script( 'astra-column-block-comp-js', ASTRA_THEME_URI . 'inc/assets/js/column-block-compatibility.js', array( 'wp-util', 'wp-hooks', 'wp-blocks' ), ASTRA_THEME_VERSION, false );
				}
			}
		}

		/**
		 * Get and return page URL
		 *
		 * @param string $menu_slug Menu name.
		 * @since 1.0
		 * @return  string page url
		 */
		public static function get_page_url( $menu_slug ) {

			$parent_page = 'themes.php';

			/** @psalm-suppress InvalidLiteralArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( strpos( $parent_page, '?' ) !== false ) {
				/** @psalm-suppress InvalidLiteralArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$query_var = '&page=' . Astra_Menu::get_theme_page_slug();
			} else {
				$query_var = '?page=' . Astra_Menu::get_theme_page_slug();
			}

			$parent_page_url = admin_url( $parent_page . $query_var );

			$url = $parent_page_url . '&action=' . $menu_slug;

			return esc_url( $url );
		}
	}

	new Astra_Admin_Settings();
}
