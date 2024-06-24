<?php
/**
 * Update Compatibility
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UAGB_Update' ) ) :

	/**
	 * UAGB Update initial setup
	 *
	 * @since 1.13.4
	 */
	class UAGB_Update {

		/**
		 * Class instance.
		 *
		 * @access private
		 * @var $instance Class instance.
		 */
		private static $instance;

		/**
		 * Initiator
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
			add_action( 'admin_init', array( $this, 'init' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'in_plugin_update_message-' . UAGB_BASE, array( $this, 'plugin_update_notification' ), 10 );
		}

		/**
		 * Init
		 *
		 * @since 1.13.4
		 * @return void
		 */
		public function init() {
			// Get auto saved version number.
			$saved_version = get_option( 'uagb-version', false );

			// Update auto saved version number.
			if ( ! $saved_version || ! is_string( $saved_version ) ) {

				// Fresh install updation.
				$this->fresh_install_update_asset_generation_option();

				// Update current version.
				update_option( 'uagb-version', UAGB_VER );
				return;
			}

			do_action( 'uagb_update_before' );

			// If equals then return.
			if ( version_compare( $saved_version, UAGB_VER, '=' ) ) {
				return;
			}

			// If user is older than 2.0.0 then set the option.
			if ( version_compare( $saved_version, '2.0.0', '<' ) ) {
				update_option( 'uagb-old-user-less-than-2', 'yes' );
			}

			// Enable Legacy Blocks for users older than 2.0.5.
			if ( version_compare( $saved_version, '2.0.5', '<' ) ) {
				UAGB_Admin_Helper::update_admin_settings_option( 'uag_enable_legacy_blocks', 'yes' );
			}

			// If user is older than equal to 2.12.1 then set the option.
			if ( version_compare( $saved_version, '2.12.1', '<=' ) ) {
				UAGB_Admin_Helper::update_admin_settings_option( 'uag_enable_quick_action_sidebar', 'disabled' );
			}

			// If user is older than equal to 2.12.9 then set the option.
			if ( version_compare( $saved_version, '2.12.9', '<=' ) ) {
				UAGB_Admin_Helper::update_admin_settings_option( 'uag_enable_header_titlebar', 'disabled' );
			}

			// Create a Core Block Array for all versions in which a Core Spectra Block was added.
			$core_blocks   = array();
			$blocks_status = UAGB_Admin_Helper::get_admin_settings_option( '_uagb_blocks' );

			// If Block Statuses exists and is not empty, enable the required Core Spectra Blocks.
			if ( is_array( $blocks_status ) && ! empty( $blocks_status ) ) {

				// If user is older than 2.0.16 then enable all the Core Spectra Blocks, as we have removed option to disable core blocks from 2.0.16.
				if ( version_compare( $saved_version, '2.0.16', '<' ) ) {
					array_push(
						$core_blocks,
						'container',
						'advanced-heading',
						'image',
						'buttons',
						'info-box',
						'call-to-action'
					);
				}

				// If user is older than 2.4.0 then enable the Icon Block that was added to the Core Blocks in this release.
				if ( version_compare( $saved_version, '2.4.0', '<' ) ) {
					array_push(
						$core_blocks,
						'icon'
					);
				}

				// If user is older than 2.6.0 then enable the Countdown Block that was added to the Core Blocks in this release.
				if ( version_compare( $saved_version, '2.6.0', '<' ) ) {
					array_push(
						$core_blocks,
						'countdown'
					);
				}

				// If user is older than 2.12.3 then enable the popup-builder Block that was added to the Core Blocks in this release.
				if ( version_compare( $saved_version, '2.12.3', '<' ) ) {
					array_push(
						$core_blocks,
						'popup-builder'
					);
				}
			}

			$inherit_from_theme = UAGB_Admin_Helper::get_admin_settings_option( 'uag_btn_inherit_from_theme' );
			// If user is older than 2.13.4 and Inherit from theme is enabled update the fallback.
			if ( version_compare( $saved_version, '2.13.4', '<' ) && 'enabled' === $inherit_from_theme ) {
				UAGB_Admin_Helper::update_admin_settings_option( 'uag_btn_inherit_from_theme_fallback', 'disabled' );
			}

			// If the core block array is not empty, update the enabled blocks option.
			if ( ! empty( $core_blocks ) ) {

				foreach ( $core_blocks as $block ) {
					$blocks_status[ $block ] = $block;
				}

				UAGB_Admin_Helper::update_admin_settings_option( '_uagb_blocks', $blocks_status );
			}

			// Create file if not present.
			uagb_install()->create_files();

			/* Create activated blocks stylesheet */
			UAGB_Admin_Helper::create_specific_stylesheet();

			// Update asset version number.
			update_option( '__uagb_asset_version', time() );

			// Update auto saved version number.
			update_option( 'uagb-version', UAGB_VER );

			do_action( 'uagb_update_after' );
		}


		/**
		 * Migrate_visibility_mode
		 *
		 * @since 2.8.0
		 * @return void
		 */
		public static function migrate_visibility_mode() {

			$old_option      = UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_coming_soon_mode' );
			$old_option_page = UAGB_Admin_Helper::get_admin_settings_option( 'uag_coming_soon_page' );

			if ( ! $old_option && ! $old_option_page ) {
				return;
			}

			// Update the option.
			UAGB_Admin_Helper::update_admin_settings_option( 'uag_visibility_mode', $old_option ? $old_option : 'disabled' );
			UAGB_Admin_Helper::update_admin_settings_option( 'uag_visibility_page', $old_option_page ? $old_option_page : '' );

			// Delete the old option.
			UAGB_Admin_Helper::delete_admin_settings_option( 'uag_enable_coming_soon_mode' );
			UAGB_Admin_Helper::delete_admin_settings_option( 'uag_coming_soon_page' );
		}

		/**
		 * Update asset generation option if it is not exist.
		 *
		 * @since 1.22.4
		 * @return void
		 */
		public function fresh_install_update_asset_generation_option() {

			uagb_install()->create_files();

			if ( UAGB_Helper::is_uag_dir_has_write_permissions() ) {
				update_option( '_uagb_allow_file_generation', 'enabled' );
			}
		}

		/**
		 * Plugin update notification.
		 *
		 * @param array $data Plugin update data.
		 * @since 2.7.2
		 * @return void
		 */
		public function plugin_update_notification( $data ) {
			if ( ! empty( $data['upgrade_notice'] ) ) { ?>
				<hr class="uagb-plugin-update-notification__separator" />
				<div class="uagb-plugin-update-notification">
					<div class="uagb-plugin-update-notification__icon">
						<span class="dashicons dashicons-info"></span>
					</div>
					<div>
						<div class="uagb-plugin-update-notification__title">
							<?php echo esc_html__( 'Heads up!', 'ultimate-addons-for-gutenberg' ); ?>
						</div>
						<div class="uagb-plugin-update-notification__message">
							<?php
								printf(
									wp_kses(
										$data['upgrade_notice'],
										array( 'a' => array( 'href' => array() ) )
									)
								);
							?>
						</div>
					</div>
				</div>
				<?php
			} //end if
		}

		/**
		 * Enqueue styles.
		 *
		 * @since 2.7.2
		 * @return void
		 */
		public function enqueue_styles() {
			$screen = get_current_screen();
			if ( empty( $screen->id ) || 'plugins' !== $screen->id ) {
				return;
			}
			wp_enqueue_style( 'uagb-update-notice', UAGB_URL . 'admin/assets/css/update-notice.css', array(), UAGB_VER );
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	UAGB_Update::get_instance();

endif;
