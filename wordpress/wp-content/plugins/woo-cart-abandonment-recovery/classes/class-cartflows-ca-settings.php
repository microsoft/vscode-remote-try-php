<?php
/**
 * Settings.
 *
 * @package Woocommerce-Cart-Abandonment-Recovery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Cartflows_Ca_Utils.
 */
class Cartflows_Ca_Settings {


	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;


	/**
	 * Cartflows_Ca_Settings constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'wcf_initialize_settings' ) );
		add_filter( 'plugin_action_links_' . CARTFLOWS_CA_BASE, array( $this, 'add_action_links' ), 999 );
	}


	/**
	 * Adding action links for plugin list page.
	 *
	 * @param array $links links.
	 * @return array
	 */
	public function add_action_links( $links ) {
		$mylinks = array(
			'<a href="' . admin_url( 'admin.php?page=' . WCF_CA_PAGE_NAME ) . '">Settings</a>',
		);

		return array_merge( $mylinks, $links );
	}
	/**
	 * Add new settings for cart abandonment settings.
	 *
	 * @since 1.1.5
	 */
	public function wcf_initialize_settings() {

		// Start: Settings for cart abandonment.
		add_settings_section(
			WCF_CA_GENERAL_SETTINGS_SECTION,
			/* translators: %1$s: html start, %2$s: html end*/
			sprintf( __( '%1$sCart Abandonment Settings%2$s', 'woo-cart-abandonment-recovery' ), '<span class="wcf-ca-tracking-settings">', '</span>' ),
			array( $this, 'wcf_cart_abandonment_options_callback' ),
			WCF_CA_PAGE_NAME
		);

		add_settings_field(
			'wcf_ca_status',
			__( 'Enable Tracking', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_status_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_GENERAL_SETTINGS_SECTION,
			array( __( 'Start capturing abandoned carts. <br/><br/> <span class="description"><strong>Note:</strong> Cart will be considered abandoned if order is not completed in cart abandoned cut-off time.</span>', 'woo-cart-abandonment-recovery' ) )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_status'
		);

		add_settings_field(
			'wcf_ca_cron_run_time',
			__( 'Cart abandoned cut-off time', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_cron_run_time_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_GENERAL_SETTINGS_SECTION,
			array( __( 'Minutes. <br/><br/> <span class="description"><strong>Note:</strong> Consider cart abandoned after above entered minutes of item being added to cart and order not placed.</span><span class="dashicons dashicons-editor-help" title="Please remove the custom code to update cron cut off time, if added."></span>', 'woo-cart-abandonment-recovery' ) )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_cron_run_time'
		);

		add_settings_field(
			'wcf_ca_ignore_users',
			__( 'Disable Tracking For', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_ignore_users_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_GENERAL_SETTINGS_SECTION,
			array( '<br><span class="description"><strong>Note:</strong>' . __( ' It will ignore selected users from abandonment process when they logged in, and hence they can not receive mail for cart abandoned by themselves.', 'woo-cart-abandonment-recovery' ) . '</span>' )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_ignore_users'
		);

		add_settings_field(
			'wcf_ca_excludes_orders',
			__( 'Exclude email sending For', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_exclude_orders_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_GENERAL_SETTINGS_SECTION,
			array( '<br><span class="description"><strong>Note:</strong>' . __( ' It will not send future recovery emails to selected order status and will mark as recovered.', 'woo-cart-abandonment-recovery' ) . '</span>' )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_excludes_orders'
		);

		add_settings_field(
			'wcar_email_admin_on_recovery',
			__( 'Notify recovery to admin', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcar_email_admin_on_recovery' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_GENERAL_SETTINGS_SECTION,
			array( __( 'This option will send an email to admin on new order recovery.', 'woo-cart-abandonment-recovery' ) )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcar_email_admin_on_recovery'
		);

		add_settings_field(
			'wcf_ca_global_param',
			__( 'UTM parameters', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcar_add_param_to_recovery_url' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_GENERAL_SETTINGS_SECTION,
			array( '<br><span>' . __( 'The UTM parameters will be appended to the checkout page links which is available in the recovery emails.', 'woo-cart-abandonment-recovery' ) . '</span>' )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_global_param'
		);

		// End: General Settings for cart abandonment.
		// Start: Delete coupons settings for cart abandonment.

		add_settings_section(
			WCF_CA_COUPONS_SETTINGS_SECTION,
			/* translators: %1$s: html start, %2$s: html end*/
			sprintf( __( '%1$sCoupons Settings%2$s', 'woo-cart-abandonment-recovery' ), '<span class="wcf-ca-coupon-settings">', '</span>' ),
			array( $this, 'wcf_cart_abandonment_options_callback' ),
			WCF_CA_PAGE_NAME
		);

		add_settings_field(
			'wcf_ca_auto_delete_coupons',
			__( 'Delete Coupons Automatically', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_auto_delete_coupons_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_COUPONS_SETTINGS_SECTION,
			array( __( 'Delete coupons automatically on weekly basis.<br><span class="description"><br/><strong>Note:</strong> This option will set a weekly cron to delete all <strong>expired</strong> and <strong>used</strong> coupons automatically in the background.</p>', 'woo-cart-abandonment-recovery' ) )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_auto_delete_coupons'
		);

		add_settings_field(
			'wcf_ca_delete_coupons',
			__( 'Delete Coupons Manually', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_delete_coupons_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_COUPONS_SETTINGS_SECTION,
			array( '<br><span class="description"> ' . __( '<br><strong>Note:</strong> This will delete all <strong>expired</strong> and <strong>used</strong> coupons that were created by Woo Cart Abandonment Recovery.</p>', 'woo-cart-abandonment-recovery' ) )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_delete_coupons'
		);

		// End: Delete coupons settings for cart abandonment.
		// Start: Settings for email templates.
		add_settings_section(
			WCF_CA_EMAIL_SETTINGS_SECTION,
			/* translators: %1$s: html start, %2$s: html end*/
			sprintf( __( '%1$sEmail Settings%2$s', 'woo-cart-abandonment-recovery' ), '<span class="wcf-ca-email-settings">', '</span>' ),
			array( $this, 'wcf_cart_abandonment_options_callback' ),
			WCF_CA_PAGE_NAME
		);

		add_settings_field(
			'wcf_ca_from_name',
			__( '"From" Name', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_from_name_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_EMAIL_SETTINGS_SECTION,
			array( __( 'Name will appear in email sent.', 'woo-cart-abandonment-recovery' ) )
		);

		add_settings_field(
			'wcf_ca_from_email',
			__( '"From" Address', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_from_email_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_EMAIL_SETTINGS_SECTION,
			array( __( 'Email which send from.', 'woo-cart-abandonment-recovery' ) )
		);

		add_settings_field(
			'wcf_ca_reply_email',
			__( '"Reply To" Address', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_reply_email_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_EMAIL_SETTINGS_SECTION,
			array( __( 'When a user clicks reply, which email address should that reply be sent to?', 'woo-cart-abandonment-recovery' ) )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_from_name'
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_from_email',
			array( $this, 'wcf_ca_from_email_validation' )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_reply_email',
			array( $this, 'wcf_ca_reply_email_validation' )
		);
		// End: Settings for email templates.
		// Start: Settings for recovery report email templates.
		add_settings_section(
			WCF_CA_RECOVERY_EMAIL_SETTINGS_SECTION,
			/* translators: %1$s: html start, %2$s: html end*/
			sprintf( __( '%1$sRecovery Report Emails%2$s', 'woo-cart-abandonment-recovery' ), '<span id="wcf-ca-weekly-report-email-settings">', '<span/>' ),
			array( $this, 'wcf_cart_abandonment_options_callback' ),
			WCF_CA_PAGE_NAME
		);

		add_settings_field(
			'wcf_ca_send_recovery_report_emails_to_admin',
			__( 'Send recovery report emails', 'woo-cart-abandonment-recovery' ),
			array( $this, 'send_recovery_report_emails_to_admin' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_RECOVERY_EMAIL_SETTINGS_SECTION,
			array( __( 'Enable sending recovery report emails.', 'woo-cart-abandonment-recovery' ) )
		);

		add_settings_field(
			'wcf_ca_admin_email',
			__( 'Email address', 'woo-cart-abandonment-recovery' ),
			array( $this, 'admin_email_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_RECOVERY_EMAIL_SETTINGS_SECTION,
			array( '<br><span>' . __( 'Email address to send recovery report emails. For multiple emails, add each email address per line.', 'woo-cart-abandonment-recovery' ) . '</span>' )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_send_recovery_report_emails_to_admin'
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_admin_email',
			array( $this, 'wcf_ca_report_emails_validation' )
		);
		// End: Settings for recovery report email templates.
		// Start: Settings for coupon code.
		add_settings_field(
			'wcf_ca_zapier_tracking_status',
			__( 'Enable Webhook', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_zapier_tracking_status_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_ZAPIER_SETTINGS_SECTION,
			array( __( 'Allows you to trigger webhook automatically upon cart abandonment and recovery.', 'woo-cart-abandonment-recovery' ) )
		);

		add_settings_field(
			'wcf_ca_zapier_cart_abandoned_webhook',
			__( 'Webhook URL', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_zapier_cart_abandoned_webhook_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_ZAPIER_SETTINGS_SECTION,
			array( '', 'woo-cart-abandonment-recovery' )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_zapier_tracking_status'
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_zapier_cart_abandoned_webhook'
		);

		add_settings_section(
			WCF_CA_ZAPIER_SETTINGS_SECTION,
			/* translators: %1$s: html start, %2$s: html end*/
			sprintf( __( '%1$sCoupon Code Settings%2$s', 'woo-cart-abandonment-recovery' ), '<span class="wcf-ca-webhook-coupon-settings">', '</span>' ),
			array( $this, 'wcf_cart_abandonment_options_callback' ),
			WCF_CA_PAGE_NAME
		);

		add_settings_field(
			'wcf_ca_coupon_code_status',
			__( 'Create Coupon Code', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_coupon_code_status_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_ZAPIER_SETTINGS_SECTION,
			array( __( 'Auto-create the special coupon for the abandoned cart to send over the emails.', 'woo-cart-abandonment-recovery' ) )
		);

		add_settings_field(
			'wcf_ca_discount_type',
			__( 'Discount Type', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_discount_type_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_ZAPIER_SETTINGS_SECTION,
			array( '', 'woo-cart-abandonment-recovery' )
		);

		add_settings_field(
			'wcf_ca_coupon_amount',
			__( 'Coupon Amount', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_coupon_amount_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_ZAPIER_SETTINGS_SECTION,
			array( '', 'woo-cart-abandonment-recovery' )
		);

		add_settings_field(
			'wcf_ca_coupon_expiry',
			__( 'Coupon Expires After', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_coupon_expiry_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_ZAPIER_SETTINGS_SECTION,
			array( __( '<br/><br/> <span class="description"><strong>Note: </strong>. Enter zero (0) to restrict coupon from expiring.</span>', 'woo-cart-abandonment-recovery' ) )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_coupon_expiry'
		);
		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_coupon_expiry_unit'
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_coupon_code_status'
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_discount_type'
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_coupon_amount',
			array( $this, 'wcf_ca_coupon_amount_validation' )
		);
		// End: Settings for coupon code.
		// Start: Settings for Zapier.
		add_settings_section(
			WCF_CA_ZAPIER_SETTINGS_SECTION,
			/* translators: %1$s: html start, %2$s: html end*/
			sprintf( __( '%1$sWebhook Settings%2$s', 'woo-cart-abandonment-recovery' ), '<span class="wcf-ca-webhook-settings">', '</span>' ),
			array( $this, 'wcf_cart_abandonment_options_callback' ),
			WCF_CA_PAGE_NAME
		);

		// End: Settings for webhook.
		// Start: GDPR Settings.
		add_settings_section(
			WCF_CA_GDPR_SETTINGS_SECTION,
			/* translators: %1$s: html start, %2$s: html end*/
			sprintf( __( '%1$sGDPR Settings%2$s', 'woo-cart-abandonment-recovery' ), '<span class="wcf-ca-gdpr-settings">', '</span>' ),
			array( $this, 'wcf_cart_abandonment_options_callback' ),
			WCF_CA_PAGE_NAME
		);

		add_settings_field(
			'wcf_ca_gdpr_status',
			__( 'Enable GDPR Integration', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_gdpr_status_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_GDPR_SETTINGS_SECTION,
			array( __( 'Ask confirmation from the user before tracking data. <br/><br/> <span class="description"><strong>Note:</strong> By checking this, it will show up confirmation text below the email id on checkout page.</span>', 'woo-cart-abandonment-recovery' ) )
		);

		add_settings_field(
			'wcf_ca_gdpr_message',
			__( 'GDPR Message', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_gdpr_message_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_GDPR_SETTINGS_SECTION,
			array( '', 'woo-cart-abandonment-recovery' )
		);

		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_gdpr_status'
		);
		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_gdpr_message'
		);

		// End: GDPR Settings.
		// Start: Plugin settings.

		add_settings_section(
			WCF_CA_PLUGIN_SETTINGS_SECTION,
			/* translators: %1$s: html start, %2$s: html end*/
			sprintf( __( '%1$sPlugin Settings%2$s', 'woo-cart-abandonment-recovery' ), '<span class="wcf-ca-plugin-settings">', '</span>' ),
			array( $this, 'wcf_cart_abandonment_options_callback' ),
			WCF_CA_PAGE_NAME
		);
		register_setting(
			WCF_CA_SETTINGS_OPTION_GROUP,
			'wcf_ca_delete_plugin_data'
		);
		add_settings_field(
			'wcf_ca_delete_plugin_data',
			__( 'Delete Plugin Data', 'woo-cart-abandonment-recovery' ),
			array( $this, 'wcf_ca_delete_plugin_data_callback' ),
			WCF_CA_PAGE_NAME,
			WCF_CA_PLUGIN_SETTINGS_SECTION,
			array( __( 'Enabling this option will delete the plugin data while deleting the Plugin.', 'woo-cart-abandonment-recovery' ) )
		);

	}

	/**
	 * Callback for send recovery report email to admin.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	public function send_recovery_report_emails_to_admin( $args ) {
		$email_admin_on_recovery = get_option( 'wcf_ca_send_recovery_report_emails_to_admin', 'on' );

		$html = '';
		printf(
			'<input type="checkbox" id="wcf_ca_send_recovery_report_emails_to_admin" name="wcf_ca_send_recovery_report_emails_to_admin" value="on"
			' . checked( 'on', $email_admin_on_recovery, false ) . ' />'
		);
		$html .= '<label for="wcf_ca_send_recovery_report_emails_to_admin"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}

	/**
	 * Callback for send recovery report email to admin.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	public function admin_email_callback( $args ) {
		$wcf_ca_admin_email = get_option( 'wcf_ca_admin_email', get_option( 'admin_email' ) );

		$html = '';
		printf(
			'<textarea rows="4" cols="60" class="wcf-ca-recovery-email-inputs" id="wcf_ca_admin_email" name="wcf_ca_admin_email" spellcheck="false" placeholder=" ' . __( 'Add email address per line.', 'woo-cart-abandonment-recovery' ) . '">%s</textarea>',
			isset( $wcf_ca_admin_email ) ? esc_attr( $wcf_ca_admin_email ) : ''
		);
		$html .= '<label for="wcf_ca_admin_email"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}

	/**
	 * Callback for add utm param.
	 *
	 * @param array $args args.
	 * @since 1.2.13
	 */
	public function wcar_add_param_to_recovery_url( $args ) {
		$wcf_ca_global_param = get_option( 'wcf_ca_global_param', false );
		$html                = '';
		printf(
			'<textarea rows="4" cols="60" id="wcf_ca_global_param" name="wcf_ca_global_param" spellcheck="false" placeholder=" ' . __( 'Add UTM parameter per line.', 'woo-cart-abandonment-recovery' ) . '">%s</textarea>',
			isset( $wcf_ca_global_param ) ? esc_attr( $wcf_ca_global_param ) : ''
		);
		$html .= '<label for="wcf_ca_global_param"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}

	/**
	 * Callback for cart abandonment status.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	public function wcf_ca_delete_plugin_data_callback( $args ) {
		$wcf_ca_delete_plugin_data = get_option( 'wcf_ca_delete_plugin_data' );
		$html                      = '';
		printf(
			'<input type="checkbox" id="wcf_ca_delete_plugin_data" name="wcf_ca_delete_plugin_data" value="on"
            ' . checked( 'on', $wcf_ca_delete_plugin_data, false ) . ' />'
		);
		$html .= '<label for="wcf_ca_delete_plugin_data"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}


	/**
	 * Callback for cart abandonment status.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	public function wcf_ca_coupon_code_status_callback( $args ) {
		$wcf_ca_coupon_code_status = get_option( 'wcf_ca_coupon_code_status' );
		$html                      = '';
		printf(
			'<input type="checkbox" id="wcf_ca_coupon_code_status" name="wcf_ca_coupon_code_status" value="on"
            ' . checked( 'on', $wcf_ca_coupon_code_status, false ) . ' />'
		);
		$html .= '<label for="wcf_ca_coupon_code_status"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}


	/**
	 * Callback for cart abandonment cut off time.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	public function wcf_ca_zapier_cart_abandoned_webhook_callback( $args ) {
		$wcf_ca_zapier_cart_abandoned_webhook = get_option( 'wcf_ca_zapier_cart_abandoned_webhook' );
		echo '<input type="text" class="wcf-ca-trigger-input" id="wcf_ca_zapier_cart_abandoned_webhook" name="wcf_ca_zapier_cart_abandoned_webhook" value="' . esc_attr( sanitize_text_field( $wcf_ca_zapier_cart_abandoned_webhook ) ) . '" />';
		echo '<button id="wcf_ca_trigger_web_hook_abandoned_btn" type="button" class="button"> Trigger Sample </button>';
		echo '<span style="margin-left: 10px;" id="wcf_ca_abandoned_btn_message"></span>';
		$html = '<label for="wcf_ca_zapier_cart_abandoned_webhook"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}


	/**
	 * Callback for cart abandonment status.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	public function wcf_ca_zapier_tracking_status_callback( $args ) {
		$wcf_ca_zapier_tracking_status = get_option( 'wcf_ca_zapier_tracking_status' );

		$html = '';
		printf(
			'<input type="checkbox" id="wcf_ca_zapier_tracking_status" name="wcf_ca_zapier_tracking_status" value="on"
            ' . checked( 'on', $wcf_ca_zapier_tracking_status, false ) . ' />'
		);
		$html .= '<label for="wcf_ca_zapier_tracking_status"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}

	/**
	 * Callback for send email to admin.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	public function wcar_email_admin_on_recovery( $args ) {
		$email_admin_on_recovery = get_option( 'wcar_email_admin_on_recovery' );

		$html = '';
		printf(
			'<input type="checkbox" id="wcar_email_admin_on_recovery" name="wcar_email_admin_on_recovery" value="on"
			' . checked( 'on', $email_admin_on_recovery, false ) . ' />'
		);
		$html .= '<label for="wcar_email_admin_on_recovery"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}

	/**
	 * Callback for cart abandonment cut off time.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	public function wcf_ca_coupon_amount_callback( $args ) {
		$wcf_ca_coupon_amount = get_option( 'wcf_ca_coupon_amount' );
		printf(
			'<input type="number" class="wcf-ca-trigger-input wcf-ca-email-inputs" id="wcf_ca_coupon_amount" name="wcf_ca_coupon_amount" value="%s" />',
			isset( $wcf_ca_coupon_amount ) ? esc_attr( $wcf_ca_coupon_amount ) : ''
		);
		$html = '<label for="wcf_ca_coupon_amount"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}

	/**
	 * Callback for cart abandonment cut off time.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	public function wcf_ca_coupon_expiry_callback( $args ) {
		$wcf_ca_coupon_expiry = intval( get_option( 'wcf_ca_coupon_expiry' ) );
		printf(
			'<input type="number" min="0" class="wcf-ca-trigger-input wcf-ca-coupon-inputs" id="wcf_ca_coupon_expiry" name="wcf_ca_coupon_expiry" value="%s" autocomplete="off" />',
			isset( $wcf_ca_coupon_expiry ) ? esc_attr( $wcf_ca_coupon_expiry ) : ''
		);

		$coupon_expiry_unit = get_option( 'wcf_ca_coupon_expiry_unit' );
		$items              = array(
			'hours' => __( 'Hour(s)', 'woo-cart-abandonment-recovery' ),
			'days'  => __( 'Day(s)', 'woo-cart-abandonment-recovery' ),
		);
		echo "<select id='wcf_ca_coupon_expiry_unit' name='wcf_ca_coupon_expiry_unit'>";
		foreach ( $items as $key => $item ) {
			$selected = ( $coupon_expiry_unit === $key ) ? 'selected="selected"' : '';
			// Can't use wp_kses_post as it does not allow option tag. Escaping attributes and content.
			echo '<option value=' . esc_attr( $key ) . " $selected>" . esc_html( $item ) . '</option>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</select>';

		$html = '<label for="wcf_ca_coupon_expiry_unit"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}



	/**
	 * Callback for cart abandonment cut off time.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	public function wcf_ca_gdpr_message_callback( $args ) {
		$wcf_ca_gdpr_message = get_option( 'wcf_ca_gdpr_message' );

		printf(
			'<textarea rows="2" cols="60" id="wcf_ca_gdpr_message" name="wcf_ca_gdpr_message" spellcheck="false">%s</textarea>',
			isset( $wcf_ca_gdpr_message ) ? esc_attr( $wcf_ca_gdpr_message ) : ''
		);
		$html = '<label for="wcf_ca_gdpr_message"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}

	/**
	 * Callback for cart abandonment cut off time.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	public function wcf_ca_discount_type_callback( $args ) {

		$discount_type = get_option( 'wcf_ca_discount_type' );
		$items         = array(
			'percent'    => __( 'Percentage discount', 'woo-cart-abandonment-recovery' ),
			'fixed_cart' => __( 'Fixed cart discount', 'woo-cart-abandonment-recovery' ),
		);
		echo "<select id='wcf_ca_discount_type' name='wcf_ca_discount_type'>";
		foreach ( $items as $key => $item ) {
			$selected = ( $discount_type === $key ) ? 'selected="selected"' : '';
			echo '<option value= ' . esc_attr( $key ) . " $selected>" . esc_html( $item ) . '</option>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</select>';
	}

	/**
	 * Validation for cart abandonment `cut-off` settings.
	 *
	 * @param array $input input.
	 * @since 1.1.5
	 */
	public function wcf_ca_coupon_amount_validation( $input ) {

		$output = '';
		if ( ( is_numeric( $input ) && $input >= 1 ) ) {
			$output = stripslashes( $input );
		} else {
			add_settings_error(
				'wcf_ca_coupon_amount',
				'error found',
				__( 'Coupon code should be numeric and has to be greater than or equals to 1.', 'woo-cart-abandonment-recovery' )
			);
		}
		return $output;
	}

	/**
	 * Callback for cart abandonment options.
	 *
	 * @since 1.1.5
	 */
	public function wcf_cart_abandonment_options_callback() {
		echo '<hr/>';
	}


	/**
	 * Callback for cart abandonment status.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	public function wcf_ca_status_callback( $args ) {
		$wcf_ca_status = get_option( 'wcf_ca_status' );
		$html          = '';
		printf(
			'<input type="checkbox" id="wcf_ca_status" name="wcf_ca_status" value="on"
            ' . checked( 'on', $wcf_ca_status, false ) . ' />'
		);
		$html .= '<label for="wcf_ca_status"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}

	/**
	 * Callback for cart abandonment status.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	public static function wcf_ca_cron_run_time_callback( $args ) {
		$wcf_ca_cron_run_time = apply_filters( 'woo_ca_update_order_cron_interval', get_option( 'wcf_ca_cron_run_time', 20 ) );
		printf(
			'<input class="wcf-ca-trigger-input wcf-ca-email-inputs" type="number" min="10" id="wcf_ca_cron_run_time" name="wcf_ca_cron_run_time" value="%s" />',
			esc_attr( $wcf_ca_cron_run_time )
		);

		$html = '<label for="wcf_ca_cron_run_time"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}


	/**
	 * Callback for ignore users from tracking cart.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	public function wcf_ca_ignore_users_callback( $args ) {

		$wcf_ca_ignore_users = get_option( 'wcf_ca_ignore_users' );
		$html                = '';
		$roles_obj           = new WP_Roles();
		$roles_names_array   = $roles_obj->get_names();
		$roles_names_array   = array_diff( $roles_names_array, array( 'Customer' ) );
		?>
		<p class="wcf_ca_ignore_users" name="wcf_ca_ignore_users" multiple="multiple">
				<?php
				foreach ( $roles_names_array as $role_name ) {
					?>
					<input type="checkbox" name="wcf_ca_ignore_users[]"
					<?php
					if ( ! empty( $wcf_ca_ignore_users ) ) {
						foreach ( $wcf_ca_ignore_users as $user ) {
							checked( $user, $role_name );
						}
					}
					?>
					value="<?php echo esc_attr( $role_name ); ?>">
					<?php
					echo esc_attr( $role_name );
					echo '<br> ';
				}
				?>
		</p>
		<?php
		$html .= '<span for="wcf_ca_ignore_users"> ' . $args[0] . '</span>';
		echo wp_kses_post( $html );
	}

	/**
	 * Callback for ignore users from tracking cart.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	public function wcf_ca_exclude_orders_callback( $args ) {
		$wcf_ca_excludes_orders = get_option( 'wcf_ca_excludes_orders', array() );

		$html             = '';
		$order_status     = wc_get_order_statuses();
		$new_order_status = str_replace( 'wc-', '', array_keys( $order_status ) );
		$order_status     = array_combine( $new_order_status, $order_status );
		$order_status     = \array_diff( $order_status, array( 'Refunded', 'Draft', 'Cancelled' ) );
		?>
		<p class="wcf-ca-excludes-orders" name="wcf-ca-excludes-orders">
			<?php
			foreach ( $order_status as $key => $value ) {
				?>
				<input type="checkbox" name="wcf_ca_excludes_orders[]"
				<?php
				if ( is_array( $wcf_ca_excludes_orders ) && ! empty( $wcf_ca_excludes_orders ) && in_array( $key, $wcf_ca_excludes_orders, true ) ) {
					checked( true, true );
				}
				?>
				value="<?php echo esc_attr( $key ); ?>">
				<?php
				echo esc_attr( $value );
				echo '<br> ';
			}
			?>
		</p>

		<?php
		$html .= '<span for="wcf_ca_excludes_orders"> ' . $args[0] . '</span>';
		echo wp_kses_post( $html );
	}
	/**
	 * Delete coupons.
	 *
	 * @param array $args args.
	 */
	public function wcf_ca_auto_delete_coupons_callback( $args ) {
		$wcf_ca_auto_delete_coupons = get_option( 'wcf_ca_auto_delete_coupons' );
		$html                       = '';
		printf(
			'<input type="checkbox" id="wcf_ca_auto_delete_coupons" name="wcf_ca_auto_delete_coupons" value="on"
  		 	' . checked( 'on', $wcf_ca_auto_delete_coupons, false ) . ' />'
		);
		$html .= '<span for="wcf_ca_auto_delete_coupons"> ' . $args[0] . '</span>';
		echo wp_kses_post( $html );
	}

	/**
	 * Delete coupons.
	 *
	 * @param array $args args.
	 */
	public function wcf_ca_delete_coupons_callback( $args ) {
		?>

		<input type="button" class="button-secondary" id="wcf_ca_delete_coupons" value="<?php esc_attr_e( 'Delete', 'woo-cart-abandonment-recovery' ); ?>" >
		<span class="spinner wcf-ca-spinner"></span>
		<span class="wcf-ca-response-msg"></span>
		<?php
		$html  = '';
		$html .= '<span for="wcf_ca_delete_coupons"> ' . $args[0] . '</span>';
		echo wp_kses_post( $html );
	}

	/**
	 * Callback for cart abandonment status.
	 *
	 * @param array $args args.
	 * @since 1.1.5
	 */
	public function wcf_ca_gdpr_status_callback( $args ) {
		$wcf_ca_gdpr_status = get_option( 'wcf_ca_gdpr_status' );
		$html               = '';
		printf(
			'<input type="checkbox" id="wcf_ca_gdpr_status" name="wcf_ca_gdpr_status" value="on"
            ' . checked( 'on', $wcf_ca_gdpr_status, false ) . ' />'
		);
		$html .= '<label for="wcf_ca_gdpr_status"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}

	/**
	 * Callback for email from name.
	 *
	 * @param array $args Arguments.
	 */
	public static function wcf_ca_from_name_callback( $args ) {
		$wcf_ca_from_name = get_option( 'wcf_ca_from_name' );
		printf(
			'<input class="wcf-ca-trigger-input wcf-ca-email-inputs" type="text" id="wcf_ca_from_name" name="wcf_ca_from_name" value="%s" />',
			isset( $wcf_ca_from_name ) ? esc_attr( $wcf_ca_from_name ) : ''
		);
		$html = '<label for="wcf_ca_from_name"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}

	/**
	 * Callback for email from.
	 *
	 * @param array $args Arguments.
	 */
	public static function wcf_ca_from_email_callback( $args ) {
		$wcf_ca_from_email = get_option( 'wcf_ca_from_email' );
		printf(
			'<input class="wcf-ca-trigger-input wcf-ca-email-inputs" type="text" id="wcf_ca_from_email" name="wcf_ca_from_email" value="%s" />',
			isset( $wcf_ca_from_email ) ? esc_attr( $wcf_ca_from_email ) : ''
		);
		$html = '<label for="wcf_ca_from_email"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}

	/**
	 * Callback for email reply.
	 *
	 * @param array $args Arguments.
	 * @since 3.5
	 */
	public static function wcf_ca_reply_email_callback( $args ) {
		$wcf_ca_reply_email = get_option( 'wcf_ca_reply_email' );
		printf(
			'<input class="wcf-ca-trigger-input wcf-ca-email-inputs" type="text" id="wcf_ca_reply_email" name="wcf_ca_reply_email" value="%s" />',
			isset( $wcf_ca_reply_email ) ? esc_attr( $wcf_ca_reply_email ) : ''
		);

		$html = '<label for="wcf_ca_reply_email"> ' . $args[0] . '</label>';
		echo wp_kses_post( $html );
	}


	/**
	 * Validation for email.
	 *
	 * @param string $input input.
	 * @since 1.1.5
	 */
	public function wcf_ca_report_emails_validation( $input ) {

		if ( ! empty( $input ) ) {

			$admin_emails = preg_split( "/[\f\r\n]+/", $input );
			$emails_ids   = array();

			foreach ( $admin_emails as $admin_email ) {
				if ( is_email( $admin_email ) ) {
					array_push( $emails_ids, sanitize_email( $admin_email ) );
				}
			}

			$emails_ids = implode( "\n", $emails_ids );

			return $emails_ids;
		}

		return $input;
	}

	/**
	 * Validation for email.
	 *
	 * @param array $input input.
	 * @since 1.1.5
	 */
	public function wcf_ca_from_email_validation( $input ) {

		if ( $input && ! is_email( $input ) ) {
			add_settings_error(
				'wcf_ca_from_email',
				'error found',
				__( 'Invalid email "From" address field', 'woo-cart-abandonment-recovery' )
			);
		}
		return sanitize_email( $input );
	}

	/**
	 * Validation for reply email.
	 *
	 * @param array $input input.
	 * @since 1.1.5
	 */
	public function wcf_ca_reply_email_validation( $input ) {

		if ( $input && ! is_email( $input ) ) {
			add_settings_error(
				'wcf_ca_reply_email',
				'error found',
				__( 'Invalid email "Reply" address field', 'woo-cart-abandonment-recovery' )
			);
		}
		return sanitize_email( $input );
	}

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}




}
Cartflows_Ca_Settings::get_instance();
