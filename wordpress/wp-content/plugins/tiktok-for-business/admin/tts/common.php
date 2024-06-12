<?php

/**
 * TikTok common code
 * such as const, util function
 */

namespace tiktok\admin\tts\common;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Inserts a new key/value after the key in the array.
 *
 * If the $needle doesn't exist in the array, the new key and value wil be
 * appended to the end of the $haystack array.
 *
 * @param string $needle    The array key to insert the element after
 * @param array  $haystack  An array to insert the element into
 * @param string $new_key   The key to insert
 * @param mixed  $new_value An value to insert
 *
 * @return array The new array.
 */
function array_insert_after( $needle, $haystack, $new_key, $new_value ) {
	// If the needle doesn't exist, just append to the end of the array.
	if ( ! array_key_exists( $needle, $haystack ) ) {
		$haystack[ $new_key ] = $new_value;

		return $haystack;
	}

	$new_array = [];
	foreach ( $haystack as $key => $value ) {
		$new_array[ $key ] = $value;

		if ( $key === $needle ) {
			$new_array[ $new_key ] = $new_value;
		}
	}

	return $new_array;
}

/**
 * Helper to create links to edit.php with params.
 *
 * @since 4.4.0
 *
 * @param  string[] $args      Associative array of URL parameters for the link.
 * @param  string   $link_text Link text.
 * @param  string   $css_class Optional. Class attribute. Default empty string.
 * @return string The formatted link string.
 *
 * located in wordpress/wp-admin/includes/class-wp-posts-list-talbe.php
 */
function get_edit_link( $args, $link_text, $css_class = '' ) {
	$url = add_query_arg( $args, 'edit.php' );

	$class_html   = '';
	$aria_current = '';

	if ( ! empty( $css_class ) ) {
		$class_html = sprintf(
			' class="%s"',
			esc_attr( $css_class )
		);

		if ( 'current' === $css_class ) {
			$aria_current = ' aria-current="page"';
		}
	}

	return sprintf(
		'<a href="%s"%s%s>%s</a>',
		esc_url( $url ),
		$class_html,
		$aria_current,
		$link_text
	);
}

/**
 * The origin of Tiktok Seller Center in different countries are different
 * We need to set the origin according to the country
 */
function get_tts_seller_center_origin() {
	$country_origin_map = [
		'GB' => 'uk',
		'US' => 'us',
		'ID' => 'id',
		'TH' => 'th',
		'MY' => 'my',
		'VN' => 'vn',
		'PH' => 'ph',
	];
	$country            = WC()->countries->get_base_country();

	return "https://seller-{$country_origin_map[$country]}.tiktok.com";
}
