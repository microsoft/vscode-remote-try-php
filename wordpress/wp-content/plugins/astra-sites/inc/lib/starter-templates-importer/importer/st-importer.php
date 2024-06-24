<?php
/**
 * Starter Templates Importer - Module.
 *
 * This file is used to register and manage the Zip AI Modules.
 *
 * @package Starter Templates Importer
 */

namespace STImporter\Importer;

use STImporter\Importer\WXR_Importer\ST_WXR_Importer;
use STImporter\Importer\ST_Widget_Importer;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * The Module Class.
 */
class ST_Importer {

	/**
	 * Instance of this class.
	 *
	 * @since 1.0.0
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Initiator of this class.
	 *
	 * @since 1.0.0
	 * @return self initialized object of this class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initiate import process flog.
	 *
	 * @since 1.0.0
	 * @param string $template_type template type.
	 * @param string $uuid uuid.
	 * @return array
	 */
	public static function set_import_process_start_flag( $template_type, $uuid = '' ) {

		if ( empty( $uuid ) && 'ai' === $template_type ) {
			return array(
				'status' => false,
				'error'  => __( 'uuid is empty.', 'st-importer', 'astra-sites' ),
			);
		}

		if ( ! empty( $uuid ) ) {
			update_option( 'astra_sites_ai_import_started', 'yes', 'no' );
		}
		do_action( 'st_before_start_import_process' );
		set_transient( 'astra_sites_import_started', 'yes', HOUR_IN_SECONDS );

		return array(
			'status' => true,
			'error'  => __( 'Import process start flof set successfully.', 'st-importer', 'astra-sites' ),
		);
	}

	/**
	 * Import Spectra Settings
	 *
	 * @since 1.0.0
	 * @param array $settings spectra settings.
	 *
	 * @return array
	 */
	public static function import_spectra_settings( $settings = array() ) {

		if ( ! is_callable( 'UAGB_Admin_Helper::get_instance' ) ) {
			return array(
				'status' => false,
				'error'  => __( 'Can\'t import Spectra Settings. Spectra Plugin is not activated.', 'st-importer', 'astra-sites' ),
			);
		}

		if ( empty( $settings ) ) {
			return array(
				'status' => false,
				'error'  => __( 'Spectra settings are empty.', 'st-importer', 'astra-sites' ),
			);
		}

		\UAGB_Admin_Helper::get_instance()->update_admin_settings_shareable_data( $settings );

		return array(
			'status'  => true,
			'message' => __( 'Spectra settings imported successfully.', 'st-importer', 'astra-sites' ),
		);

	}

	/**
	 * Import Surecart Settings
	 *
	 * @since 1.0.0
	 *
	 * @param int $id id.
	 * @return mixed
	 */
	public static function import_surecart_settings( $id = 0 ) {

		$id = ! empty( $id ) ? base64_decode( sanitize_text_field( $id ) ) : ''; // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

		if ( empty( $id ) ) {
			return array(
				'status' => false,
				'error'  => __( 'Id is empty.', 'st-importer', 'astra-sites' ),
			);
		}

		if ( ! is_callable( 'SureCart\Models\ProvisionalAccount::create' ) ) {
			return array(
				'status' => false,
				'error'  => __( 'SureCart\Models\ProvisionalAccount::create function is not callable.', 'st-importer', 'astra-sites' ),
			);
		}

		$currency = isset( $_POST['source_currency'] ) ? sanitize_text_field( $_POST['source_currency'] ) : 'usd'; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$token    = \SureCart\Models\ApiToken::get();
		if ( ! empty( $token ) ) {
			\SureCart\Models\ApiToken::clear();
		}
		return \SureCart\Models\ProvisionalAccount::create(
			array(
				'account_currency'  => $currency, // It will default to USD.
				'account_name'      => '', // if you do not pass this it will default to the site name.
				'account_url'       => '', // if you do not pass this it will default to the site url.
				'email'             => '', // optional.
				'source_account_id' => $id,
				'seed'              => true,
			)
		);

	}

	/**
	 * Import Customizer Settings.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $customizer_data Customizer Data.
	 * @return array
	 */
	public static function import_customizer_settings( $customizer_data = array() ) {

		if ( empty( $customizer_data ) ) {
			return array(
				'status' => false,
				'error'  => __( 'Customizer data is empty.', 'st-importer', 'astra-sites' ),
			);
		}

		if ( ! empty( $customizer_data ) ) {
			update_option( '_astra_sites_old_customizer_data', $customizer_data, 'no' );

			// Update Astra Theme customizer settings.
			if ( isset( $customizer_data['astra-settings'] ) ) {
				update_option( 'astra-settings', $customizer_data['astra-settings'] );
			}

			// Add Custom CSS.
			if ( isset( $customizer_data['custom-css'] ) ) {
				wp_update_custom_css_post( $customizer_data['custom-css'] );
			}

			return array(
				'status'  => true,
				'message' => __( 'Customizer data imported successfully.', 'st-importer', 'astra-sites' ),
			);
		}
	}

	/**
	 * Prepare XML Data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $wxr_url url.
	 * @return array
	 */
	public static function prepare_xml_data( $wxr_url ) {

		if ( ! ST_WXR_Importer::is_valid_wxr_url( $wxr_url ) ) {
			return array(
				'status' => false,
				/* Translators: %s is WXR URL. */
				'error'  => sprintf( __( 'Invalid WXR Request URL - %s', 'st-importer', 'astra-sites' ), $wxr_url ),
			);
		}

		$overrides = array(
			'wp_handle_sideload' => 'upload',
		);

		// Download XML file.
		$xml_path = ST_WXR_Importer::download_file( $wxr_url, $overrides );

		if ( $xml_path['success'] ) {

			$post = array(
				'post_title'     => basename( $wxr_url ),
				'guid'           => $xml_path['data']['url'],
				'post_mime_type' => $xml_path['data']['type'],
			);

			// As per wp-admin/includes/upload.php.
			$post_id = wp_insert_attachment( $post, $xml_path['data']['file'] );

			if ( is_wp_error( $post_id ) ) {
				return array(
					'status' => false,
					'error'  => __( 'There was an error downloading the XML file.', 'st-importer', 'astra-sites' ),
				);
			} else {
				update_option( 'astra_sites_imported_wxr_id', $post_id, 'no' );
				$attachment_metadata = wp_generate_attachment_metadata( $post_id, $xml_path['data']['file'] );
				wp_update_attachment_metadata( $post_id, $attachment_metadata );
				$data        = ST_WXR_Importer::get_xml_data( $xml_path['data']['file'], $post_id );
				$data['xml'] = $xml_path['data'];
				return array(
					'status' => true,
					'data'   => $data,
				);
			}
		} else {
			return array(
				'status' => false,
				'error'  => $xml_path['data'],
			);
		}
	}

	/**
	 * Import site options.
	 *
	 * @since  1.0.0
	 *
	 * @param array $options Array of options to be imported from the demo.
	 * @param array $site_options Array of site options to be imported from the demo.
	 *
	 * @return array
	 */
	public static function import_options( $options = array(), $site_options = array() ) {

		if ( empty( $options ) ) {
			return array(
				'status' => false,
				'error'  => __( 'Site options are empty!', 'st-importer', 'astra-sites' ),
			);
		}

		if ( ! empty( $options ) ) {
			// Set meta for tracking the post.
			if ( is_array( $options ) ) {
				update_option( '_astra_sites_old_site_options', $options, 'no' );
			}

			try {
				foreach ( $options as $option_name => $option_value ) {

					// Is option exist in defined array site_options()?
					if ( null !== $option_value ) {

						// Is option exist in defined array site_options()?
						if ( in_array( $option_name, $site_options, true ) ) {

							switch ( $option_name ) {
								case 'page_for_posts':
								case 'page_on_front':
										ST_Option_Importer::update_page_id_by_option_value( $option_name, $option_value );
									break;

								// nav menu locations.
								case 'nav_menu_locations':
										ST_Option_Importer::set_nav_menu_locations( $option_value );
									break;

								// insert logo.
								case 'custom_logo':
										ST_Option_Importer::insert_logo( $option_value );
									break;

								case 'site_title':
									update_option( 'blogname', $option_value );
									break;

								default:
									update_option( $option_name, $option_value );
									break;
							}
						}
					}
				}

				do_action( 'st_importer_import_site_options', $options, $site_options );

				return array(
					'status'  => true,
					'message' => __( 'Options imported successfully.', 'st-importer', 'astra-sites' ),
				);
			} catch ( \Exception $e ) {
				return array(
					'status' => false,
					'error'  => $e,
				);
			}
		}
	}

	/**
	 * Import Widgets.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $widgets_data Widgets Data.
	 * @param  string $data Widgets Data.
	 * @return array
	 */
	public static function import_widgets( $widgets_data, $data = '' ) {

		if ( isset( $data ) && is_object( $data ) ) {
			// $data is set and is an object.
			$widgets_data = $data;
		} elseif ( isset( $data ) && is_string( $data ) ) {
			// $data is set but is not an object.
			$widgets_data = (object) json_decode( $data );
		} else {
			// $data is not set.
			$widgets_data = (object) $widgets_data;
		}

		if ( empty( $widgets_data ) ) {
			return array(
				'status' => false,
				'error'  => __( 'Widget data is empty!', 'st-importer', 'astra-sites' ),
			);
		}

		if ( ! empty( $widgets_data ) ) {
			ST_Widget_Importer::import_widgets_data( $widgets_data );
			$sidebars_widgets = get_option( 'sidebars_widgets', array() );
			update_option( '_astra_sites_old_widgets_data', $sidebars_widgets, 'no' );
			return array(
				'status'  => true,
				'message' => __( 'Widgets imported successfully.', 'st-importer', 'astra-sites' ),
			);
		}
	}
}
