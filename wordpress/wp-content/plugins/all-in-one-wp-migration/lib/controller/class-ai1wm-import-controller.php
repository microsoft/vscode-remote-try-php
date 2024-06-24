<?php
/**
 * Copyright (C) 2014-2023 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Kangaroos cannot jump here' );
}

class Ai1wm_Import_Controller {

	public static function index() {
		Ai1wm_Template::render( 'import/index' );
	}

	public static function import( $params = array() ) {
		global $ai1wm_params;
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( array_merge( $_GET, $_POST ) );
		}

		// Set priority
		if ( ! isset( $params['priority'] ) ) {
			$params['priority'] = 10;
		}

		// Set secret key
		$secret_key = null;
		if ( isset( $params['secret_key'] ) ) {
			$secret_key = trim( $params['secret_key'] );
		}

		try {
			// Ensure that unauthorized people cannot access import action
			ai1wm_verify_secret_key( $secret_key );
		} catch ( Ai1wm_Not_Valid_Secret_Key_Exception $e ) {
			exit;
		}

		$ai1wm_params = $params;

		// Loop over filters
		if ( ( $filters = ai1wm_get_filters( 'ai1wm_import' ) ) ) {
			while ( $hooks = current( $filters ) ) {
				if ( intval( $params['priority'] ) === key( $filters ) ) {
					foreach ( $hooks as $hook ) {
						try {

							// Run function hook
							$params = call_user_func_array( $hook['function'], array( $params ) );

						} catch ( Ai1wm_Import_Retry_Exception $e ) {
							if ( defined( 'WP_CLI' ) ) {
								WP_CLI::error( sprintf( __( 'Unable to import. Error code: %s. %s', AI1WM_PLUGIN_NAME ), $e->getCode(), $e->getMessage() ) );
							} else {
								status_header( $e->getCode() );
								ai1wm_json_response( array( 'errors' => array( array( 'code' => $e->getCode(), 'message' => $e->getMessage() ) ) ) );
							}
							exit;
						} catch ( Ai1wm_Database_Exception $e ) {
							if ( defined( 'WP_CLI' ) ) {
								WP_CLI::error( sprintf( __( 'Unable to import. Error code: %s. %s', AI1WM_PLUGIN_NAME ), $e->getCode(), $e->getMessage() ) );
							} else {
								status_header( $e->getCode() );
								ai1wm_json_response( array( 'errors' => array( array( 'code' => $e->getCode(), 'message' => $e->getMessage() ) ) ) );
							}
							Ai1wm_Directory::delete( ai1wm_storage_path( $params ) );
							exit;
						} catch ( Exception $e ) {
							if ( defined( 'WP_CLI' ) ) {
								WP_CLI::error( sprintf( __( 'Unable to import: %s', AI1WM_PLUGIN_NAME ), $e->getMessage() ) );
							} else {
								Ai1wm_Status::error( __( 'Unable to import', AI1WM_PLUGIN_NAME ), $e->getMessage() );
								Ai1wm_Notification::error( __( 'Unable to import', AI1WM_PLUGIN_NAME ), $e->getMessage() );
							}
							Ai1wm_Directory::delete( ai1wm_storage_path( $params ) );
							exit;
						}
					}

					// Set completed
					$completed = true;
					if ( isset( $params['completed'] ) ) {
						$completed = (bool) $params['completed'];
					}

					// Do request
					if ( $completed === false || ( $next = next( $filters ) ) && ( $params['priority'] = key( $filters ) ) ) {
						if ( defined( 'WP_CLI' ) ) {
							if ( ! defined( 'DOING_CRON' ) ) {
								continue;
							}
						}

						if ( isset( $params['ai1wm_manual_import'] ) || isset( $params['ai1wm_manual_restore'] ) ) {
							ai1wm_json_response( $params );
							exit;
						}

						wp_remote_request(
							apply_filters( 'ai1wm_http_import_url', add_query_arg( array( 'ai1wm_import' => 1 ), admin_url( 'admin-ajax.php?action=ai1wm_import' ) ) ),
							array(
								'method'    => apply_filters( 'ai1wm_http_import_method', 'POST' ),
								'timeout'   => apply_filters( 'ai1wm_http_import_timeout', 10 ),
								'blocking'  => apply_filters( 'ai1wm_http_import_blocking', false ),
								'sslverify' => apply_filters( 'ai1wm_http_import_sslverify', false ),
								'headers'   => apply_filters( 'ai1wm_http_import_headers', array() ),
								'body'      => apply_filters( 'ai1wm_http_import_body', $params ),
							)
						);
						exit;
					}
				}

				next( $filters );
			}
		}

		return $params;
	}

	public static function buttons() {
		$active_filters = array();
		$static_filters = array();

		// All-in-One WP Migration
		if ( defined( 'AI1WM_PLUGIN_NAME' ) ) {
			$active_filters[] = apply_filters( 'ai1wm_import_file', Ai1wm_Template::get_content( 'import/button-file' ) );
		} else {
			$static_filters[] = apply_filters( 'ai1wm_import_file', Ai1wm_Template::get_content( 'import/button-file' ) );
		}

		// Add URL Extension
		if ( defined( 'AI1WMLE_PLUGIN_NAME' ) ) {
			$active_filters[] = apply_filters( 'ai1wm_import_url', Ai1wm_Template::get_content( 'import/button-url' ) );
		} else {
			$static_filters[] = apply_filters( 'ai1wm_import_url', Ai1wm_Template::get_content( 'import/button-url' ) );
		}

		// Add FTP Extension
		if ( defined( 'AI1WMFE_PLUGIN_NAME' ) ) {
			$active_filters[] = apply_filters( 'ai1wm_import_ftp', Ai1wm_Template::get_content( 'import/button-ftp' ) );
		} else {
			$static_filters[] = apply_filters( 'ai1wm_import_ftp', Ai1wm_Template::get_content( 'import/button-ftp' ) );
		}

		// Add Dropbox Extension
		if ( defined( 'AI1WMDE_PLUGIN_NAME' ) ) {
			$active_filters[] = apply_filters( 'ai1wm_import_dropbox', Ai1wm_Template::get_content( 'import/button-dropbox' ) );
		} else {
			$static_filters[] = apply_filters( 'ai1wm_import_dropbox', Ai1wm_Template::get_content( 'import/button-dropbox' ) );
		}

		// Add Google Drive Extension
		if ( defined( 'AI1WMGE_PLUGIN_NAME' ) ) {
			$active_filters[] = apply_filters( 'ai1wm_import_gdrive', Ai1wm_Template::get_content( 'import/button-gdrive' ) );
		} else {
			$static_filters[] = apply_filters( 'ai1wm_import_gdrive', Ai1wm_Template::get_content( 'import/button-gdrive' ) );
		}

		// Add Amazon S3 Extension
		if ( defined( 'AI1WMSE_PLUGIN_NAME' ) ) {
			$active_filters[] = apply_filters( 'ai1wm_import_s3', Ai1wm_Template::get_content( 'import/button-s3' ) );
		} else {
			$static_filters[] = apply_filters( 'ai1wm_import_s3', Ai1wm_Template::get_content( 'import/button-s3' ) );
		}

		// Add Backblaze B2 Extension
		if ( defined( 'AI1WMAE_PLUGIN_NAME' ) ) {
			$active_filters[] = apply_filters( 'ai1wm_import_b2', Ai1wm_Template::get_content( 'import/button-b2' ) );
		} else {
			$static_filters[] = apply_filters( 'ai1wm_import_b2', Ai1wm_Template::get_content( 'import/button-b2' ) );
		}

		// Add OneDrive Extension
		if ( defined( 'AI1WMOE_PLUGIN_NAME' ) ) {
			$active_filters[] = apply_filters( 'ai1wm_import_onedrive', Ai1wm_Template::get_content( 'import/button-onedrive' ) );
		} else {
			$static_filters[] = apply_filters( 'ai1wm_import_onedrive', Ai1wm_Template::get_content( 'import/button-onedrive' ) );
		}

		// Add Box Extension
		if ( defined( 'AI1WMBE_PLUGIN_NAME' ) ) {
			$active_filters[] = apply_filters( 'ai1wm_import_box', Ai1wm_Template::get_content( 'import/button-box' ) );
		} else {
			$static_filters[] = apply_filters( 'ai1wm_import_box', Ai1wm_Template::get_content( 'import/button-box' ) );
		}

		// Add Mega Extension
		if ( defined( 'AI1WMEE_PLUGIN_NAME' ) ) {
			$active_filters[] = apply_filters( 'ai1wm_import_mega', Ai1wm_Template::get_content( 'import/button-mega' ) );
		} else {
			$static_filters[] = apply_filters( 'ai1wm_import_mega', Ai1wm_Template::get_content( 'import/button-mega' ) );
		}

		// Add DigitalOcean Spaces Extension
		if ( defined( 'AI1WMIE_PLUGIN_NAME' ) ) {
			$active_filters[] = apply_filters( 'ai1wm_import_digitalocean', Ai1wm_Template::get_content( 'import/button-digitalocean' ) );
		} else {
			$static_filters[] = apply_filters( 'ai1wm_import_digitalocean', Ai1wm_Template::get_content( 'import/button-digitalocean' ) );
		}

		// Add Google Cloud Storage Extension
		if ( defined( 'AI1WMCE_PLUGIN_NAME' ) ) {
			$active_filters[] = apply_filters( 'ai1wm_import_gcloud_storage', Ai1wm_Template::get_content( 'import/button-gcloud-storage' ) );
		} else {
			$static_filters[] = apply_filters( 'ai1wm_import_gcloud_storage', Ai1wm_Template::get_content( 'import/button-gcloud-storage' ) );
		}

		// Add Microsoft Azure Extension
		if ( defined( 'AI1WMZE_PLUGIN_NAME' ) ) {
			$active_filters[] = apply_filters( 'ai1wm_import_azure_storage', Ai1wm_Template::get_content( 'import/button-azure-storage' ) );
		} else {
			$static_filters[] = apply_filters( 'ai1wm_import_azure_storage', Ai1wm_Template::get_content( 'import/button-azure-storage' ) );
		}

		// Add Amazon Glacier Extension
		if ( defined( 'AI1WMRE_PLUGIN_NAME' ) ) {
			$active_filters[] = apply_filters( 'ai1wm_import_glacier', Ai1wm_Template::get_content( 'import/button-glacier' ) );
		} else {
			$static_filters[] = apply_filters( 'ai1wm_import_glacier', Ai1wm_Template::get_content( 'import/button-glacier' ) );
		}

		// Add pCloud Extension
		if ( defined( 'AI1WMPE_PLUGIN_NAME' ) ) {
			$active_filters[] = apply_filters( 'ai1wm_import_pcloud', Ai1wm_Template::get_content( 'import/button-pcloud' ) );
		} else {
			$static_filters[] = apply_filters( 'ai1wm_import_pcloud', Ai1wm_Template::get_content( 'import/button-pcloud' ) );
		}

		// Add WebDAV Extension
		if ( defined( 'AI1WMWE_PLUGIN_NAME' ) ) {
			$active_filters[] = apply_filters( 'ai1wm_import_webdav', Ai1wm_Template::get_content( 'import/button-webdav' ) );
		} else {
			$static_filters[] = apply_filters( 'ai1wm_import_webdav', Ai1wm_Template::get_content( 'import/button-webdav' ) );
		}

		// Add S3 Client Extension
		if ( defined( 'AI1WMNE_PLUGIN_NAME' ) ) {
			$active_filters[] = apply_filters( 'ai1wm_import_s3_client', Ai1wm_Template::get_content( 'import/button-s3-client' ) );
		} else {
			$static_filters[] = apply_filters( 'ai1wm_import_s3_client', Ai1wm_Template::get_content( 'import/button-s3-client' ) );
		}

		return array_merge( $active_filters, $static_filters );
	}

	public static function pro() {
		return Ai1wm_Template::get_content( 'import/pro' );
	}

	public static function max_chunk_size() {
		return min(
			ai1wm_parse_size( ini_get( 'post_max_size' ), AI1WM_MAX_CHUNK_SIZE ),
			ai1wm_parse_size( ini_get( 'upload_max_filesize' ), AI1WM_MAX_CHUNK_SIZE ),
			ai1wm_parse_size( AI1WM_MAX_CHUNK_SIZE )
		);
	}
}
