<?php
/**
 * Zip AI - Helper.
 *
 * This file contains the helper functions of Zip AI.
 * Helpers are functions that are used throughout the library.
 *
 * @package zip-ai
 */

namespace ZipAI\Classes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Classes to be used, in alphabetical order.
use ZipAI\Classes\Token_Calculator;
use ZipAI\Classes\Utils;

/**
 * The Helper Class.
 */
class Helper {
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
	 * Update an option from the database.
	 *
	 * @param string $key              The option key.
	 * @param mixed  $value            The value to update.
	 * @param bool   $network_override Whether to allow the network_override admin setting to be overridden on subsites.
	 * @since 1.0.0
	 * @return bool True if the option was updated, false otherwise.
	 */
	public static function update_admin_settings_option( $key, $value, $network_override = false ) {
		// Update the site-wide option if we're in the network admin, and return the updated status.
		return $network_override && is_multisite() ? update_site_option( $key, $value ) : update_option( $key, $value );
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
	 * Check if Zip AI is authorized.
	 *
	 * @since 1.0.0
	 * @return boolean True if Zip AI is authorized, false otherwise.
	 */
	public static function is_authorized() {
		// Get the Zip AI settings.
		$existing_settings = self::get_admin_settings_option( 'zip_ai_settings' );

		// If the Zip AI settings are empty, return false.
		if ( empty( $existing_settings ) || ! is_array( $existing_settings ) ) {
			return false;
		}

		// Return true if the auth token is set and is a string.
		return (
			! empty( $existing_settings['auth_token'] )
			&& is_string( $existing_settings['auth_token'] )
			&& ! empty( trim( $existing_settings['auth_token'] ) )
		);
	}

	/**
	 * Get the Zip AI Settings.
	 *
	 * If used with a key, it will return that specific setting.
	 * If used without a key, it will return the entire settings array.
	 *
	 * @param string $key The setting key.
	 * @param mixed  $default The default value to return if the setting is not found.
	 * @since 1.0.0
	 * @return mixed|array The setting value, or the default.
	 */
	public static function get_setting( $key = '', $default = array() ) {

		// Get the Zip AI settings.
		$existing_settings = self::get_admin_settings_option( 'zip_ai_settings' );

		// If the Zip AI settings are empty, return the fallback.
		if ( empty( $existing_settings ) || ! is_array( $existing_settings ) ) {
			return $default;
		}

		// If the key is empty, return the entire settings array - otherwise return the specific setting or the fallback.
		if ( empty( $key ) ) {
			return $existing_settings;
		} else {
			return isset( $existing_settings[ $key ] ) ? $existing_settings[ $key ] : $default;
		}
	}

	/**
	 * Get the Token Count for a given message.
	 *
	 * @param string $message The message to get the token count for.
	 * @since 1.0.0
	 * @return int The token count.
	 */
	public static function get_token_count( $message ) {
		// Get the formatted token array.
		$token_array = Token_Calculator::gpt_encode( $message );

		// If the token array is empty, return 0, else return the count of the token array.
		return ( empty( $token_array ) || ! is_array( $token_array ) ) ? 0 : count( $token_array );
	}

	/**
	 * Get the Zip AI Response from the Zip Credit Server.
	 *
	 * @param string $endpoint The endpoint to get the response from.
	 * @param array  $body The data to be passed as the request body, if any.
	 * @param array  $extra_args Extra arguments to be passed to the request, if any.
	 * @since 1.0.0
	 * @return array The Zip AI Response.
	 */
	public static function get_credit_server_response( $endpoint, $body = [], $extra_args = [] ) {
		// If the endpoint is not a string, then abandon ship.
		if ( ! is_string( $endpoint ) ) {
			return array(
				'error' => __( 'The Zip AI Endpoint was not declared', 'zip-ai' ),
			);
		}

		// Get the Auth Token from the Zip AI Settings.
		$auth_token = self::get_decrypted_auth_token();

		// If the Zip Auth Token is not set, then abandon ship.
		if ( empty( $auth_token ) || ! is_string( $auth_token ) ) {
			return array(
				'error' => __( 'The Zip AI Auth Token is not set.', 'zip-ai' ),
			);
		}

		// Set the API URL.
		$api_url  = ZIP_AI_CREDIT_SERVER_API . $endpoint;
		$api_args = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $auth_token,
			),
			'timeout' => 30, // phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout -- 30 seconds is required sometime for open ai responses
		);

		// If the data array was passed, add it to the args.
		if ( ! empty( $body ) && is_array( $body ) ) {
			$api_args['body'] = $body;
		}

		// If there are any extra arguments, then we can overwrite the required arguments.
		if ( ! empty( $extra_args ) && is_array( $extra_args ) ) {
			$api_args = array_merge(
				$api_args,
				$extra_args
			);
		}

		// Get the response from the endpoint.
		$response = wp_remote_post(
			$api_url,
			$api_args
		);

		// If the response was an error, or not a 200 status code, then abandon ship.
		if ( is_wp_error( $response ) || empty( $response['response'] ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return array(
				'error' => __( 'The Zip AI Middleware is not responding.', 'zip-ai' ),
			);
		}

		// Get the response body.
		$response_body = wp_remote_retrieve_body( $response );

		// If the response body is not a JSON, then abandon ship.
		if ( empty( $response_body ) || ! json_decode( $response_body ) ) {
			return array(
				'error' => __( 'The Zip AI Middleware encountered an error.', 'zip-ai' ),
			);
		}

		// Return the response body.
		return json_decode( $response_body, true );
	}

	/**
	 * Get a response from the ZipWP API server.
	 *
	 * @param string $endpoint The endpoint to get the response from.
	 * @since 1.1.2
	 * @return array The ZipWP API Response.
	 */
	public static function get_zipwp_api_response( $endpoint ) {
		// If the endpoint is not a string, then abandon ship.
		if ( ! is_string( $endpoint ) ) {
			return array(
				'error' => __( 'The ZipWP Endpoint was not declared', 'zip-ai' ),
			);
		}

		// Get the ZipWP Token from the Zip AI Settings.
		$zipwp_token = self::get_decrypted_zipwp_token();

		// If the ZipWP Token is not set, then abandon ship.
		if ( empty( $zipwp_token ) || ! is_string( $zipwp_token ) ) {
			return array(
				'error' => __( 'The ZipWP Token is not set.', 'zip-ai' ),
			);
		}

		// Set the API URL.
		$api_url = ZIP_AI_ZIPWP_API . $endpoint;

		// Get the response from the endpoint.
		$response = wp_remote_get(
			$api_url,
			array(
				'headers'   => array(
					'Content-Type'  => 'application/json',
					'Accept'        => 'application/json',
					'Authorization' => 'Bearer ' . $zipwp_token,
				),
				'sslverify' => false,
				'timeout'   => 30, // phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout -- 30 seconds is required sometime for the ZipWP API response
			)
		);

		// If the response was an error, or not a 200 status code, then abandon ship.
		if ( is_wp_error( $response ) || empty( $response['response'] ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return array(
				'error' => __( 'The ZipWP API server is not responding.', 'zip-ai' ),
			);
		}

		// Get the response body.
		$response_body = wp_remote_retrieve_body( $response );

		// If the response body is not a JSON, then abandon ship.
		if ( empty( $response_body ) || ! json_decode( $response_body ) ) {
			return array(
				'error' => __( 'The ZipWP API server encountered an error.', 'zip-ai' ),
			);
		}

		// Return the response body.
		return json_decode( $response_body, true );
	}

	/**
	 * Get the decrypted token from the Zip AI Settings.
	 *
	 * @param string $token_name The name of the token.
	 * @since 1.1.2
	 * @return string The decrypted token.
	 */
	private static function get_decrypted_token( $token_name ) {
		// Get the Zip AI Settings.
		$zip_ai_token = self::get_setting( $token_name );

		// Return early if the ZipWP token is not set.
		if ( empty( $zip_ai_token ) || ! is_string( $zip_ai_token ) ) {
			return '';
		}

		// Return the decrypted ZipWP token.
		return ! empty( trim( $zip_ai_token ) ) ? Utils::decrypt( $zip_ai_token ) : '';
	}

	/**
	 * Get the decrypted auth token.
	 *
	 * @since 1.0.0
	 * @return string The decrypted auth token.
	 */
	public static function get_decrypted_auth_token() {
		return self::get_decrypted_token( 'auth_token' );
	}

	/**
	 * Get the decrypted ZipWP token.
	 *
	 * @since 1.1.2
	 * @return string The decrypted ZipWP token.
	 */
	public static function get_decrypted_zipwp_token() {
		return self::get_decrypted_token( 'zip_token' );
	}


	/**
	 * This helper function returns credit details.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_credit_details() {
		// Set the default credit details.
		$credit_details = array(
			'used'       => 0,
			'total'      => 0,
			'threshold'  => array(
				'medium' => ZIP_AI_CREDIT_THRESHOLD_MEDIUM,
				'high'   => ZIP_AI_CREDIT_THRESHOLD_HIGH,
			),
			'percentage' => 0,
			'status'     => 'success',
		);

		// Get the response from the endpoint.
		$response = self::get_credit_server_response( 'usage' );

		// If the response is not an error, then update the credit details.
		if (
			empty( $response['error'] )
			&& ! empty( $response['total_credits'] )
		) {
			$credit_details['used']       = ! empty( $response['total_used_credits'] ) ? $response['total_used_credits'] : 0;
			$credit_details['total']      = $response['total_credits'];
			$credit_details['percentage'] = intval( ( $credit_details['used'] / $credit_details['total'] ) * 100 );
		} else {
			$credit_details['status'] = 'error';
		}

		return $credit_details;
	}


	/**
	 * This helper function returns the current plan details.
	 *
	 * @since 1.1.2
	 * @return array
	 */
	public static function get_current_plan_details() {
		$current_plan_details = [];

		// Get the response from the endpoint.
		$response = self::get_zipwp_api_response( 'plan/current-plan' );

		// If the response is not an error, then use it - else create an error response array.
		if ( empty( $response['error'] ) && is_array( $response ) ) {
			$current_plan_details = $response;
			if ( empty( $current_plan_details['status'] ) ) {
				$current_plan_details['status'] = 'ok';
			}
		} else {
			$current_plan_details['status'] = 'error';
			if ( ! empty( $response['error'] ) ) {
				$current_plan_details['error'] = $response['error'];
			}
		}

		return $current_plan_details;
	}

	/**
	 * Get the authorization middleware url.
	 *
	 * @param array $params An array of parameters to add to the middleware URL.
	 * @since 1.0.0
	 * @return string The authorization middleware url.
	 */
	public static function get_auth_middleware_url( $params = [] ) {

		// Create the Redirect URL.
		$redirect_url = add_query_arg(
			array(
				'nonce'         => wp_create_nonce( 'zip_ai_auth_nonce' ),
				'scs-authorize' => 'true',
			),
			admin_url()
		);

		// Create the Authentication URL.
		$auth_url = add_query_arg(
			apply_filters(
				'zip_ai_auth_middleware_args',
				array(
					'type'         => 'token',
					'redirect_url' => rawurlencode( $redirect_url ),
				)
			),
			ZIP_AI_MIDDLEWARE
		);

		// Add the plugin param if passed.
		if ( ! empty( $params['plugin'] ) && is_string( $params['plugin'] ) ) {
			$auth_url = add_query_arg(
				'plugin',
				sanitize_text_field( $params['plugin'] ),
				$auth_url
			);
		}

		// Add the affiliate param if passed.
		$affiliate = get_option( 'zipwp_partner_url_param', '' );
		$affiliate = is_string( $affiliate ) ? sanitize_text_field( $affiliate ) : '';

		if ( ! empty( $affiliate ) ) {
			$auth_url = add_query_arg(
				'aff',
				$affiliate,
				$auth_url
			);
		}

		return $auth_url;
	}

	/**
	 * Get the revoke url for the auth token.
	 *
	 * @since 1.0.0
	 * @return string The authorization revoke url.
	 */
	public static function get_auth_revoke_url() {
		$revoke_url = add_query_arg(
			apply_filters(
				'zip_ai_auth_revoke_args',
				array(
					array(
						'nonce' => wp_create_nonce( 'zip_ai_auth_nonce' ),
						'revoke_zip_ai_authorization_token' => 'definitely',
					),
					admin_url(),
				)
			)
		);

		return $revoke_url;
	}
}
