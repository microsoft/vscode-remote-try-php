<?php
/**
 * Ai Builder
 *
 * @since  1.0.0
 * @package Ai Builder
 */

namespace AiBuilder\Inc\Classes\Zipwp;

use AiBuilder\Inc\Traits\Helper;
use AiBuilder\Inc\Traits\Instance;
use AiBuilder\Inc\Classes\Zipwp\Ai_Builder_ZipWP_Integration;
use AiBuilder\Inc\Classes\Ai_Builder_Importer_Log;
use STImporter\Importer\WXR_Importer\ST_WXR_Importer;
use STImporter\Importer\ST_Importer_File_System;

/**
 * Class ZipWP API
 *
 * @since 1.0.0
 */
class Ai_Builder_ZipWP_Api {

	use Instance;
	/**
	 * Constructor
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_route' ) );
	}



	/**
	 * Get api domain
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public function get_api_domain() {
		return ( defined( 'ZIPWP_API' ) ? ZIPWP_API : 'https://api.zipwp.com/api/' );
	}

	/**
	 * Get api namespace
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public function get_api_namespace() {
		return 'zipwp/v1';
	}

	/**
	 * Get API headers
	 *
	 * @since 4.0.0
	 * @return array
	 */
	public function get_api_headers() {
		return array(
			'Content-Type'  => 'application/json',
			'Accept'        => 'application/json',
			'Authorization' => 'Bearer ' . Ai_Builder_ZipWP_Integration::get_token(),
		);
	}

	/**
	 * Check whether a given request has permission to read notes.
	 *
	 * @param  object $request WP_REST_Request Full details about the request.
	 * @return object|boolean
	 */
	public function get_item_permissions_check( $request ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error(
				'gt_rest_cannot_access',
				__( 'Sorry, you are not allowed to do that.', 'ai-builder', 'astra-sites' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		return true;
	}

	/**
	 * Register route
	 *
	 * @since 4.0.0
	 * @return void
	 */
	public function register_route() {
		$namespace = $this->get_api_namespace();

		register_rest_route(
			$namespace,
			'/description/',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'get_description' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'business_name'        => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'business_description' => array(
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'category'             => array(
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
							'required'          => false,
						),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/images/',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'get_images' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'keywords'    => array(
							'type'     => 'string',
							'required' => true,
						),
						'per_page'    => array(
							'type'     => 'string',
							'required' => false,
						),
						'page'        => array(
							'type'     => 'string',
							'required' => false,
						),
						'orientation' => array(
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
							'required'          => false,
						),
						'engine'      => array(
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/keywords/',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'get_keywords' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'business_name'        => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'business_description' => array(
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'category'             => array(
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
							'required'          => false,
						),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/template-keywords/',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'get_template_keywords' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'business_name'        => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'business_description' => array(
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'business_category'    => array(
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
							'required'          => false,
						),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/categories/',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_categories' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/site-features/',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_site_features' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/site-languages/',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_site_languages' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/get-credits/',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_user_credits' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/import-status/',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_import_status' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/migration-status/',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_migration_status' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/templates/',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'get_templates' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'business_name' => array(
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'keyword'       => array(
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
							'required'          => false,
						),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/all-templates/',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'get_all_templates' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'business_name' => array(
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'per_page'      => array(
							'type'     => 'integer',
							'required' => false,
						),
						'page'          => array(
							'type'     => 'integer',
							'required' => true,
						),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/user-details/',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_user_details' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'business_description' => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'business_name'        => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'business_addeess'     => array(
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'business_phone'       => array(
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'business_email'       => array(
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'social_profiles'      => array(
							'type'     => 'array',
							'required' => false,
						),
						'business_category'    => array(
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
							'required'          => true,
						),
						'site_language'        => array(
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
							'required'          => true,
						),
						'keywords'             => array(
							'type'     => 'array',
							'required' => true,
						),
						'images'               => array(
							'type'     => 'array',
							'required' => true,
						),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/site/',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_site' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'template' => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/wxr/',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'prepare_xml' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'template'             => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'business_name'        => array(
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'business_description' => array(
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'language'             => array(
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/ai-site/',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'get_demo' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'template'      => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'business_name' => array(
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/zip-plan/',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'get_zip_plan_details' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => array(),
			)
		);

		register_rest_route(
			$namespace,
			'/search-category/',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'search_business_category' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'keyword' => array(
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
							'required'          => true,
						),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/import-error-log/',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_error_log' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Get the error log details
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 *
	 * @since 1.0.36
	 */
	public function get_error_log( $request ) {

		$nonce = (string) $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		wp_send_json_success(
			array(
				'data'   => get_option( 'ai_import_logger', array() ),
				'status' => true,
			)
		);
	}

	/**
	 * Get the zip plan details
	 *
	 * @since 4.0.0
	 */
	public function get_zip_plan_details() {
		$zip_plan = $this->get_zip_plans();

		$response = new \WP_REST_Response(
			array(
				'success' => $zip_plan['status'],
				'data'    => $zip_plan['data'],
			)
		);
		$response->set_status( 200 );
		return $response;
	}

		/**
		 * Get ZIP Plans.
		 */
	public function get_zip_plans() {
		$api_endpoint = $this->get_api_domain() . '/plan/current-plan';

		$request_args = array(
			'headers'   => $this->get_api_headers(),
			'timeout'   => 100,
			'sslverify' => false,
		);
		$response     = wp_safe_remote_get( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			return array(
				'data'   => 'Failed ' . $response->get_error_message(),
				'status' => false,
			);
		} else {
			$response_code = wp_remote_retrieve_response_code( $response );
			$response_body = wp_remote_retrieve_body( $response );
			if ( 200 === $response_code ) {
				$response_data = json_decode( $response_body, true );
				if ( $response_data ) {
					return array(
						'data'   => $response_data,
						'status' => true,
					);
				} else {
					return array(
						'data'   => 'Failed ' . $response_data,
						'status' => false,
					);
				}
			} else {
				return array(
					'data'   => 'Failed',
					'status' => false,
				);
			}
		}
	}

	/**
	 * Create site.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function create_site( $request ) {

		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		$api_endpoint = $this->get_api_domain() . '/starter-templates/site/';

		$post_data = array(
			'template'               => isset( $request['template'] ) ? sanitize_text_field( $request['template'] ) : '',
			'business_email'         => isset( $request['business_email'] ) ? $request['business_email'] : '',
			'email'                  => $this->get_zip_user_email(),
			'business_desc'          => isset( $request['business_description'] ) ? $request['business_description'] : '',
			'business_name'          => isset( $request['business_name'] ) ? $request['business_name'] : '',
			'title'                  => isset( $request['business_name'] ) ? $request['business_name'] : '',
			'business_phone'         => isset( $request['business_phone'] ) ? $request['business_phone'] : '',
			'business_address'       => isset( $request['business_address'] ) ? $request['business_address'] : '',
			'business_category'      => isset( $request['business_category'] ) ? $request['business_category'] : '',
			'business_category_name' => isset( $request['business_category'] ) ? $request['business_category'] : '',
			'image_keyword'          => isset( $request['image_keyword'] ) ? $request['image_keyword'] : '',
			'social_profiles'        => isset( $request['social_profiles'] ) ? $request['social_profiles'] : [],
			'images'                 => isset( $request['images'] ) ? $request['images'] : '',
			'language'               => isset( $request['language'] ) ? $request['language'] : 'en',
			'site_type'              => 'ai',
			'site_source'            => apply_filters( 'ai_builder_site_source', 'starter-templates' ),
			'site_features'          => isset( $request['site_features'] ) ? $request['site_features'] : [],
		);

		if ( empty( $post_data['images'] ) ) {
			$post_data['images'] = Helper::get_image_placeholders();
		}

		$request_args = array(
			'body'    => wp_json_encode( $post_data ),
			'headers' => $this->get_api_headers(),
			'timeout' => 1000,
		);
		$response     = wp_safe_remote_post( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response->get_error_message(),
					'status' => false,
				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 201 === $response_code || 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			if ( $response_data ) {
				$site_data = $response_data['site'];
				update_option( 'zipwp_import_site_details', $site_data );
				wp_send_json_success(
					array(
						'data'   => $response_data,
						'status' => true,
					)
				);
			} else {
				wp_send_json_error(
					array(
						'data'   => 'Failed ' . $response_data,
						'status' => false,

					)
				);
			}
		} else {
			wp_send_json_error(
				array(
					'data'   => 'Failed - ' . $response_body,
					'status' => false,

				)
			);
		}

	}

	/**
	 * Prepare site XML.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function prepare_xml( $request ) {

		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		$api_endpoint = $this->get_api_domain() . '/starter-templates/wxr/';

		$post_data = array(
			'template'      => isset( $request['template'] ) ? sanitize_text_field( $request['template'] ) : '',
			'business_name' => isset( $request['business_name'] ) ? $request['business_name'] : '',
		);

		$request_args = array(
			'body'    => wp_json_encode( $post_data ),
			'headers' => $this->get_api_headers(),
			'timeout' => 1000,
		);
		$response     = wp_safe_remote_post( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response->get_error_message(),
					'status' => false,
				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 201 === $response_code || 200 === $response_code ) {
			if ( $response_body ) {
				// Get the WordPress upload directory.
				$upload_dir = wp_upload_dir();

				// Define the file path where the attachment will be saved.
				$file_path = $upload_dir['path'] . '/wxr.xml';

				// Save the XML content to a file.
				file_put_contents( $file_path, $response_body ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
				require_once ABSPATH . 'wp-admin/includes/image.php';
				// Prepare the attachment data.
				$attachment = array(
					'post_title'     => 'Response XML',
					'post_mime_type' => 'application/xml',
					'post_content'   => '',
					'post_status'    => 'inherit',
				);

				Ai_builder_Importer_Log::add( wp_json_encode( $attachment ) );
				Ai_builder_Importer_Log::add( wp_json_encode( $file_path ) );

				// Insert the attachment into the media library.
				$attachment_id = wp_insert_attachment( $attachment, $file_path );

				Ai_builder_Importer_Log::add( wp_json_encode( $attachment_id ) );

				if ( is_wp_error( $attachment_id ) ) {
					wp_send_json_error( __( 'There was an error downloading the XML file.', 'ai-builder', 'astra-sites' ) );
				} else {

					update_option( 'astra_sites_imported_wxr_id', $attachment_id, 'no' );
					$attachment_metadata = wp_generate_attachment_metadata( $attachment_id, $file_path );
					wp_update_attachment_metadata( $attachment_id, $attachment_metadata );
					$data        = ST_WXR_Importer::get_xml_data( $file_path, $attachment_id );
					$data['xml'] = $file_path;
					wp_send_json_success( $data );
				}

				wp_send_json_success(
					array(
						'data'   => $response_body,
						'status' => true,
					)
				);
			} else {
				wp_send_json_error(
					array(
						'data'   => 'Failed ' . $response_body,
						'status' => false,

					)
				);
			}
		} else {
			wp_send_json_error(
				array(
					'data'   => 'Failed - ' . $response_body,
					'status' => false,

				)
			);
		}

	}

	/**
	 * Get AI demo site.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function get_demo( $request ) {

		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		$api_endpoint = $this->get_api_domain() . '/starter-templates/export/' . sanitize_text_field( $request['uuid'] );

		$post_data = array(
			'template'      => isset( $request['template'] ) ? sanitize_text_field( $request['template'] ) : '',
			'business_name' => isset( $request['business_name'] ) ? $request['business_name'] : '',
		);

		$request_args = array(
			'body'    => wp_json_encode( $post_data ),
			'headers' => $this->get_api_headers(),
			'timeout' => 1000,
		);
		$response     = wp_safe_remote_post( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response->get_error_message(),
					'status' => false,
				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 201 === $response_code || 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			if ( $response_body ) {
				$exported_data                   = $response_data['data'];
				$exported_data['astra-site-url'] = $exported_data['host'];
				ST_Importer_File_System::get_instance()->update_demo_data( $exported_data );
				set_transient( 'astra_sites_current_import_template_type', 'ai', HOUR_IN_SECONDS );
				update_option( 'astra_sites_batch_process_complete', 'no' );
				$host_url = $exported_data['host'] ?? '';
				update_option( 'ast_ai_import_current_url', $host_url );
				wp_send_json_success(
					array(
						'data'   => $exported_data,
						'status' => true,
					)
				);
			} else {
				wp_send_json_error(
					array(
						'data'   => 'Failed ' . $response_body,
						'status' => false,

					)
				);
			}
		} else {
			wp_send_json_error(
				array(
					'data'   => 'Failed - ' . $response_body,
					'status' => false,

				)
			);
		}

	}

	/**
	 * Save user details.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function save_user_details( $request ) {

		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		$email = $this->get_zip_user_email();

		$api_endpoint = $this->get_api_domain() . '/sites/generate-user-cache/';

		$business_details = array(
			'business_description' => isset( $request['business_description'] ) ? sanitize_text_field( $request['business_description'] ) : '',
			'business_name'        => isset( $request['business_name'] ) ? sanitize_text_field( $request['business_name'] ) : '',
			'business_email'       => isset( $request['business_email'] ) ? sanitize_text_field( $request['business_email'] ) : '',
			'business_address'     => isset( $request['business_address'] ) ? sanitize_text_field( $request['business_address'] ) : '',
			'business_phone'       => isset( $request['business_phone'] ) ? sanitize_text_field( $request['business_phone'] ) : '',
			'business_category'    => isset( $request['business_category'] ) ? sanitize_text_field( $request['business_category'] ) : '',
			'category_name'        => isset( $request['business_category'] ) ? sanitize_text_field( $request['business_category'] ) : '',
			'image_keyword'        => isset( $request['keywords'] ) ? $request['keywords'] : [],
			'images'               => isset( $request['images'] ) ? $request['images'] : [],
			'social_profiles'      => isset( $request['social_profiles'] ) ? $request['social_profiles'] : [],
			'language'             => isset( $request['site_language'] ) ? sanitize_text_field( $request['site_language'] ) : 'en',
			'templates'            => get_option( 'zipwp_selection_templates', array() ),
		);

		update_option(
			'zipwp_user_business_details',
			$business_details
		);

		if ( empty( $business_details['images'] ) ) {
			$business_details['images'] = Helper::get_image_placeholders();
		}

		$post_data              = array_merge( $business_details, [ 'email' => $email ] );
		$post_data['templates'] = [];

		$request_args = array(
			'body'    => wp_json_encode( $post_data ),
			'headers' => $this->get_api_headers(),
			'timeout' => 100,
		);
		$response     = wp_safe_remote_post( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response->get_error_message(),
					'status' => false,

				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			wp_send_json_success(
				array(
					'data'   => $response_data,
					'status' => true,
				)
			);

		} else {
			wp_send_json_error(
				array(
					'data'   => 'Failed - ' . $response_body,
					'status' => false,

				)
			);
		}
	}

	/**
	 * Get AI based description.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function get_description( $request ) {

		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		$api_endpoint = $this->get_api_domain() . '/sites/suggest-description/';

		$post_data = array(
			'business_name'     => isset( $request['business_name'] ) ? sanitize_text_field( $request['business_name'] ) : '',
			'business_desc'     => isset( $request['business_description'] ) ? sanitize_text_field( $request['business_description'] ) : '',
			'business_category' => isset( $request['category'] ) ? sanitize_text_field( $request['category'] ) : '',
		);

		$request_args = array(
			'body'    => wp_json_encode( $post_data ),
			'headers' => $this->get_api_headers(),
			'timeout' => 100,
		);
		$response     = wp_safe_remote_post( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response->get_error_message(),
					'status' => false,

				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			if ( $response_data['description'] ) {
				wp_send_json_success(
					array(
						'data'   => $response_data['description'],
						'status' => true,
					)
				);
			} else {
				wp_send_json_error(
					array(
						'data'   => 'Failed ' . $response_data,
						'status' => false,

					)
				);
			}
		} else {
			wp_send_json_error(
				array(
					'data'   => 'Failed',
					'status' => false,

				)
			);
		}
	}

	/**
	 * Get Images.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function get_images( $request ) {

		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		$api_endpoint = $this->get_api_domain() . '/images/';

		$post_data = array(
			'keywords'    => isset( $request['keywords'] ) ? [ $request['keywords'] ] : [],
			'per_page'    => isset( $request['per_page'] ) ? $request['per_page'] : 20,
			'page'        => isset( $request['page'] ) ? $request['page'] : 1,
			'orientation' => isset( $request['orientation'] ) ? sanitize_text_field( $request['orientation'] ) : '',
			'engine'      => isset( $request['engine'] ) ? sanitize_text_field( $request['engine'] ) : '',
		);

		$request_args = array(
			'body'    => wp_json_encode( $post_data ),
			'headers' => $this->get_api_headers(),
			'timeout' => 100,
		);
		$response     = wp_safe_remote_post( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response->get_error_message(),
					'status' => false,

				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			wp_send_json_success(
				array(
					'data'   => $response_data['images'],
					'status' => true,
				)
			);

		} else {
			wp_send_json_error(
				array(
					'data'   => 'Failed',
					'status' => false,

				)
			);
		}
	}

	/**
	 * Get Keywords for image search.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function get_keywords( $request ) {

		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		$api_endpoint = $this->get_api_domain() . '/images/keyword/';

		$post_data = array(
			'business_desc' => isset( $request['business_description'] ) ? sanitize_text_field( $request['business_description'] ) : '',
		);

		$request_args = array(
			'body'    => wp_json_encode( $post_data ),
			'headers' => $this->get_api_headers(),
			'timeout' => 100,
		);
		$response     = wp_safe_remote_post( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response->get_error_message(),
					'status' => false,

				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			if ( $response_data ) {
				wp_send_json_success(
					array(
						'data'   => $response_data['keywords'],
						'status' => true,
					)
				);
			} else {
				wp_send_json_error(
					array(
						'data'   => 'Failed ' . $response_data,
						'status' => false,

					)
				);
			}
		} else {
			wp_send_json_error(
				array(
					'data'   => 'Failed',
					'status' => false,

				)
			);
		}
	}

	/**
	 * Get Template Keywords for image search.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function get_template_keywords( $request ) {

		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		$api_endpoint = $this->get_api_domain() . '/templates-keywords/';

		$post_data = array(
			'business_desc'          => isset( $request['business_description'] ) ? sanitize_text_field( $request['business_description'] ) : '',
			'business_cat'           => isset( $request['business_category'] ) ? sanitize_text_field( $request['business_category'] ) : '',
			'business_category_name' => isset( $request['business_category'] ) ? sanitize_text_field( $request['business_category'] ) : '',
			'business_name'          => isset( $request['business_name'] ) ? sanitize_text_field( $request['business_name'] ) : '',
			'language'               => 'en',
		);

		$request_args = array(
			'body'    => wp_json_encode( $post_data ),
			'headers' => $this->get_api_headers(),
			'timeout' => 100,
		);
		$response     = wp_safe_remote_post( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response->get_error_message(),
					'status' => false,

				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			if ( $response_data ) {
				wp_send_json_success(
					array(
						'data'   => $response_data['keywords'],
						'status' => true,
					)
				);
			} else {
				wp_send_json_error(
					array(
						'data'   => 'Failed ' . $response_data,
						'status' => false,

					)
				);
			}
		} else {
			wp_send_json_error(
				array(
					'data'   => 'Failed',
					'status' => false,

				)
			);
		}
	}

	/**
	 * Get Keywords for image search.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function get_templates( $request ) {

		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		$keyword = isset( $request['keyword'] ) ? sanitize_text_field( $request['keyword'] ) : 'multipurpose';

		$api_endpoint = $this->get_api_domain() . '/templates-search?query=' . $keyword;

		$post_data = array(
			'business_name' => isset( $request['business_name'] ) ? sanitize_text_field( $request['business_name'] ) : '',
			'email'         => $this->get_zip_user_email(),
			'email'         => '',
		);

		$request_args = array(
			'body'    => wp_json_encode( $post_data ),
			'headers' => $this->get_api_headers(),
			'timeout' => 100,
		);
		$response     = wp_safe_remote_post( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response->get_error_message(),
					'status' => false,

				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			if ( $response_data ) {
				update_option( 'zipwp_selection_templates', $response_data );
				wp_send_json_success(
					array(
						'data'   => $response_data,
						'status' => true,
					)
				);
			} else {
				wp_send_json_error(
					array(
						'data'   => 'Failed ' . $response_data,
						'status' => false,

					)
				);
			}
		} else {
			wp_send_json_error(
				array(
					'data'   => 'Failed',
					'status' => false,

				)
			);
		}
	}

	/**
	 * Get templates by page.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function get_all_templates( $request ) {

		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		$per_page = isset( $request['per_page'] ) ? intval( $request['per_page'] ) : 9;
		$page     = isset( $request['page'] ) ? intval( $request['page'] ) : 1;

		$api_endpoint = $this->get_api_domain() . '/all-templates';

		$post_data = array(
			'business_name' => isset( $request['business_name'] ) ? sanitize_text_field( $request['business_name'] ) : '',
			'email'         => $this->get_zip_user_email(),
			'per_page'      => $per_page,
			'page'          => $page,
		);

		$request_args = array(
			'body'    => wp_json_encode( $post_data ),
			'headers' => $this->get_api_headers(),
			'timeout' => 100,
		);
		$response     = wp_safe_remote_post( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response->get_error_message(),
					'status' => false,

				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			if ( $response_data ) {
				update_option( 'zipwp_selection_templates', $response_data );
				wp_send_json_success(
					array(
						'data'   => $response_data,
						'status' => true,
					)
				);
			} else {
				wp_send_json_error(
					array(
						'data'   => 'Failed ' . $response_data,
						'status' => false,

					)
				);
			}
		} else {
			wp_send_json_error(
				array(
					'data'   => 'Failed',
					'status' => false,

				)
			);
		}
	}

	/**
	 * Get Categories.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function get_categories( $request ) {
		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		$api_endpoint = $this->get_api_domain() . '/all-parent-categories/';
		$request_args = array(
			'headers' => $this->get_api_headers(),
			'timeout' => 100,
		);
		$response     = wp_safe_remote_get( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response->get_error_message(),
					'status' => false,

				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			if ( $response_data ) {
				wp_send_json_success(
					array(
						'data'   => $response_data['categories'],
						'status' => true,
					)
				);
			} else {
				wp_send_json_error(
					array(
						'data'   => 'Failed ' . $response_data,
						'status' => false,

					)
				);
			}
		} elseif ( 401 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			wp_send_json_error(
				array(
					'data'   => $response_data['message'],
					'status' => false,

				)
			);
		} else {
			wp_send_json_error(
				array(
					'data'   => 'Failed',
					'status' => false,

				)
			);
		}
	}


	/**
	 * Get ZipWP Features list.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function get_site_features( $request ) {
		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		$api_endpoint = $this->get_api_domain() . '/sites/features/';
		$request_args = array(
			'headers' => $this->get_api_headers(),
			'timeout' => 100,
		);
		$response     = wp_safe_remote_get( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response->get_error_message(),
					'status' => false,

				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			if ( $response_data ) {
				wp_send_json_success(
					array(
						'data'   => $response_data['data'],
						'status' => true,
					)
				);
			}
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response_data,
					'status' => false,

				)
			);
		}
		wp_send_json_error(
			array(
				'data'   => 'Failed ' . $response_body,
				'status' => false,

			)
		);
	}

	/**
	 * Get ZipWP Languages list.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function get_site_languages( $request ) {
		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		$api_endpoint = $this->get_api_domain() . '/sites/languages/';
		$request_args = array(
			'headers' => $this->get_api_headers(),
			'timeout' => 100,
		);
		$response     = wp_safe_remote_get( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response->get_error_message(),
					'status' => false,

				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			if ( $response_data ) {
				wp_send_json_success(
					array(
						'data'   => $response_data['data'],
						'status' => true,
					)
				);
			}
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response_data,
					'status' => false,

				)
			);
		}
		wp_send_json_error(
			array(
				'data'   => 'Failed ' . $response_body,
				'status' => false,

			)
		);
	}

	/**
	 * Get User Credits.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function get_user_credits( $request ) {
		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		$api_endpoint = $this->get_api_domain() . '/scs-usage/';
		$request_args = array(
			'headers' => $this->get_api_headers(),
			'timeout' => 100,
		);
		$response     = wp_safe_remote_post( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response->get_error_message(),
					'status' => false,

				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		if ( 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			if ( $response_data ) {
				$credit_details               = array();
				$credit_details['used']       = ! empty( $response_data['total_used_credits'] ) ? $response_data['total_used_credits'] : 0;
				$credit_details['total']      = $response_data['total_credits'];
				$credit_details['percentage'] = intval( ( $credit_details['used'] / $credit_details['total'] ) * 100 );
				$credit_details['free_user']  = $response_data['free_user'];
				wp_send_json_success(
					array(
						'data'   => $credit_details,
						'status' => true,
					)
				);
			}
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response_data,
					'status' => false,

				)
			);
		}
		wp_send_json_error(
			array(
				'data'   => 'Failed ' . $response_body,
				'status' => false,

			)
		);
	}

	/**
	 * Get Import Status.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function get_import_status( $request ) {
		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		$site         = get_option( 'zipwp_import_site_details', array() );
		$api_endpoint = $this->get_api_domain() . '/sites/import-status/' . $site['uuid'] . '/';
		$request_args = array(
			'headers' => $this->get_api_headers(),
			'timeout' => 100,
		);
		$response     = wp_safe_remote_get( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response->get_error_message(),
					'status' => false,
				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			if ( $response_data ) {
				wp_send_json_success(
					array(
						'data'   => $response_data,
						'status' => true,
					)
				);
			} else {
				wp_send_json_error(
					array(
						'data'   => 'Failed ' . $response_data,
						'status' => false,
					)
				);
			}
		} else {
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response_code,
					'status' => false,
				)
			);
		}
	}

	/**
	 * Get Migration Status.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function get_migration_status( $request ) {
		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}
		wp_send_json_success(
			array(
				'data'   => get_option( 'astra_sites_batch_process_complete', 'no' ),
				'status' => true,
			)
		);
	}

	/**
	 * Search business category.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function search_business_category( $request ) {

		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'ai-builder', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		$keyword      = $request['keyword'];
		$api_endpoint = $this->get_api_domain() . '/sites/business/search?q=' . $keyword;

		$request_args = array(
			'headers' => $this->get_api_headers(),
			'timeout' => 100,
		);
		$response     = wp_safe_remote_get( $api_endpoint, $request_args );
		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response->get_error_message(),
					'status' => false,

				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			wp_send_json_success(
				array(
					'data'   => $response_data['results'],
					'status' => true,
				)
			);

		} else {
			wp_send_json_error(
				array(
					'data'   => 'Failed - ' . $response_body,
					'status' => false,

				)
			);
		}
	}

	/**
	 * Get Saved ZipWP user email.
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public static function get_zip_user_email() {
		$token_details = get_option(
			'zip_ai_settings',
			array(
				'auth_token' => '',
				'zip_token'  => '',
				'email'      => '',
			)
		);
		return isset( $token_details['email'] ) ? $token_details['email'] : '';
	}

}

Ai_Builder_ZipWP_Api::Instance();
