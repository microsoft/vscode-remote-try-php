<?php
/**
 * Quick_Links Setup.
 *
 * @since 2.6.2
 * @package Astra Sites
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BSF_Quick_Links' ) ) {

	/**
	 * Quick_Links.
	 */
	class BSF_Quick_Links {
		/**
		 * Quick_Links version.
		 *
		 * @access private
		 * @var array Quick_Links.
		 * @since 2.6.2
		 */
		private static $version = '1.0.0';

		/**
		 * Quick_Links
		 *
		 * @access private
		 * @var array Quick_Links.
		 * @since 2.6.2
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 2.6.2
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
		 *
		 * @since 2.6.2
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Enqueue Scripts.
		 *
		 * @since 2.6.2
		 * @return void
		 */
		public function enqueue_scripts() {
			wp_register_script( 'bsf-quick-links', $this->get_uri() . 'quicklinks.js', array( 'jquery' ), self::$version, true );
			wp_register_style( 'bsf-quick-links-css', $this->get_uri() . 'quicklink.css', array(), self::$version, 'screen' );
		}

		/**
		 * Get URI
		 *
		 * @return mixed URL.
		 */
		public function get_uri() {
			$path      = wp_normalize_path( dirname( __FILE__ ) );
			$theme_dir = wp_normalize_path( get_template_directory() );

			if ( strpos( $path, $theme_dir ) !== false ) {
				return trailingslashit( get_template_directory_uri() . str_replace( $theme_dir, '', $path ) );
			}

			return plugin_dir_url( __FILE__ );

		}

		/**
		 * Generate Quick Links Markup.
		 *
		 * @param array $data links array.
		 */
		public function generate_quick_links_markup( $data ) {

			wp_enqueue_script( 'bsf-quick-links' );
			wp_enqueue_style( 'bsf-quick-links-css' );

			?>
			<div class="bsf-quick-link-wrap">
				<div class="bsf-quick-link-items-wrap hide-wrapper">
					<?php echo $this->get_links_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- All attributes are escaped inside of this function. ?>
				</div>
				<a href="#" class="bsf-quick-link">
					<div class="quick-link-button-wrap">
						<img src="<?php echo esc_url( $data['default_logo']['url'] ); ?>">
						<span><?php esc_html_e( 'Quick Links', 'astra-sites' ); ?></span>
					</div>
				</a>
			</div>
			<?php
		}

		/**
		 * Generate links markup.
		 *
		 * @param array $data links array.
		 */
		private function get_links_html( $data ) {
			$items_html = '';

			foreach ( $data['links'] as $item_key => $item ) {
				$items_html .= sprintf(
					'<a href="%1$s" target="_blank" rel="noopener noreferrer" class="bsf-quick-link-item bsf-quick-link-item-%4$s">
						<div class="bsf-quick-link-label">%2$s</div>
						<div class="dashicons %3$s menu-item-logo" %5$s></div>
					</a>',
					esc_url( $item['url'] ),
					esc_html( $item['label'] ),
					sanitize_html_class( $item['icon'] ),
					$item_key,
					! empty( $item['bgcolor'] ) ? ' style="background-color: ' . esc_attr( $item['bgcolor'] ) . '"' : ''
				);
			}

			return $items_html;
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	BSF_Quick_Links::get_instance();

}
if ( ! function_exists( 'bsf_quick_links' ) ) {
	/**
	 * Add BSF Quick Links.
	 *
	 * @param array $args links array.
	 */
	function bsf_quick_links( $args ) {
		BSF_Quick_Links::get_instance()->generate_quick_links_markup( $args );
	}
}
