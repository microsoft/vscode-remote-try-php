<?php
/**
 * Ai Builder
 *
 * @since  1.0.0
 * @package Ai Builder
 */

namespace AiBuilder\Inc\Classes\Zipwp;

use AiBuilder\Inc\Traits\Instance;
use AiBuilder\Inc\Classes\Ai_Builder_Importer_Log;

/**
 * ZipWP Integration
 */
class Ai_Builder_ZipWP_Integration {

	use Instance;

	/**
	 * Constructor
	 *
	 * @since 4.0.0
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'save_auth_token' ) );
	}

	/**
	 * Save auth token
	 *
	 * @since 4.0.0
	 * @return void
	 */
	public function save_auth_token() {

		global $pagenow;

		//phpcs:disable WordPress.Security.NonceVerification.Recommended

		if ( ! is_admin() || ! isset( $_GET['page'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		// Check if we are on the starter templates page.
		if ( 'themes.php' !== $pagenow || 'ai-builder' !== $_GET['page'] ) {
			return;
		}

		if ( isset( $_GET['token'] ) && isset( $_GET['email'] ) && isset( $_GET['credit_token'] ) ) {

			$spec_ai_settings = $this->get_setting();

			// Update the auth token if needed.
			if ( isset( $_GET['credit_token'] ) && is_string( $_GET['credit_token'] ) ) {
				$spec_ai_settings['auth_token'] = $this->encrypt( sanitize_text_field( $_GET['credit_token'] ) );
			}

			// Update the Zip token if needed.
			if ( isset( $_GET['token'] ) && is_string( $_GET['token'] ) ) {
				$spec_ai_settings['zip_token'] = $this->encrypt( sanitize_text_field( $_GET['token'] ) );
			}

			// Update the email if needed.
			if ( isset( $_GET['email'] ) && is_string( $_GET['email'] ) ) {
				$spec_ai_settings['email'] = sanitize_email( $_GET['email'] );
			}

			update_option( 'zip_ai_settings', $spec_ai_settings );
		}

		//phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Get Saved settings.
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public static function get_setting() {
		return get_option(
			'zip_ai_settings',
			array(
				'auth_token' => '',
				'zip_token'  => '',
				'email'      => '',
			)
		);
	}

	/**
	 * Encrypt data using base64.
	 *
	 * @param string $input The input string which needs to be encrypted.
	 * @since 4.0.0
	 * @return string The encrypted string.
	 */
	public static function encrypt( $input ) {
		// If the input is empty or not a string, then abandon ship.
		if ( empty( $input ) || ! is_string( $input ) ) {
			return '';
		}

		// Encrypt the input and return it.
		$base_64 = base64_encode( $input ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$encode  = rtrim( $base_64, '=' );
		return $encode;
	}

	/**
	 * Get Business details.
	 *
	 * @since 4.0.0
	 * @param string $key options name.
	 * @return array<string,string,string,string,string,string,string,int> | string Array for business details or single detail in a string.
	 */
	public static function get_business_details( $key = '' ) {
		$details = get_option(
			'zipwp_user_business_details',
			array(
				'business_name'        => '',
				'business_address'     => '',
				'business_phone'       => '',
				'business_email'       => '',
				'business_category'    => '',
				'business_description' => '',
				'templates'            => array(),
				'language'             => 'en',
				'images'               => array(),
				'image_keyword'        => array(),
				'social_profiles'      => array(),
			)
		);

		$details = array(
			'business_name'        => ( ! empty( $details['business_name'] ) ) ? $details['business_name'] : '',
			'business_address'     => ( ! empty( $details['business_address'] ) ) ? $details['business_address'] : '',
			'business_phone'       => ( ! empty( $details['business_phone'] ) ) ? $details['business_phone'] : '',
			'business_email'       => ( ! empty( $details['business_email'] ) ) ? $details['business_email'] : '',
			'business_category'    => ( ! empty( $details['business_category'] ) ) ? $details['business_category'] : '',
			'business_description' => ( ! empty( $details['business_description'] ) ) ? $details['business_description'] : '',
			'templates'            => ( ! empty( $details['templates'] ) ) ? $details['templates'] : array(),
			'language'             => ( ! empty( $details['language'] ) ) ? $details['language'] : 'en',
			'images'               => ( ! empty( $details['images'] ) ) ? $details['images'] : array(),
			'social_profiles'      => ( ! empty( $details['social_profiles'] ) ) ? $details['social_profiles'] : array(),
			'image_keyword'        => ( ! empty( $details['image_keyword'] ) ) ? $details['image_keyword'] : array(),
		);

		if ( ! empty( $key ) ) {
			return isset( $details[ $key ] ) ? $details[ $key ] : array();
		}

		return $details;
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
	 * Get Saved Auth Token.
	 *
	 * @since 4.1.0
	 * @return string
	 */
	public static function get_auth_token() {
		$token_details = get_option(
			'zip_ai_settings',
			array(
				'auth_token' => '',
				'zip_token'  => '',
				'email'      => '',
			)
		);
		return isset( $token_details['auth_token'] ) ? self::decrypt( $token_details['auth_token'] ) : '';
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

}

Ai_Builder_ZipWP_Integration::Instance();
