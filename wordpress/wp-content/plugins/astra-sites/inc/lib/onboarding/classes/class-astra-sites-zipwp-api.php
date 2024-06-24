<?php

class Astra_Sites_ZipWP_Api {

    /**
     * Member Variable
     *
     * @var instance
     */
    private static $instance;

    /**
     * Initiator
     *
     * @since 4.0.0
     */
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

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
		return (defined('ZIPWP_API') ? ZIPWP_API : 'https://api.zipwp.com/api/');
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
			'Content-Type' => 'application/json',
			'Accept' => 'application/json',
			'Authorization' => 'Bearer ' . Astra_Sites_ZipWP_Helper::get_token(),
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
				__( 'Sorry, you are not allowed to do that.', 'astra-sites' ),
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
			'/zip-plan/',
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'get_zip_plan_details' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args' => array(),
			)
		);

		register_rest_route(
			$namespace,
			'/revoke-access/',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'revoke_access' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
			)
		);
    }

	/**
     * Get the zip plan details
     * @since 4.0.0
     */
    public function get_zip_plan_details() {
        $zip_plan = Astra_Sites_ZipWP_Integration::get_instance()->get_zip_plans();

        $response = new \WP_REST_Response(
            array(
                'success' => $zip_plan['status'],
                'data' => $zip_plan['data'],
            )
        );
        $response->set_status( 200 );
        return $response;
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
					'data' => __( 'Nonce verification failed.', 'astra-sites' ),
					'status'  => false,

				)
			);
		}

		$api_endpoint = $this->get_api_domain() . '/scs-usage/';
		$request_args = array(
			'headers' => $this->get_api_headers(),
			'timeout' => 100,
		);
		$response = wp_safe_remote_post( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data' => 'Failed ' . $response->get_error_message(),
					'status'  => false,

				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		if ( 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			if ( $response_data ) {
				$credit_details = array();
				$credit_details['used']       = ! empty( $response_data['total_used_credits'] ) ? $response_data['total_used_credits'] : 0;
				$credit_details['total']      = $response_data['total_credits'];
				$credit_details['percentage'] = intval( ( $credit_details['used'] / $credit_details['total'] ) * 100 );
				$credit_details['free_user'] = $response_data['free_user'];
				wp_send_json_success(
					array(
						'data' => $credit_details,
						'status'  => true,
					)
				);
			}
			wp_send_json_error(
				array(
					'data' => 'Failed ' . $response_data,
					'status'  => false,

				)
			);
		}
		wp_send_json_error(
			array(
				'data' => 'Failed ' . $response_body,
				'status'  => false,

			)
		);
	}

	/**
	 * Revoke access.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response
	 */
	public function revoke_access( $request ): WP_REST_Response {

		$nonce = $request->get_header( 'X-WP-Nonce' );
		$nonce = isset( $nonce ) ? sanitize_text_field( $nonce ) : '';
		// Verify the nonce.
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data' => __( 'Nonce verification failed.', 'astra-sites' ),
					'status'  => false,

				)
			);
		}
		
		$business_details = get_option( 'ast-templates-business-details', false );
		delete_option( 'ast-block-templates-show-onboarding' );
		if ( ! $business_details ) {
			$business_details = array();
		}

		$business_details['token'] = '';
		$updated = update_option( 'ast-templates-business-details', $business_details );
		delete_option( 'zip_ai_settings' );
		$response = new WP_REST_Response(
			array(
				'success' => $updated,
			)
		);
		$response->set_status( 200 );
		return $response;
	}
}

Astra_Sites_ZipWP_Api::get_instance();
