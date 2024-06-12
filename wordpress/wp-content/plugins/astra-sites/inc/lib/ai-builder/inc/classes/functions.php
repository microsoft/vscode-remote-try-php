<?php
/**
 * Functions
 *
 * @since  1.0.0
 * @package Astra Sites
 */

use STImporter\Importer\ST_Importer_Helper;
use STImporter\Importer\ST_Importer_File_System;

if ( ! function_exists( 'astra_sites_error_log' ) ) :

	/**
	 * Error Log
	 *
	 * A wrapper function for the error_log() function.
	 *
	 * @since 2.0.0
	 *
	 * @param  mixed $message Error message.
	 * @return void
	 */
	function astra_sites_error_log( $message = '' ) {
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			if ( is_array( $message ) ) {
				$message = wp_json_encode( $message );
			}

			if ( apply_filters( 'astra_sites_debug_logs', false ) ) {
				error_log( $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- This is for the debug logs while importing. This is conditional and will not be logged in the debug.log file for normal users.
			}
		}
	}

endif;

if ( ! function_exists( 'astra_sites_get_suggestion_link' ) ) :
	/**
	 *
	 * Get suggestion link.
	 *
	 * @since 2.6.1
	 *
	 * @return suggestion link.
	 */
	function astra_sites_get_suggestion_link() {

		$white_label_link = 'https://wpastra.com/sites-suggestions/?utm_source=demo-import-panel&utm_campaign=astra-sites&utm_medium=suggestions';

		return apply_filters( 'astra_sites_suggestion_link', $white_label_link );
	}
endif;

if ( ! function_exists( 'astra_sites_is_valid_image' ) ) :
	/**
	 * Check for the valid image
	 *
	 * @param string $link  The Image link.
	 *
	 * @since 2.6.2
	 * @return boolean
	 */
	function astra_sites_is_valid_image( $link = '' ) {
		return preg_match( '/^((https?:\/\/)|(www\.))([a-z0-9-].?)+(:[0-9]+)?\/[\w\-\@]+\.(jpg|png|gif|jpeg|svg)\/?$/i', $link );
	}
endif;

if ( ! function_exists( 'astra_get_site_data' ) ) :
	/**
	 * Returns the value of the index for the Site Data
	 *
	 * @param string $index  The index value of the data.
	 *
	 * @since 2.6.14
	 * @return mixed
	 */
	function astra_get_site_data( $index = '' ) {

		$demo_data = ST_Importer_File_System::get_instance()->get_demo_content();
		if ( ! empty( $demo_data ) && isset( $demo_data[ $index ] ) ) {
			return $demo_data[ $index ];
		}
		return '';
	}
endif;


/**
 * Get all the posts to be reset.
 *
 * @since 3.0.3
 * @return array
 */
function astra_sites_get_reset_post_data() {
	global $wpdb;

	$post_ids = $wpdb->get_col( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_astra_sites_imported_post'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- We need this to get all the posts and pages. Traditional WP_Query would have been expensive here.

	return $post_ids;
}



/**
 * Get API params
 *
 * @since 2.7.3
 * @return array
 */
function astra_sites_get_api_params() {
	return apply_filters(
		'astra_sites_api_params',
		array(
			'purchase_key'    => '',
			'site_url'        => get_site_url(),
			'per-page'        => 15,
			'template_status' => '',
			'version'         => ASTRA_SITES_VER,
		)
	);
}

/**
 * Check if Import for Astra Site is in progress
 *
 * @since 3.0.21
 * @return array
 */
function astra_sites_has_import_started() {
	$has_import_started = get_transient( 'astra_sites_import_started' );
	if ( 'yes' === $has_import_started ) {
		return true;
	}
	return false;
}

/**
 * Remove the post excerpt
 *
 * @param int $post_id  The post ID.
 * @since 3.1.0
 */
function astra_sites_empty_post_excerpt( $post_id = 0 ) {
	if ( ! $post_id ) {
		return;
	}

	wp_update_post(
		array(
			'ID'           => $post_id,
			'post_excerpt' => '',
		)
	);
}

/**
 * Get the WP Forms URL.
 *
 * @since 3.2.4
 * @param int $id  The template ID.
 * @return string
 */
function astra_sites_get_wp_forms_url( $id ) {
	$demo_data = get_option( 'astra_sites_import_elementor_data_' . $id, array() );
	if ( empty( $demo_data ) ) {
		return '';
	}

	if ( isset( $demo_data['type'] ) ) {
		$type = $demo_data['type'];
		if ( 'site-pages' === $type && isset( $demo_data['astra-site-wpforms-path'] ) ) {
			return $demo_data['astra-site-wpforms-path'];
		}

		if ( 'astra-blocks' === $type && isset( $demo_data['post-meta'] ) ) {
			return $demo_data['post-meta']['astra-site-wpforms-path'];
		}
	}

	return '';
}

/**
 * Check is valid URL
 *
 * @param string $url  The site URL.
 *
 * @since 2.7.1
 * @return string
 */
function astra_sites_is_valid_url( $url = '' ) {
	if ( empty( $url ) ) {
		return false;
	}

	$parse_url = wp_parse_url( $url );
	if ( empty( $parse_url ) || ! is_array( $parse_url ) ) {
		return false;
	}

	$valid_hosts = apply_filters(
		'astra_sites_valid_url',
		array(
			'lh3.googleusercontent.com',
			'pixabay.com',
		)
	);

	$ai_site_url = get_option( 'ast_ai_import_current_url', '' );

	if ( '' !== $ai_site_url ) {
		$url           = wp_parse_url( $ai_site_url );
		$valid_hosts[] = $url ? $url['host'] : '';
	}

	$api_domain_parse_url = wp_parse_url( ST_Importer_Helper::get_api_domain() );
	$valid_hosts[]        = $api_domain_parse_url['host'];

	// Validate host.
	if ( in_array( $parse_url['host'], $valid_hosts, true ) ) {
		return true;
	}

	return false;
}
