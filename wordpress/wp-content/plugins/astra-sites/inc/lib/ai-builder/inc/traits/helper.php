<?php
/**
 * Trait.
 *
 * @package {{package}}
 * @since 0.0.1
 */

namespace AiBuilder\Inc\Traits;

use AiBuilder\Inc\Traits\Instance;
use STImporter\Resetter\ST_Resetter;
use STImporter\Importer\ST_Importer;
use AiBuilder\Inc\Classes\Importer\Ai_Builder_Error_Handler;
use STImporter\Importer\ST_Importer_File_System;
use AiBuilder\Inc\Classes\Ai_Builder_Importer_Log;
use STImporter\Importer\ST_Option_Importer;

/**
 * Trait Instance.
 */
class Helper {

	use Instance;

	/**
	 * Get an option from the database.
	 *
	 * @param string  $key              The option key.
	 * @param mixed   $default          The option default value if option is not available.
	 * @param boolean $network_override Whether to allow the network admin setting to be overridden on subsites.
	 * @since 1.0.0
	 * @return mixed  The option value.
	 */
	public static function get_admin_settings_option( $key, $default = false, $network_override = false ) {
		// Get the site-wide option if we're in the network admin.
		return $network_override && is_multisite() ? get_site_option( $key, $default ) : get_option( $key, $default );
	}

	/**
	 * Delete an option from the database for.
	 *
	 * @param string  $key              The option key.
	 * @param boolean $network_override Whether to allow the network admin setting to be overridden on subsites.
	 * @since 1.0.0
	 * @return void
	 */
	public static function delete_admin_settings_option( $key, $network_override = false ) {
		// Delete the site-wide option if we're in the network admin.
		if ( $network_override && is_multisite() ) {
			delete_site_option( $key );
		} else {
			delete_option( $key );
		}
	}

	/**
	 * Get image placeholder array.
	 *
	 * @since 4.0.9
	 * @return array<string, array<string, string>>
	 */
	public static function get_image_placeholders() {

		return array(
			array(
				'auther_name'   => 'Placeholder',
				'id'            => 'placeholder-landscape',
				'orientation'   => 'landscape',
				'optimized_url' => 'https://websitedemos.net/wp-content/uploads/2024/02/placeholder-landscape.png',
				'url'           => 'https://websitedemos.net/wp-content/uploads/2024/02/placeholder-landscape.png',
			),
			array(
				'auther_name'   => 'Placeholder',
				'id'            => 'placeholder-portrait',
				'orientation'   => 'portrait',
				'optimized_url' => 'https://websitedemos.net/wp-content/uploads/2024/02/placeholder-portrait.png',
				'url'           => 'https://websitedemos.net/wp-content/uploads/2024/02/placeholder-portrait.png',
			),
		);
	}

	/**
	 * Get Saved Token.
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public static function get_token() {
		$token_details = get_option(
			'zip_ai_settings',
			array(
				'auth_token' => '',
				'zip_token'  => '',
				'email'      => '',
			)
		);
		return isset( $token_details['zip_token'] ) ? self::decrypt( $token_details['zip_token'] ) : '';
	}

		/**
		 * Decrypt data using base64.
		 *
		 * @param string $input The input string which needs to be decrypted.
		 * @since 4.0.0
		 * @return string The decrypted string.
		 */
	public static function decrypt( $input ) {
		// If the input is empty or not a string, then abandon ship.
		if ( empty( $input ) || ! is_string( $input ) ) {
			return '';
		}

		// Decrypt the input and return it.
		$base_64 = $input . str_repeat( '=', strlen( $input ) % 4 );
		$decode  = base64_decode( $base_64 ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		return $decode;
	}

	/**
	 * Get installed PHP version.
	 *
	 * @return float PHP version.
	 * @since 3.0.16
	 */
	public static function get_php_version() {
		if ( defined( 'PHP_MAJOR_VERSION' ) && defined( 'PHP_MINOR_VERSION' ) && defined( 'PHP_RELEASE_VERSION' ) ) { // phpcs:ignore
			return PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION;
		}

		return phpversion();
	}

	/**
	 * Has Pro Version Support?
	 * And
	 * Is Pro Version Installed?
	 *
	 * Check Pro plugin version exist of requested plugin lite version.
	 *
	 * Eg. If plugin 'BB Lite Version' required to import demo. Then we check the 'BB Agency Version' is exist?
	 * If yes then we only 'Activate' Agency Version. [We couldn't install agency version.]
	 * Else we 'Activate' or 'Install' Lite Version.
	 *
	 * @since 1.0.1
	 *
	 * @param  string $lite_version Lite version init file.
	 * @return mixed               Return false if not installed or not supported by us
	 *                                    else return 'Pro' version details.
	 */
	public static function pro_plugin_exist( $lite_version = '' ) {

		// Lite init => Pro init.
		$plugins = apply_filters(
			'astra_sites_pro_plugin_exist',
			array(
				'beaver-builder-lite-version/fl-builder.php' => array(
					'slug' => 'bb-plugin',
					'init' => 'bb-plugin/fl-builder.php',
					'name' => 'Beaver Builder Plugin',
				),
				'ultimate-addons-for-beaver-builder-lite/bb-ultimate-addon.php' => array(
					'slug' => 'bb-ultimate-addon',
					'init' => 'bb-ultimate-addon/bb-ultimate-addon.php',
					'name' => 'Ultimate Addon for Beaver Builder',
				),
				'wpforms-lite/wpforms.php' => array(
					'slug' => 'wpforms',
					'init' => 'wpforms/wpforms.php',
					'name' => 'WPForms',
				),
			),
			$lite_version
		);

		if ( isset( $plugins[ $lite_version ] ) ) {

			// Pro plugin directory exist?
			if ( file_exists( WP_PLUGIN_DIR . '/' . $plugins[ $lite_version ]['init'] ) ) {
				return $plugins[ $lite_version ];
			}
		}

		return false;
	}

	/**
	 * Get the status of file system permission of "/wp-content/uploads" directory.
	 *
	 * @return void
	 */
	public static function filesystem_permission() {
		if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'customize' ) ) {
				wp_send_json_error( __( 'You do not have permission to perform this action.', 'ai-builder', 'astra-sites' ) );
			}
		}
		$wp_upload_path = wp_upload_dir();
		$permissions    = array(
			'is_readable' => false,
			'is_writable' => false,
		);

		foreach ( $permissions as $file_permission => $value ) {
			$permissions[ $file_permission ] = $file_permission( $wp_upload_path['basedir'] );
		}

		$permissions['is_wp_filesystem'] = true;
		if ( ! WP_Filesystem() ) {
			$permissions['is_wp_filesystem'] = false;
		}

		if ( defined( 'WP_CLI' ) ) {
			if ( ! $permissions['is_readable'] || ! $permissions['is_writable'] || ! $permissions['is_wp_filesystem'] ) {
				\WP_CLI::error( esc_html__( 'Please contact the hosting service provider to help you update the permissions so that you can successfully import a complete template.', 'ai-builder', 'astra-sites' ) );
			}
		} else {
			wp_send_json_success(
				array(
					'permissions' => $permissions,
					'directory'   => $wp_upload_path['basedir'],
				)
			);
		}
	}

	/**
	 * Required Plugins
	 *
	 * @since 2.0.0
	 *
	 * @param  array $required_plugins Required Plugins.
	 * @param  array $options            Site Options.
	 * @param  array $enabled_extensions Enabled Extensions.
	 * @return mixed
	 */
	public static function required_plugins( $required_plugins = array(), $options = array(), $enabled_extensions = array() ) {

		// Verify Nonce.
		if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );
			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error(
					array(
						'error' => __( 'Permission Denied!', 'ai-builder', 'astra-sites' ),
					)
				);
			}
		}

		$response = array(
			'active'       => array(),
			'inactive'     => array(),
			'notinstalled' => array(),
		);

		$required_plugins = astra_get_site_data( 'required-plugins' );

		$data = self::get_required_plugins_data( $response, $required_plugins );

		if ( wp_doing_ajax() ) {
			wp_send_json_success( $data );
		} else {
			return $data;
		}
	}

		/**
		 * Retrieves the required plugins data based on the response and required plugin list.
		 *
		 * @param array $response            The response containing the plugin data.
		 * @param array $required_plugins    The list of required plugins.
		 * @since 3.2.5
		 * @return array                     The array of required plugins data.
		 */
	public static function get_required_plugins_data( $response, $required_plugins ) {

		$learndash_course_grid = 'https://www.learndash.com/add-on/course-grid/';
		$learndash_woocommerce = 'https://www.learndash.com/add-on/woocommerce/';
		if ( is_plugin_active( 'sfwd-lms/sfwd_lms.php' ) ) {
			$learndash_addons_url  = admin_url( 'admin.php?page=learndash_lms_addons' );
			$learndash_course_grid = $learndash_addons_url;
			$learndash_woocommerce = $learndash_addons_url;
		}

		$third_party_required_plugins = array();
		$third_party_plugins          = array(
			'sfwd-lms'              => array(
				'init' => 'sfwd-lms/sfwd_lms.php',
				'name' => 'LearnDash LMS',
				'link' => 'https://www.learndash.com/',
			),
			'learndash-course-grid' => array(
				'init' => 'learndash-course-grid/learndash_course_grid.php',
				'name' => 'LearnDash Course Grid',
				'link' => $learndash_course_grid,
			),
			'learndash-woocommerce' => array(
				'init' => 'learndash-woocommerce/learndash_woocommerce.php',
				'name' => 'LearnDash WooCommerce Integration',
				'link' => $learndash_woocommerce,
			),
		);

		$plugin_updates          = get_plugin_updates();
		$update_avilable_plugins = array();
		$incompatible_plugins    = array();

		if ( ! empty( $required_plugins ) ) {
			$php_version = Helper::get_php_version();
			foreach ( $required_plugins as $key => $plugin ) {

				$plugin = (array) $plugin;

				if ( 'woocommerce' === $plugin['slug'] && version_compare( $php_version, '7.0', '<' ) ) {
					$plugin['min_php_version'] = '7.0';
					$incompatible_plugins[]    = $plugin;
				}

				if ( 'presto-player' === $plugin['slug'] && version_compare( $php_version, '7.3', '<' ) ) {
					$plugin['min_php_version'] = '7.3';
					$incompatible_plugins[]    = $plugin;
				}

				/**
				 * Has Pro Version Support?
				 * And
				 * Is Pro Version Installed?
				 */
				$plugin_pro = Helper::pro_plugin_exist( $plugin['init'] );
				if ( $plugin_pro ) {

					if ( array_key_exists( $plugin_pro['init'], $plugin_updates ) ) {
						$update_avilable_plugins[] = $plugin_pro;
					}

					// Pro - Active.
					if ( is_plugin_active( $plugin_pro['init'] ) ) {
						$response['active'][] = $plugin_pro;

						self::after_plugin_activate( $plugin['init'] );

						// Pro - Inactive.
					} else {
						$response['inactive'][] = $plugin_pro;
					}
				} else {
					if ( array_key_exists( $plugin['init'], $plugin_updates ) ) {
						$update_avilable_plugins[] = $plugin;
					}

					// Lite - Installed but Inactive.
					if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin['init'] ) && is_plugin_inactive( $plugin['init'] ) ) {
						$link                   = wp_nonce_url(
							add_query_arg(
								array(
									'action' => 'activate',
									'plugin' => $plugin['init'],
								),
								admin_url( 'plugins.php' )
							),
							'activate-plugin_' . $plugin['init']
						);
						$link                   = str_replace( '&amp;', '&', $link );
						$plugin['action']       = $link;
						$response['inactive'][] = $plugin;

						// Lite - Not Installed.
					} elseif ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin['init'] ) ) {

						// Added premium plugins which need to install first.
						if ( array_key_exists( $plugin['slug'], $third_party_plugins ) ) {
							$third_party_required_plugins[] = $third_party_plugins[ $plugin['slug'] ];
						} else {
							$link                       = wp_nonce_url(
								add_query_arg(
									array(
										'action' => 'install-plugin',
										'plugin' => $plugin['slug'],
									),
									admin_url( 'update.php' )
								),
								'install-plugin_' . $plugin['slug']
							);
							$link                       = str_replace( '&amp;', '&', $link );
							$plugin['action']           = $link;
							$response['notinstalled'][] = $plugin;
						}

						// Lite - Active.
					} else {
						$response['active'][] = $plugin;

						self::after_plugin_activate( $plugin['init'] );
					}
				}
			}
		}

		// Checking the `install_plugins` and `activate_plugins` capability for the current user.
		// To perform plugin installation process.
		if (
			( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) &&
			( ( ! current_user_can( 'install_plugins' ) && ! empty( $response['notinstalled'] ) ) || ( ! current_user_can( 'activate_plugins' ) && ! empty( $response['inactive'] ) ) ) ) {
			$message               = __( 'Insufficient Permission. Please contact your Super Admin to allow the install required plugin permissions.', 'ai-builder', 'astra-sites' );
			$required_plugins_list = array_merge( $response['notinstalled'], $response['inactive'] );
			$markup                = $message;
			$markup               .= '<ul>';
			foreach ( $required_plugins_list as $key => $required_plugin ) {
				$markup .= '<li>' . esc_html( $required_plugin['name'] ) . '</li>';
			}
			$markup .= '</ul>';

			wp_send_json_error( $markup );
		}

		$data = array(
			'required_plugins'             => $response,
			'third_party_required_plugins' => $third_party_required_plugins,
			'update_avilable_plugins'      => $update_avilable_plugins,
			'incompatible_plugins'         => $incompatible_plugins,
		);

		return $data;
	}

		/**
		 * After Plugin Activate
		 *
		 * @since 2.0.0
		 *
		 * @param  string $plugin_init        Plugin Init File.
		 * @param  array  $options            Site Options.
		 * @param  array  $enabled_extensions Enabled Extensions.
		 * @return void
		 */
	public static function after_plugin_activate( $plugin_init = '', $options = array(), $enabled_extensions = array() ) {
		$data = array(
			'astra_site_options' => $options,
			'enabled_extensions' => $enabled_extensions,
		);

		do_action( 'astra_sites_after_plugin_activation', $plugin_init, $data );
	}

	/**
	 * Required Plugin Activate
	 *
	 * @since 2.0.0 Added parameters $init, $options & $enabled_extensions to add the WP CLI support.
	 * @since 1.0.0
	 * @param  string $init               Plugin init file.
	 * @param  array  $options            Site options.
	 * @param  array  $enabled_extensions Enabled extensions.
	 * @return void
	 */
	public static function required_plugin_activate( $init = '', $options = array(), $enabled_extensions = array() ) {

		if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'install_plugins' ) || ! isset( $_POST['init'] ) || ! sanitize_text_field( $_POST['init'] ) ) {
				wp_send_json_error(
					array(
						'success' => false,
						'message' => __( 'Error: You don\'t have the required permissions to install plugins.', 'ai-builder', 'astra-sites' ),
					)
				);
			}
		}

		Ai_Builder_Error_Handler::Instance()->start_error_handler();

		$plugin_init = ( isset( $_POST['init'] ) ) ? esc_attr( sanitize_text_field( $_POST['init'] ) ) : $init;

		/**
		 * Disabled redirection to plugin page after activation.
		 * Silecing the callback for WP Live Chat plugin.
		 */
		add_filter( 'wp_redirect', '__return_false' );
		$silent = ( 'wp-live-chat-support/wp-live-chat-support.php' === $plugin_init ) ? true : false;

		$activate = activate_plugin( $plugin_init, '', false, $silent );

		Ai_Builder_Error_Handler::Instance()->stop_error_handler();

		if ( is_wp_error( $activate ) ) {
			if ( defined( 'WP_CLI' ) ) {
				\WP_CLI::error( 'Plugin Activation Error: ' . $activate->get_error_message() );
			} elseif ( wp_doing_ajax() ) {
				wp_send_json_error(
					array(
						'success' => false,
						'message' => $activate->get_error_message(),
					)
				);
			}
		}

		$options            = astra_get_site_data( 'astra-site-options-data' );
		$enabled_extensions = astra_get_site_data( 'astra-enabled-extensions' );

		self::after_plugin_activate( $plugin_init, $options, $enabled_extensions );

		if ( defined( 'WP_CLI' ) ) {
			\WP_CLI::line( 'Plugin Activated!' );
		} elseif ( wp_doing_ajax() ) {
			wp_send_json_success(
				array(
					'success' => true,
					'message' => __( 'Plugin Activated', 'ai-builder', 'astra-sites' ),
				)
			);
		}
	}

	/**
	 * Backup our existing settings.
	 */
	public static function backup_settings() {

		if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( __( 'User does not have permission!', 'ai-builder', 'astra-sites' ) );
			}
		}

		$log_file_path = ST_Resetter::backup_settings();

		if ( defined( 'WP_CLI' ) ) {
			\WP_CLI::line( 'File generated at ' . $log_file_path );
		} elseif ( wp_doing_ajax() ) {
			wp_send_json_success();
		}
	}

	/**
	 * Import Options.
	 *
	 * @since 1.0.14
	 * @since 1.4.0 The `$options_data` was added.
	 *
	 * @param  array $options_data Site Options.
	 * @return void
	 */
	public static function import_options( $options_data = array() ) {

		if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'customize' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'ai-builder', 'astra-sites' ) );
			}
		}
		if ( empty( $options_data ) ) {
			$options_data = astra_get_site_data( 'astra-site-options-data' );
		}

		$result = ST_Importer::import_options( $options_data, ST_Option_Importer::site_options() );

		if ( false === $result['status'] ) {
			if ( defined( 'WP_CLI' ) ) {
				\WP_CLI::line( $result['error'] );
			} elseif ( wp_doing_ajax() ) {
				wp_send_json_error( $result['error'] );
			}
		}

		if ( defined( 'WP_CLI' ) ) {
			\WP_CLI::line( 'Site options Imported!' );
		} elseif ( wp_doing_ajax() ) {
			wp_send_json_success( __( 'Site options Imported!', 'ai-builder', 'astra-sites' ) );
		}
	}

	/**
	 * Import Widgets.
	 *
	 * @since 1.0.14
	 * @since 1.4.0 The `$widgets_data` was added.
	 *
	 * @param  string $widgets_data Widgets Data.
	 * @return void
	 */
	public static function import_widgets( $widgets_data = '' ) {

		if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'customize' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'ai-builder', 'astra-sites' ) );
			}
		}

		$data = astra_get_site_data( 'astra-site-widgets-data' );

		$result = ST_Importer::import_widgets( $widgets_data, $data );

		if ( false === $result['status'] ) {
			if ( defined( 'WP_CLI' ) ) {
				\WP_CLI::line( $result['error'] );
			} elseif ( wp_doing_ajax() ) {
				wp_send_json_error( $result['error'] );
			}
		} else {
			if ( defined( 'WP_CLI' ) ) {
				\WP_CLI::line( 'Widget Imported!' );
			} elseif ( wp_doing_ajax() ) {
				wp_send_json_success( 'Widget Imported!' );
			}
		}
	}

	/**
	 * Import End.
	 *
	 * @since 1.0.14
	 * @return void
	 */
	public static function import_end() {

		if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'customize' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'ai-builder', 'astra-sites' ) );
			}
		}

		$demo_data = ST_Importer_File_System::get_instance()->get_demo_content();
		// Set permalink structure to use post name.
		update_option( 'permalink_structure', '/%postname%/' );
		update_option( 'astra-site-permalink-update-status', 'no' );

		do_action( 'astra_sites_import_complete', $demo_data );

		if ( wp_doing_ajax() ) {
			wp_send_json_success();
		}
	}

	/**
	 * Reset customizer data
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public static function reset_customizer_data() {

		if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'customize' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'ai-builder', 'astra-sites' ) );
			}
		}

		Ai_Builder_Importer_Log::add( 'Deleted customizer Settings ' . wp_json_encode( get_option( 'astra-settings', array() ) ) );

		ST_Resetter::reset_customizer_data();

		if ( defined( 'WP_CLI' ) ) {
			\WP_CLI::line( 'Deleted Customizer Settings!' );
		} elseif ( wp_doing_ajax() ) {
			wp_send_json_success();
		}
	}

	/**
	 * Reset site options
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public static function reset_site_options() {

		if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'customize' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'ai-builder', 'astra-sites' ) );
			}
		}

		$options = get_option( '_astra_sites_old_site_options', array() );

		Ai_Builder_Importer_Log::add( 'Deleted - Site Options ' . wp_json_encode( $options ) );

		ST_Resetter::reset_site_options( $options );

		if ( defined( 'WP_CLI' ) ) {
			\WP_CLI::line( 'Deleted Site Options!' );
		} elseif ( wp_doing_ajax() ) {
			wp_send_json_success();
		}
	}

	/**
	 * Reset widgets data
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public static function reset_widgets_data() {

		if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'customize' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'ai-builder', 'astra-sites' ) );
			}
		}

		// Get all old widget ids.
		$old_widgets_data = (array) get_option( '_astra_sites_old_widgets_data', array() );

		ST_Resetter::reset_widgets_data( $old_widgets_data );

		if ( defined( 'WP_CLI' ) ) {
			\WP_CLI::line( 'Deleted Widgets!' );
		} elseif ( wp_doing_ajax() ) {
			wp_send_json_success( __( 'Deleted Widgets!', 'ai-builder', 'astra-sites' ) );
		}
	}

	/**
	 * Import Customizer Settings.
	 *
	 * @since 1.0.14
	 * @since 1.4.0  The `$customizer_data` was added.
	 *
	 * @param  array $customizer_data Customizer Data.
	 * @return void
	 */
	public static function import_customizer_settings( $customizer_data = array() ) {

		if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'customize' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'ai-builder', 'astra-sites' ) );
			}
		}

		if ( empty( $customizer_data ) ) {
			$customizer_data = astra_get_site_data( 'astra-site-customizer-data' );
		}

		if ( defined( 'WP_CLI' ) && empty( $customizer_data ) ) {
			\WP_CLI::line( 'Customizer data is empty!' );
		} elseif ( wp_doing_ajax() && empty( $customizer_data ) ) {
			wp_send_json_error( __( 'Customizer data is empty!', 'ai-builder', 'astra-sites' ) );
		}

		$result = ST_Importer::import_customizer_settings( $customizer_data );

		if ( false === $result['status'] ) {
			wp_send_json_error( $result['error'] );
		}

		wp_send_json_success();
	}
}

