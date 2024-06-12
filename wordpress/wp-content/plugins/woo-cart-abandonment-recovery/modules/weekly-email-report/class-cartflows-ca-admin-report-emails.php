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
 * Class Cartflows_Ca_Admin_Report_Emails.
 */
class Cartflows_Ca_Admin_Report_Emails {


	/**
	 * Class instance.
	 *
	 * @access private
	 * @var $instance Class instance.
	 */
	private static $instance;

		/**
		 * Initiator
		 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'schedule_weekly_report_email' ) );

		add_action( 'cartflows_ca_send_report_summary_email', array( $this, 'send_weekly_report_email' ) );

		add_action( 'admin_init', array( $this, 'unsubscribe_cart_abandonment_weekly_emails' ), 9 );

	}

	/**
	 * Sechedule the email.
	 */
	public function schedule_weekly_report_email() {

		$is_emails_enabled = get_option( 'wcf_ca_send_recovery_report_emails_to_admin', 'on' );

		if ( 'on' === $is_emails_enabled && function_exists( 'as_next_scheduled_action' ) && false === as_next_scheduled_action( 'cartflows_ca_send_report_summary_email' ) ) {

			$date = new DateTime( 'next monday 2pm' );

			// It will automatically reschedule the action once initiated.
			as_schedule_recurring_action( $date, WEEK_IN_SECONDS, 'cartflows_ca_send_report_summary_email' );
		} elseif ( 'on' !== $is_emails_enabled && as_next_scheduled_action( 'cartflows_ca_send_report_summary_email' ) ) {
				as_unschedule_all_actions( 'cartflows_ca_send_report_summary_email' );
		}
	}

	/**
	 * Send the email.
	 */
	public function send_weekly_report_email() {

		$is_emails_enabled = get_option( 'wcf_ca_send_recovery_report_emails_to_admin', 'on' );

		$admin_emails = get_option( 'wcf_ca_admin_email', get_option( 'admin_email' ) );

		if ( 'on' === $is_emails_enabled && ! empty( $admin_emails ) && apply_filters( 'cartflows_ca_send_weekly_report_email', true ) ) {

			$report_details = $this->get_last_week_report();

			if ( isset( $report_details['recovered_orders'] ) && 0 < intval( $report_details['recovered_orders'] ) ) {

				$subject = $this->get_email_subject();

				$headers  = 'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>' . "\r\n";
				$headers .= "Content-Type: text/html;\r\n";

				$admin_emails = preg_split( "/[\f\r\n]+/", $admin_emails );

				foreach ( $admin_emails as $admin_email ) {
					$user_info = get_user_by( 'email', $admin_email );
					$user_name = $user_info ? $user_info->display_name : __( 'There', 'woo-cart-abandonment-recovery' );

					$email_body = $this->get_email_content( $report_details, $user_name, $admin_email );
					// Ignoring the below rule as rule asking to use third party mailing functions but third-party SMTP plugins overrides the wp_mail and uses their mailing system.
					wp_mail( $admin_email, $subject, stripslashes( $email_body ), $headers ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_mail_wp_mail
				}
			}
		}
	}

	/**
	 * Get admin report email subject.
	 */
	public function get_email_subject() {

		return esc_html__( 'Your Weekly Cart Abandonment Summary is here - CartFlows', 'woo-cart-abandonment-recovery' );
	}

	/**
	 * Get admin report email content.
	 *
	 * @param array  $report_details report data.
	 * @param string $user_name user name.
	 * @param string $admin_email admin email.
	 */
	public function get_email_content( $report_details, $user_name, $admin_email ) {

		$cf_logo          = CARTFLOWS_CA_URL . 'admin/assets/images/cartflows-email-logo.png';
		$unsubscribe_link = add_query_arg(
			array(
				'page'                     => 'woo-cart-abandonment-recovery',
				'unsubscribe_weekly_email' => true,
				'email'                    => $admin_email,
			),
			admin_url( 'admin.php' )
		);

		$facebook_icon = CARTFLOWS_CA_URL . 'admin/assets/images/facebook2x.png';
		$twitter_icon  = CARTFLOWS_CA_URL . 'admin/assets/images/twitter2x.png';
		$youtube_icon  = CARTFLOWS_CA_URL . 'admin/assets/images/youtube2x.png';

		$from_date  = gmdate( 'M j', strtotime( '-7 days' ) );
		$to_date    = gmdate( 'M j' );
		$store_name = get_bloginfo( 'name' );

		return include CARTFLOWS_CA_DIR . 'modules/weekly-email-report/templates/email-body.php';
	}

	/**
	 *  Unsubscribe the user from the mailing list.
	 */
	public function unsubscribe_cart_abandonment_weekly_emails() {

		$unsubscribe = filter_input( INPUT_GET, 'unsubscribe_weekly_email', FILTER_VALIDATE_BOOLEAN );
		$page        = Cartflows_Ca_Helper::get_instance()->sanitize_text_filter( 'page', 'GET' );
		$email       = filter_input( INPUT_GET, 'email', FILTER_SANITIZE_EMAIL );

		if ( $unsubscribe && 'woo-cart-abandonment-recovery' === $page && ! empty( $email ) && is_user_logged_in() && current_user_can( 'manage_options' ) ) {

			$email_list = get_option( 'wcf_ca_admin_email', false );

			if ( ! empty( $email_list ) ) {
				$email_list = preg_split( "/[\f\r\n]+/", $email_list );

				$email_list = array_filter(
					$email_list,
					function( $e ) use ( $email ) {
						return ( $e !== $email );
					}
				);

				$email_list = array_filter( $email_list, 'sanitize_email' );

				$email_list = implode( "\n", $email_list );

				update_option( 'wcf_ca_admin_email', $email_list );
			}

			wp_die( esc_html__( 'You have successfully unsubscribed from our weekly emails list.', 'woo-cart-abandonment-recovery' ), esc_html__( 'Unsubscribed', 'woo-cart-abandonment-recovery' ) );
		}

	}

	/**
	 *  Get last week report.
	 */
	public function get_last_week_report() {

		$from_date       = gmdate( 'Y-m-d', strtotime( '-7 days' ) );
		$to_date         = gmdate( 'Y-m-d' );
		$conversion_rate = 0;

		$report_instance  = Cartflows_Ca_Tabs::get_instance();
		$abandoned_report = $report_instance->get_report_by_type( $from_date, $to_date, WCF_CART_ABANDONED_ORDER );
		$recovered_report = $report_instance->get_report_by_type( $from_date, $to_date, WCF_CART_COMPLETED_ORDER );
		$lost_report      = $report_instance->get_report_by_type( $from_date, $to_date, WCF_CART_LOST_ORDER );

		$total_orders = ( $recovered_report['no_of_orders'] + $abandoned_report['no_of_orders'] + $lost_report['no_of_orders'] );
		if ( $total_orders ) {
			$conversion_rate = ( $recovered_report['no_of_orders'] / $total_orders ) * 100;
		}

		$from_date                   = gmdate( 'Y-m-d', strtotime( '-30 days' ) );
		$last_month_recovered_report = $report_instance->get_report_by_type( $from_date, $to_date, WCF_CART_COMPLETED_ORDER );

		return array(
			'recovered_revenue'            => $recovered_report['revenue'],
			'recovered_orders'             => $recovered_report['no_of_orders'],
			'abandonded_orders'            => $abandoned_report['no_of_orders'],
			'last_month_recovered_Revenue' => $last_month_recovered_report['revenue'],
			'conversion_rate'              => number_format_i18n( $conversion_rate, 2 ),
		);
	}

}

Cartflows_Ca_Admin_Report_Emails::get_instance();
