<?php
/**
 * Customizer Site options importer class.
 *
 * @since  1.0.0
 * @package Astra Addon
 */

use STImporter\Importer\ST_Importer_Helper;
use STImporter\Importer\Helpers\ST_Image_Importer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customizer Site options importer class.
 *
 * @since  1.0.0
 */
class Astra_Site_Options_Import {

	/**
	 * Instance of Astra_Site_Options_Importer
	 *
	 * @since  1.0.0
	 * @var (Object) Astra_Site_Options_Importer
	 */
	private static $instance = null;

	/**
	 * Instanciate Astra_Site_Options_Importer
	 *
	 * @since  1.0.0
	 * @return (Object) Astra_Site_Options_Importer
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'st_importer_site_options', array( $this, 'classic_templates_options' ), 10, 1 );
		add_action( 'st_importer_import_site_options', array( $this, 'import_classic_templates_options' ), 10, 2 );
	}

	/**
	 * Classic templates options.
	 *
	 * @since 4.3.0
	 * @param array<int, string> $default_options List of defined array.
	 * @return array<int, string> List of defined array.
	 */
	public function classic_templates_options( $default_options ) {

		$classic_templates_options = array(
			'custom_logo',
			'nav_menu_locations',
			'show_on_front',
			'page_on_front',
			'page_for_posts',
			'site_title',

			// Plugin: Elementor.
			'elementor_container_width',
			'elementor_cpt_support',
			'elementor_css_print_method',
			'elementor_default_generic_fonts',
			'elementor_disable_color_schemes',
			'elementor_disable_typography_schemes',
			'elementor_editor_break_lines',
			'elementor_exclude_user_roles',
			'elementor_global_image_lightbox',
			'elementor_page_title_selector',
			'elementor_scheme_color',
			'elementor_scheme_color-picker',
			'elementor_scheme_typography',
			'elementor_space_between_widgets',
			'elementor_stretched_section_container',
			'elementor_load_fa4_shim',
			'elementor_active_kit',
			'elementor_experiment-container',

			// Plugin: Beaver Builder.
			'_fl_builder_enabled_icons',
			'_fl_builder_enabled_modules',
			'_fl_builder_post_types',
			'_fl_builder_color_presets',
			'_fl_builder_services',
			'_fl_builder_settings',
			'_fl_builder_user_access',
			'_fl_builder_enabled_templates',

			// Plugin: WooCommerce.
			// Pages.
			'woocommerce_shop_page_title',
			'woocommerce_cart_page_title',
			'woocommerce_checkout_page_title',
			'woocommerce_myaccount_page_title',
			'woocommerce_edit_address_page_title',
			'woocommerce_view_order_page_title',
			'woocommerce_change_password_page_title',
			'woocommerce_logout_page_title',

			// Account & Privacy.
			'woocommerce_enable_guest_checkout',
			'woocommerce_enable_checkout_login_reminder',
			'woocommerce_enable_signup_and_login_from_checkout',
			'woocommerce_enable_myaccount_registration',
			'woocommerce_registration_generate_username',

			// Plugin: Easy Digital Downloads - EDD.
			'edd_settings',

			// Plugin: WPForms.
			'wpforms_settings',

			// Categories.
			'woocommerce_product_cat',

			// Plugin: LearnDash LMS.
			'learndash_settings_theme_ld30',
			'learndash_settings_courses_themes',

			// Astra Theme Global Color Palette and Typography Preset options.
			'astra-color-palettes',
			'astra-typography-presets',
		);
		$options = array_merge( $default_options, $classic_templates_options );
		return $options; 
		
	}

	/**
	 * Import Classic Templates Options.
	 *
	 * @since 4.3.0
	 * 
	 * @param array<string, mixed> $options List of default options.
	 * @param array<int, string>   $site_options List of site options.
	 * 
	 * @return void
	 */
	public function import_classic_templates_options( $options, $site_options ) {

		if ( ! isset( $options ) ) {
			return;
		}

		try {
			foreach ( $options as $option_name => $option_value ) {

				// Is option exist in defined array site_options()?
				if ( null !== $option_value ) {

					// Is option exist in defined array site_options()?
					if ( in_array( $option_name, $site_options, true ) ) {

						switch ( $option_name ) {

							// Set WooCommerce page ID by page Title.
							case 'woocommerce_shop_page_title':
							case 'woocommerce_cart_page_title':
							case 'woocommerce_checkout_page_title':
							case 'woocommerce_myaccount_page_title':
							case 'woocommerce_edit_address_page_title':
							case 'woocommerce_view_order_page_title':
							case 'woocommerce_change_password_page_title':
							case 'woocommerce_logout_page_title':
									$this->update_woocommerce_page_id_by_option_value( $option_name, $option_value );
								break;

							case 'page_for_posts':
							case 'page_on_front':
									$this->update_page_id_by_option_value( $option_name, $option_value );
								break;

							// nav menu locations.
							case 'nav_menu_locations':
									$this->set_nav_menu_locations( $option_value );
								break;

							// import WooCommerce category images.
							case 'woocommerce_product_cat':
									$this->set_woocommerce_product_cat( $option_value );
								break;

							// insert logo.
							case 'custom_logo':
									$this->insert_logo( $option_value );
								break;

							case 'elementor_active_kit':
								if ( '' !== $option_value ) {
									$this->set_elementor_kit();
								}
								break;

							case 'site_title':
								update_option( 'blogname', $option_value );
								break;

							default:
								update_option( $option_name, $option_value );
								break;
						}
					}
				}
			}
		} catch ( Exception $e ) {
			// Do nothing.
			astra_sites_error_log( 'Error while importing site options: ' . $e->getMessage() );
		}

	}

	/**
	 * Update post option
	 *
	 * @since 2.2.2
	 *
	 * @return void
	 */
	private function set_elementor_kit() {

		// Update Elementor Theme Kit Option.
		$args = array(
			'post_type'   => 'elementor_library',
			'post_status' => 'publish',
			'numberposts' => 1,
			'meta_query'  => array( //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Setting elementor kit. WP Query would have been expensive.
				array(
					'key'   => '_astra_sites_imported_post',
					'value' => '1',
				),
				array(
					'key'   => '_elementor_template_type',
					'value' => 'kit',
				),
			),
		);

		$query = get_posts( $args );
		if ( ! empty( $query ) && isset( $query[0] ) && isset( $query[0]->ID ) ) {
			update_option( 'elementor_active_kit', $query[0]->ID );
		}
	}


	/**
	 * Get post from post title and post type.
	 *
	 * @since 4.0.6
	 *
	 * @param  mixed  $post_title  post title.
	 * @param  string $post_type post type.
	 * @return mixed
	 */
	public function get_page_by_title( $post_title, $post_type ) {
		$page = array();
		$query = new WP_Query(
			array(
				'post_type'              => $post_type,
				'title'                  => $post_title,
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'orderby'                => 'ID',
				'order'                  => 'DESC',
			)
		);
		if ( $query->have_posts() ) {
			$page = $query->posts[0];
		}
		return $page;
	}

	/**
	 * Update post option
	 *
	 * @since 1.0.2
	 *
	 * @param  string $option_name  Option name.
	 * @param  mixed  $option_value Option value.
	 * @return void
	 */
	private function update_page_id_by_option_value( $option_name, $option_value ) {
		if ( empty( $option_value ) ) {
			return;
		}

		$page = $this->get_page_by_title( $option_value, 'page' );
		
		if ( is_object( $page ) ) {
			update_option( $option_name, $page->ID );
		}
	}

	/**
	 * Update WooCommerce page ids.
	 *
	 * @since 1.1.6
	 *
	 * @param  string $option_name  Option name.
	 * @param  mixed  $option_value Option value.
	 * @return void
	 */
	private function update_woocommerce_page_id_by_option_value( $option_name, $option_value ) {
		$option_name = str_replace( '_title', '_id', $option_name );
		$this->update_page_id_by_option_value( $option_name, $option_value );
	}

	/**
	 * In WP nav menu is stored as ( 'menu_location' => 'menu_id' );
	 * In export we send 'menu_slug' like ( 'menu_location' => 'menu_slug' );
	 * In import we set 'menu_id' from menu slug like ( 'menu_location' => 'menu_id' );
	 *
	 * @since 1.0.0
	 * @param array $nav_menu_locations Array of nav menu locations.
	 */
	private function set_nav_menu_locations( $nav_menu_locations = array() ) {

		$menu_locations = array();

		// Update menu locations.
		if ( isset( $nav_menu_locations ) ) {

			foreach ( $nav_menu_locations as $menu => $value ) {

				$term = get_term_by( 'slug', $value, 'nav_menu' );

				if ( is_object( $term ) ) {
					$menu_locations[ $menu ] = $term->term_id;
				}
			}

			set_theme_mod( 'nav_menu_locations', $menu_locations );
		}
	}

	/**
	 * Set WooCommerce category images.
	 *
	 * @since 1.1.4
	 *
	 * @param array $cats Array of categories.
	 */
	private function set_woocommerce_product_cat( $cats = array() ) {

		if ( isset( $cats ) ) {

			foreach ( $cats as $key => $cat ) {

				if ( ! empty( $cat['slug'] ) && ! empty( $cat['thumbnail_src'] ) ) {

					$downloaded_image = ST_Image_Importer::get_instance()->import(
						array(
							'url' => $cat['thumbnail_src'],
							'id'  => 0,
						)
					);

					if ( $downloaded_image['id'] ) {

						$term = get_term_by( 'slug', $cat['slug'], 'product_cat' );

						if ( is_object( $term ) ) {
							update_term_meta( $term->term_id, 'thumbnail_id', $downloaded_image['id'] );
						}
					}
				}
			}
		}
	}

	/**
	 * Insert Logo By URL
	 *
	 * @since 1.0.0
	 * @param  string $image_url Logo URL.
	 * @return void
	 */
	private function insert_logo( $image_url = '' ) {

		$downloaded_image = ST_Image_Importer::get_instance()->import(
			array(
				'url' => $image_url,
				'id'  => 0,
			)
		);

		if ( $downloaded_image['id'] ) {
			ST_Importer_Helper::track_post( $downloaded_image['id'] );
			set_theme_mod( 'custom_logo', $downloaded_image['id'] );
		}

	}

}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Astra_Site_Options_Import::instance();
