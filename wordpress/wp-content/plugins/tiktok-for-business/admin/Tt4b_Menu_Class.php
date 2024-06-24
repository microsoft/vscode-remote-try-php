<?php
/**
 * Copyright (c) Bytedance, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * @package TikTok
 */
class Tt4b_Menu_Class {

	/**
	 * Loads the menu.
	 *
	 * @return void
	 */
	public static function tt4b_admin_menu() {
		 // if on woocommerce, load into woo marketing submenu. Else load into admin dashboard
		if ( did_action( 'woocommerce_loaded' ) > 0 ) {
			add_submenu_page(
				'woocommerce-marketing',
				'TikTok',
				'TikTok',
				'manage_woocommerce',
				'tiktok',
				[ 'Tt4b_Menu_Class', 'tt4b_admin_menu_main' ],
				5
			);
		} else {
			add_menu_page(
				'TikTok',
				'TikTok',
				'manage_options',
				'tiktok',
				[ 'Tt4b_Menu_Class', 'tt4b_admin_menu_main' ],
				'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IS0tIFVwbG9hZGVkIHRvOiBTVkcgUmVwbywgd3d3LnN2Z3JlcG8uY29tLCBHZW5lcmF0b3I6IFNWRyBSZXBvIE1peGVyIFRvb2xzIC0tPgo8c3ZnIGZpbGw9IiMwMDAwMDAiIHdpZHRoPSI4MDBweCIgaGVpZ2h0PSI4MDBweCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIGlkPSJpY29ucyIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNNDEyLjE5LDExOC42NmExMDkuMjcsMTA5LjI3LDAsMCwxLTkuNDUtNS41LDEzMi44NywxMzIuODcsMCwwLDEtMjQuMjctMjAuNjJjLTE4LjEtMjAuNzEtMjQuODYtNDEuNzItMjcuMzUtNTYuNDNoLjFDMzQ5LjE0LDIzLjksMzUwLDE2LDM1MC4xMywxNkgyNjcuNjlWMzM0Ljc4YzAsNC4yOCwwLDguNTEtLjE4LDEyLjY5LDAsLjUyLS4wNSwxLS4wOCwxLjU2LDAsLjIzLDAsLjQ3LS4wNS43MSwwLC4wNiwwLC4xMiwwLC4xOGE3MCw3MCwwLDAsMS0zNS4yMiw1NS41Niw2OC44LDY4LjgsMCwwLDEtMzQuMTEsOWMtMzguNDEsMC02OS41NC0zMS4zMi02OS41NC03MHMzMS4xMy03MCw2OS41NC03MGE2OC45LDY4LjksMCwwLDEsMjEuNDEsMy4zOWwuMS04My45NGExNTMuMTQsMTUzLjE0LDAsMCwwLTExOCwzNC41MiwxNjEuNzksMTYxLjc5LDAsMCwwLTM1LjMsNDMuNTNjLTMuNDgsNi0xNi42MSwzMC4xMS0xOC4yLDY5LjI0LTEsMjIuMjEsNS42Nyw0NS4yMiw4Ljg1LDU0Ljczdi4yYzIsNS42LDkuNzUsMjQuNzEsMjIuMzgsNDAuODJBMTY3LjUzLDE2Ny41MywwLDAsMCwxMTUsNDcwLjY2di0uMmwuMi4yQzE1NS4xMSw0OTcuNzgsMTk5LjM2LDQ5NiwxOTkuMzYsNDk2YzcuNjYtLjMxLDMzLjMyLDAsNjIuNDYtMTMuODEsMzIuMzItMTUuMzEsNTAuNzItMzguMTIsNTAuNzItMzguMTJhMTU4LjQ2LDE1OC40NiwwLDAsMCwyNy42NC00NS45M2M3LjQ2LTE5LjYxLDkuOTUtNDMuMTMsOS45NS01Mi41M1YxNzYuNDljMSwuNiwxNC4zMiw5LjQxLDE0LjMyLDkuNDFzMTkuMTksMTIuMyw0OS4xMywyMC4zMWMyMS40OCw1LjcsNTAuNDIsNi45LDUwLjQyLDYuOVYxMzEuMjdDNDUzLjg2LDEzMi4zNyw0MzMuMjcsMTI5LjE3LDQxMi4xOSwxMTguNjZaIi8+PC9zdmc+',
				40
			);
		}

		add_action( 'admin_enqueue_scripts', [ 'tt4b_menu_class', 'load_styles' ] );
	}

	/**
	 * Loads the plug-in page.
	 */
	public static function tt4b_admin_menu_main() {
		$logger      = new Logger();
		$mapi        = new Tt4b_Mapi_Class( $logger );
		$request_uri = '';
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$request_uri = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}
		// business profile data, to be replaced with response from business profile endpoint.
		$is_connected         = false;
		$business_platform    = 'WOO_COMMERCE';
		$external_business_id = get_option( 'tt4b_external_business_id' );
		if ( false === $external_business_id ) {
			$external_business_id = uniqid( 'tt4b_woocommerce_' );
			update_option( 'tt4b_external_business_id', $external_business_id );
		}
		// to ensure non-null. To be populated with real data.
		$advertiser_id     = '';
		$bc_id             = '';
		$catalog_id        = '';
		$pixel_code        = '';
		$processing        = 0;
		$approved          = 0;
		$rejected          = 0;
		$advanced_matching = false;
		$shop_name         = get_bloginfo( 'name' );
		$redirect_uri      = admin_url();

		$app_id            = get_option( 'tt4b_app_id' );
		$secret            = get_option( 'tt4b_secret' );
		$external_data_key = get_option( 'tt4b_external_data_key' );
		if ( false === $app_id || false === $secret || false === $external_data_key ) {
			// create open source app.
			$cleaned_redirect = preg_replace( '/[^A-Za-z0-9\-]/', '', admin_url() );
			$smb_id           = $external_business_id . $cleaned_redirect;
			$app_rsp          = $mapi->create_open_source_app( $smb_id, $business_platform, $redirect_uri );
			if ( false !== $app_rsp ) {
				$open_source_app_rsp = json_decode( $app_rsp, true );
				$app_id              = isset( $open_source_app_rsp['data']['app_id'] ) ? $open_source_app_rsp['data']['app_id'] : '';
				$secret              = isset( $open_source_app_rsp['data']['app_secret'] ) ? $open_source_app_rsp['data']['app_secret'] : '';
				$external_data_key   = isset( $open_source_app_rsp['data']['external_data_key'] ) ? $open_source_app_rsp['data']['external_data_key'] : '';
				$redirect_uri        = isset( $open_source_app_rsp['data']['redirect_uri'] ) ? $open_source_app_rsp['data']['redirect_uri'] : '';
				update_option( 'tt4b_app_id', $app_id );
				update_option( 'tt4b_secret', $secret );
				update_option( 'tt4b_external_data_key', $external_data_key );
			} else {
				wp_die( "An error occurred generating credentials needed for the integration. Please reach out to support <a href='https://ads.tiktok.com/athena/user-feedback/form?identify_key=6a1e079024806640c5e1e695d13db80949525168a052299b4970f9c99cb5ac78&board_id=1705151646989423'>here</a>." );
			}
		}

		$current_tiktok_for_woocommerce_version = get_option( 'tt4b_version' );
		$version                                = '1.5';
		$industry_id                            = '291408';
		$locale                                 = strtok( get_locale(), '_' );
		$now                                    = new DateTime();
		$timestamp                              = (string) ( $now->getTimestamp() * 1000 );
		$timezone                               = 'GMT+00:00';
		$gmt_offset                             = get_option( 'gmt_offset' );
		if ( $gmt_offset > 0 ) {
			$timezone = 'GMT+' . $gmt_offset . ':00';
		} elseif ( $gmt_offset < 0 ) {
			$timezone = 'GMT' . $gmt_offset . ':00';
		}

		$email       = get_option( 'admin_email' );
		$shop_url    = get_site_url();
		$shop_domain = get_site_url();
		$hmac_str    = 'version=' . $version . '&timestamp=' . $timestamp . '&locale=' . $locale .
					'&business_platform=' . $business_platform . '&external_business_id=' . $external_business_id;

		$hmac = hash_hmac( 'sha256', $hmac_str, $external_data_key );

		$obj = [
			'external_business_id' => $external_business_id,
			'business_platform'    => $business_platform,
			'locale'               => $locale,
			'version'              => $version,
			'timestamp'            => $timestamp,
			'timezone'             => $timezone,
			'email'                => $email,
			'industry_id'          => $industry_id,
			'store_name'           => $shop_name,
			'website_url'          => $shop_url,
			'domain'               => $shop_domain,
			'app_id'               => $app_id,
			'redirect_uri'         => $redirect_uri,
			'hmac'                 => $hmac,
			'close_method'         => 'redirect_inside_tiktok',
			'extra_data'           => $current_tiktok_for_woocommerce_version,
		];

		$obj = self::get_country_and_currency_for_external_data( $obj );
		$obj = self::get_eligibility_info_for_external_data( $obj );
		$obj = self::get_address_info_for_external_data( $obj );
		$obj = self::get_extra_data_for_external_data( $obj );

		$external_data = base64_encode( json_encode( $obj, JSON_UNESCAPED_SLASHES ) );
		update_option( 'tt4b_external_data', $external_data );
		// log the external_data for ease of debugging.
		$logger->log( __METHOD__, 'external_data: ' . $external_data );

		$access_token = get_option( 'tt4b_access_token' );
		if ( false !== $access_token ) {
			$is_connected = true;
			// get business profile information to pass into external data.
			$business_profile_rsp = $mapi->get_business_profile( $access_token, $external_business_id );
			$business_profile     = json_decode( $business_profile_rsp, true );
			// Check connection status against profile status
			if ( [] === $business_profile['data'] ) {
				$is_connected = false;
				delete_option( 'tt4b_access_token' );
			} elseif ( ! is_null( $business_profile['data']['status'] ) && 2 !== $business_profile['data']['status'] ) {
				$is_connected = false;
			} else {
				if ( ! is_null( $business_profile['data']['adv_id'] ) ) {
					$advertiser_id = $business_profile['data']['adv_id'];
					update_option( 'tt4b_advertiser_id', $advertiser_id );
				}
				if ( ! is_null( $business_profile['data']['bc_id'] ) ) {
					$bc_id = $business_profile['data']['bc_id'];
					update_option( 'tt4b_bc_id', $bc_id );
				}
				if ( ! is_null( $business_profile['data']['pixel_code'] ) ) {
					$pixel_code = $business_profile['data']['pixel_code'];
					update_option( 'tt4b_pixel_code', $pixel_code );
				}
				if ( ! is_null( $business_profile['data'] ) && array_key_exists( 'catalog_id', $business_profile['data'] ) && ! is_null( $business_profile['data']['catalog_id'] ) ) {
					$catalog_id = $business_profile['data']['catalog_id'];
					update_option( 'tt4b_catalog_id', $catalog_id );
				}

				if ( ! is_null( $business_profile['data'] ) && array_key_exists( 'catalog_id', $business_profile['data'] ) && ! is_null( $business_profile['data']['catalog_id'] ) && array_key_exists( 'bc_id', $business_profile['data'] ) && ! is_null( $business_profile['data']['bc_id'] ) ) {
					if ( did_action( 'woocommerce_loaded' ) > 0 ) {
						$catalog_obj = new Tt4b_Catalog_Class( $mapi, $logger );
						$logger->log( __METHOD__, 'initiate catalog sync started' );
						$catalog_obj->initiate_catalog_sync( $catalog_id, $bc_id, $shop_name, $access_token );
						$product_review_status = $catalog_obj->get_catalog_processing_status( $access_token, $bc_id, $catalog_id );
						$processing            = $product_review_status['processing'];
						$approved              = $product_review_status['approved'];
						$rejected              = $product_review_status['rejected'];
					}
				}
				if ( is_null( $advertiser_id )
					|| is_null( $pixel_code )
					|| is_null( $access_token )
				) {
					// set advanced matching to false if the pixel cannot be found.
					$advanced_matching = false;
				} else {
					$pixel_obj = new Tt4b_Pixel_Class();
					$pixel_rsp = $pixel_obj->get_pixels(
						$access_token,
						$advertiser_id,
						$pixel_code
					);
					$pixel     = json_decode( $pixel_rsp, true );
					if ( '' !== $pixel ) {
						$connected_pixel   = $pixel['data']['pixels'][0];
						$advanced_matching = $connected_pixel['advanced_matching_fields']['email'];
						update_option( 'tt4b_advanced_matching', $advanced_matching );
					}
				}

				// update eligibility
				$total_gmv              = intval( get_option( 'tt4b_mapi_total_gmv' ) );
				$total_orders           = intval( get_option( 'tt4b_mapi_total_orders' ) );
				$days_since_first_order = intval( get_option( 'tt4b_mapi_tenure' ) );
				$mapi->update_business_profile( $access_token, $external_business_id, $total_gmv, $total_orders, $days_since_first_order, $current_tiktok_for_woocommerce_version );
			}
		}

		// enqueue js.
		echo '<div class="tt4b_wrap" id="tiktok-for-business-root"></div>';
		wp_register_script( 'tt4b_cdn', 'https://sf-ttmp.ttcdn-row.com/obj/static-us/tiktok-business-plugin/tbp_external_platform-v2.3.11.js', '', 'v1', false );
		wp_register_script( 'tt4b_script', plugins_url( '/admin/js/localJs.js', dirname( __DIR__ ) . '/Tiktokforbusiness.php' ), [ 'tt4b_cdn' ], 'v1', false );
		wp_enqueue_script( 'tt4b_script' );
		wp_localize_script(
			'tt4b_script',
			'tt4b_script_vars',
			[
				'is_connected'         => $is_connected,
				'external_business_id' => $external_business_id,
				'business_platform'    => $business_platform,
				'base_uri'             => $request_uri,
				'external_data'        => $external_data,
				'bc_id'                => $bc_id,
				'adv_id'               => $advertiser_id,
				'store_name'           => $shop_name,
				'pixel_code'           => $pixel_code,
				'catalog_id'           => $catalog_id,
				'advanced_matching'    => $advanced_matching,
				'approved'             => $approved,
				'processing'           => $processing,
				'rejected'             => $rejected,
				'country'              => get_option( 'tt4b_user_country' ),
			]
		);
	}

	/**
	 * Populates eligibility for woocommerce merchants.
	 */
	public static function get_eligibility_info_for_external_data( $external_data ) {
		// pull eligibility
		// pull eligibility metrics for tt shopping.
		if ( ! did_action( 'woocommerce_loaded' ) > 0 ) {
			return $external_data;
		}
		$logger = new Logger();
		$mapi   = new Tt4b_Mapi_Class( $logger );
		$mapi->fetch_eligibility();
		$total_gmv              = intval( get_option( 'tt4b_mapi_total_gmv' ) );
		$total_orders           = intval( get_option( 'tt4b_mapi_total_orders' ) );
		$days_since_first_order = intval( get_option( 'tt4b_mapi_tenure' ) );
		$net_gmv                = [
			[
				'interval' => 'LIFETIME',
				'min'      => $total_gmv,
				'max'      => $total_gmv,
				'unit'     => 'CURRENCY',
			],
		];
		$net_order_count        = [
			[
				'interval' => 'LIFETIME',
				'min'      => $total_orders,
				'max'      => $total_orders,
				'unit'     => 'COUNT',
			],
		];
		$tenure                 = [
			'min'  => $days_since_first_order,
			'unit' => 'DAYS',
		];

		$external_data['is_email_verified'] = true;
		$external_data['is_verified']       = true;
		$external_data['net_gmv']           = $net_gmv;
		$external_data['net_order_count']   = $net_order_count;
		$external_data['tenure']            = $tenure;

		return $external_data;
	}

	/**
	 * Populates the extra data for external data.
	 */
	public static function get_extra_data_for_external_data( $external_data ) {
		$current_tiktok_for_woocommerce_version = get_option( 'tt4b_version' );
		$extra_data                             = [
			'version' => $current_tiktok_for_woocommerce_version,
		];

		if ( ! did_action( 'woocommerce_loaded' ) > 0 ) {
			$extra_data['plugin_type'] = 'LEAD_GEN';
		}

		$external_data['extra_data'] = json_encode( $extra_data );
		return $external_data;
	}

	/**
	 * Populates address info for woocommerce merchants.
	 */
	public static function get_address_info_for_external_data( $external_data ) {
		if ( did_action( 'woocommerce_loaded' ) > 0 ) {
			$store_address   = get_option( 'woocommerce_store_address' );
			$store_address_2 = get_option( 'woocommerce_store_address_2' );
			$store_city      = get_option( 'woocommerce_store_city' );
			$store_postcode  = get_option( 'woocommerce_store_postcode' );

			// country and state separated.
			$store_raw_country = get_option( 'woocommerce_default_country' );
			$split_country     = explode( ':', $store_raw_country );
			$store_state       = '';
			if ( count( $split_country ) > 1 ) {
				$store_state = $split_country[1];
				$menu_obj    = new Tt4b_Menu_Class();
				$store_state = $menu_obj->convert_state( $store_state );
			}

			$external_data['address_1'] = $store_address;
			$external_data['address_2'] = $store_address_2;
			$external_data['city']      = $store_city;
			$external_data['state']     = $store_state;
			$external_data['zip_code']  = $store_postcode;

		}

		return $external_data;

	}

	/**
	 * Populates currency and country_iso for woocommerce merchants.
	 */
	public static function get_country_and_currency_for_external_data( $external_data ) {
		if ( did_action( 'woocommerce_loaded' ) > 0 ) {
			$external_data['currency']       = get_woocommerce_currency();
			$external_data['country_region'] = WC()->countries->get_base_country();
			update_option( 'tt4b_user_country', WC()->countries->get_base_country() );
			return $external_data;
		}

		$country_region = self::get_user_country();
		if ( '' !== $country_region ) {
			$external_data['country_region'] = $country_region;
		}
		update_option( 'tt4b_user_country', $country_region );

		return $external_data;
	}

	/**
	 * Gets the user country.
	 *
	 * @return string
	 */
	private static function get_user_country() {
		$logger       = new Logger();
		$mapi         = new Tt4b_Mapi_Class( $logger );
		$resp         = $mapi->get_user_location();
		$decoded_resp = json_decode( $resp, true );
		if ( ! is_null( $decoded_resp['data'] && ! is_null( $decoded_resp['data']['country_code'] ) ) ) {
			return $decoded_resp['data']['country_code'];
		}
		return '';
	}

	/**
	 * Loads content from TikTok CDN into the page head.
	 *
	 * @param $hook_suffix string The hook used
	 *
	 * @return void
	 */
	public static function load_styles( $hook_suffix = '' ) {
		if ( 'marketing_page_tiktok' !== $hook_suffix ) {
			return;
		}
		wp_enqueue_style( 'tt4b_localCss', plugins_url( '/admin/css/main.css', dirname( __DIR__ ) . '/tiktok-for-woocommerce.php' ), '', 'v1' );
	}

	/**
	 * Stores the mapi issued access token.
	 *
	 * @return void
	 */
	public static function tt4b_store_access_token() {
		$logger = new Logger();
		$mapi   = new Tt4b_Mapi_Class( $logger );
		$url    = '';
		if ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
			$url = esc_url_raw( wp_unslash( $_SERVER['HTTP_HOST'] ) . wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}
		$auth_code        = '';
		$split_url        = explode( '&', $url );
		$split_url_params = count( $split_url );
		for ( $i = 0; $i < $split_url_params; $i++ ) {
			if ( false !== strpos( $split_url[ $i ], 'auth_code' ) ) {
				$auth_code = substr( $split_url[ $i ], strpos( $split_url[ $i ], '=' ) + 1 );
				$logger->log( __METHOD__, "auth_code retrieved: $auth_code" );
			}
		}
		if ( '' !== $auth_code ) {
			$app_id           = get_option( 'tt4b_app_id' );
			$secret           = get_option( 'tt4b_secret' );
			$access_token_rsp = $mapi->get_access_token( $app_id, $secret, $auth_code, 'v1.2' );
			$results          = json_decode( $access_token_rsp, true );
			if ( 'OK' === $results['message'] ) {
				// status OK.
				$access_token = $results['data']['access_token'];
				update_option( 'tt4b_access_token', $access_token );
				wp_safe_redirect( get_admin_url() . 'admin.php?page=tiktok' );
			}
		}
	}

	/**
	 * Converts state abbreviation to full state name.
	 *
	 * @param string $name The state abbreviation or name
	 *
	 * @return string
	 */
	public static function convert_state( $name ) {
		$states  = [
			[
				'name' => 'Alabama',
				'abbr' => 'AL',
			],
			[
				'name' => 'Alaska',
				'abbr' => 'AK',
			],
			[
				'name' => 'Arizona',
				'abbr' => 'AZ',
			],
			[
				'name' => 'Arkansas',
				'abbr' => 'AR',
			],
			[
				'name' => 'California',
				'abbr' => 'CA',
			],
			[
				'name' => 'Colorado',
				'abbr' => 'CO',
			],
			[
				'name' => 'Connecticut',
				'abbr' => 'CT',
			],
			[
				'name' => 'District of Columbia',
				'abbr' => 'DC',
			],
			[
				'name' => 'Delaware',
				'abbr' => 'DE',
			],
			[
				'name' => 'Florida',
				'abbr' => 'FL',
			],
			[
				'name' => 'Georgia',
				'abbr' => 'GA',
			],
			[
				'name' => 'Hawaii',
				'abbr' => 'HI',
			],
			[
				'name' => 'Idaho',
				'abbr' => 'ID',
			],
			[
				'name' => 'Illinois',
				'abbr' => 'IL',
			],
			[
				'name' => 'Indiana',
				'abbr' => 'IN',
			],
			[
				'name' => 'Iowa',
				'abbr' => 'IA',
			],
			[
				'name' => 'Kansas',
				'abbr' => 'KS',
			],
			[
				'name' => 'Kentucky',
				'abbr' => 'KY',
			],
			[
				'name' => 'Louisiana',
				'abbr' => 'LA',
			],
			[
				'name' => 'Maine',
				'abbr' => 'ME',
			],
			[
				'name' => 'Maryland',
				'abbr' => 'MD',
			],
			[
				'name' => 'Massachusetts',
				'abbr' => 'MA',
			],
			[
				'name' => 'Michigan',
				'abbr' => 'MI',
			],
			[
				'name' => 'Minnesota',
				'abbr' => 'MN',
			],
			[
				'name' => 'Mississippi',
				'abbr' => 'MS',
			],
			[
				'name' => 'Missouri',
				'abbr' => 'MO',
			],
			[
				'name' => 'Montana',
				'abbr' => 'MT',
			],
			[
				'name' => 'Nebraska',
				'abbr' => 'NE',
			],
			[
				'name' => 'Nevada',
				'abbr' => 'NV',
			],
			[
				'name' => 'New Hampshire',
				'abbr' => 'NH',
			],
			[
				'name' => 'New Jersey',
				'abbr' => 'NJ',
			],
			[
				'name' => 'New Mexico',
				'abbr' => 'NM',
			],
			[
				'name' => 'New York',
				'abbr' => 'NY',
			],
			[
				'name' => 'North Carolina',
				'abbr' => 'NC',
			],
			[
				'name' => 'North Dakota',
				'abbr' => 'ND',
			],
			[
				'name' => 'Ohio',
				'abbr' => 'OH',
			],
			[
				'name' => 'Oklahoma',
				'abbr' => 'OK',
			],
			[
				'name' => 'Oregon',
				'abbr' => 'OR',
			],
			[
				'name' => 'Pennsylvania',
				'abbr' => 'PA',
			],
			[
				'name' => 'Rhode Island',
				'abbr' => 'RI',
			],
			[
				'name' => 'South Carolina',
				'abbr' => 'SC',
			],
			[
				'name' => 'South Dakota',
				'abbr' => 'SD',
			],
			[
				'name' => 'Tennessee',
				'abbr' => 'TN',
			],
			[
				'name' => 'Texas',
				'abbr' => 'TX',
			],
			[
				'name' => 'Utah',
				'abbr' => 'UT',
			],
			[
				'name' => 'Vermont',
				'abbr' => 'VT',
			],
			[
				'name' => 'Virginia',
				'abbr' => 'VA',
			],
			[
				'name' => 'Washington',
				'abbr' => 'WA',
			],
			[
				'name' => 'West Virginia',
				'abbr' => 'WV',
			],
			[
				'name' => 'Wisconsin',
				'abbr' => 'WI',
			],
			[
				'name' => 'Wyoming',
				'abbr' => 'WY',
			],
			[
				'name' => 'Virgin Islands',
				'abbr' => 'V.I.',
			],
			[
				'name' => 'Guam',
				'abbr' => 'GU',
			],
			[
				'name' => 'Puerto Rico',
				'abbr' => 'PR',
			],
		];
		$return  = '';
		$str_len = strlen( $name );

		foreach ( $states as $state ) :
			if ( $str_len < 2 ) {
				return '';
			} elseif ( 2 === $str_len ) {
				if ( strtolower( $state['abbr'] ) === strtolower( $name ) ) {
					$return = $state['name'];
					break;
				}
			} elseif ( strtolower( $state['name'] ) === strtolower( $name ) ) {
					$return = strtoupper( $state['abbr'] );
					break;
			}
		endforeach;

		return $return;
	}
}
