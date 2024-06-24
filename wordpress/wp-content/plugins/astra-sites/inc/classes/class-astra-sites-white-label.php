<?php
/**
 * Astra Sites White Label
 *
 * @package Astra Sites
 * @since 1.0.12
 */

if ( ! class_exists( 'Astra_Sites_White_Label' ) ) :

	/**
	 * Astra_Sites_White_Label
	 *
	 * @since 1.0.12
	 */
	class Astra_Sites_White_Label {

		/**
		 * Instance
		 *
		 * @since 1.0.12
		 *
		 * @var object Class Object.
		 * @access private
		 */
		private static $instance;

		/**
		 * Member Variable
		 *
		 * @since 1.0.12
		 *
		 * @var array branding
		 * @access private
		 */
		private static $branding;

		/**
		 * Settings
		 *
		 * @since 1.2.11
		 *
		 * @var array settings
		 *
		 * @access private
		 */
		private $settings;

		/**
		 * Initiator
		 *
		 * @since 1.0.12
		 *
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
		 * @since 1.0.12
		 */
		public function __construct() {
			add_filter( 'all_plugins', array( $this, 'plugins_page' ) );
			add_filter( 'astra_addon_branding_options', __CLASS__ . '::settings' );
			add_action( 'astra_pro_white_label_add_form', __CLASS__ . '::add_white_label_form' );
			add_filter( 'astra_sites_menu_page_title', array( $this, 'get_white_label_name' ) );
			add_filter( 'astra_sites_page_title', array( $this, 'get_white_label_name' ) );

			// Update Astra's admin top level menu position.
			add_filter( 'astra_menu_priority', array( $this, 'update_admin_menu_position' ) );

			// Display the link with the plugin meta.
			if ( is_admin() ) {
				add_filter( 'plugin_row_meta', array( $this, 'plugin_links' ), 10, 4 );
			}

			add_filter( 'gutenberg_templates_localize_vars', array( $this, 'add_white_label_name' ) );

			add_filter( 'ast_block_templates_white_label', array( $this, 'is_white_labeled' ) );
			add_filter( 'ast_block_templates_white_label_name', array( $this, 'get_white_label' ) );
		}

		/**
		 * Update Astra's menu priority to show after Dashboard menu.
		 *
		 * @param int $menu_priority top level menu priority.
		 * @since 3.1.22
		 */
		public function update_admin_menu_position( $menu_priority ) {
			return 2.1;
		}

		/**
		 * Add White Label data
		 *
		 * @param array $args White label.
		 *  @since 2.6.0
		 */
		public function add_white_label_name( $args = array() ) {
			$args['white_label_name'] = $this->get_white_label();
			return $args;
		}

		/**
		 * White labels the plugins page.
		 *
		 * @since 1.0.12
		 *
		 * @param array $plugins Plugins Array.
		 * @return array
		 */
		public function plugins_page( $plugins ) {

			if ( ! is_callable( 'Astra_Ext_White_Label_Markup::get_whitelabel_string' ) ) {
				return $plugins;
			}

			if ( ! isset( $plugins[ ASTRA_SITES_BASE ] ) ) {
				return $plugins;
			}

			// Set White Labels.
			$name        = Astra_Ext_White_Label_Markup::get_whitelabel_string( 'astra-sites', 'name' );
			$description = Astra_Ext_White_Label_Markup::get_whitelabel_string( 'astra-sites', 'description' );
			$author      = Astra_Ext_White_Label_Markup::get_whitelabel_string( 'astra-agency', 'author' );
			$author_uri  = Astra_Ext_White_Label_Markup::get_whitelabel_string( 'astra-agency', 'author_url' );

			if ( ! empty( $name ) ) {
				$plugins[ ASTRA_SITES_BASE ]['Name'] = $name;

				// Remove Plugin URI if Agency White Label name is set.
				$plugins[ ASTRA_SITES_BASE ]['PluginURI'] = '';
			}

			if ( ! empty( $description ) ) {
				$plugins[ ASTRA_SITES_BASE ]['Description'] = $description;
			}

			if ( ! empty( $author ) ) {
				$plugins[ ASTRA_SITES_BASE ]['Author'] = $author;
			}

			if ( ! empty( $author_uri ) ) {
				$plugins[ ASTRA_SITES_BASE ]['AuthorURI'] = $author_uri;
			}

			return $plugins;
		}

		/**
		 * Get value of single key from option array.
		 *
		 * @since  2.0.0.
		 * @param  string $type Option type.
		 * @param  string $key  Option key.
		 * @param  string $default  Default value if key not found.
		 * @return mixed        Return stored option value.
		 */
		public static function get_option( $type = '', $key = '', $default = null ) {

			if ( ! is_callable( 'Astra_Ext_White_Label_Markup::get_white_label' ) ) {
				return $default;
			}

			$value = Astra_Ext_White_Label_Markup::get_white_label( $type, $key );
			if ( ! empty( $value ) ) {
				return $value;
			}

			return $default;

		}

		/**
		 * Remove a "view details" link from the plugin list table
		 *
		 * @since 1.0.12
		 *
		 * @param array  $plugin_meta  List of links.
		 * @param string $plugin_file Relative path to the main plugin file from the plugins directory.
		 * @param array  $plugin_data  Data from the plugin headers.
		 * @return array
		 */
		public function plugin_links( $plugin_meta, $plugin_file, $plugin_data ) {

			if ( ! is_callable( 'Astra_Ext_White_Label_Markup::get_whitelabel_string' ) ) {
				return $plugin_meta;
			}

			// Set White Labels.
			if ( ASTRA_SITES_BASE === $plugin_file ) {

				$name        = Astra_Ext_White_Label_Markup::get_whitelabel_string( 'astra-sites', 'name' );
				$description = Astra_Ext_White_Label_Markup::get_whitelabel_string( 'astra-sites', 'description' );

				// Remove Plugin URI if Agency White Label name is set.
				if ( ! empty( $name ) ) {
					unset( $plugin_meta[2] );
				}
			}

			return $plugin_meta;
		}

		/**
		 * Add White Label setting's
		 *
		 * @since 1.0.12
		 *
		 * @param  array $settings White label setting.
		 * @return array
		 */
		public static function settings( $settings = array() ) {

			$settings['astra-sites'] = array(
				'name'        => '',
				'description' => '',
			);

			return $settings;
		}

		/**
		 * Add White Label form
		 *
		 * @since 1.0.12
		 *
		 * @param  array $settings White label setting.
		 * @return void
		 */
		public static function add_white_label_form( $settings = array() ) {

			/* translators: %1$s product name */
			$plugin_name = sprintf( __( '%1$s Branding', 'astra-sites' ), ASTRA_SITES_NAME );

			require_once ASTRA_SITES_DIR . 'inc/includes/white-label.php';
		}

		/**
		 * Page Title
		 *
		 * @since 1.0.12
		 *
		 * @param  string $title Page Title.
		 * @return string        Filtered Page Title.
		 */
		public function get_white_label_name( $title = '' ) {
			if ( is_callable( 'Astra_Ext_White_Label_Markup::get_whitelabel_string' ) ) {
				$astra_sites_name = Astra_Ext_White_Label_Markup::get_whitelabel_string( 'astra-sites', 'name' );
				if ( ! empty( $astra_sites_name ) ) {
					return Astra_Ext_White_Label_Markup::get_whitelabel_string( 'astra-sites', 'name' );
				}
			}

			return ASTRA_SITES_NAME;
		}

		/**
		 * White Label Link
		 *
		 * @since 2.0.0
		 *
		 * @param  string $link  Default link.
		 * @return string        Filtered Page Title.
		 */
		public function get_white_label_link( $link = '' ) {
			if ( is_callable( 'Astra_Ext_White_Label_Markup::get_whitelabel_string' ) ) {
				$white_label_link = Astra_Ext_White_Label_Markup::get_whitelabel_string( 'astra-agency', 'licence' );
				if ( ! empty( $white_label_link ) ) {
					return $white_label_link;
				}
			}

			return $link;
		}

		/**
		 * Is Astra sites White labeled
		 *
		 * @since 1.2.13
		 *
		 * @return string
		 */
		public function is_white_labeled() {
			$white_label = $this->get_white_label();

			if ( empty( $white_label ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Get white label name
		 *
		 * @since 2.6.0
		 *
		 * @return string
		 */
		public function get_white_label() {
			if ( ! is_callable( 'Astra_Ext_White_Label_Markup::get_whitelabel_string' ) ) {
				return '';
			}

			$name = Astra_Ext_White_Label_Markup::get_whitelabel_string( 'astra-sites', 'name' );

			if ( ! empty( $name ) ) {
				return $name;
			}

			return '';
		}

	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Astra_Sites_White_Label::get_instance();

endif;
