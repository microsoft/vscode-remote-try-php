<?php
/**
 * Cart Abandonment
 *
 * @package Woocommerce-Cart-Abandonment-Recovery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Cart abandonment tracking class.
 */
class Cartflows_Ca_Helper {



	/**
	 * Member Variable
	 *
	 * @var object instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 *  Constructor function that initializes required actions and hooks.
	 */
	public function __construct() {
	}

	/**
	 * Get checkout url.
	 *
	 * @param  integer $post_id    post id.
	 * @param  string  $token_data token data.
	 * @return string
	 */
	public function get_checkout_url( $post_id, $token_data ) {

		$token        = $this->wcf_generate_token( (array) $token_data );
		$global_param = get_option( 'wcf_ca_global_param', false );
		$checkout_url = get_permalink( $post_id );

		$token_param  = array(
			'wcf_ac_token' => $token,
		);
		$checkout_url = add_query_arg( $token_param, $checkout_url );

		if ( ! empty( $global_param ) ) {

			$query_param  = array();
			$global_param = preg_split( "/[\f\r\n]+/", $global_param );

			foreach ( $global_param as $key => $param ) {

				$param_parts                            = explode( '=', $param );
				$query_param[ trim( $param_parts[0] ) ] = trim( $param_parts[1] );
			}
			$checkout_url = add_query_arg( $query_param, $checkout_url );
		}

		return esc_url( $checkout_url );
	}

	/**
	 *  Geberate the token for the given data.
	 *
	 * @param array $data data.
	 */
	public function wcf_generate_token( $data ) {
		return urlencode( base64_encode( http_build_query( $data ) ) );
	}

	/**
	 * Get the acceptable order statuses.
	 */
	public function get_acceptable_order_statuses() {

		$acceptable_order_statuses = get_option( 'wcf_ca_excludes_orders' );

		if ( is_array( $acceptable_order_statuses ) && ! empty( $acceptable_order_statuses ) ) {
			$acceptable_order_statuses = array_map( 'strtolower', $acceptable_order_statuses );
		}

		return $acceptable_order_statuses;
	}

	/**
	 * Generate comma separated products.
	 *
	 * @param string $cart_contents user cart details.
	 */
	public function get_comma_separated_products( $cart_contents ) {
		$cart_comma_string = '';
		if ( ! $cart_contents ) {
			return $cart_comma_string;
		}
		$cart_data = maybe_unserialize( $cart_contents );

		$cart_length = count( $cart_data );
		$index       = 0;
		foreach ( $cart_data as $key => $product ) {

			if ( ! isset( $product['product_id'] ) ) {
				continue;
			}

			$cart_product = wc_get_product( $product['product_id'] );

			if ( $cart_product ) {
				$cart_comma_string = $cart_comma_string . $cart_product->get_title();
				if ( ( $cart_length - 2 ) === $index ) {
					$cart_comma_string = $cart_comma_string . ' & ';
				} elseif ( ( $cart_length - 1 ) !== $index ) {
					$cart_comma_string = $cart_comma_string . ', ';
				}
				$index++;
			}
		}
		return $cart_comma_string;

	}

	/**
	 * Count abandoned carts
	 *
	 * @since 1.1.5
	 */
	public function abandoned_cart_count() {
		global $wpdb;
		$cart_abandonment_table_name = $wpdb->prefix . CARTFLOWS_CA_CART_ABANDONMENT_TABLE;
		// Can't use placeholders for table/column names, it will be wrapped by a single quote (') instead of a backquote (`).
		$total_items = $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(`id`) FROM {$cart_abandonment_table_name}  WHERE `order_status` = %s", WCF_CART_ABANDONED_ORDER ) //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		); // db call ok; no cache ok.
		return $total_items;
	}

		/**
		 * Get start and end date for given interval.
		 *
		 * @param  string $interval interval .
		 * @return array
		 */
	public function get_start_end_by_interval( $interval ) {

		if ( 'today' === $interval ) {
			$start_date = gmdate( 'Y-m-d' );
			$end_date   = gmdate( 'Y-m-d' );
		} else {

			$days = $interval;

			$start_date = gmdate( 'Y-m-d', strtotime( '-' . $days . ' days' ) );
			$end_date   = gmdate( 'Y-m-d' );
		}

		return array(
			'start' => $start_date,
			'end'   => $end_date,
		);
	}

	/**
	 * Get the checkout details for the user.
	 *
	 * @param string $wcf_session_id checkout page session id.
	 * @since 1.0.0
	 */
	public function get_checkout_details( $wcf_session_id ) {
		global $wpdb;
		$cart_abandonment_table = $wpdb->prefix . CARTFLOWS_CA_CART_ABANDONMENT_TABLE;
		// Can't use placeholders for table/column names, it will be wrapped by a single quote (') instead of a backquote (`).
		$result = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$cart_abandonment_table} WHERE session_id = %s", $wcf_session_id ) //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		); // db call ok; no cache ok.
		return $result;
	}

	/**
	 * Fetch all the scheduled emails with templates for the specific session.
	 *
	 * @param string  $session_id session id.
	 * @param boolean $fetch_sent sfetch sent emails.
	 * @return array|object|null
	 */
	public function fetch_scheduled_emails( $session_id, $fetch_sent = false ) {
		global $wpdb;
		$email_history_table  = $wpdb->prefix . CARTFLOWS_CA_EMAIL_HISTORY_TABLE;
		$email_template_table = $wpdb->prefix . CARTFLOWS_CA_EMAIL_TEMPLATE_TABLE;
		// Can't use placeholders for table/column names, it will be wrapped by a single quote (') instead of a backquote (`).
		$query = $wpdb->prepare( "SELECT * FROM  {$email_history_table} as eht INNER JOIN {$email_template_table} as ett ON eht.template_id = ett.id WHERE ca_session_id = %s", sanitize_text_field( $session_id ) ); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( $fetch_sent ) {
			$query .= ' AND email_sent = 1';
		}
		// Query is prepared above.
		$result = $wpdb->get_results(
			$query //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		);// db call ok; no cache ok.
		return $result;
	}

	/**
	 * Sanitize text field.
	 *
	 * @param string $key field key to sanitize.
	 * @param string $method method type.
	 */
	public function sanitize_text_filter( $key, $method = 'POST' ) {

		$sanitized_value = '';
		//phpcs:disable WordPress.Security.NonceVerification
		if ( 'POST' === $method && isset( $_POST[ $key ] ) ) {
			$sanitized_value = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
		}

		if ( 'GET' === $method && isset( $_GET[ $key ] ) ) {
			$sanitized_value = sanitize_text_field( wp_unslash( $_GET[ $key ] ) );
		}
		//phpcs:enable WordPress.Security.NonceVerification
		return $sanitized_value;
	}

	/**
	 * Conditional file extentions.
	 *
	 * @return array
	 */
	public function get_js_file_ext() {

		return array(
			'folder'   => SCRIPT_DEBUG ? 'js' : 'min-js',
			'file_ext' => SCRIPT_DEBUG ? 'js' : 'min.js',
		);
	}

}

Cartflows_Ca_Helper::get_instance();
