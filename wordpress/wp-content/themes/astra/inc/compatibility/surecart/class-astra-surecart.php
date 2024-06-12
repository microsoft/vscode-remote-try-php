<?php
/**
 * SureCart Compatibility File.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'SURECART_PLUGIN_FILE' ) ) {
	return;
}

/**
 * Astra SureCart Compatibility
 *
 * @since 4.4.0
 */
class Astra_SureCart {

	/**
	 * The post type slug.
	 *
	 * @var string
	 */
	public $post_type = 'sc_product';

	/**
	 * Shop Page ID.
	 *
	 * @var int
	 */
	public $shop_page_id = 0;

	/**
	 * SureCart Shop Page Status.
	 *
	 * @var null|bool
	 */
	public $shop_page_status = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->shop_page_id = absint( get_option( 'surecart_shop_page_id' ) );
		add_action( 'astra_header_after', array( $this, 'astra_surecart_archive_page_banner_support' ) );
		add_action( 'astra_entry_top', array( $this, 'revert_surecart_support' ) );
		add_filter( 'astra_page_layout', array( $this, 'sc_shop_sidebar_layout' ) );
		add_filter( 'astra_get_content_layout', array( $this, 'sc_shop_content_layout' ) );

		// Boxed layout support.
		add_filter( 'astra_is_content_layout_boxed', array( $this, 'sc_shop_content_boxed_layout' ) );
		add_filter( 'astra_is_sidebar_layout_boxed', array( $this, 'sc_shop_sidebar_boxed_layout' ) );
		add_action( 'customize_register', array( $this, 'customize_register' ), 2 );
	}

		/**
		 * Register Customizer sections and panel for SureCart.
		 *
		 * @since 4.6.13
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
	public function customize_register( $wp_customize ) {

		// @codingStandardsIgnoreStart WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		/**
		 * Register Sections & Panels
		 */
		require ASTRA_THEME_DIR . 'inc/compatibility/surecart/customizer/class-astra-customizer-register-surecart-section.php';
		
	}

	/**
	 * Check is SureCart Shop Page.
	 *
	 * @return bool True if SureCart Shop Page.
	 * @since 4.4.0
	 */
	public function astra_is_surecart_shop_page() {
		if ( ! is_customize_preview() && ! is_null( $this->shop_page_status ) ) {
			return $this->shop_page_status;
		}

		$this->shop_page_status = false;
		$supported_post_types   = Astra_Posts_Structure_Loader::get_supported_post_types();
		if ( ! in_array( $this->post_type, $supported_post_types ) ) {
			$this->shop_page_status = false;
		}

		if ( ! is_page() || ! $this->shop_page_id ) {
			$this->shop_page_status = false;
		}

		$page_id = absint( astra_get_post_id() );
		if ( $page_id === $this->shop_page_id ) {
			$this->shop_page_status = true;
		}

		return $this->shop_page_status;
	}

	/**
	 * SureCart Shop Sidebar Layout
	 *
	 * @param string $sidebar_layout Layout type.
	 * @return string $sidebar_layout Layout type.
	 * @since 4.4.0
	 */
	public function sc_shop_sidebar_layout( $sidebar_layout ) {
		if ( $this->astra_is_surecart_shop_page() ) {
			$sc_shop_sidebar = astra_get_option( 'archive-' . $this->post_type . '-sidebar-layout', 'default' );

			if ( 'default' !== $sc_shop_sidebar && ! empty( $sc_shop_sidebar ) ) {
				$sidebar_layout = $sc_shop_sidebar;
			}
		}

		return apply_filters( 'astra_get_surecart_shop_sidebar_layout', $sidebar_layout );
	}

	/**
	 * SureCart Shop Container
	 *
	 * @param string $content_layout Layout type.
	 * @return string $content_layout Layout type.
	 * @since 4.4.0
	 */
	public function sc_shop_content_layout( $content_layout ) {
		if ( $this->astra_is_surecart_shop_page() ) {
			$sc_shop_layout = astra_toggle_layout( 'archive-' . $this->post_type . '-ast-content-layout', 'single', false );

			if ( 'default' !== $sc_shop_layout && ! empty( $sc_shop_layout ) ) {
				$content_layout = $sc_shop_layout;
			}
		}

		return apply_filters( 'astra_get_store_content_layout', $content_layout );
	}

	/**
	 * SureCart Shop Container Style
	 *
	 * @param string $is_style_boxed Layout style.
	 * @return string $is_style_boxed Layout style.
	 * @since 4.4.0
	 */
	public function sc_shop_content_boxed_layout( $is_style_boxed ) {
		if ( $this->astra_is_surecart_shop_page() ) {
			$sc_shop_layout_style = astra_get_option( 'archive-' . $this->post_type . '-content-style', 'default' );

			if ( 'boxed' === $sc_shop_layout_style ) {
				$is_style_boxed = true;
			}
		}

		return apply_filters( 'astra_get_store_layout_style', $is_style_boxed );
	}

	/**
	 * SureCart Shop Sidebar Style
	 *
	 * @param string $is_style_boxed Layout style.
	 * @return string $is_style_boxed Layout style.
	 * @since 4.4.0
	 */
	public function sc_shop_sidebar_boxed_layout( $is_style_boxed ) {
		if ( $this->astra_is_surecart_shop_page() ) {
			$sc_shop_layout_style = astra_get_option( 'archive-' . $this->post_type . '-sidebar-style', 'default' );

			if ( 'boxed' === $sc_shop_layout_style ) {
				$is_style_boxed = true;
			}
		}

		return apply_filters( 'astra_get_store_sidebar_style', $is_style_boxed );
	}

	/**
	 * SureCart Archive Banner Support.
	 * Making 'Shop Page' as archive of SureCart Products.
	 *
	 * @since 4.4.0
	 */
	public function astra_surecart_archive_page_banner_support() {
		if ( false === $this->astra_is_surecart_shop_page() ) {
			return;
		}

		$page_id = absint( astra_get_post_id() );

		$visibility = get_post_meta( $page_id, 'ast-banner-title-visibility', true );
		$visibility = apply_filters( 'astra_banner_title_area_visibility', $visibility );
		if ( 'disabled' === $visibility ) {
			$this->disable_page_loaded_banner_area();
			return;
		}

		$banner_layout = astra_get_option( 'ast-dynamic-archive-sc_product-layout', 'layout-1' );
		add_filter( 'astra_banner_elements_structure', array( $this, 'update_astra_banner_elements_structure' ) );
		add_filter( 'astra_banner_elements_post_type', array( $this, 'update_astra_banner_elements_post_type' ) );
		add_filter( 'astra_banner_elements_prefix', array( $this, 'update_astra_banner_elements_prefix' ) );
		add_filter( 'the_title', array( $this, 'update_the_title' ), 10, 2 );

		if ( 'layout-2' === $banner_layout ) {
			$astra_banner_hook = apply_filters( 'astra_banner_hook', 'astra_content_before' );
			add_action( $astra_banner_hook, array( $this, 'astra_surecart_hero_section' ), 20 );
		} else {
			add_filter( 'astra_single_layout_one_banner_visibility', '__return_false' );
			add_filter( 'astra_apply_hero_header_banner', '__return_false' );
			add_action( 'astra_primary_content_top', array( $this, 'astra_force_render_banner_layout_1' ) );
		}
	}

	/**
	 * Enable layout 1 for some cases. Ex. SureCart Product.
	 *
	 * @since 4.4.0
	 * @return void
	 */
	public function astra_force_render_banner_layout_1() {
		add_filter( 'astra_remove_entry_header_content', '__return_false' );
		?>
			<section class="ast-archive-description">
				<?php
					do_action( 'astra_before_archive_title' );
					astra_banner_elements_order();
				?>
			</section>
		<?php
		do_action( 'astra_after_archive_title' );
	}

	/**
	 * SureCart Hero Section.
	 *
	 * @since 4.4.0
	 */
	public function astra_surecart_hero_section() {
		if ( false === apply_filters( 'astra_apply_hero_header_banner', true ) ) {
			return;
		}

		$args = array( 'post_type' => $this->post_type );
		do_action( 'astra_before_archive_' . $this->post_type . '_banner_content' );
		get_template_part( 'template-parts/archive', 'banner', $args );
		do_action( 'astra_after_archive_' . $this->post_type . '_banner_content' );

		$this->disable_page_loaded_banner_area();
	}

	/**
	 * SureCart Section banner element structure.
	 *
	 * @param array $structure Elements structure.
	 * @since 4.4.0
	 */
	public function update_astra_banner_elements_structure( $structure ) {
		return astra_get_option( 'ast-dynamic-archive-' . $this->post_type . '-structure', array( 'ast-dynamic-archive-' . $this->post_type . '-title', 'ast-dynamic-archive-' . $this->post_type . '-description' ) );
	}

	/**
	 * SureCart Section banner reference post type.
	 *
	 * @param string $post_type Post type.
	 * @since 4.4.0
	 */
	public function update_astra_banner_elements_post_type( $post_type ) {
		return $this->post_type;
	}

	/**
	 * SureCart Section banner prefix.
	 *
	 * @param string $prefix Prefix.
	 * @since 4.4.0
	 */
	public function update_astra_banner_elements_prefix( $prefix ) {
		return 'archive';
	}

	/**
	 * Support custom title & description support for archive.
	 *
	 * @param string $title Default archive title.
	 * @param int    $post_id Post ID.
	 * @since 4.4.0
	 * @return string
	 */
	public function update_the_title( $title, $post_id ) {
		if ( $this->shop_page_id !== $post_id ) {
			return $title;
		}
		$custom_title = astra_get_option( 'ast-dynamic-archive-' . $this->post_type . '-custom-title', '' );
		$title        = ! empty( $custom_title ) ? $custom_title : $title;
		return $title;
	}

	/**
	 * Disable Astra's next page's banner as we already loaded.
	 *
	 * @since 4.4.0
	 */
	public function disable_page_loaded_banner_area() {
		add_filter( 'astra_apply_hero_header_banner', '__return_false' );
		add_filter( 'astra_remove_entry_header_content', '__return_true' );
		add_filter( 'astra_single_layout_one_banner_visibility', '__return_false' );
	}

	/**
	 * Revert SureCart Support, after banner loaded.
	 *
	 * @since 4.4.0
	 */
	public function revert_surecart_support() {
		if ( false === $this->astra_is_surecart_shop_page() ) {
			return;
		}

		remove_filter( 'astra_is_content_layout_boxed', array( $this, 'sc_shop_content_boxed_layout' ) );
		remove_filter( 'astra_is_sidebar_layout_boxed', array( $this, 'sc_shop_sidebar_boxed_layout' ) );

		remove_filter( 'astra_banner_elements_structure', array( $this, 'update_astra_banner_elements_structure' ) );
		remove_filter( 'astra_banner_elements_post_type', array( $this, 'update_astra_banner_elements_post_type' ) );
		remove_filter( 'astra_banner_elements_prefix', array( $this, 'update_astra_banner_elements_prefix' ) );
		remove_filter( 'the_title', array( $this, 'update_the_title' ), 10 );
	}
}

/**
 * Kicking this off by object.
 *
 * @since 4.4.0
 */
new Astra_SureCart();
