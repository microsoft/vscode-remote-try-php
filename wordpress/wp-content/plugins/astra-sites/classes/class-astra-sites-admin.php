<?php
/**
 * Admin Notices
 *
 * @since 2.3.7
 * @package Astra Sites
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Sites_Admin' ) ) :

	/**
	 * Admin
	 */
	class Astra_Sites_Admin {

		/**
		 * Instance of Astra_Sites_Admin
		 *
		 * @since 2.3.7
		 * @var (Object) Astra_Sites_Admin
		 */
		private static $instance = null;

		/**
		 * Instance of Astra_Sites_Admin.
		 *
		 * @since 2.3.7
		 *
		 * @return object Class object.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 2.3.7
		 */
		private function __construct() {
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'astra_notice_before_markup', array( $this, 'notice_assets' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
			add_action( 'astra_sites_after_site_grid', array( $this, 'custom_upgrade_cta' ) );
			add_filter( 'astra_sites_quick_links', array( $this, 'change_quick_links' ) );
		}

		/**
		 * Change quick links
		 *
		 * @since 2.6.18
		 * @param array $links  All quick links.
		 * @return array
		 */
		public function change_quick_links( $links = array() ) {

			if ( ! isset( $links['links']['upgrade'] ) ) {
				return $links;
			}

			// Change default call to action link.
			$links['links']['upgrade']['url'] = Astra_Sites::get_instance()->get_cta_link( 'quick-links-corner' );

			return $links;
		}

		/**
		 * Admin Assets
		 */
		public function admin_assets() {
			$current_screen = get_current_screen();

			if ( 'appearance_page_starter-templates' !== $current_screen->id ) {
				return;
			}

			if ( Astra_Sites_White_Label::get_instance()->is_white_labeled() ) {
				return;
			}

			wp_enqueue_style( 'astra-sites-admin-page', ASTRA_SITES_URI . 'assets/css/admin.css', ASTRA_SITES_VER, true );
			wp_enqueue_script( 'astra-sites-admin-js', ASTRA_SITES_URI . 'assets/js/admin.js', array( 'astra-sites-admin-page', 'jquery' ), ASTRA_SITES_VER, true );
		}

		/**
		 * Add Custom CTA Infobar.
		 */
		public function custom_upgrade_cta() {
			$current_screen = get_current_screen();

			if ( 'appearance_page_starter-templates' !== $current_screen->id ) {
				return;
			}

			if ( Astra_Sites_White_Label::get_instance()->is_white_labeled() ) {
				return;
			}

			$custom_cta_content_data = apply_filters(
				'astra_sites_custom_cta_vars',
				array(
					'text'        => __( 'Get unlimited access to all premium Starter Templates and more, at a single low cost!', 'astra-sites' ),
					'button_text' => __( 'Get Essential Toolkit', 'astra-sites' ),
					'cta_link'    => Astra_Sites::get_instance()->get_cta_link(),
				)
			);

			$html  = '<div class="astra-sites-custom-cta-wrap">';
			$html .= '<span class="astra-sites-cta-title">' . esc_html( $custom_cta_content_data['text'] ) . '</span>';
			$html .= '<span class="astra-sites-cta-btn">';
			$html .= '<a class="astra-sites-cta-link" href="' . esc_url( $custom_cta_content_data['cta_link'] ) . '"  target="_blank" >' . esc_html( $custom_cta_content_data['button_text'] ) . '</a>';
			$html .= '</span>';
			$html .= '</div>';
			echo wp_kses_post( $html );
		}

		/**
		 * Admin Notices
		 *
		 * @since 2.3.7
		 * @return void
		 */
		public function admin_notices() {

			$image_path = esc_url( ASTRA_SITES_URI . 'inc/assets/images/logo.svg' );

			$complete = get_option( 'astra_sites_import_complete', '' );

			Astra_Notices::add_notice(
				array(
					'id'      => 'astra-sites-5-start-notice',
					'type'    => 'info',
					'class'   => 'astra-sites-5-star',
					'show_if' => ( 'yes' === $complete && false === Astra_Sites_White_Label::get_instance()->is_white_labeled() ),
					/* translators: %1$s white label plugin name and %2$s deactivation link */
					'message' => sprintf(
						'<div class="notice-image" style="display: flex;">
							<img src="%1$s" class="custom-logo" alt="Starter Templates" itemprop="logo" style="max-width: 90px;"></div>
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
						__( 'Hello! Seems like you have used Starter Templates to build this website &mdash; Thanks a ton!', 'astra-sites' ),
						__( 'Could you please do us a BIG favor and give it a 5-star rating on WordPress? This would boost our motivation and help other users make a comfortable decision while choosing the Starter Templates.', 'astra-sites' ),
						'https://wordpress.org/support/plugin/astra-sites/reviews/?filter=5#new-post',
						__( 'Ok, you deserve it', 'astra-sites' ),
						MONTH_IN_SECONDS,
						__( 'Nope, maybe later', 'astra-sites' ),
						__( 'I already did', 'astra-sites' )
					),
				)
			);
		}

		/**
		 * Enqueue Astra Notices CSS.
		 *
		 * @since 2.3.7
		 *
		 * @return void
		 */
		public static function notice_assets() {
			$file = is_rtl() ? 'astra-notices-rtl.css' : 'astra-notices.css';
			wp_enqueue_style( 'astra-sites-notices', ASTRA_SITES_URI . 'assets/css/' . $file, array(), ASTRA_SITES_VER );
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Astra_Sites_Admin::get_instance();

endif;
