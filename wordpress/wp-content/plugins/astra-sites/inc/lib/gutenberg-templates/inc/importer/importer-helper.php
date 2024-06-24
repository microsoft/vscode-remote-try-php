<?php
/**
 * Import Helper.
 *
 * @package {{package}}
 * @since {{since}}
 */

namespace Gutenberg_Templates\Inc\Importer;

use Gutenberg_Templates\Inc\Traits\Helper;
use WP_Query;

/**
 * Importer Helper
 *
 * @since {{since}}
 */
class Importer_Helper {

	/**
	 * Get pages.
	 *
	 * @return array<int|\WP_Post> Array for pages.
	 * @param string $type Post type.
	 * @since  {{since}}
	 */
	public static function get_pages( $type = 'page' ) {
		$query_args = array(
			'post_type'           => array( $type ),
			// Query performance optimization.
			'fields'              => array( 'ids', 'post_content', 'post_title' ),
			'posts_per_page'      => '10',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		$query = new WP_Query( $query_args );

		$desired_first_page_id = intval( get_option( 'page_on_front', 0 ) );
		$pages                 = $query->posts ? $query->posts : array();

		$desired_page_index = false;

		if ( is_array( $pages ) && ! empty( $pages ) && ! empty( $desired_first_page_id ) ) {
			foreach ( $pages as $key => $page ) {

				if ( isset( $page->ID ) && $page->ID === $desired_first_page_id ) {
					$desired_page_index = $key;
					break;
				}
			}

			if ( false !== $desired_page_index ) {
				$desired_page = $pages[ $desired_page_index ];
				unset( $pages[ $desired_page_index ] );
				array_unshift( $pages, $desired_page );
			}
		}

		return $pages;
	}

	/**
	 * Get Business details.
	 *
	 * @since {{since}}
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
				'business_category'   => '',
				'business_email'   => '',
				'social_profiles'  => array(),
				'business_description' => '',
				'token' => '',
				'images' => array(),
				'image_keyword' => array(),
				'templates' => array(),
				'language' => 'en',
			)
		);

		$details = array(
			'business_name'    => ( ! empty( $details['business_name'] ) ) ? $details['business_name'] : '',
			'business_address' => ( ! empty( $details['business_address'] ) ) ? $details['business_address'] : '2360 Hood Avenue, San Diego, CA, 92123',
			'business_phone'   => ( ! empty( $details['business_phone'] ) ) ? $details['business_phone'] : '202-555-0188',
			'business_category'   => ( ! empty( $details['business_category'] ) ) ? $details['business_category'] : '',
			'business_email'   => ( ! empty( $details['business_email'] ) ) ? $details['business_email'] : 'contact@example.com',
			'social_profiles'  => ( ! empty( $details['social_profiles'] ) ) ? $details['social_profiles'] : array(),
			'business_description' => ( ! empty( $details['business_description'] ) ) ? $details['business_description'] : '',
			'token' => Helper::get_decrypted_auth_token(),
			'images' => ( ! empty( $details['images'] ) ) ? $details['images'] : array(),
			'image_keyword' => ( ! empty( $details['image_keyword'] ) ) ? $details['image_keyword'] : array(),
			'templates' => ( ! empty( $details['templates'] ) ) ? $details['templates'] : array(),
			'language' => ( ! empty( $details['language'] ) ) ? $details['language'] : '',
		);

		if ( ! empty( $key ) ) {
			return isset( $details[ $key ] ) ? $details[ $key ] : array();
		}

		return $details;
	}

	/**
	 * Check if we need to skip the URL.
	 *
	 * @param string $url URL to check.
	 * @return boolean
	 * @since {{since}}
	 */
	public static function is_skipable( $url ) {
		if ( strpos( $url, 'skip' ) !== false ) {
			return true;
		}
		return false;
	}

	/**
	 * Get image orientation of the specified image.
	 *
	 * @param string $url Image URL.
	 * @return string Image orientation.
	 * @since {{since}}
	 */
	public static function get_image_orientation( $url ) {
		list( $width, $height ) = getimagesize( $url );
		if ( isset( $width ) && isset( $height ) ) {
			if ( $width > $height ) {
				return 'landscape';
			}
			return 'portrait';
		}
		return 'landscape';
	}
}
