<?php
/**
 * Cart Abandonment
 *
 * @package Woocommerce-Cart-Abandonment-Recovery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'CARTFLOWS_EMAIL_TEMPLATE_DIR', CARTFLOWS_CA_DIR . 'modules/cart-abandonment/' );
define( 'CARTFLOWS_EMAIL_TEMPLATE_URL', CARTFLOWS_CA_URL . 'modules/cart-abandonment/' );

/**
 * Class for analytics tracking.
 */
class Cartflows_Ca_Email_Templates {



	/**
	 * Member Variable
	 *
	 * @var object instance
	 */
	private static $instance;

	/**
	 * Member Variable
	 *
	 * @var object instance
	 */
	public $email_history_table;

	/**
	 * Table name for email templates
	 *
	 * @var string
	 */
	public $cart_abandonment_template_table_name;

	/**
	 * Table name for email templates meta table
	 *
	 * @var string
	 */
	public $email_templates_meta_table;

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
	 * Constructor function that initializes required actions and hooks
	 */
	public function __construct() {
		$this->define_template_constants();
		global $wpdb;
		$this->cart_abandonment_template_table_name = $wpdb->prefix . CARTFLOWS_CA_EMAIL_TEMPLATE_TABLE;
		$this->email_templates_meta_table           = $wpdb->prefix . CARTFLOWS_CA_EMAIL_TEMPLATE_META_TABLE;
		$this->email_history_table                  = $wpdb->prefix . CARTFLOWS_CA_EMAIL_HISTORY_TABLE;

		add_action( 'admin_enqueue_scripts', __class__ . '::load_email_templates_script', 15 );
		add_action( 'wp_ajax_activate_email_templates', array( $this, 'update_email_toggle_button' ) );
	}




	/**
	 * Add email template JS script.
	 */
	public static function load_email_templates_script() {

		$page = Cartflows_Ca_Helper::get_instance()->sanitize_text_filter( 'page', 'GET' );

		if ( WCF_CA_PAGE_NAME !== $page && ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-ui-style' );

		$file_ext = Cartflows_Ca_Helper::get_instance()->get_js_file_ext();

		wp_enqueue_script(
			'cartflows-ca-email-tmpl-settings',
			CARTFLOWS_CA_URL . 'admin/assets/' . $file_ext['folder'] . '/admin-email-templates.' . $file_ext['file_ext'],
			array( 'jquery' ),
			CARTFLOWS_CA_VER,
			false
		);

		$current_user = wp_get_current_user();
		$vars         = array(
			'email'                           => $current_user->user_email,
			'name'                            => $current_user->user_firstname,
			'surname'                         => $current_user->user_lastname,
			'phone'                           => get_user_meta( $current_user->ID, 'billing_phone', true ),
			'billing_company'                 => get_user_meta( $current_user->ID, 'billing_company', true ),
			'billing_address_1'               => get_user_meta( $current_user->ID, 'billing_address_1', true ),
			'billing_address_2'               => get_user_meta( $current_user->ID, 'billing_address_2', true ),
			'billing_state'                   => get_user_meta( $current_user->ID, 'billing_state', true ),
			'billing_postcode'                => get_user_meta( $current_user->ID, 'billing_postcode', true ),
			'shipping_first_name'             => $current_user->user_firstname,
			'shipping_last_name'              => $current_user->user_lastname,
			'shipping_company'                => get_user_meta( $current_user->ID, 'shipping_company', true ),
			'shipping_address_1'              => get_user_meta( $current_user->ID, 'shipping_address_1', true ),
			'shipping_address_2'              => get_user_meta( $current_user->ID, 'shipping_address_2', true ),
			'shipping_city'                   => get_user_meta( $current_user->ID, 'shipping_city', true ),
			'shipping_state'                  => get_user_meta( $current_user->ID, 'shipping_state', true ),
			'shipping_postcode'               => get_user_meta( $current_user->ID, 'shipping_postcode', true ),
			'woo_currency_symbol'             => get_woocommerce_currency_symbol(),
			'email_toggle_button_nonce'       => wp_create_nonce( 'activate_email_templates' ),
			'admin_firstname'                 => __( 'Admin Firstname', 'woo-cart-abandonment-recovery' ),
			'admin_company'                   => __( 'Admin Company', 'woo-cart-abandonment-recovery' ),
			'abandoned_product_details_table' => __( 'Abandoned Product Details Table', 'woo-cart-abandonment-recovery' ),
			'abandoned_product_names'         => __( 'Abandoned Product Names', 'woo-cart-abandonment-recovery' ),
			'cart_checkout_url'               => __( 'Cart Checkout URL', 'woo-cart-abandonment-recovery' ),
			'coupon_code'                     => __( 'Coupon Code', 'woo-cart-abandonment-recovery' ),
			'customer_firstname'              => __( 'Customer First Name', 'woo-cart-abandonment-recovery' ),
			'customer_lastname'               => __( 'Customer Last Name', 'woo-cart-abandonment-recovery' ),
			'customer_full_name'              => __( 'Customer Full Name', 'woo-cart-abandonment-recovery' ),
			'cart_abandonment_date'           => __( 'Cart Abandonment Date', 'woo-cart-abandonment-recovery' ),
			'site_url'                        => __( 'Site URL', 'woo-cart-abandonment-recovery' ),
			'unsubscribe_link'                => __( 'Unsubscribe Link', 'woo-cart-abandonment-recovery' ),
			'strings'                         => array(
				'trigger_process'  => __( 'Triggering...', 'woo-cart-abandonment-recovery' ),
				'trigger_failed'   => __( 'Trigger Failed.', 'woo-cart-abandonment-recovery' ),
				'trigger_success'  => __( 'Trigger Success.', 'woo-cart-abandonment-recovery' ),
				'verify_url'       => __( 'Please verify webhook URL.', 'woo-cart-abandonment-recovery' ),
				'verify_url_error' => __( 'Webhook URL is required.', 'woo-cart-abandonment-recovery' ),
			),
		);
		wp_localize_script( 'cartflows-ca-email-tmpl-settings', 'wcf_ca_details', $vars );

	}


	/**
	 * Update the activate email template toggle button.
	 */
	public function update_email_toggle_button() {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( __( 'Permission denied.', 'woo-cart-abandonment-recovery' ) );
		}

		check_ajax_referer( 'activate_email_templates', 'security' );
		global $wpdb;
		$cart_abandonment_template_table_name = $wpdb->prefix . CARTFLOWS_CA_EMAIL_TEMPLATE_TABLE;

		$id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );

		$is_activated = Cartflows_Ca_Helper::get_instance()->sanitize_text_filter( 'state', 'POST' );

		$response = __( 'Something went wrong', 'woo-cart-abandonment-recovery' );
		if ( ! isset( $is_activated ) || ! isset( $id ) ) {
			wp_send_json_error( $response );
		}

		if ( $is_activated && 'on' === $is_activated ) {
			$is_activated = 1;
			$response     = __( 'Activated', 'woo-cart-abandonment-recovery' );
		} else {
			$is_activated = 0;
			$response     = __( 'Deactivated', 'woo-cart-abandonment-recovery' );
		}
		// Can't use placeholders for table/column names, it will be wrapped by a single quote (') instead of a backquote (`).
		$wpdb->query(
			$wpdb->prepare( "UPDATE {$cart_abandonment_template_table_name} SET is_activated = %d WHERE id = %d ", $is_activated, $id ) //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		); // db call ok; no cache ok.
		wp_send_json_success( $response );

	}

	/**
	 *  Initialise all the constants
	 */
	public function define_template_constants() {
		define( 'WCF_CA_PAGE_NAME', 'woo-cart-abandonment-recovery' );

		define( 'WCF_CA_GENERAL_SETTINGS_SECTION', 'cartflows_cart_abandonment_settings_section' );
		define( 'WCF_CA_COUPONS_SETTINGS_SECTION', 'cartflows_cart_abandonment_coupons_settings_section' );
		define( 'WCF_CA_EMAIL_SETTINGS_SECTION', 'cartflows_email_template_settings_section' );
		define( 'WCF_CA_COUPON_CODE_SECTION', 'cartflows_coupon_code_settings_section' );
		define( 'WCF_CA_ZAPIER_SETTINGS_SECTION', 'cartflows_zapier_settings_section' );
		define( 'WCF_CA_GDPR_SETTINGS_SECTION', 'cartflows_gdpr_settings_section' );
		define( 'WCF_CA_PLUGIN_SETTINGS_SECTION', 'cartflows_cart_abandonment_plugin_settings_section' );
		define( 'WCF_CA_RECOVERY_EMAIL_SETTINGS_SECTION', 'cartflows_cart_abandonment_recovery_report_settings_section' );

		define( 'WCF_CA_SETTINGS_OPTION_GROUP', 'cartflows-cart-abandonment-settings' );
		define( 'WCF_CA_EMAIL_SETTINGS_OPTION_GROUP', 'cartflows-cart-abandonment-email-settings' );

		define( 'WCF_ACTION_EMAIL_TEMPLATES', 'email_tmpl' );

		define( 'WCF_SUB_ACTION_ADD_EMAIL_TEMPLATES', 'add_email_tmpl' );
		define( 'WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES', 'edit_email_tmpl' );
		define( 'WCF_SUB_ACTION_DELETE_EMAIL_TEMPLATES', 'delete_email_tmpl' );
		define( 'WCF_SUB_ACTION_CLONE_EMAIL_TEMPLATES', 'clone_email_tmpl' );
		define( 'WCF_SUB_ACTION_DELETE_BULK_EMAIL_TEMPLATES', 'delete_bulk_email_tmpl' );
		define( 'WCF_SUB_ACTION_SAVE_EMAIL_TEMPLATES', 'save_email_template' );
		define( 'WCF_SUB_ACTION_RESTORE_EMAIL_TEMPLATES', 'restore_default_email_tmpl' );

		define( 'WCF_SUB_ACTION_CART_ABANDONMENT_SETTINGS', 'cart_abandonment_settings' );
		define( 'WCF_SUB_ACTION_EMAIL_SETTINGS', 'email_settings' );
		define( 'WCF_SUB_ACTION_COUPON_CODE_SETTINGS', 'coupon_code_settings' );
		define( 'WCF_SUB_ACTION_ZAPIER_SETTINGS', 'zapier_settings' );

		define( 'WCF_EMAIL_TEMPLATES_NONCE', 'email_template_nonce' );

	}

	/**
	 *  Show success messages for email templates.
	 */
	public function show_messages() {

		$helper_class             = Cartflows_Ca_Helper::get_instance();
		$wcf_ca_template_created  = $helper_class->sanitize_text_filter( 'wcf_ca_template_created', 'GET' );
		$wcf_ca_template_cloned   = $helper_class->sanitize_text_filter( 'wcf_ca_template_cloned', 'GET' );
		$wcf_ca_template_deleted  = $helper_class->sanitize_text_filter( 'wcf_ca_template_deleted', 'GET' );
		$wcf_ca_template_updated  = $helper_class->sanitize_text_filter( 'wcf_ca_template_updated', 'GET' );
		$wcf_ca_template_restored = $helper_class->sanitize_text_filter( 'wcf_ca_template_restored', 'GET' );

		?>
		<?php if ( 'YES' === $wcf_ca_template_created ) { ?>
		<div id="message" class="notice notice-success is-dismissible">
			<p>
				<strong>
					<?php esc_html_e( 'The Email Template has been successfully added.', 'woo-cart-abandonment-recovery' ); ?>
				</strong>
			</p>
		</div>
	<?php } ?>

		<?php if ( 'YES' === $wcf_ca_template_cloned ) { ?>
		<div id="message" class="notice notice-success is-dismissible">
			<p>
				<strong>
					<?php esc_html_e( 'The Email Template has been cloned successfully.', 'woo-cart-abandonment-recovery' ); ?>
				</strong>
			</p>
		</div>
	<?php } ?>

		<?php if ( 'YES' === $wcf_ca_template_deleted ) { ?>
		<div id="message" class="notice notice-success is-dismissible">
			<p>
				<strong>
					<?php esc_html_e( 'The Email Template has been successfully deleted.', 'woo-cart-abandonment-recovery' ); ?>
				</strong>
			</p>
		</div>
	<?php } ?>
		<?php if ( 'YES' === $wcf_ca_template_updated ) { ?>
		<div id="message" class="notice notice-success is-dismissible">
			<p>
				<strong>
					<?php esc_html_e( 'The Email Template has been successfully updated.', 'woo-cart-abandonment-recovery' ); ?>
				</strong>
			</p>
		</div>
	<?php } ?>

		<?php if ( 'YES' === $wcf_ca_template_restored ) { ?>
			<div id="message" class="notice notice-success is-dismissible">
				<p>
					<strong>
						<?php esc_html_e( 'Default Email Templates has been restored successfully.', 'woo-cart-abandonment-recovery' ); ?>
					</strong>
				</p>
			</div>
		<?php } ?>
		<?php

	}

	/**
	 *  Delete bulk email templates.
	 */
	public function delete_bulk_templates() {
		$wcf_template_list = new Cartflows_Ca_Email_Templates_Table();
		$wcf_template_list->process_bulk_action();
		$param        = array(
			'page'                    => WCF_CA_PAGE_NAME,
			'action'                  => WCF_ACTION_EMAIL_TEMPLATES,
			'wcf_ca_template_deleted' => 'YES',
		);
		$redirect_url = add_query_arg( $param, admin_url( '/admin.php' ) );
		wp_safe_redirect( $redirect_url );
		exit;
	}


	/**
	 *  Delete email templates.
	 */
	public function delete_single_template() {

		$id      = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		$wpnonce = Cartflows_Ca_Helper::get_instance()->sanitize_text_filter( '_wpnonce', 'GET' );

		if ( $id && $wpnonce && wp_verify_nonce( $wpnonce, WCF_EMAIL_TEMPLATES_NONCE ) ) {
			global $wpdb;
			$wpdb->delete(
				$this->cart_abandonment_template_table_name,
				array( 'id' => $id ),
				'%d'
			); // db call ok; no cache ok.
			$param        = array(
				'page'                    => WCF_CA_PAGE_NAME,
				'action'                  => WCF_ACTION_EMAIL_TEMPLATES,
				'wcf_ca_template_deleted' => 'YES',
			);
			$redirect_url = add_query_arg( $param, admin_url( '/admin.php' ) );
			wp_safe_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 *  Delete email templates.
	 */
	public function clone_email_template() {

		$id      = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		$wpnonce = Cartflows_Ca_Helper::get_instance()->sanitize_text_filter( '_wpnonce', 'GET' );

		if ( $id && $wpnonce && wp_verify_nonce( $wpnonce, WCF_EMAIL_TEMPLATES_NONCE ) ) {

			$email_template = $this->get_template_by_id( $id );
			global $wpdb;
			$wpdb->insert(
				$this->cart_abandonment_template_table_name,
				array(
					'template_name'  => sanitize_text_field( $email_template->template_name ),
					'email_subject'  => sanitize_text_field( $email_template->email_subject ),
					'email_body'     => $email_template->email_body,
					'frequency'      => intval( sanitize_text_field( $email_template->frequency ) ),
					'frequency_unit' => sanitize_text_field( $email_template->frequency_unit ),

				),
				array( '%s', '%s', '%s', '%d', '%s' )
			); // db call ok; no cache ok.

			$email_template_id = $wpdb->insert_id;
			$meta_data         = array(
				'override_global_coupon' => false,
				'discount_type'          => 'percent',
				'coupon_amount'          => 10,
				'coupon_expiry_date'     => '',
				'coupon_expiry_unit'     => 'hours',
				'use_woo_email_style'    => false,
			);

			foreach ( $meta_data as $mera_key => $meta_value ) {
				$this->add_email_template_meta( $email_template_id, $mera_key, $meta_value );
			}

			$param        = array(
				'page'                   => WCF_CA_PAGE_NAME,
				'action'                 => WCF_ACTION_EMAIL_TEMPLATES,
				'wcf_ca_template_cloned' => 'YES',
			);
			$redirect_url = add_query_arg( $param, admin_url( '/admin.php' ) );
			wp_safe_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 *  Get email template by id.
	 *
	 * @param int $email_tmpl_id template id.
	 */
	public function get_email_template_by_id( $email_tmpl_id ) {
		global $wpdb;
		// Can't use placeholders for table/column names, it will be wrapped by a single quote (') instead of a backquote (`).
		return $wpdb->get_row(
			$wpdb->prepare( "SELECT  *  FROM {$this->cart_abandonment_template_table_name} WHERE id = %d ", $email_tmpl_id )  //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		); // db call ok; no cache ok.

	}

	/**
	 *  Render email template add/edit form.
	 *
	 * @param string $sub_action sub_action.
	 */
	public function render_email_template_form( $sub_action = WCF_SUB_ACTION_ADD_EMAIL_TEMPLATES ) {

		$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );

		if ( $id ) {
			$results = $this->get_email_template_by_id( $id );
		}

		?>

		<div id="content">

			<?php
			$param             = array(
				'page'       => WCF_CA_PAGE_NAME,
				'action'     => WCF_ACTION_EMAIL_TEMPLATES,
				'sub_action' => WCF_SUB_ACTION_SAVE_EMAIL_TEMPLATES,
			);
			$save_template_url = add_query_arg( $param, admin_url( '/admin.php' ) );
			?>

			<form method="post" action="<?php echo esc_url( $save_template_url ); ?>" id="wcf_settings">
				<input type="hidden" name="sub_action" value="<?php echo esc_attr( $sub_action ); ?>"/>
				<?php
				$id_by = '';
				if ( isset( $id ) ) {
					$id_by = $id;
				}
				?>
				<input type="hidden" name="id" value="<?php echo esc_attr( $id_by ); ?>"/>
				<?php

				$button_sub_action = 'save';
				$display_message   = 'Add New';

				if ( WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES === $sub_action ) {
					$button_sub_action = 'update';
					$display_message   = 'Edit';
				}
				print '<input type="hidden" name="wcf_settings_frm" value="' . esc_attr( $button_sub_action ) . '">';
				?>
				<div id="poststuff">
					<div> <!-- <div class="postbox" > -->
						<h3><?php /* translators: %s Message */ echo esc_html( sprintf( __( '%s Email Template:', 'woo-cart-abandonment-recovery' ), $display_message ) ); ?></h3>
						<hr/>
						<div>
							<table class="form-table" id="addedit_template">
								<tr>
									<th>
										<label for="wcf_email_subject"><b><?php esc_html_e( 'Activate Template now?', 'woo-cart-abandonment-recovery' ); ?></b></label>
									</th>
									<td>
										<?php
										$is_activated  = '';
										$active_status = 0;
										if ( WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES === $sub_action && $results && isset( $results->is_activated ) ) {
											$active_status = stripslashes( $results->is_activated );
											$is_activated  = $active_status ? 'on' : 'off';

										}
										print '<button type="button" class="wcf-ca-switch wcf-toggle-template-status" wcf-template-id="1" wcf-ca-template-switch="' . esc_attr( $is_activated ) . '"> ' . esc_html( $is_activated ) . ' </button>';
										print '<input type="hidden" name="wcf_activate_email_template" id="wcf_activate_email_template" value="' . esc_attr( $active_status ) . '" />';
										?>

									</td>
								</tr>

								<tr>
									<th>
										<label for="wcf_template_name"><b><?php esc_html_e( 'Template Name:', 'woo-cart-abandonment-recovery' ); ?></b></label>
									</th>
									<td>
										<?php
										$template_name = '';
										if ( WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES === $sub_action && $results && isset( $results->template_name ) ) {
											$template_name = $results->template_name;
										}
										print '<input type="text" name="wcf_template_name" id="wcf_template_name" class="wcf-ca-trigger-input" value="' . esc_attr( $template_name ) . '">';
										?>
									</td>
								</tr>

								<tr>
									<th>
										<label for="wcf_email_subject"><b><?php esc_html_e( 'Email Subject:', 'woo-cart-abandonment-recovery' ); ?></b></label>
									</th>
									<td>
										<?php
										$subject_edit = '';
										if ( WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES === $sub_action && $results && isset( $results->email_subject ) ) {
											$subject_edit = stripslashes( $results->email_subject );
										}
										print '<input type="text" name="wcf_email_subject" id="wcf_email_subject" class="wcf-ca-trigger-input" value="' . esc_attr( $subject_edit ) . '">';
										?>
									</td>
								</tr>

								<tr>
									<th>
										<label for="wcf_email_body"><b><?php esc_html_e( 'Email Body:', 'woo-cart-abandonment-recovery' ); ?></b></label>
									</th>
									<td>
										<?php
										$initial_data = '';
										if ( WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES === $sub_action && $results && isset( $results->email_body ) ) {
											$initial_data = stripslashes( $results->email_body );
										}

										wp_editor(
											$initial_data,
											'wcf_email_body',
											array(
												'media_buttons' => true,
												'textarea_rows' => 15,
												'tabindex' => 4,
												'tinymce'  => array(
													'theme_advanced_buttons1' => 'bold,italic,underline,|,bullist,numlist,blockquote,|,link,unlink,|,spellchecker,fullscreen,|,formatselect,styleselect',
												),
											)
										);

										?>
										<?php echo stripslashes( get_option( 'wcf_email_body' ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</td>
								</tr>

								<tr>
									<th>
										<label for="wcf_use_woo_email_style"><b><?php esc_html_e( 'Use WooCommerce email style', 'woo-cart-abandonment-recovery' ); ?></b></label>
									</th>
									<td>
										<?php

										$wcf_woo_email_style = '';
										if ( WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES === $sub_action && $results ) {
											$wcf_woo_email_style = $this->get_email_template_meta_by_key( $results->id, 'use_woo_email_style' );
											if ( isset( $wcf_woo_email_style->meta_value ) ) {
												$wcf_woo_email_style = $wcf_woo_email_style->meta_value ? 'checked' : '';
											}
										}

										print '<input ' . esc_attr( $wcf_woo_email_style ) . ' id="wcf_use_woo_email_style" name="wcf_use_woo_email_style" type="checkbox" value="" /><span class="description">' . esc_html__( 'Email will be sent in WooCommerce email format. Also the sender name and sender email address will be replaced by the Woocommerce Email sender options.', 'woo-cart-abandonment-recovery' ) . '</span>';
										?>

									</td>
								</tr>

								<tr>
									<th>
										<label for="wcf_override_global_coupon"><b><?php esc_html_e( 'Create Coupon', 'woo-cart-abandonment-recovery' ); ?></b></label>
									</th>
									<td>
										<?php

										$wcf_override_global_coupon = '';
										if ( WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES === $sub_action && $results ) {
											$wcf_override_global_coupon = $this->get_email_template_meta_by_key( $results->id, 'override_global_coupon' );
											if ( isset( $wcf_override_global_coupon->meta_value ) ) {
												$wcf_override_global_coupon = $wcf_override_global_coupon->meta_value ? 'checked' : '';
											}
										}

										print '<input ' . esc_attr( $wcf_override_global_coupon ) . ' id="wcf_override_global_coupon" name="wcf_override_global_coupon" type="checkbox" value="" /><span class="description">' . esc_html__( 'Allows you to send new coupon only for this template.', 'woo-cart-abandonment-recovery' ) . '</span>';
										?>
									</td>
								</tr>

								<tr>
									<th>
										<label class="wcf-sub-heading" for="wcf_email_discount_type"> <?php esc_html_e( 'Discount Type', 'woo-cart-abandonment-recovery' ); ?> </label>
									</th>
									<td>
										<?php

										$wcf_email_discount_type = 'percent';
										if ( WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES === $sub_action && $results ) {
											$wcf_email_discount_type = $this->get_email_template_meta_by_key( $results->id, 'discount_type' );
											if ( isset( $wcf_email_discount_type->meta_value ) ) {
												$wcf_email_discount_type = $wcf_email_discount_type->meta_value;
											}
										}

										$dropdown_options = array(
											'percent'    => 'Percentage discount',
											'fixed_cart' => 'Fixed cart discount',
										);

										echo '<select id="wcf_email_discount_type" name="wcf_email_discount_type">';
										foreach ( $dropdown_options as $key => $value ) {
											$is_selected = $key === $wcf_email_discount_type ? 'selected' : '';
											echo '<option ' . esc_html( $is_selected ) . ' value=' . esc_attr( $key ) . '>' . esc_html( $value ) . '</option>';

										}
										echo '</select>';

										?>
									</td>
								</tr>

								<tr>
									<th>
										<label class="wcf-sub-heading" for="wcf_email_discount_amount"> <?php esc_html_e( 'Coupon Amount', 'woo-cart-abandonment-recovery' ); ?> </label>
									</th>
									<td>
										<?php
										$wcf_email_discount_amount = 10;
										if ( WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES === $sub_action && $results ) {
											$wcf_email_discount_amount = $this->get_email_template_meta_by_key( $results->id, 'coupon_amount' );
											if ( isset( $wcf_email_discount_amount->meta_value ) ) {
												$wcf_email_discount_amount = $wcf_email_discount_amount->meta_value;
											}
										}
										print '<input class="wcf-ca-trigger-input wcf-ca-email-inputs" type="number" id="wcf_email_discount_amount" name="wcf_email_discount_amount" value="' . esc_attr( $wcf_email_discount_amount ) . '">';
										?>
									</td>
								</tr>

								<tr>
									<th>
										<label class="wcf-sub-heading" for="wcf_email_coupon_expiry_date"> <?php esc_html_e( 'Coupon expiry date', 'woo-cart-abandonment-recovery' ); ?> </label>
									</th>
									<td>
										<?php
											$wcf_email_coupon_expiry_date = 0;
										$coupon_expiry_unit               = 'hours';

										if ( WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES === $sub_action && $results ) {
											$wcf_email_coupon_expiry_date = $this->get_email_template_meta_by_key( $results->id, 'coupon_expiry_date' );
											$wcf_email_coupon_expiry_unit = $this->get_email_template_meta_by_key( $results->id, 'coupon_expiry_unit' );

											if ( isset( $wcf_email_coupon_expiry_date->meta_value ) ) {
												$wcf_email_coupon_expiry_date = $wcf_email_coupon_expiry_date->meta_value;
											}
											if ( isset( $wcf_email_coupon_expiry_unit->meta_value ) ) {
												$coupon_expiry_unit = $wcf_email_coupon_expiry_unit->meta_value;
											}
										}
										print '<input type="number" min="0" class="wcf-ca-trigger-input wcf-ca-coupon-inputs" id="wcf_email_coupon_expiry_date" name="wcf_email_coupon_expiry_date" value="' . intval( $wcf_email_coupon_expiry_date ) . '" autocomplete="off" />';
										$items = array(
											'hours' => esc_html__( 'Hour(s)', 'woo-cart-abandonment-recovery' ),
											'days'  => esc_html__( 'Day(s)', 'woo-cart-abandonment-recovery' ),
										);
										echo "<select id='wcf_coupon_expiry_unit' name='wcf_coupon_expiry_unit'>";
										foreach ( $items as $key => $item ) {
											$selected = ( $coupon_expiry_unit === $key ) ? 'selected="selected"' : '';
											// Can't use wp_kses_post as it does not allow option tag. Escaping attributes and content.
											echo "<option value='" . esc_attr( $key ) . "' $selected>" . esc_html( $item ) . '</option>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										}
										echo '</select>';

										echo " <span class='description'>" . esc_html__( 'Enter zero (0) to restrict coupon from expiring', 'woo-cart-abandonment-recovery' ) . ' </span>'
										?>
									</td>
								</tr>
								<tr>
									<th>
										<label class="wcf-sub-heading" for="wcf_free_shipping_coupon"> <?php esc_html_e( 'Free Shipping', 'woo-cart-abandonment-recovery' ); ?> </label>
									</th>
									<td>
										<?php

										$wcf_free_shipping_coupon = '';
										if ( WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES === $sub_action && $results ) {
											$wcf_free_shipping_coupon = $this->get_email_template_meta_by_key( $results->id, 'free_shipping_coupon' );
											if ( isset( $wcf_free_shipping_coupon->meta_value ) ) {
												$wcf_free_shipping_coupon = $wcf_free_shipping_coupon->meta_value ? 'checked' : '';
											}
										}

										print '<input ' . esc_attr( $wcf_free_shipping_coupon ) . ' id="wcf_free_shipping_coupon" name="wcf_free_shipping_coupon" type="checkbox" value="" /><span class="description"> ' . esc_html__( 'Allows you to grant free shipping. A free shipping method must be enabled in your shipping zone and be set to require "a valid free shipping coupon". ', 'woo-cart-abandonment-recovery' ) . '</span>';

										?>
									</td>
								</tr>
								<tr>
									<th>
										<label class="wcf-sub-heading" for="wcf_individual_use_only"><?php esc_html_e( 'Individual use only', 'woo-cart-abandonment-recovery' ); ?></label>
									</th>
									<td>
										<?php

										$wcf_individual_use_only = '';
										if ( WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES === $sub_action && $results ) {
											$wcf_individual_use_only = $this->get_email_template_meta_by_key( $results->id, 'individual_use_only' );
											if ( isset( $wcf_individual_use_only->meta_value ) ) {
												$wcf_individual_use_only = $wcf_individual_use_only->meta_value ? 'checked' : '';
											}
										}

										print '<input ' . esc_attr( $wcf_individual_use_only ) . ' id="wcf_individual_use_only" name="wcf_individual_use_only" type="checkbox" value="" />
                                        <span class="description">' . esc_html__( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'woo-cart-abandonment-recovery' ) . '   </span>';

										?>
									</td>
								</tr>
								<tr>
									<th>
										<label class="wcf-sub-heading" for="wcf_apply_coupon_auto"> <?php esc_html_e( 'Auto Apply Coupon', 'woo-cart-abandonment-recovery' ); ?> </label>
									</th>
									<td>
										<?php

										$wcf_apply_coupon_auto = '';
										if ( WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES === $sub_action && $results ) {
											$wcf_apply_coupon_auto = $this->get_email_template_meta_by_key( $results->id, 'auto_coupon' );

											if ( isset( $wcf_apply_coupon_auto->meta_value ) ) {
												$wcf_apply_coupon_auto = $wcf_apply_coupon_auto->meta_value ? 'checked' : '';
											}
										}

										print '<input ' . esc_attr( $wcf_apply_coupon_auto ) . ' id="wcf_auto_coupon_apply" name="wcf_auto_coupon_apply" type="checkbox" value="" /><span class="description" > ' . esc_html__( ' Automatically add the coupon to the cart at the checkout.', 'woo-cart-abandonment-recovery' ) . ' </span>';
										?>
									</td>
								</tr>
								<tr>
									<th>
										<label for="wcf_email_subject"><b><?php esc_html_e( 'Send This Email', 'woo-cart-abandonment-recovery' ); ?></b></label>
									</th>
									<td>
										<?php
										$frequency_edit = '';
										if ( WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES === $sub_action && $results && isset( $results->frequency ) ) {
											$frequency_edit = $results->frequency;
										}
										print '<input style="width:15%" type="number" min="0" name="wcf_email_frequency" id="wcf_email_frequency" class="wcf-ca-trigger-input" value="' . esc_attr( $frequency_edit ) . '">';
										?>

										<select name="wcf_email_frequency_unit" id="wcf_email_frequency_unit">
											<?php
											$frequency_unit = '';
											if ( WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES === $sub_action && $results && isset( $results->frequency_unit ) ) {
												$frequency_unit = $results->frequency_unit;
											}
											$days_or_hours = array(
												'MINUTE' => esc_html__( 'Minute(s)', 'woo-cart-abandonment-recovery' ),
												'HOUR'   => esc_html__( 'Hour(s)', 'woo-cart-abandonment-recovery' ),
												'DAY'    => esc_html__( 'Day(s)', 'woo-cart-abandonment-recovery' ),
											);
											foreach ( $days_or_hours as $key => $value ) {
												printf(
													"<option %s value='%s'>%s</option>\n",
													selected( $key, $frequency_unit, false ),
													esc_attr( $key ),
													esc_attr( $value )
												);
											}
											?>
										</select>
										<span class="description">
		<?php esc_html_e( 'after cart is abandoned.', 'woo-cart-abandonment-recovery' ); ?>
										</span>


									</td>
								</tr>

								<tr>
									<?php $current_user = wp_get_current_user(); ?>
									<th>
										<label for="wcf_email_preview"><b><?php esc_html_e( 'Send Test Email To:', 'woo-cart-abandonment-recovery' ); ?></b></label>
									</th>
									<td>
										<input class="wcf-ca-trigger-input" type="text" id="wcf_send_test_email" name="send_test_email" value="<?php echo esc_attr( $current_user->user_email ); ?>" class="wcf-ca-trigger-input">
										<input class="button" type="button" value=" <?php esc_html_e( 'Send a test email', 'woo-cart-abandonment-recovery' ); ?>" id="wcf_preview_email"/> <br/>

										<label id="mail_response_msg"> </label>
									</td>
								</tr>

							</table>
						</div>
					</div>
				</div>
				<?php wp_nonce_field( WCF_EMAIL_TEMPLATES_NONCE, '_wpnonce' ); ?>
				<p class="submit">
					<?php
					$button_value = esc_html__( 'Save Changes', 'woo-cart-abandonment-recovery' );
					if ( WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES === $sub_action ) {
						$button_value = esc_html__( 'Update Changes', 'woo-cart-abandonment-recovery' );
					}
					?>
					<input type="submit" name="Submit" class="button-primary" value="<?php echo esc_attr( $button_value ); ?>"/>
				</p>
			</form>
		</div>
		<?php

	}


	/**
	 * Sanitize email post data.
	 *
	 * @return array
	 */
	public function sanitize_email_post_data() {
		check_ajax_referer( WCF_EMAIL_TEMPLATES_NONCE, '_wpnonce' );
		$input_post_values = array(
			'wcf_email_subject'            => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_email_body'               => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			),
			'wcf_template_name'            => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_email_frequency'          => array(
				'default'  => 30,
				'sanitize' => FILTER_SANITIZE_NUMBER_INT,
			),
			'wcf_email_frequency_unit'     => array(
				'default'  => 'MINUTE',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_activate_email_template'  => array(
				'default'  => 0,
				'sanitize' => FILTER_SANITIZE_NUMBER_INT,
			),

			'wcf_email_discount_type'      => array(
				'default'  => 'percent',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_email_discount_amount'    => array(
				'default'  => 10,
				'sanitize' => FILTER_SANITIZE_NUMBER_INT,
			),
			'wcf_email_coupon_expiry_date' => array(
				'default'  => '',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'wcf_coupon_expiry_unit'       => array(
				'default'  => 'hours',
				'sanitize' => 'FILTER_SANITIZE_STRING',
			),
			'id'                           => array(
				'default'  => null,
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

		$sanitized_post['wcf_override_global_coupon'] = isset( $_POST['wcf_override_global_coupon'] ) ? true : false;
		$sanitized_post['wcf_auto_coupon_apply']      = isset( $_POST['wcf_auto_coupon_apply'] ) ? true : false;
		$sanitized_post['wcf_free_shipping_coupon']   = isset( $_POST['wcf_free_shipping_coupon'] ) ? true : false;
		$sanitized_post['wcf_individual_use_only']    = isset( $_POST['wcf_individual_use_only'] ) ? true : false;
		$sanitized_post['wcf_email_body']             = html_entity_decode( $sanitized_post['wcf_email_body'], ENT_COMPAT, 'UTF-8' );
		$sanitized_post['wcf_use_woo_email_style']    = isset( $_POST['wcf_use_woo_email_style'] ) ? true : false;

		return $sanitized_post;

	}


	/**
	 *  Add email template callback ajax.
	 */
	public function add_email_template() {

		$sanitized_post = $this->sanitize_email_post_data();
		global $wpdb;
		$wpdb->insert(
			$this->cart_abandonment_template_table_name,
			array(
				'template_name'  => $sanitized_post['wcf_template_name'],
				'email_subject'  => $sanitized_post['wcf_email_subject'],
				'email_body'     => $sanitized_post['wcf_email_body'],
				'frequency'      => $sanitized_post['wcf_email_frequency'],
				'frequency_unit' => $sanitized_post['wcf_email_frequency_unit'],
				'is_activated'   => $sanitized_post['wcf_activate_email_template'],
			),
			array( '%s', '%s', '%s', '%d', '%s', '%d' )
		); // db call ok; no cache ok.

		$email_template_id = $wpdb->insert_id;
		$meta_data         = array(
			'override_global_coupon' => $sanitized_post['wcf_override_global_coupon'],
			'discount_type'          => $sanitized_post['wcf_email_discount_type'],
			'coupon_amount'          => $sanitized_post['wcf_email_discount_amount'],
			'coupon_expiry_date'     => $sanitized_post['wcf_email_coupon_expiry_date'],
			'coupon_expiry_unit'     => $sanitized_post['wcf_coupon_expiry_unit'],
			'auto_coupon'            => $sanitized_post['wcf_auto_coupon_apply'],
			'free_shipping_coupon'   => $sanitized_post['wcf_free_shipping_coupon'],
			'individual_use_only'    => $sanitized_post['wcf_individual_use_only'],
			'use_woo_email_style'    => $sanitized_post['wcf_use_woo_email_style'],

		);

		foreach ( $meta_data as $mera_key => $meta_value ) {
			$this->add_email_template_meta( $email_template_id, $mera_key, $meta_value );
		}

		$param        = array(
			'page'                    => WCF_CA_PAGE_NAME,
			'action'                  => WCF_ACTION_EMAIL_TEMPLATES,
			'sub_action'              => WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES,
			'id'                      => $email_template_id,
			'wcf_ca_template_created' => 'YES',
		);
		$redirect_url = add_query_arg( $param, admin_url( '/admin.php' ) );
		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 *  Edit email template callback ajax.
	 */
	public function edit_email_template() {
		$sanitized_post    = $this->sanitize_email_post_data();
		$email_template_id = $sanitized_post['id'];
		global $wpdb;
		$wpdb->update(
			$this->cart_abandonment_template_table_name,
			array(
				'template_name'  => $sanitized_post['wcf_template_name'],
				'email_subject'  => $sanitized_post['wcf_email_subject'],
				'email_body'     => $sanitized_post['wcf_email_body'],
				'frequency'      => $sanitized_post['wcf_email_frequency'],
				'frequency_unit' => $sanitized_post['wcf_email_frequency_unit'],
				'is_activated'   => $sanitized_post['wcf_activate_email_template'],
			),
			array( 'id' => $email_template_id ),
			array( '%s', '%s', '%s', '%d', '%s', '%d' ),
			array( '%d' )
		); // db call ok; no cache ok.

		$meta_data = array(
			'override_global_coupon' => $sanitized_post['wcf_override_global_coupon'],
			'discount_type'          => $sanitized_post['wcf_email_discount_type'],
			'coupon_amount'          => $sanitized_post['wcf_email_discount_amount'],
			'coupon_expiry_date'     => $sanitized_post['wcf_email_coupon_expiry_date'],
			'coupon_expiry_unit'     => $sanitized_post['wcf_coupon_expiry_unit'],
			'auto_coupon'            => $sanitized_post['wcf_auto_coupon_apply'],
			'free_shipping_coupon'   => $sanitized_post['wcf_free_shipping_coupon'],
			'individual_use_only'    => $sanitized_post['wcf_individual_use_only'],
			'use_woo_email_style'    => $sanitized_post['wcf_use_woo_email_style'],

		);
		foreach ( $meta_data as $mera_key => $meta_value ) {
			$this->update_email_template_meta( $email_template_id, $mera_key, $meta_value );
		}

		$param        = array(
			'page'                    => WCF_CA_PAGE_NAME,
			'action'                  => WCF_ACTION_EMAIL_TEMPLATES,
			'sub_action'              => WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES,
			'id'                      => $email_template_id,
			'wcf_ca_template_updated' => 'YES',
		);
		$redirect_url = add_query_arg( $param, admin_url( '/admin.php' ) );

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 *  Restore default email templates.
	 */
	public function restore_email_templates() {

		$wpnonce = Cartflows_Ca_Helper::get_instance()->sanitize_text_filter( '_wpnonce', 'GET' );

		if ( $wpnonce && wp_verify_nonce( $wpnonce, WCF_EMAIL_TEMPLATES_NONCE ) ) {

			include_once CARTFLOWS_CA_DIR . 'modules/cart-abandonment/classes/class-cartflows-ca-database.php';
			$db = Cartflows_Ca_Database::get_instance();
			$db->template_table_seeder( true );

			$param        = array(
				'page'                     => WCF_CA_PAGE_NAME,
				'action'                   => WCF_ACTION_EMAIL_TEMPLATES,
				'wcf_ca_template_restored' => 'YES',
			);
			$redirect_url = add_query_arg( $param, admin_url( '/admin.php' ) );
			wp_safe_redirect( $redirect_url );
			exit;
		}

	}

	/**
	 * Update the meta values.
	 *
	 * @param integer $email_template_id email template id.
	 * @param string  $meta_key meta key.
	 * @param string  $meta_value meta value.
	 */
	public function update_email_template_meta( $email_template_id, $meta_key, $meta_value ) {

		$template_meta = $this->get_email_template_meta_by_key( $email_template_id, $meta_key );

		if ( $template_meta ) {
			global $wpdb;
			$wpdb->update(
				$this->email_templates_meta_table,
				array(
					'meta_value' => sanitize_text_field( $meta_value ), //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				),
				array(
					'email_template_id' => $email_template_id,
					'meta_key'          => sanitize_text_field( $meta_key ),
				)
			); // db call ok; no cache ok.
		} else {
			$this->add_email_template_meta( $email_template_id, $meta_key, $meta_value );
		}

	}


	/**
	 * Add the meta values.
	 *
	 * @param integer $email_template_id email template id.
	 * @param string  $meta_key meta key.
	 * @param string  $meta_value meta value.
	 */
	public function add_email_template_meta( $email_template_id, $meta_key, $meta_value ) {
		global $wpdb;
		$wpdb->insert(
			$this->email_templates_meta_table,
			array(
				'email_template_id' => $email_template_id,
				'meta_key'          => sanitize_text_field( $meta_key ),
				'meta_value'        => sanitize_text_field( $meta_value ), //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		); // db call ok; no cache ok.
	}

	/**
	 * Get the meta values.
	 *
	 * @param integer $email_template_id email template id.
	 * @param string  $meta_key meta key.
	 */
	public function get_email_template_meta_by_key( $email_template_id, $meta_key ) {
		global $wpdb;
		// Can't use placeholders for table/column names, it will be wrapped by a single quote (') instead of a backquote (`).
		return $wpdb->get_row(
			$wpdb->prepare( "select * from {$this->email_templates_meta_table} where email_template_id = %d AND meta_key = %s", $email_template_id, $meta_key ) //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		); // db call ok; no cache ok.
	}

	/**
	 *  Render email template grid.
	 */
	public function show_email_template_data_table() {
		$wcf_template_list = new Cartflows_Ca_Email_Templates_Table();
		$wcf_template_list->prepare_items();
		$page = Cartflows_Ca_Helper::get_instance()->sanitize_text_filter( 'page', 'GET' );
		?>
		<div class="wrap">
			<form id="wcf-cart-abandonment-template-table" method="GET">
				<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>"/>
				<input type="hidden" name="action" value="<?php echo esc_attr( WCF_ACTION_EMAIL_TEMPLATES ); ?>"/>
				<input type="hidden" name="sub_action" value="<?php echo esc_attr( WCF_SUB_ACTION_DELETE_BULK_EMAIL_TEMPLATES ); ?>"/>
				<input type="hidden" name="<?php echo esc_attr( WCF_SUB_ACTION_DELETE_BULK_EMAIL_TEMPLATES . '_nonce' ); ?>" value="<?php echo esc_attr( wp_create_nonce( WCF_SUB_ACTION_DELETE_BULK_EMAIL_TEMPLATES ) ); ?>"/>

				<?php $wcf_template_list->display(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 *  Render 'Add Email Template button'.
	 */
	public function show_add_new_template_button() {
		$param = array(
			'page'       => WCF_CA_PAGE_NAME,
			'action'     => WCF_ACTION_EMAIL_TEMPLATES,
			'sub_action' => WCF_SUB_ACTION_ADD_EMAIL_TEMPLATES,
		);

		$add_new_template_url = wp_nonce_url( add_query_arg( $param, admin_url( '/admin.php' ) ), WCF_EMAIL_TEMPLATES_NONCE );

		$param['sub_action']  = WCF_SUB_ACTION_RESTORE_EMAIL_TEMPLATES;
		$restore_template_url = wp_nonce_url( add_query_arg( $param, admin_url( '/admin.php' ) ), WCF_EMAIL_TEMPLATES_NONCE );

		?>
		<div class="wcf-ca-report-btn">
			<div  class="wcf-ca-left-report-field-group">
				<a style="cursor: pointer" href="<?php echo esc_url( $add_new_template_url ); ?>" class="button-secondary"><?php esc_html_e( 'Create New Template', 'woo-cart-abandonment-recovery' ); ?></a>
			</div>
			<div  class="wcf-ca-right-report-field-group">
				<a onclick="return confirm('Are you sure to restore email templates?');" style="cursor: pointer" href="<?php echo esc_url( $restore_template_url ); ?>" class="button-secondary"><?php esc_html_e( ' Restore Default Templates', 'woo-cart-abandonment-recovery' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Get all active templates.
	 *
	 * @return array|object|null
	 */
	public function fetch_all_active_templates() {
		global $wpdb;
		// Can't use placeholders for table/column names, it will be wrapped by a single quote (') instead of a backquote (`).
		$result = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$this->cart_abandonment_template_table_name} WHERE is_activated = %s", true ) //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		); // db call ok; no cache ok.
		return $result;
	}

	/**
	 * Get specific template by id.
	 *
	 * @param integer $tmpl_id template id.
	 * @return array|object|void|null
	 */
	public function get_template_by_id( $tmpl_id ) {
		global $wpdb;
		// Can't use placeholders for table/column names, it will be wrapped by a single quote (') instead of a backquote (`).
		$result = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$this->cart_abandonment_template_table_name} WHERE id = %s", $tmpl_id ) //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		); // db call ok; no cache ok.
		return $result;
	}

	/**
	 *  Get the email history.
	 *
	 * @param integer $email_history_id email history id.
	 * @return array|object|void|null
	 */
	public function get_email_history_by_id( $email_history_id ) {
		global $wpdb;
		$result = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$this->email_history_table} WHERE id = %s", $email_history_id ) //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		); // db call ok; no cache ok.
		return $result;
	}
}

Cartflows_Ca_Email_Templates::get_instance();
