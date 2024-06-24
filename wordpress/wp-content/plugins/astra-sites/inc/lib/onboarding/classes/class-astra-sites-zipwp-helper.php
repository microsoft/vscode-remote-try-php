<?php
/**
 * ZipWP Helper.
 *
 * @package {{package}}
 * @since 4.0.0
 */

/**
 * Importer Helper
 *
 * @since 4.0.0
 */
class Astra_Sites_ZipWP_Helper {

    /**
     * Get Saved Token.
     * 
     * @since 4.0.0
     * @return string
     */
    public static function get_token() {
        $token_details = get_option( 'zip_ai_settings', array( 'auth_token' => '', 'zip_token' => '', 'email' => '' ) );
        return isset( $token_details['zip_token'] ) ? self::decrypt( $token_details['zip_token'] ) : '';
    }

	/**
     * Get Saved Auth Token.
     * 
     * @since 4.1.0
     * @return string
     */
    public static function get_auth_token() {
        $token_details = get_option( 'zip_ai_settings', array( 'auth_token' => '', 'zip_token' => '', 'email' => '' ) );
        return isset( $token_details['auth_token'] ) ? self::decrypt( $token_details['auth_token'] ) : '';
    }

	/**
     * Get Saved ZipWP user email.
     * 
     * @since 4.0.0
     * @return string
     */
    public static function get_zip_user_email() {
        $token_details = get_option( 'zip_ai_settings', array( 'auth_token' => '', 'zip_token' => '', 'email' => '' ) );
        return isset( $token_details['email'] ) ? $token_details['email'] : '';
    }

	 /**
     * Get Saved settings.
     * 
     * @since 4.0.0
     * @return string
     */
    public static function get_setting() {
        return get_option( 'zip_ai_settings', array( 'auth_token' => '', 'zip_token' => '', 'email' => '' ) );
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
				'business_name'    => '',
				'business_address' => '',
				'business_phone'   => '',
				'business_email'   => '',
				'business_category'  => '',
				'business_description' => '',
				'templates' => array(),
				'language' => 'en',
				'images' => array(),
				'image_keyword' => array(),
				'social_profiles' => array()
			)
		);

		$details = array(
			'business_name'    => ( ! empty( $details['business_name'] ) ) ? $details['business_name'] : '',
			'business_address' => ( ! empty( $details['business_address'] ) ) ? $details['business_address'] : '',
			'business_phone'   => ( ! empty( $details['business_phone'] ) ) ? $details['business_phone'] : '',
			'business_email'   => ( ! empty( $details['business_email'] ) ) ? $details['business_email'] : '',
			'business_category'  => ( ! empty( $details['business_category'] ) ) ? $details['business_category'] : '',
			'business_description' => ( ! empty( $details['business_description'] ) ) ? $details['business_description'] : '',
			'templates' => ( ! empty( $details['templates'] ) ) ? $details['templates'] : array(),
			'language' => ( ! empty( $details['language'] ) ) ? $details['language'] : 'en',
			'images' => ( ! empty( $details['images'] ) ) ? $details['images'] : array(),
			'social_profiles' => ( ! empty( $details['social_profiles'] ) ) ? $details['social_profiles'] : array(),
			'image_keyword' => ( ! empty( $details['image_keyword'] ) ) ? $details['image_keyword'] : array(),
		);

		if ( ! empty( $key ) ) {
			return isset( $details[ $key ] ) ? $details[ $key ] : array();
		}

		return $details;
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
					"auther_name"=> 'Placeholder',
					"id"=> "placeholder-landscape",
					"orientation"=> 'landscape',
					'optimized_url' => 'https://websitedemos.net/wp-content/uploads/2024/02/placeholder-landscape.png',
					'url' => 'https://websitedemos.net/wp-content/uploads/2024/02/placeholder-landscape.png'
				),
				array(
					"auther_name"=> 'Placeholder',
					"id"=> "placeholder-portrait",
					"orientation"=> 'portrait',
					'optimized_url' => 'https://websitedemos.net/wp-content/uploads/2024/02/placeholder-portrait.png',
					'url' => 'https://websitedemos.net/wp-content/uploads/2024/02/placeholder-portrait.png'
				),
			);
	}

		/**
	 * Download image from URL.
	 *
	 * @param array $image Image data.
	 * @return int|\WP_Error Image ID or WP_Error.
	 * @since {{since}}
	 */
	public static function download_image( $image ) {

 		$image_url = $image['url'];

		$id = $image['id'];

		$downloaded_ids = get_option( 'ast_sites_downloaded_images', array() );
		$downloaded_ids = ( is_array( $downloaded_ids ) ) ? $downloaded_ids : array();

		if ( array_key_exists( $id, $downloaded_ids ) ) {
			return $downloaded_ids[ $id ];
		}

		// Check if image is uploaded/downloaded already. If yes the update meta and mark it as downloaded.
		$site_domain = parse_url( get_home_url(), PHP_URL_HOST );

		if( strpos( $image_url, $site_domain ) !== false ){

			$downloaded_ids[ $id ] = $id;

			// Add our meta data for uploaded image.
			if( '1' !== get_post_meta( intval( $downloaded_ids[ $id ] ), '_astra_sites_imported_post', true ) ){
				update_post_meta( $downloaded_ids[ $id ], '_astra_sites_imported_post', true );
			}
			
			update_option( 'ast_sites_downloaded_images', $downloaded_ids );

			return $downloaded_ids[ $id ];
		}

		// Use parse_url to get the path component of the URL.
		$path = wp_parse_url( $image_url, PHP_URL_PATH );

		if ( empty( $path ) ) {
			return new \WP_Error( 'parse_url', 'Unable to parse URL' );
		}

		// Using $id to create image name instead of $path.
		$image_name = 'zipwp-image-' . sanitize_title( $id );

		// Fallback name.
		$image_name = $image_name ? $image_name : sanitize_title( $id );

		// Use pathinfo to get the file name without the extension.
		$image_extension = pathinfo( $image_name, PATHINFO_EXTENSION );
		

		// If the extension is empty, default to jpg. Set image_name with the extension.
		if ( empty( $image_extension ) ) {
			$image_extension = 'jpeg';
			$image_name      = $image_name . '.' . $image_extension;
		}

		$description = $image['description'] ?? '';

		Astra_Sites_Importer_Log::add( 'Downloading Image as - ' . $image_name );

		$new_attachment_id = Astra_Sites::get_instance()->create_image_from_url( $image_url, $image_name, $id, $description );

		//Mark image downloaded.
		$downloaded_ids[ $id ] = $new_attachment_id;
		update_option( 'ast_sites_downloaded_images', $downloaded_ids );

		return $new_attachment_id;
	}
}
