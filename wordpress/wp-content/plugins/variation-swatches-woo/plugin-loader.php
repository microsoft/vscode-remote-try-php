<?php
/**
 * Plugin Loader.
 *
 * @package variation-swatches-woo
 * @since 1.0.0
 */

namespace CFVSW;

use CFVSW\Admin\Attributes_Config;
use CFVSW\Admin\Term_Meta_Config;
use CFVSW\Admin\Product_Config;
use CFVSW\Admin_Core\Admin_Menu;
use CFVSW\Inc\Swatches;
/**
 * Plugin_Loader
 *
 * @since 1.0.0
 */
class Plugin_Loader {

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class Instance.
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
	 * Autoload classes.
	 *
	 * @param string $class class name.
	 */
	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$class_to_load = $class;

		$filename = strtolower(
			preg_replace(
				[ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
				[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
				$class_to_load
			)
		);

		$file = CFVSW_DIR . $filename . '.php';

		// if the file redable, include it.
		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		spl_autoload_register( [ $this, 'autoload' ] );

		add_action( 'plugins_loaded', [ $this, 'load_classes' ] );
		add_filter( 'plugin_action_links_' . CFVSW_BASE, [ $this, 'action_links' ] );
	}

	/**
	 * Loads plugin classes as per requirement.
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public function load_classes() {
		if ( ! class_exists( 'woocommerce' ) ) {
			add_action( 'admin_notices', [ $this, 'wc_is_not_active' ] );
			return;
		}

		if ( is_admin() ) {
			Attributes_Config::get_instance();
			Term_Meta_Config::get_instance();
			Product_Config::get_instance();
			Admin_Menu::get_instance();
		}

		Swatches::get_instance();

		$this->load_textdomain();
	}

	/**
	 * Loads classes on plugins_loaded hook.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function wc_is_not_active() {
		$plugin  = 'woocommerce/woocommerce.php';
		$plugins = get_plugins();

		if ( isset( $plugins[ $plugin ] ) ) {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			$action_url   = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
			$button_label = __( 'Activate WooCommerce', 'variation-swatches-woo' );
		} else {
			if ( ! current_user_can( 'install_plugins' ) ) {
				return;
			}

			$action_url   = wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'install-plugin',
						'plugin' => 'woocommerce',
					),
					admin_url( 'update.php' )
				),
				'install-plugin_woocommerce'
			);
			$button_label = __( 'Install WooCommerce', 'variation-swatches-woo' );
		}

		echo '<div class="notice notice-error is-dismissible"><p>';
		// translators: 1$-2$: opening and closing <strong> tags, 3$-4$: link tags, takes to woocommerce plugin on wp.org, 5$-6$: opening and closing link tags, leads to plugins.php in admin.
		echo sprintf( esc_html__( '%1$sVariation Swatches for WooCommerce is inactive.%2$s The %3$sWooCommerce plugin%4$s must be active for Variation Swatches for WooCommerce to work. Please %5$s%6$s%7$s', 'variation-swatches-woo' ), '<strong>', '</strong>', '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . esc_url( $action_url ) . '">', esc_html( $button_label ), '</a>' );
		echo '</p></div>';
	}

	/**
	 * Loads plugins translation file
	 *
	 * @return void
	 * @since 1.3.0
	 */
	public function load_textdomain() {
		// Default languages directory.
		$lang_dir = CFVSW_DIR . 'languages/';

		// Traditional WordPress plugin locale filter.
		global $wp_version;

		$get_locale = get_locale();

		if ( $wp_version >= 4.7 ) {
			$get_locale = get_user_locale();
		}

		$locale = apply_filters( 'plugin_locale', $get_locale, 'variation-swatches-woo' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'variation-swatches-woo', $locale );

		// Setup paths to current locale file.
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/variation-swatches-woo/ folder.
			load_textdomain( 'variation-swatches-woo', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/variation-swatches-woo/languages/ folder.
			load_textdomain( 'variation-swatches-woo', $mofile_local );
		} else {
			// Load the default language files.
			load_plugin_textdomain( 'variation-swatches-woo', false, $lang_dir );
		}
	}

	/**
	 * Adds links in Plugins page
	 *
	 * @param array $links existing links.
	 * @return array
	 * @since 1.0.0
	 */
	public function action_links( $links ) {
		return array_merge(
			[
				'cfvsw_settings' => '<a href="' . esc_url( admin_url( 'admin.php?page=cfvsw_settings&path=settings' ) ) . '">' . __( 'Settings', 'variation-swatches-woo' ) . '</a>',
			],
			$links
		);
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Plugin_Loader::get_instance();
