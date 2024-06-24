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
class Cartflows_Ca_Email_Schedule {



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

		add_action( 'wp_ajax_wcf_ca_preview_email_send', array( $this, 'send_preview_email' ) );
	}

	/**
	 *  Send preview emails.
	 */
	public function send_preview_email() {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( __( 'Permission denied.', 'woo-cart-abandonment-recovery' ) );
		}

		check_ajax_referer( WCF_EMAIL_TEMPLATES_NONCE, 'security' );
		$mail_result = $this->send_email_templates( null, true );
		if ( $mail_result ) {
			wp_send_json_success( __( 'Mail has been sent successfully!', 'woo-cart-abandonment-recovery' ) );
		} else {
			wp_send_json_error( __( 'Mail sending failed!', 'woo-cart-abandonment-recovery' ) );
		}
	}

		/**
		 * Callback function to send email templates.
		 *
		 * @param array   $email_data email data  .
		 * @param boolean $preview_email preview email.
		 * @since 1.0.0
		 */
	public function send_email_templates( $email_data, $preview_email = false ) {

		if ( $preview_email ) {
			$email_data = $this->create_dummy_session_for_preview_email();
		}

		if ( filter_var( $email_data->email, FILTER_VALIDATE_EMAIL ) ) {
			if ( ! $preview_email ) {
				if ( ! $this->check_if_already_purchased_by_email_product_ids( $email_data, $email_data->cart_contents ) ) {
					return false;
				}
			}

			$email_data = apply_filters( 'woo_ca_recovery_email_data', $email_data, $preview_email );

			$other_fields = maybe_unserialize( $email_data->other_fields );

			$from_email_name    = get_option( 'wcf_ca_from_name' );
			$reply_name_preview = get_option( 'wcf_ca_reply_email' );
			$from_email_preview = get_option( 'wcf_ca_from_email' );

			$user_first_name = ! empty( $other_fields['wcf_first_name'] ) ? ucfirst( $other_fields['wcf_first_name'] ) : ucfirst( $other_fields['wcf_shipping_first_name'] );
			$user_first_name = $user_first_name ? $user_first_name : apply_filters( 'woo_ca_default_first_name', __( 'there', 'woo-cart-abandonment-recovery' ) );
			$user_last_name  = ucfirst( $other_fields['wcf_last_name'] );
			$user_full_name  = trim( $user_first_name . ' ' . $user_last_name );

			$subject_email_preview = stripslashes( html_entity_decode( $email_data->email_subject, ENT_QUOTES, 'UTF-8' ) );
			$subject_email_preview = convert_smilies( $subject_email_preview );
			$subject_email_preview = str_replace( '{{customer.firstname}}', $user_first_name, $subject_email_preview );
			$body_email_preview    = convert_smilies( $email_data->email_body );
			$body_email_preview    = str_replace( '{{customer.firstname}}', $user_first_name, $body_email_preview );
			$body_email_preview    = str_replace( '{{customer.lastname}}', $user_last_name, $body_email_preview );
			$body_email_preview    = str_replace( '{{customer.fullname}}', $user_full_name, $body_email_preview );

			$email_instance = Cartflows_Ca_Email_Templates::get_instance();
			if ( $preview_email ) {
				$coupon_code = 'DUMMY-COUPON';
			} else {
				$override_global_coupon = $email_instance->get_email_template_meta_by_key( $email_data->email_template_id, 'override_global_coupon' );
				if ( $override_global_coupon->meta_value ) {
					$email_history = $email_instance->get_email_history_by_id( $email_data->email_history_id );
					$coupon_code   = $email_history->coupon_code;
				} else {
					$coupon_code = $email_data->coupon_code;
				}
			}

			$auto_apply_coupon = $email_instance->get_email_template_meta_by_key( $email_data->email_template_id, 'auto_coupon' );

			$token_data = array(
				'wcf_session_id'    => $email_data->session_id,
				'wcf_coupon_code'   => isset( $auto_apply_coupon ) && $auto_apply_coupon->meta_value ? $coupon_code : null,
				'wcf_preview_email' => $preview_email ? true : false,
			);

			$checkout_url = Cartflows_Ca_Helper::get_instance()->get_checkout_url( $email_data->checkout_id, $token_data );

			$body_email_preview = str_replace( '{{cart.coupon_code}}', $coupon_code, $body_email_preview );

			$current_time_stamp  = $email_data->time;
			$body_email_preview  = str_replace( '{{cart.abandoned_date}}', $current_time_stamp, $body_email_preview );
			$unsubscribe_element = '<a target="_blank" style="color: lightgray" href="' . $checkout_url . '&unsubscribe=true ">' . __( 'Don\'t remind me again.', 'woo-cart-abandonment-recovery' ) . '</a>';
			$body_email_preview  = str_replace( '{{cart.unsubscribe}}', $unsubscribe_element, $body_email_preview );
			$body_email_preview  = str_replace( 'http://{{cart.checkout_url}}', '{{cart.checkout_url}}', $body_email_preview );
			$body_email_preview  = str_replace( 'https://{{cart.checkout_url}}', '{{cart.checkout_url}}', $body_email_preview );
			$body_email_preview  = str_replace( '{{cart.checkout_url}}', $checkout_url, $body_email_preview );
			$host                = wp_parse_url( get_site_url() );
			$body_email_preview  = str_replace( '{{site.url}}', $host['host'], $body_email_preview );

			if ( false !== strpos( $body_email_preview, '{{cart.product.names}}' ) ) {
				$body_email_preview = str_replace( '{{cart.product.names}}', Cartflows_Ca_Helper::get_instance()->get_comma_separated_products( $email_data->cart_contents ), $body_email_preview );
			}

			$admin_user         = get_users(
				array(
					'role'   => 'Administrator',
					'number' => 1,
				)
			);
			$admin_user         = reset( $admin_user );
			$admin_first_name   = $admin_user->user_firstname ? $admin_user->user_firstname : __( 'Admin', 'woo-cart-abandonment-recovery' );
			$body_email_preview = str_replace( '{{admin.firstname}}', $admin_first_name, $body_email_preview );
			$body_email_preview = str_replace( '{{admin.company}}', get_bloginfo( 'name' ), $body_email_preview );

			$headers  = 'From: ' . $from_email_name . ' <' . $from_email_preview . '>' . "\r\n";
			$headers .= 'Content-Type: text/html' . "\r\n";
			$headers .= 'Reply-To:  ' . $reply_name_preview . ' ' . "\r\n";
			$var      = $this->get_email_product_block( $email_data->cart_contents, $email_data->cart_total );

			$body_email_preview = str_replace( '{{cart.product.table}}', $var, $body_email_preview );
			$body_email_preview = wpautop( $body_email_preview );

			$use_woo_style = $email_instance->get_email_template_meta_by_key( $email_data->email_template_id, 'use_woo_email_style' );

			if ( '1' === $use_woo_style->meta_value ) {
				ob_start();

				wc_get_template( 'emails/email-header.php', array( 'email_heading' => $subject_email_preview ) );
				$email_body_template_header = ob_get_clean();

				ob_start();

				wc_get_template( 'emails/email-footer.php' );
				$email_body_template_footer = ob_get_clean();

				$site_title                 = get_bloginfo( 'name' );
				$email_body_template_footer = str_ireplace( '{site_title}', $site_title, $email_body_template_footer );

				$final_email_body = $email_body_template_header . $body_email_preview . $email_body_template_footer;
				wc_mail( $email_data->email, $subject_email_preview, stripslashes( $final_email_body ), $headers );

				return true;
			}

			// Ignoring the below rule as rule asking to use third party mailing functions but third-party SMTP plugins overrides the wp_mail and uses their mailing system.
			//phpcs:disable WordPressVIPMinimum.Functions.RestrictedFunctions.wp_mail_wp_mail

			$mail_result = wp_mail( $email_data->email, $subject_email_preview, stripslashes( $body_email_preview ), $headers );
			if ( $mail_result ) {
				return true;
			} else {
				// Retry sending mail.
				$mail_result = wp_mail( $email_data->email, $subject_email_preview, stripslashes( $body_email_preview ), $headers );
				if ( ! $preview_email ) {
					return true;
				}
				return false;
			}
			//phpcs:enable WordPressVIPMinimum.Functions.RestrictedFunctions.wp_mail_wp_mail
		} else {
			return false;
		}

	}

		/**
		 * Create a dummy object for the preview email.
		 *
		 * @return stdClass
		 */
	public function create_dummy_session_for_preview_email() {

		$email_data                    = new stdClass();
		$current_user                  = wp_get_current_user();
		$helper_class                  = Cartflows_Ca_Helper::get_instance();
		$email_data->email_template_id = $helper_class->sanitize_text_filter( 'email_template_id', 'POST' );

		$email_data->checkout_id   = wc_get_page_id( 'checkout' );
		$email_data->session_id    = 'dummy-session-id';
		$email_send_to             = filter_input( INPUT_POST, 'email_send_to', FILTER_SANITIZE_EMAIL );
		$email_data->email         = $email_send_to ? $email_send_to : $current_user->user_email;
		$email_data->email_body    = filter_input( INPUT_POST, 'email_body', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$email_data->email_subject = $helper_class->sanitize_text_filter( 'email_subject', 'POST' );
		$email_data->email_body    = html_entity_decode( $email_data->email_body, ENT_COMPAT, 'UTF-8' );
		$email_data->other_fields  = maybe_serialize(
			array(
				'wcf_first_name' => $current_user->user_firstname,
				'wcf_last_name'  => $current_user->user_lastname,
			)
		);
		if ( ! WC()->cart->get_cart_contents_count() ) {
			$args = array(
				'posts_per_page' => 1,
				'orderby'        => 'rand',
				'post_type'      => 'product',
				'meta_query'     => array( //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					// Exclude out of stock products.
					array(
						'key'     => '_stock_status',
						'value'   => 'outofstock',
						'compare' => 'NOT IN',
					),
				),
				'tax_query'      => array( //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => 'simple',
					),
				),
			);

			$random_products = get_posts( $args );
			if ( ! empty( $random_products ) ) {
				$random_product = reset( $random_products );
				WC()->cart->add_to_cart( $random_product->ID );
			}
		}

		$email_data->cart_total    = WC()->cart->total + floatval( WC()->cart->get_cart_shipping_total() );
		$email_data->cart_contents = maybe_serialize( WC()->cart->get_cart() );
		$email_data->time          = current_time( WCF_CA_DATETIME_FORMAT );
		return $email_data;
	}


		/**
		 * Generate the view for email product cart block.
		 *
		 * @param  string $cart_contents user cart contents details.
		 * @param  float  $cart_total user cart total.
		 * @return string
		 */
	public function get_email_product_block( $cart_contents, $cart_total ) {

		$cart_items = maybe_unserialize( $cart_contents );

		if ( ! is_array( $cart_items ) || ! count( $cart_items ) ) {
			return;
		}

		$tr    = '';
		$style = array(
			'product_image' => array(
				'style'     => 'height: 42px; width: 42px;',
				'attribute' => 'width=42 height=42',
			),
			'table'         => array(
				'style'     => 'color: #636363; border: 1px solid #e5e5e5;',
				'attribute' => 'align= left;',
			),
		);

		$style_filter        = apply_filters( 'woo_ca_email_template_table_style', $style );
		$product_image_style = isset( $style_filter['product_image']['style'] ) ? $style_filter['product_image']['style'] : '';
		$style               = isset( $style_filter['table']['style'] ) ? $style_filter['table']['style'] : '';

		foreach ( $cart_items as $cart_item ) {

			if ( isset( $cart_item['product_id'] ) && isset( $cart_item['quantity'] ) && isset( $cart_item['line_total'] ) ) {
				$id        = 0 !== $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];
				$image_url = get_the_post_thumbnail_url( $id );
				$image_url = ! empty( $image_url ) ? $image_url : get_the_post_thumbnail_url( $cart_item['product_id'] );

				$product      = wc_get_product( $id );
				$product_name = $product ? $product->get_formatted_name() : '';

				if ( empty( $image_url ) ) {
					$image_url = CARTFLOWS_CA_URL . 'admin/assets/images/image-placeholder.png';
				}
				$tr = $tr . '<tr style=' . $style . ' align="center">
                           <td style="' . $style . '"><img  class="demo_img" style="' . $product_image_style . '" src="' . esc_url( $image_url ) . '" ' . $style_filter['product_image']['attribute'] . '></td>
                           <td style="' . $style . '">' . $product_name . '</td>
                           <td style="' . $style . '"> ' . $cart_item['quantity'] . ' </td>
                           <td style="' . $style . '">' . wc_price( $cart_item['line_total'] ) . '</td>
                           <td style="' . $style . '" >' . wc_price( $cart_item['line_total'] ) . '</td>
                        </tr> ';
			}
		}

		/**
		 * Add filter to toggle the Cart Total row.
		 */
		$enable_cart_total = apply_filters( 'woo_ca_recovery_enable_cart_total', false );
		if ( $enable_cart_total ) {
			$tr = $tr . '<tr style="' . $style . '" align="center">
                           <td colspan="4" style="' . $style . '"> ' . __( 'Cart Total ( Cart Total + Shipping + Tax )', 'woo-cart-abandonment-recovery' ) . ' </td>
                           <td style="' . $style . '" >' . wc_price( $cart_total ) . '</td>
                        </tr> ';
		}

		return '<table ' . $style_filter['table']['attribute'] . ' cellpadding="10" cellspacing="0" style="float: none; border: 1px solid #e5e5e5;">
	                <tr align="center">
	                   <th  style="' . $style . '">' . __( 'Item', 'woo-cart-abandonment-recovery' ) . '</th>
	                   <th  style="' . $style . '">' . __( 'Name', 'woo-cart-abandonment-recovery' ) . '</th>
	                   <th  style="' . $style . '">' . __( 'Quantity', 'woo-cart-abandonment-recovery' ) . '</th>
	                   <th  style="' . $style . '">' . __( 'Price', 'woo-cart-abandonment-recovery' ) . '</th>
	                   <th  style="' . $style . '">' . __( 'Line Subtotal', 'woo-cart-abandonment-recovery' ) . '</th>
	                </tr> ' . $tr . '
	        </table>';
	}

		/**
		 * Check before emails actually send to user.
		 *
		 * @param object $email_data email_data.
		 * @param string $current_cart_data cart data.
		 * @return bool
		 */
	public function check_if_already_purchased_by_email_product_ids( $email_data, $current_cart_data ) {

		global $wpdb;
		$current_cart_data = maybe_unserialize( $current_cart_data );

		// Fetch products & variations.
		$products         = array_values( wp_list_pluck( $current_cart_data, 'product_id' ) );
		$variations       = array_values( wp_list_pluck( $current_cart_data, 'variation_id' ) );
		$current_products = array_unique( array_merge( $products, $variations ) );

		$cart_abandonment_table = $wpdb->prefix . CARTFLOWS_CA_CART_ABANDONMENT_TABLE;

		$orders             = wc_get_orders(
			array(
				'billing_email' => $email_data->email,
				'status'        => array( 'processing', 'completed' ),
				'date_after'    => gmdate(
					'Y-m-d h:i:s',
					strtotime( '-30 days' )
				),
			)
		);
		$need_to_send_email = true;

		foreach ( $orders as $order ) {
			$order = wc_get_order( $order->get_id() );
			$items = $order->get_items();
			foreach ( $items as $item ) {
				$product_id = $item->get_product_id();
				if ( in_array( $product_id, $current_products, true ) ) {
					/**
					 * Remove duplicate captured order for tracking.
					 */
					$wpdb->delete( $cart_abandonment_table, array( 'session_id' => sanitize_key( $email_data->session_id ) ) ); // db call ok; no-cache ok.
					$need_to_send_email = false;
					break;
				}
			}
		}
		return $need_to_send_email;
	}

	/**
	 * Schedule events for the abadoned carts to send emails.
	 *
	 * @param integer $session_id user session id.
	 * @param boolean $force_reschedule force reschedule.
	 */
	public function schedule_emails( $session_id, $force_reschedule = false ) {

		$checkout_details = Cartflows_Ca_Helper::get_instance()->get_checkout_details( $session_id );

		if ( ( $checkout_details->unsubscribed ) || ( WCF_CART_COMPLETED_ORDER === $checkout_details->order_status ) ) {
			return;
		}

		$scheduled_emails = Cartflows_Ca_Helper::get_instance()->fetch_scheduled_emails( $session_id );
		// We are recommending PHP 7.2 version to use our plugin.
		$scheduled_templates = array_column( $scheduled_emails, 'template_id' ); //phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.array_columnFound
		$scheduled_time_from = $checkout_details->time;

		if ( $force_reschedule ) {
			$scheduled_time_from = current_time( WCF_CA_DATETIME_FORMAT );
		}

		$email_tmpl = Cartflows_Ca_Email_Templates::get_instance();
		$templates  = $email_tmpl->fetch_all_active_templates();

		global $wpdb;

		$email_history_table = $wpdb->prefix . CARTFLOWS_CA_EMAIL_HISTORY_TABLE;

		foreach ( $templates as $template ) {

			if ( false !== array_search( $template->id, $scheduled_templates, true ) && false === $force_reschedule ) {
				continue;
			}

			$timestamp_str  = '+' . $template->frequency . ' ' . $template->frequency_unit . 'S';
			$scheduled_time = gmdate( WCF_CA_DATETIME_FORMAT, strtotime( $scheduled_time_from . $timestamp_str ) );
			$discount_type  = $email_tmpl->get_email_template_meta_by_key( $template->id, 'discount_type' );
			$discount_type  = isset( $discount_type->meta_value ) ? $discount_type->meta_value : '';
			$amount         = $email_tmpl->get_email_template_meta_by_key( $template->id, 'coupon_amount' );
			$amount         = isset( $amount->meta_value ) ? $amount->meta_value : '';

			$coupon_expiry_date = $email_tmpl->get_email_template_meta_by_key( $template->id, 'coupon_expiry_date' );
			$coupon_expiry_unit = $email_tmpl->get_email_template_meta_by_key( $template->id, 'coupon_expiry_unit' );
			$coupon_expiry_date = isset( $coupon_expiry_date->meta_value ) ? $coupon_expiry_date->meta_value : '';
			$coupon_expiry_unit = isset( $coupon_expiry_unit->meta_value ) ? $coupon_expiry_unit->meta_value : 'hours';

			$coupon_expiry_date = $coupon_expiry_date ? strtotime( $scheduled_time . ' +' . $coupon_expiry_date . ' ' . $coupon_expiry_unit ) : '';

			$free_shipping_coupon = $email_tmpl->get_email_template_meta_by_key( $template->id, 'free_shipping_coupon' );
			$free_shipping        = ( isset( $free_shipping_coupon ) && ( $free_shipping_coupon->meta_value ) ) ? 'yes' : 'no';

			$individual_use_only = $email_tmpl->get_email_template_meta_by_key( $template->id, 'individual_use_only' );
			$individual_use      = ( isset( $individual_use_only ) && ( $individual_use_only->meta_value ) ) ? 'yes' : 'no';

			$override_global_coupon = $email_tmpl->get_email_template_meta_by_key( $template->id, 'override_global_coupon' );

			$new_coupon_code = '';
			if ( $override_global_coupon->meta_value ) {
				$new_coupon_code = $this->generate_coupon_code( $discount_type, $amount, $coupon_expiry_date, $free_shipping, $individual_use );
			}

			$wpdb->replace(
				$email_history_table,
				array(
					'template_id'    => $template->id,
					'ca_session_id'  => $checkout_details->session_id,
					'coupon_code'    => $new_coupon_code,
					'scheduled_time' => $scheduled_time,
				)
			); // db call ok; no-cache ok.
		}
	}

	/**
	 *  Generate new coupon code for abandoned cart.
	 *
	 * @param string $discount_type discount type.
	 * @param float  $amount amount.
	 * @param string $expiry expiry.
	 * @param string $free_shipping is free shipping.
	 * @param string $individual_use use coupon individual.
	 */
	public function generate_coupon_code( $discount_type, $amount, $expiry = '', $free_shipping = 'no', $individual_use = 'no' ) {

		$coupon_code = '';

			$coupon_code = wp_generate_password( 8, false, false );

			$new_coupon_id = wp_insert_post(
				array(
					'post_title'   => $coupon_code,
					'post_content' => '',
					'post_status'  => 'publish',
					'post_author'  => 1,
					'post_type'    => 'shop_coupon',
				)
			);

			$coupon_post_data = array(
				'discount_type'       => $discount_type,
				'description'         => WCF_CA_COUPON_DESCRIPTION,
				'coupon_amount'       => $amount,
				'individual_use'      => $individual_use,
				'product_ids'         => '',
				'exclude_product_ids' => '',
				'usage_limit'         => '1',
				'usage_count'         => '0',
				'date_expires'        => $expiry,
				'apply_before_tax'    => 'yes',
				'free_shipping'       => $free_shipping,
				'coupon_generated_by' => WCF_CA_COUPON_GENERATED_BY,
			);

			$coupon_post_data = apply_filters( 'woo_ca_generate_coupon', $coupon_post_data );

			foreach ( $coupon_post_data as $key => $value ) {
				update_post_meta( $new_coupon_id, $key, $value );
			}

			return $coupon_code;
	}

}

Cartflows_Ca_Email_Schedule::get_instance();
