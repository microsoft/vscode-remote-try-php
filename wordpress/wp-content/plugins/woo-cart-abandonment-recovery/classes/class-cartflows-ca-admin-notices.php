<?php
/**
 * CartFlows Ca Admin Notices.
 *
 * @package CartFlows
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Class Cartflows_Ca_Admin_Notices.
 */
class Cartflows_Ca_Admin_Notices {

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'show_admin_notices' ) );

		add_action( 'admin_notices', array( $this, 'show_weekly_report_email_settings_notice' ) );

		add_action( 'wp_ajax_wcar_disable_weekly_report_email_notice', array( $this, 'disable_weekly_report_email_notice' ) );
	}

	/**
	 *  Show admin notices.
	 */
	public function show_admin_notices() {

		$image_path = esc_url( CARTFLOWS_CA_URL . 'admin/assets/images/wcar-icon.png' );
		$review_url = esc_url( apply_filters( 'woo_ca_plugin_review_url', 'https://wordpress.org/support/plugin/woo-cart-abandonment-recovery/reviews/?filter=5#new-post' ) );


		Astra_Notices::add_notice(
			array(
				'id'                   => 'cartflows-ca-5-star-notice',
				'type'                 => 'info',
				'class'                => 'cartflows-ca-5-star',
				'show_if'              => true,
				/* translators: %1$s white label plugin name and %2$s deactivation link */
				'message'              => sprintf(
					'<div class="notice-image" style="display: flex;">
                        <img src="%1$s" class="custom-logo" alt="CartFlows Icon" itemprop="logo" style="max-width: 90px;"></div>
                        <div class="notice-content">
                            <div class="notice-heading">
                                %2$s
                            </div>
                            %3$s<br />
                            <div class="astra-review-notice-container">
                                <a href="%4$s" class="astra-notice-close astra-review-notice button-primary" target="_blank">
                                %5$s
                                </a>
                            <span class="dashicons dashicons-calendar"></span>
                                <a href="#" data-repeat-notice-after="%6$s" class="astra-notice-close astra-review-notice">
                                %7$s
                                </a>
                            <span class="dashicons dashicons-smiley"></span>
                                <a href="#" class="astra-notice-close astra-review-notice">
                                %8$s
                                </a>
                            </div>
                        </div>',
					$image_path,
					__( 'Hello! Seems like you have used WooCommerce Cart Abandonment Recovery by CartFlows plugin to recover abandoned carts. &mdash; Thanks a ton!', 'woo-cart-abandonment-recovery' ),
					__( 'Could you please do us a BIG favor and give it a 5-star rating on WordPress? This would boost our motivation and help other users make a comfortable decision while choosing the CartFlows cart abandonment plugin.', 'woo-cart-abandonment-recovery' ),
					$review_url,
					__( 'Ok, you deserve it', 'woo-cart-abandonment-recovery' ),
					MONTH_IN_SECONDS,
					__( 'Nope, maybe later', 'woo-cart-abandonment-recovery' ),
					__( 'I already did', 'woo-cart-abandonment-recovery' )
				),
				'repeat-notice-after'  => MONTH_IN_SECONDS,
				'display-notice-after' => ( 3 * WEEK_IN_SECONDS ), // Display notice after 2 weeks.
			)
		);
	}

	/**
	 * Show the weekly email Notice
	 *
	 * @return void
	 */
	public function show_weekly_report_email_settings_notice() {

		if ( ! $this->allowed_screen_for_notices() ) {
			return;
		}

		$is_show_notice = get_option( 'wcf_ca_show_weekly_report_email_notice', 'no' );

		if ( 'yes' === $is_show_notice && current_user_can( 'manage_options' ) ) {

			$setting_url = admin_url( 'admin.php?page=woo-cart-abandonment-recovery&action=settings#wcf-ca-weekly-report-email-settings' );

			/* translators: %1$s Software Title, %2$s Plugin, %3$s Anchor opening tag, %4$s Anchor closing tag, %5$s Software Title. */
			$message = sprintf( __( '%1$sWooCommerce Cart Abandonment recovery:%2$s We just introduced an awesome new feature, weekly order recovery reports via email. Now you can see how many orders we are recovering for your store each week, without having to log into your website. You can set the email address for these email from %3$shere.%4$s', 'woo-cart-abandonment-recovery' ), '<strong>', '</strong>', '<a class="wcf-ca-redirect-to-settings" target="_blank" href=" ' . esc_url( $setting_url ) . ' ">', '</a>' );
			$output  = '<div class="weekly-report-email-notice wcar-dismissible-notice notice notice-info is-dismissible">';
			$output .= '<p>' . $message . '</p>';
			$output .= '</div>';

			echo wp_kses_post( $output );
		}

	}

	/**
	 * Disable the weekly email Notice
	 *
	 * @return void
	 */
	public function disable_weekly_report_email_notice() {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		check_ajax_referer( 'wcar_disable_weekly_report_email_notice', 'security' );
		delete_option( 'wcf_ca_show_weekly_report_email_notice' );
		wp_send_json_success();
	}

	/**
	 * Check allowed screen for notices.
	 *
	 * @return bool
	 */
	public function allowed_screen_for_notices() {

		$screen          = get_current_screen();
		$screen_id       = $screen ? $screen->id : '';
		$allowed_screens = array(
			'woocommerce_page_woo-cart-abandonment-recovery',
			'dashboard',
			'plugins',
		);

		if ( in_array( $screen_id, $allowed_screens, true ) ) {
			return true;
		}

		return false;
	}
}

Cartflows_Ca_Admin_Notices::get_instance();
