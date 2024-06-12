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
class Cartflows_Ca_Tracking {



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

		$this->define_cart_abandonment_constants();

		// Adding the styles and scripts for the cart abandonment.
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_cart_abandonment_script' ), 20 );

		if ( wcf_ca()->utils->is_cart_abandonment_tracking_enabled() && ! isset( $_COOKIE['wcf_ca_skip_track_data'] ) ) {

			// Add script to track the cart abandonment.
			add_action( 'woocommerce_after_checkout_form', array( $this, 'cart_abandonment_tracking_script' ) );

			// Store user details from the current checkout page.
			add_action( 'wp_ajax_cartflows_save_cart_abandonment_data', array( $this, 'save_cart_abandonment_data' ) );
			add_action( 'wp_ajax_nopriv_cartflows_save_cart_abandonment_data', array( $this, 'save_cart_abandonment_data' ) );

			// Delete the stored cart abandonment data once order gets created.
			add_action( 'woocommerce_new_order', array( $this, 'delete_cart_abandonment_data' ) );
			add_action( 'woocommerce_thankyou', array( $this, 'delete_cart_abandonment_data' ) );
			add_action( 'woocommerce_order_status_changed', array( $this, 'wcf_ca_update_order_status' ), 999, 3 );

			// Adding filter to restore the data if recreating abandonment order.
			add_action( 'wp', array( $this, 'restore_cart_abandonment_data' ), 10 );
			add_action( 'wp', array( $this, 'unsubscribe_cart_abandonment_emails' ), 10 );

			// Adding notice to checkout page to inform about test email checkout page.
			add_action( 'woocommerce_before_checkout_form', array( $this, 'test_email_checkout_page' ), 9 );

			add_action( 'cartflows_ca_update_order_status_action', array( $this, 'update_order_status' ) );

		}

	}

		/**
		 *  Initialise all the constants
		 */
	public function define_cart_abandonment_constants() {
		define( 'CARTFLOWS_CART_ABANDONMENT_TRACKING_DIR', CARTFLOWS_CA_DIR . 'modules/cart-abandonment/' );
		define( 'CARTFLOWS_CART_ABANDONMENT_TRACKING_URL', CARTFLOWS_CA_URL . 'modules/cart-abandonment/' );
		define( 'WCF_CART_ABANDONED_ORDER', 'abandoned' );
		define( 'WCF_CART_COMPLETED_ORDER', 'completed' );
		define( 'WCF_CART_LOST_ORDER', 'lost' );
		define( 'WCF_CART_NORMAL_ORDER', 'normal' );
		define( 'WCF_CART_FAILED_ORDER', 'failed' );
		define( 'CARTFLOWS_ZAPIER_ACTION_AFTER_TIME', 1800 );

		define( 'WCF_ACTION_ABANDONED_CARTS', 'abandoned_carts' );
		define( 'WCF_ACTION_RECOVERED_CARTS', 'recovered_carts' );
		define( 'WCF_ACTION_LOST_CARTS', 'lost_carts' );
		define( 'WCF_ACTION_SETTINGS', 'settings' );
		define( 'WCF_ACTION_REPORTS', 'reports' );

		define( 'WCF_SUB_ACTION_REPORTS_VIEW', 'view' );
		define( 'WCF_SUB_ACTION_REPORTS_RESCHEDULE', 'reschedule' );

		define( 'WCF_DEFAULT_CUT_OFF_TIME', 15 );
		define( 'WCF_DEFAULT_COUPON_AMOUNT', 10 );

		define( 'WCF_CA_DATETIME_FORMAT', 'Y-m-d H:i:s' );

		define( 'WCF_CA_COUPON_DESCRIPTION', __( 'This coupon is for abandoned cart email templates.', 'woo-cart-abandonment-recovery' ) );
		define( 'WCF_CA_COUPON_GENERATED_BY', 'woo-cart-abandonment-recovery' );
	}

	/**
	 * This function will send the email to the store admin when any abandoned cart email recovered.
	 *
	 * @param int | string $order_id Order id.
	 * @param string       $wcar_old_status Old status of the order.
	 * @param string       $wcar_new_status New status of the order.
	 */
	public function wcar_send_successful_recovery_email_to_admin( $order_id, $wcar_old_status, $wcar_new_status ) {
		global $woocommerce;

		if ( in_array( $wcar_old_status, array( 'pending', 'failed', 'on-hold' ), true ) &&
				in_array( $wcar_new_status, array( 'processing', 'completed' ), true )
			) {
			$user_id = get_current_user_id();
			$order   = wc_get_order( $order_id );
			if ( version_compare( $woocommerce->version, '3.0.0', '>=' ) ) {
					$user_id = $order->get_user_id();
			} else {
				$user_id = $order->user_id;
			}

			$is_recoverd = $this->wcar_check_order_is_recovered( $order_id );

			if ( $is_recoverd ) {
				$order = wc_get_order( $order_id );
				/* translators: %d order id */
				$email_heading = sprintf( __( 'New Customer Order - Recovered Order ID: %d', 'woo-cart-abandonment-recovery' ), $order_id );
				$blogname      = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
				/* translators: %d order id */
				$email_subject = sprintf( __( 'New Customer Order - Recovered Order ID: %d', 'woo-cart-abandonment-recovery' ), $order_id );
				$user_email    = get_option( 'admin_email' );
				$headers[]     = 'From: Admin <' . $user_email . '>';
				$headers[]     = 'Content-Type: text/html';

				ob_start();
				wc_get_template(
					'emails/admin-new-order.php',
					array(
						'order'              => $order,
						'email_heading'      => $email_heading,
						'sent_to_admin'      => false,
						'plain_text'         => false,
						'email'              => true,
						'additional_content' => '',
					)
				);

				$email_body = ob_get_clean();
				wc_mail( $user_email, $email_subject, $email_body, $headers );
			}
		}
	}

	/**
	 * This function will check if cart is recoverd from woocommerce and WCAR.
	 *
	 * @param int $order_id order id.
	 */
	public function wcar_check_order_is_recovered( $order_id ) {

		global $wpdb;
		$order                       = wc_get_order( $order_id );
		$email                       = $order->get_billing_email();
		$cart_abandonment_table_name = $wpdb->prefix . CARTFLOWS_CA_CART_ABANDONMENT_TABLE;
		// Can't use placeholders for table/column names, it will be wrapped by a single quote (') instead of a backquote (`).
		$wcar_status = $wpdb->get_var(
			$wpdb->prepare( "SELECT `order_status` FROM {$cart_abandonment_table_name}  WHERE `email` = %s", $email ) //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		); // db call ok; no cache ok.
		$woo_status  = $order->get_status();

		if ( 'completed' === $wcar_status && in_array( $woo_status, array( 'completed', 'processing' ), true ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Update the Order status.
	 *
	 * @param integer $order_id order id.
	 * @param string  $old_order_status old order status.
	 * @param string  $new_order_status new order status.
	 */
	public function wcf_ca_update_order_status( $order_id, $old_order_status, $new_order_status ) {

		$acceptable_order_statuses = Cartflows_Ca_Helper::get_instance()->get_acceptable_order_statuses();

		if ( ( WCF_CART_FAILED_ORDER === $new_order_status ) ) {
			return;
		}

		if ( $order_id && is_array( $acceptable_order_statuses ) && ! empty( $acceptable_order_statuses ) && in_array( $new_order_status, $acceptable_order_statuses, true ) ) {

			$order = wc_get_order( $order_id );

			$order_email   = $order->get_billing_email();
			$captured_data = ( WCF_CART_FAILED_ORDER === $new_order_status ) ? $this->get_tracked_data_without_status( $order_email ) : $this->get_captured_data_by_email( $order_email );

			if ( $captured_data && is_object( $captured_data ) ) {
				$capture_status = $captured_data->order_status;
				global $wpdb;
				$cart_abandonment_table = $wpdb->prefix . CARTFLOWS_CA_CART_ABANDONMENT_TABLE;

				if ( ( WCF_CART_NORMAL_ORDER === $capture_status ) ) {
					$wpdb->delete( $cart_abandonment_table, array( 'session_id' => sanitize_key( $captured_data->session_id ) ) ); // db call ok; no cache ok.
				}

				if ( ( WCF_CART_ABANDONED_ORDER === $capture_status || WCF_CART_LOST_ORDER === $capture_status ) ) {
					$this->skip_future_emails_when_order_is_completed( sanitize_key( $captured_data->session_id ) );
					$this->trigger_zapier_webhook( $captured_data->session_id, WCF_CART_COMPLETED_ORDER );
					$note = __( 'This order was abandoned & subsequently recovered.', 'woo-cart-abandonment-recovery' );
					$order->add_order_note( $note );
					$order->save();
					if ( WC()->session ) {
						WC()->session->__unset( 'wcf_session_id' );
					}
				}
			}
			$wcar_email_admin_recovery = get_option( 'wcar_email_admin_on_recovery' );
			if ( 'on' === $wcar_email_admin_recovery ) {
				$this->wcar_send_successful_recovery_email_to_admin( $order_id, $old_order_status, $new_order_status );
			}
		}

	}

	/**
	 *  Unsubscribe the user from the mailing list.
	 */
	public function unsubscribe_cart_abandonment_emails() {

		$unsubscribe  = filter_input( INPUT_GET, 'unsubscribe', FILTER_VALIDATE_BOOLEAN );
		$wcf_ac_token = Cartflows_Ca_Helper::get_instance()->sanitize_text_filter( 'wcf_ac_token', 'GET' );

		if ( $unsubscribe && $this->is_valid_token( $wcf_ac_token ) ) {
			$token_data = $this->wcf_decode_token( $wcf_ac_token );
			if ( isset( $token_data['wcf_session_id'] ) ) {
				$session_id = $token_data['wcf_session_id'];

				global $wpdb;
				$cart_abandonment_table = $wpdb->prefix . CARTFLOWS_CA_CART_ABANDONMENT_TABLE;
				$wpdb->update(
					$cart_abandonment_table,
					array( 'unsubscribed' => true ),
					array( 'session_id' => $session_id )
				); // db call ok; no cache ok.

				$unsubscribe_notice = apply_filters(
					'woo_ca_recovery_email_unsubscribe_notice',
					__( 'You have successfully unsubscribed from our email list.', 'woo-cart-abandonment-recovery' )
				);

				wp_die( esc_html( $unsubscribe_notice ), esc_html__( 'Unsubscribed', 'woo-cart-abandonment-recovery' ) );
			}
		}
	}

	/**
	 * Restore cart abandonemnt data on checkout page.
	 */
	public function restore_cart_abandonment_data() {
		global $woocommerce;
		$result = array();
		// Restore only of user is not logged in.
		$wcf_ac_token = Cartflows_Ca_Helper::get_instance()->sanitize_text_filter( 'wcf_ac_token', 'GET' );

		if ( $this->is_valid_token( $wcf_ac_token ) ) {

			// Check if `wcf_restore_token` exists to restore cart data.
			$token_data = $this->wcf_decode_token( $wcf_ac_token );
			if ( is_array( $token_data ) && isset( $token_data['wcf_session_id'] ) ) {
				$result = Cartflows_Ca_Helper::get_instance()->get_checkout_details( $token_data['wcf_session_id'] );
				if ( isset( $result ) && WCF_CART_ABANDONED_ORDER === $result->order_status || WCF_CART_LOST_ORDER === $result->order_status ) {
					WC()->session->set( 'wcf_session_id', $token_data['wcf_session_id'] );
				}
			}

			if ( $result ) {
				$cart_content = maybe_unserialize( $result->cart_contents );

				if ( $cart_content ) {
					$woocommerce->cart->empty_cart();
					wc_clear_notices();
					foreach ( $cart_content as $cart_item ) {

						$cart_item_data = array();
						$variation_data = array();
						$id             = $cart_item['product_id'];
						$qty            = $cart_item['quantity'];

						// Skip bundled products when added main product.
						if ( isset( $cart_item['bundled_by'] ) ) {
							continue;
						}

						if ( isset( $cart_item['variation'] ) ) {
							foreach ( $cart_item['variation']  as $key => $value ) {
								$variation_data[ $key ] = $value;
							}
						}

						$cart_item_data = $cart_item;

						$woocommerce->cart->add_to_cart( $id, $qty, $cart_item['variation_id'], $variation_data, $cart_item_data );
					}

					if ( isset( $token_data['wcf_coupon_code'] ) && ! $woocommerce->cart->applied_coupons ) {
						$woocommerce->cart->add_discount( $token_data['wcf_coupon_code'] );
					}
				}
				$other_fields = maybe_unserialize( $result->other_fields );

				$parts = explode( ',', $other_fields['wcf_location'] );
				if ( count( $parts ) > 1 ) {
					$country = $parts[0];
					$city    = trim( $parts[1] );
				} else {
					$country = $parts[0];
					$city    = '';
				}

				foreach ( $other_fields as $key => $value ) {
					$key           = str_replace( 'wcf_', '', $key );
					$_POST[ $key ] = sanitize_text_field( $value );
				}
				$_POST['billing_first_name'] = sanitize_text_field( $other_fields['wcf_first_name'] );
				$_POST['billing_last_name']  = sanitize_text_field( $other_fields['wcf_last_name'] );
				$_POST['billing_phone']      = sanitize_text_field( $other_fields['wcf_phone_number'] );
				$_POST['billing_email']      = sanitize_email( $result->email );
				$_POST['billing_city']       = sanitize_text_field( $city );
				$_POST['billing_country']    = sanitize_text_field( $country );

				// Update the Cart Contents. This will be useful when there are product addons fields added in the cart data.
				$woocommerce->cart->set_cart_contents( $cart_content );

			}
		}
	}

	/**
	 * Add notice to inform user about test email checkout page.
	 */
	public function test_email_checkout_page() {

		$wcf_ac_token = Cartflows_Ca_Helper::get_instance()->sanitize_text_filter( 'wcf_ac_token', 'GET' );
		$token_data   = $this->wcf_decode_token( $wcf_ac_token );
		if ( is_checkout() && ! is_wc_endpoint_url() && isset( $token_data['wcf_preview_email'] ) && $token_data['wcf_preview_email'] ) {
			wc_print_notice( __( 'This checkout page is generated by WooCommerce Cart Abandonment Recovery plugin from test mail.', 'woo-cart-abandonment-recovery' ), 'notice' );
		}
	}


	/**
	 * Load cart abandonemnt tracking script.
	 *
	 * @return void
	 */
	public function cart_abandonment_tracking_script() {

		$wcf_ca_ignore_users = get_option( 'wcf_ca_ignore_users' );
		$current_user        = wp_get_current_user();
		$roles               = $current_user->roles;
		$role                = array_shift( $roles );
		if ( ! empty( $wcf_ca_ignore_users ) ) {
			foreach ( $wcf_ca_ignore_users as $user ) {
				$user = strtolower( $user );
				$role = preg_replace( '/_/', ' ', $role );
				if ( $role === $user ) {
					return;
				}
			}
		}

		global $post;
		wp_enqueue_script(
			'cartflows-cart-abandonment-tracking',
			CARTFLOWS_CART_ABANDONMENT_TRACKING_URL . 'assets/js/cart-abandonment-tracking.js',
			array( 'jquery' ),
			CARTFLOWS_CA_VER,
			true
		);

		$vars = array(
			'ajaxurl'                   => admin_url( 'admin-ajax.php' ),
			'_nonce'                    => wp_create_nonce( 'cartflows_save_cart_abandonment_data' ),
			'_gdpr_nonce'               => wp_create_nonce( 'cartflows_skip_cart_tracking_gdpr' ),
			'_post_id'                  => get_the_ID(),
			'_show_gdpr_message'        => ( wcf_ca()->utils->is_gdpr_enabled() && ! isset( $_COOKIE['wcf_ca_skip_track_data'] ) ),
			'_gdpr_message'             => get_option( 'wcf_ca_gdpr_message' ),
			'_gdpr_nothanks_msg'        => __( 'No Thanks', 'woo-cart-abandonment-recovery' ),
			'_gdpr_after_no_thanks_msg' => __( 'You won\'t receive further emails from us, thank you!', 'woo-cart-abandonment-recovery' ),
			'enable_ca_tracking'        => true,
		);

		wp_localize_script( 'cartflows-cart-abandonment-tracking', 'wcf_ca_vars', $vars );

	}

	/**
	 * Validate the token before use.
	 *
	 * @param  string $token token form the url.
	 * @return bool
	 */
	public function is_valid_token( $token ) {
		$is_valid   = false;
		$token_data = $this->wcf_decode_token( $token );
		if ( is_array( $token_data ) && array_key_exists( 'wcf_session_id', $token_data ) ) {
			$result = Cartflows_Ca_Helper::get_instance()->get_checkout_details( $token_data['wcf_session_id'] );
			if ( isset( $result ) ) {
				$is_valid = true;
			}
		}
		return $is_valid;
	}


	/**
	 * Execute Zapier webhook for further action inside Zapier.
	 *
	 * @since 1.0.0
	 */
	public function update_order_status() {

		global $wpdb;
		$cart_abandonment_table = $wpdb->prefix . CARTFLOWS_CA_CART_ABANDONMENT_TABLE;
		$email_history_table    = $wpdb->prefix . CARTFLOWS_CA_EMAIL_HISTORY_TABLE;
		$minutes                = wcf_ca()->utils->get_cart_abandonment_tracking_cut_off_time();
		$email_instance         = Cartflows_Ca_Email_Schedule::get_instance();

		/**
		 * Delete abandoned cart orders if empty.
		 */
		$this->delete_empty_abandoned_order();

		$wp_current_datetime = current_time( WCF_CA_DATETIME_FORMAT );
		$abandoned_ids       = $wpdb->get_results(
			$wpdb->prepare( "SELECT `session_id` FROM {$cart_abandonment_table} WHERE `order_status` = %s AND ADDDATE( `time`, INTERVAL %d MINUTE) <= %s", WCF_CART_NORMAL_ORDER, $minutes, $wp_current_datetime ), //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			ARRAY_A
		); // db call ok; no cache ok.

		foreach ( $abandoned_ids as $session_id ) {

			if ( isset( $session_id['session_id'] ) ) {

				$current_session_id = $session_id['session_id'];
				$email_instance->schedule_emails( $current_session_id );

				$coupon_code               = '';
				$wcf_ca_coupon_code_status = get_option( 'wcf_ca_coupon_code_status' );

				if ( 'on' === $wcf_ca_coupon_code_status ) {
					$discount_type        = get_option( 'wcf_ca_discount_type' );
					$discount_type        = $discount_type ? $discount_type : 'percent';
					$amount               = get_option( 'wcf_ca_coupon_amount' );
					$amount               = $amount ? $amount : WCF_DEFAULT_COUPON_AMOUNT;
					$coupon_expiry_date   = get_option( 'wcf_ca_coupon_expiry' );
					$coupon_expiry_unit   = get_option( 'wcf_ca_coupon_expiry_unit' );
					$coupon_expiry_date   = $coupon_expiry_date ? strtotime( $wp_current_datetime . ' +' . $coupon_expiry_date . ' ' . $coupon_expiry_unit ) : '';
					$free_shipping_coupon = get_option( 'wcf_ca_free_shipping_coupon' );
					$free_shipping        = ( isset( $free_shipping_coupon ) ) && ( $free_shipping_coupon->meta_value ) ? 'yes' : 'no';

					$individual_use_only = get_option( 'wcf_ca_individual_use_only' );
					$individual_use      = ( isset( $individual_use_only ) ) && ( $individual_use_only->meta_value ) ? 'yes' : 'no';

					$coupon_code = $email_instance->generate_coupon_code( $discount_type, $amount, $coupon_expiry_date, $free_shipping, $individual_use );
				}

				$wpdb->update(
					$cart_abandonment_table,
					array(
						'order_status' => WCF_CART_ABANDONED_ORDER,
						'coupon_code'  => $coupon_code,
					),
					array( 'session_id' => $current_session_id )
				); // db call ok; no cache ok.

				$this->trigger_zapier_webhook( $current_session_id, WCF_CART_ABANDONED_ORDER );

				$checkout_details = Cartflows_Ca_Helper::get_instance()->get_checkout_details( $current_session_id );
				do_action( 'wcf_ca_process_abandoned_order', $checkout_details );
			}
		}

		/**
		 * Send scheduled emails.
		 */
		$this->send_emails_to_callback();

		// Update order status to lost after campaign complete.
		// Can't use placeholders for table/column names, it will be wrapped by a single quote (') instead of a backquote (`).
        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$cart_abandonment_table} as ca SET order_status = 'lost' WHERE ca.order_status = %s AND DATE(ca.time) <= DATE_SUB( %s , INTERVAL 30 DAY)
              AND ( (SELECT count(*) FROM {$email_history_table} WHERE ca_session_id = ca.session_id ) =
              (SELECT count(*) FROM {$email_history_table} WHERE ca_session_id = ca.session_id AND email_sent = 1) )",
				WCF_CART_ABANDONED_ORDER,
				$wp_current_datetime
			)
		); // db call ok; no cache ok.
        // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		/**
		 * Delete garbage coupons.
		 */
		$wcf_ca_auto_delete_coupons = get_option( 'wcf_ca_auto_delete_coupons' );

		if ( isset( $wcf_ca_auto_delete_coupons ) && 'on' === $wcf_ca_auto_delete_coupons ) {
			Cartflows_Ca_Setting_Functions::get_instance()->delete_used_and_expired_coupons();
		}

	}

	/**
	 * Send zapier webhook.
	 *
	 * @param string $session_id   session id.
	 * @param string $order_status order status.
	 */
	public function trigger_zapier_webhook( $session_id, $order_status ) {

		$checkout_details = Cartflows_Ca_Helper::get_instance()->get_checkout_details( $session_id );

		if ( $checkout_details && wcf_ca()->utils->is_zapier_trigger_enabled() ) {
			$trigger_details = array();
			$url             = get_option( 'wcf_ca_zapier_cart_abandoned_webhook' );

			$other_details                       = maybe_unserialize( $checkout_details->other_fields );
			$trigger_details['first_name']       = $other_details['wcf_first_name'];
			$trigger_details['last_name']        = $other_details['wcf_last_name'];
			$trigger_details['phone_number']     = $other_details['wcf_phone_number'];
			$trigger_details['billing_address']  = $other_details['wcf_billing_company'] . ' ' . $other_details['wcf_billing_address_1'] . ', ' . $other_details['wcf_billing_state'] . ', ' . $other_details['wcf_location'] . ', ' . $other_details['wcf_billing_postcode'];
			$trigger_details['billing_address']  = trim( $trigger_details['billing_address'], ', ' );
			$trigger_details['shipping_address'] = $other_details['wcf_shipping_company'] . ' ' . $other_details['wcf_shipping_address_1'] . ', ' . $other_details['wcf_shipping_city'] . ', ' . $other_details['wcf_shipping_state'] . ', ' . $other_details['wcf_shipping_postcode'];
			$trigger_details['shipping_address'] = trim( $trigger_details['shipping_address'], ', ' );
			$trigger_details['email']            = $checkout_details->email;
			$token_data                          = array( 'wcf_session_id' => $checkout_details->session_id );
			$trigger_details['checkout_url']     = Cartflows_Ca_Helper::get_instance()->get_checkout_url( $checkout_details->checkout_id, $token_data );
			$trigger_details['product_names']    = Cartflows_Ca_Helper::get_instance()->get_comma_separated_products( $checkout_details->cart_contents );
			$trigger_details['coupon_code']      = $checkout_details->coupon_code;
			$trigger_details['order_status']     = $order_status;
			$trigger_details['cart_total']       = $checkout_details->cart_total;
			$trigger_details['product_table']    = Cartflows_Ca_Email_Schedule::get_instance()->get_email_product_block( $checkout_details->cart_contents, $checkout_details->cart_total );

			$trigger_details = apply_filters( 'woo_ca_webhook_trigger_details', $trigger_details );

			$parameters = http_build_query( $trigger_details );

			do_action( 'wcf_ca_before_trigger_webhook', $trigger_details, $checkout_details, $order_status );

			wp_remote_post(
				$url,
				array(
					'body'        => $parameters,
					'redirection' => '5',
					'httpversion' => '1.0',
					'blocking'    => true,
					'headers'     => array(),
					'cookies'     => array(),
				)
			);

		}
	}


	/**
	 * Sanitize post array.
	 *
	 * @param string $action action name to verify nonce.
	 *
	 * @return array
	 */
	public function sanitize_post_data( $action ) {

		check_ajax_referer( $action, 'security' );

		$input_post_values = array(
			'wcf_billing_company'     => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_email'               => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_EMAIL,
			),
			'wcf_billing_address_1'   => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_billing_address_2'   => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_billing_state'       => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_billing_postcode'    => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_shipping_first_name' => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_shipping_last_name'  => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_shipping_company'    => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_shipping_country'    => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_shipping_address_1'  => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_shipping_address_2'  => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_shipping_city'       => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_shipping_state'      => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_shipping_postcode'   => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_order_comments'      => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_name'                => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_surname'             => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_phone'               => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_country'             => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_city'                => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_post_id'             => array(
				'default'  => 0,
				'sanitize' => FILTER_SANITIZE_NUMBER_INT,
			),

		);

		$sanitized_post = array();
		foreach ( $input_post_values as $key => $input_post_value ) {

			if ( isset( $_POST[ $key ] ) ) {
				if ( 'FILTER_SANITIZE_STRING' === $input_post_value['sanitize'] ) {
					$sanitized_post[ $key ] = Cartflows_Ca_Helper::get_instance()->sanitize_text_filter( $key, 'POST' );
				} else {
					$sanitized_post[ $key ] = filter_input( INPUT_POST, $key, $input_post_value['sanitize'] );
				}
			} else {
				$sanitized_post[ $key ] = $input_post_value['default'];
			}
		}
		return $sanitized_post;

	}

	/**
	 * Save cart abandonment tracking and schedule new event.
	 *
	 * @since 1.0.0
	 */
	public function save_cart_abandonment_data() {
		check_ajax_referer( 'cartflows_save_cart_abandonment_data', 'security' );
		$post_data = $this->sanitize_post_data( 'cartflows_save_cart_abandonment_data' );
		if ( isset( $post_data['wcf_email'] ) ) {
			$user_email = sanitize_email( $post_data['wcf_email'] );
			global $wpdb;
			$cart_abandonment_table = $wpdb->prefix . CARTFLOWS_CA_CART_ABANDONMENT_TABLE;

			// Verify if email is already exists.
			$session_id               = WC()->session->get( 'wcf_session_id' );
			$session_checkout_details = null;
			if ( isset( $session_id ) ) {
				$session_checkout_details = Cartflows_Ca_Helper::get_instance()->get_checkout_details( $session_id );
			} else {
				$session_checkout_details = $this->get_checkout_details_by_email( $user_email );
				if ( $session_checkout_details ) {
					$session_id = $session_checkout_details->session_id;
					WC()->session->set( 'wcf_session_id', $session_id );
				} else {
					$session_id = md5( uniqid( wp_rand(), true ) );
				}
			}

			$checkout_details = $this->prepare_abandonment_data( $post_data );

			if ( isset( $session_checkout_details ) && WCF_CART_COMPLETED_ORDER === $session_checkout_details->order_status ) {
				WC()->session->__unset( 'wcf_session_id' );
				$session_id = md5( uniqid( wp_rand(), true ) );
			}

			if ( isset( $checkout_details['cart_total'] ) && $checkout_details['cart_total'] > 0 ) {

				if ( ( ! is_null( $session_id ) ) && ! is_null( $session_checkout_details ) ) {

					// Updating row in the Database where users Session id = same as prevously saved in Session.
					$wpdb->update(
						$cart_abandonment_table,
						$checkout_details,
						array( 'session_id' => $session_id )
					); // db call ok; no cache ok.

				} else {

					$checkout_details['session_id'] = sanitize_text_field( $session_id );
					// Inserting row into Database.
					$wpdb->insert(
						$cart_abandonment_table,
						$checkout_details
					); // db call ok; no cache ok.

					// Storing session_id in WooCommerce session.
					WC()->session->set( 'wcf_session_id', $session_id );

				}
			} else {
				$wpdb->delete( $cart_abandonment_table, array( 'session_id' => sanitize_key( $session_id ) ) ); // db call ok; no cache ok.
			}

			wp_send_json_success();
		}
	}

	/**
	 * Prepare cart data to save for abandonment.
	 *
	 * @param array $post_data post data.
	 * @return array
	 */
	public function prepare_abandonment_data( $post_data = array() ) {

		if ( function_exists( 'WC' ) ) {

			// Retrieving cart total value and currency.
			$cart_total = WC()->cart->total;

			$payment_gateway = WC()->session->chosen_payment_method;

			// Retrieving cart products and their quantities.
			$products     = WC()->cart->get_cart();
			$current_time = current_time( WCF_CA_DATETIME_FORMAT );
			$other_fields = array(
				'wcf_billing_company'     => $post_data['wcf_billing_company'],
				'wcf_billing_address_1'   => $post_data['wcf_billing_address_1'],
				'wcf_billing_address_2'   => $post_data['wcf_billing_address_2'],
				'wcf_billing_state'       => $post_data['wcf_billing_state'],
				'wcf_billing_postcode'    => $post_data['wcf_billing_postcode'],
				'wcf_shipping_first_name' => $post_data['wcf_shipping_first_name'],
				'wcf_shipping_last_name'  => $post_data['wcf_shipping_last_name'],
				'wcf_shipping_company'    => $post_data['wcf_shipping_company'],
				'wcf_shipping_country'    => $post_data['wcf_shipping_country'],
				'wcf_shipping_address_1'  => $post_data['wcf_shipping_address_1'],
				'wcf_shipping_address_2'  => $post_data['wcf_shipping_address_2'],
				'wcf_shipping_city'       => $post_data['wcf_shipping_city'],
				'wcf_shipping_state'      => $post_data['wcf_shipping_state'],
				'wcf_shipping_postcode'   => $post_data['wcf_shipping_postcode'],
				'wcf_order_comments'      => $post_data['wcf_order_comments'],
				'wcf_first_name'          => $post_data['wcf_name'],
				'wcf_last_name'           => $post_data['wcf_surname'],
				'wcf_phone_number'        => $post_data['wcf_phone'],
				'wcf_location'            => $post_data['wcf_country'] . ', ' . $post_data['wcf_city'],
			);

			$checkout_details = apply_filters(
				'woo_ca_session_abandoned_data',
				array(
					'email'         => $post_data['wcf_email'],
					'cart_contents' => maybe_serialize( $products ),
					'cart_total'    => sanitize_text_field( $cart_total ),
					'time'          => sanitize_text_field( $current_time ),
					'other_fields'  => maybe_serialize( $other_fields ),
					'checkout_id'   => $post_data['wcf_post_id'],
				)
			);
		}
		return $checkout_details;
	}

	/**
	 * Deletes cart abandonment tracking and scheduled event.
	 *
	 * @param int $order_id Order ID.
	 * @since 1.0.0
	 */
	public function delete_cart_abandonment_data( $order_id ) {

		$acceptable_order_statuses = Cartflows_Ca_Helper::get_instance()->get_acceptable_order_statuses();

		$order        = wc_get_order( $order_id );
		$order_status = $order->get_status();
		if ( is_array( $acceptable_order_statuses ) && ! empty( $acceptable_order_statuses ) && ! in_array( $order_status, $acceptable_order_statuses, true ) ) {
			// Proceed if order status in completed or processing.
			return;
		}

		global $wpdb;
		$cart_abandonment_table = $wpdb->prefix . CARTFLOWS_CA_CART_ABANDONMENT_TABLE;
		$email_history_table    = $wpdb->prefix . CARTFLOWS_CA_EMAIL_HISTORY_TABLE;

		if ( isset( WC()->session ) ) {
			$session_id = WC()->session->get( 'wcf_session_id' );

			if ( isset( $session_id ) ) {
				$checkout_details = Cartflows_Ca_Helper::get_instance()->get_checkout_details( $session_id );

				$has_mail_sent = count( Cartflows_Ca_Helper::get_instance()->fetch_scheduled_emails( $session_id, true ) );

				if ( ! $has_mail_sent ) {
					$wpdb->delete( $cart_abandonment_table, array( 'session_id' => sanitize_key( $session_id ) ) ); // db call ok; no cache ok.
				} else {
					if ( $checkout_details && ( WCF_CART_ABANDONED_ORDER === $checkout_details->order_status || WCF_CART_LOST_ORDER === $checkout_details->order_status ) ) {

						$this->skip_future_emails_when_order_is_completed( $session_id );

						$this->trigger_zapier_webhook( $session_id, WCF_CART_COMPLETED_ORDER );

						$order = wc_get_order( $order_id );
						$note  = __( 'This order was abandoned & subsequently recovered.', 'woo-cart-abandonment-recovery' );
						$order->add_order_note( $note );
						$order->save();

					} elseif ( WCF_CART_COMPLETED_ORDER !== $checkout_details->order_status ) {
						// Normal checkout.

						$billing_email = filter_input( INPUT_POST, 'billing_email', FILTER_SANITIZE_EMAIL );

						if ( $billing_email ) {
							$order_data = $this->get_captured_data_by_email( $billing_email );

							if ( ! is_null( $order_data ) ) {
								$existing_cart_contents = maybe_unserialize( $order_data->cart_contents );
								$order_cart_contents    = maybe_unserialize( $checkout_details->cart_contents );
								$existing_cart_products = array_keys( (array) $existing_cart_contents );
								$order_cart_products    = array_keys( (array) $order_cart_contents );
								if ( $this->check_if_similar_cart( $existing_cart_products, $order_cart_products ) ) {
									$this->skip_future_emails_when_order_is_completed( $order_data->session_id );
								}
							}
						}
						$wpdb->delete( $cart_abandonment_table, array( 'session_id' => sanitize_key( $session_id ) ) ); // db call ok; no cache ok.
					}
				}
			}
			if ( WC()->session ) {
				WC()->session->__unset( 'wcf_session_id' );
			}
		}
	}

	/**
	 * Unschedule future emails for completed orders.
	 *
	 * @param string $session_id session id.
	 * @param bool   $skip_complete skip update query.
	 */
	public function skip_future_emails_when_order_is_completed( $session_id, $skip_complete = false ) {

		global $wpdb;
		$email_history_table    = $wpdb->prefix . CARTFLOWS_CA_EMAIL_HISTORY_TABLE;
		$cart_abandonment_table = $wpdb->prefix . CARTFLOWS_CA_CART_ABANDONMENT_TABLE;

		if ( ! $skip_complete ) {
			$wpdb->update(
				$cart_abandonment_table,
				array(
					'order_status' => WCF_CART_COMPLETED_ORDER,
				),
				array(
					'session_id' => sanitize_key( $session_id ),
				)
			); // db call ok; no cache ok.
		}

		$wpdb->update(
			$email_history_table,
			array( 'email_sent' => -1 ),
			array(
				'ca_session_id' => $session_id,
				'email_sent'    => 0,
			)
		); // db call ok; no cache ok.
	}

	/**
	 * Compare cart if similar products.
	 *
	 * @param array $cart_a cart_a.
	 * @param array $cart_b cart_b.
	 * @return bool
	 */
	public function check_if_similar_cart( $cart_a, $cart_b ) {
		return (
			is_array( $cart_a )
			&& is_array( $cart_b )
			&& count( $cart_a ) === count( $cart_b )
			&& array_diff( $cart_a, $cart_b ) === array_diff( $cart_b, $cart_a )
		);
	}

	/**
	 * Get the checkout details for the user.
	 *
	 * @param string $email user email.
	 * @since 1.0.0
	 */
	public function get_checkout_details_by_email( $email ) {
		global $wpdb;
		$cart_abandonment_table = $wpdb->prefix . CARTFLOWS_CA_CART_ABANDONMENT_TABLE;
		$result                 = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$cart_abandonment_table} WHERE email = %s AND `order_status` IN ( %s, %s )", $email, WCF_CART_ABANDONED_ORDER, WCF_CART_NORMAL_ORDER ) //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		); // db call ok; no cache ok.
		return $result;
	}


	/**
	 * Get the checkout details for the user.
	 *
	 * @param string $value value.
	 * @since 1.0.0
	 */
	public function get_captured_data_by_email( $value ) {
		global $wpdb;
		$cart_abandonment_table = $wpdb->prefix . CARTFLOWS_CA_CART_ABANDONMENT_TABLE;
		$result                 = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$cart_abandonment_table} WHERE email = %s AND `order_status` IN (%s, %s) ORDER BY `time` DESC LIMIT 1", //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$value,
				WCF_CART_ABANDONED_ORDER,
				WCF_CART_LOST_ORDER
			)
		); // db call ok; no cache ok.
		return $result;
	}


	/**
	 * Get the checkout details for the user.
	 *
	 * @param string $value value.
	 * @since 1.0.0
	 */
	public function get_tracked_data_without_status( $value ) {
		global $wpdb;
		$cart_abandonment_table = $wpdb->prefix . CARTFLOWS_CA_CART_ABANDONMENT_TABLE;
		// Can't use placeholders for table/column names, it will be wrapped by a single quote (') instead of a backquote (`).
		$result = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$cart_abandonment_table} WHERE email = %s LIMIT 1", $value ) //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		); // db call ok; no cache ok.
		return $result;
	}

	/**
	 * Load analytics scripts.
	 */
	public function load_admin_cart_abandonment_script() {

		$admin_notice = Cartflows_Ca_Admin_Notices::get_instance();
		if ( $admin_notice->allowed_screen_for_notices() ) {

			$file_ext = Cartflows_Ca_Helper::get_instance()->get_js_file_ext();

			wp_enqueue_script(
				'cartflows-cart-abandonment-admin-notices',
				CARTFLOWS_CA_URL . 'admin/assets/' . $file_ext['folder'] . '/admin-notices.' . $file_ext['file_ext'],
				array( 'jquery' ),
				CARTFLOWS_CA_VER,
				false
			);

			$notices_vars = array(
				'weekly_report_email_notice_nonce' => wp_create_nonce( 'wcar_disable_weekly_report_email_notice' ),
			);

			wp_localize_script( 'cartflows-cart-abandonment-admin-notices', 'wcf_ca_notices_vars', $notices_vars );

		}

		$wcar_page = Cartflows_Ca_Helper::get_instance()->sanitize_text_filter( 'page', 'GET' );

		if ( WCF_CA_PAGE_NAME !== $wcar_page && ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// Styles.
		$folder   = SCRIPT_DEBUG ? 'css' : 'min-css';
		$file_rtl = ( is_rtl() ) ? '-rtl' : '';
		$file_ext = SCRIPT_DEBUG ? '.css' : '.min.css';
		wp_enqueue_style( 'cartflows-cart-abandonment-admin', CARTFLOWS_CA_URL . 'admin/assets/' . $folder . '/admin-cart-abandonment' . $file_rtl . $file_ext, array(), CARTFLOWS_CA_VER );

		$file_ext = Cartflows_Ca_Helper::get_instance()->get_js_file_ext();

		wp_enqueue_script(
			'cartflows-cart-abandonment-admin',
			CARTFLOWS_CA_URL . 'admin/assets/' . $file_ext['folder'] . '/admin-settings.' . $file_ext['file_ext'],
			array( 'jquery' ),
			CARTFLOWS_CA_VER,
			false
		);

		$vars = array(
			'url'                  => 'admin-ajax.php',

			// For delete coupons.
			'_delete_coupon_nonce' => wp_create_nonce( 'wcf_ca_delete_garbage_coupons' ),
			'_export_orders_nonce' => wp_create_nonce( 'wcf_ca_export_orders' ),
			'_confirm_msg'         => __( 'Do you really want to delete the used and expired coupons created by Cart Abandonment Plugin?', 'woo-cart-abandonment-recovery' ),
			'_confirm_msg_export'  => __( 'Do you really want to export orders?', 'woo-cart-abandonment-recovery' ),

			// For Search orders.
			'_search_button_nonce' => wp_create_nonce( 'wcf_ca_search_orders' ),
			'_result_msg'          => __( 'No such order is found.', 'woo-cart-abandonment-recovery' ),

		);
		wp_localize_script( 'cartflows-ca-email-tmpl-settings', 'wcf_ca_localized_vars', $vars );
	}

	/**
	 *  Decode and get the original contents.
	 *
	 * @param string $token token.
	 */
	public function wcf_decode_token( $token ) {
		$token = sanitize_text_field( $token );
		parse_str( base64_decode( urldecode( $token ) ), $token );
		return $token;
	}

	/**
	 *  Callback trigger event to send the emails.
	 */
	public function send_emails_to_callback() {

		global $wpdb;
		$email_history_table    = $wpdb->prefix . CARTFLOWS_CA_EMAIL_HISTORY_TABLE;
		$cart_abandonment_table = $wpdb->prefix . CARTFLOWS_CA_CART_ABANDONMENT_TABLE;
		$email_template_table   = $wpdb->prefix . CARTFLOWS_CA_EMAIL_TEMPLATE_TABLE;

		$current_time = current_time( WCF_CA_DATETIME_FORMAT );
        // phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		$emails_send_to = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT *, EHT.id as email_history_id, ETT.id as email_template_id FROM ' . $email_history_table . ' as EHT
		        INNER JOIN ' . $cart_abandonment_table . ' as CAT ON EHT.`ca_session_id` = CAT.`session_id`
		        INNER JOIN ' . $email_template_table . ' as ETT ON ETT.`id` = EHT.`template_id`
		        WHERE CAT.`order_status` = %s AND CAT.unsubscribed = 0 AND EHT.`email_sent` = 0 AND EHT.`scheduled_time` <= %s',
				WCF_CART_ABANDONED_ORDER,
				$current_time
			)
		); // db call ok; no cache ok.
        // phpcs:enable WordPress.DB.PreparedSQL.NotPrepared
		foreach ( $emails_send_to as $email_send_to ) {
			$email_result = Cartflows_Ca_Email_Schedule::get_instance()->send_email_templates( $email_send_to );
			if ( $email_result ) {
				$wpdb->update(
					$email_history_table,
					array( 'email_sent' => true ),
					array( 'id' => $email_send_to->email_history_id )
				); // db call ok; no cache ok.
			}
		}
	}

	/**
	 * Delete orders from cart abandonment table whose cart total is zero and order status is abandoned.
	 */
	public function delete_empty_abandoned_order() {
		global $wpdb;

		$cart_abandonment_table = $wpdb->prefix . CARTFLOWS_CA_CART_ABANDONMENT_TABLE;

		$where = array(
			'cart_total' => 0,
		);

		$wpdb->delete( $cart_abandonment_table, $where ); // db call ok; no cache ok.
	}
}

Cartflows_Ca_Tracking::get_instance();
