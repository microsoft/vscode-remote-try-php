<?php
/**
 * Zipwp Images Script
 *
 * @since 1.0.0
 * @package Zipwp Images Script
 */

namespace ZipWP_Images\Classes;

/**
 * Ai_Builder
 */
class Zipwp_Images_Script {

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class Instance.
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'editor_load_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'bb_editor_load_scripts' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'editor_load_scripts' ) );
	}

	/**
	 * Load script for block editor and elementor editor.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function editor_load_scripts() {

		if ( ! is_admin() ) {
			return;
		}

		$this->load_script();
	}

	/**
	 * Load script for block BB editor.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function bb_editor_load_scripts() {

		if ( class_exists( 'FLBuilderModel' ) && \FLBuilderModel::is_builder_active() || is_customize_preview() ) {
			$this->load_script();
		}
	}

	/**
	 * Load all the required files in the importer.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_script() {
		// Enqueue JS.
		wp_enqueue_script( 'zipwp-images-script', ZIPWP_IMAGES_URL . 'dist/main.js', array( 'jquery', 'media-views', 'react', 'wp-element', 'wp-api-fetch' ), ZIPWP_IMAGES_VER, true );

		$data = apply_filters(
			'zipwp_images_vars',
			array(
				'ajaxurl'             => esc_url( admin_url( 'admin-ajax.php' ) ),
				'asyncurl'            => esc_url( admin_url( 'async-upload.php' ) ),
				'is_bb_active'        => ( class_exists( 'FLBuilderModel' ) ),
				'is_brizy_active'     => ( class_exists( 'Brizy_Editor_Post' ) ),
				'is_elementor_active' => ( did_action( 'elementor/loaded' ) ),
				'is_elementor_editor' => ( did_action( 'elementor/loaded' ) ) && class_exists( '\Elementor\Plugin' ) ? \Elementor\Plugin::instance()->editor->is_edit_mode() : false,
				'is_bb_editor'        => ( class_exists( '\FLBuilderModel' ) ) ? ( \FLBuilderModel::is_builder_active() ) : false,
				'is_brizy_editor'     => ( class_exists( 'Brizy_Editor_Post' ) ) ? ( isset( $_GET['brizy-edit'] ) || isset( $_GET['brizy-edit-iframe'] ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Fetching GET parameter, no nonce associated with this action.
				'saved_images'        => get_option( 'zipwp-images-saved-images', array() ),
				'title'               => apply_filters( 'zipwp_images_tab_title', __( 'ZipWP Images', 'zipwp-images', 'astra-sites' ) ),
				'search_placeholder'  => __( 'Search - Ex: flowers', 'zipwp-images', 'astra-sites' ),
				'downloading'         => __( 'Downloading...', 'zipwp-images', 'astra-sites' ),
				'validating'          => __( 'Validating...', 'zipwp-images', 'astra-sites' ),
				'_ajax_nonce'         => wp_create_nonce( 'zipwp-images' ),
				'rest_api_nonce'      => ( current_user_can( 'manage_options' ) ) ? wp_create_nonce( 'wp_rest' ) : '',
			)
		);

		// Add localize JS.
		wp_localize_script(
			'zipwp-images-script',
			'zipwpImages',
			$data
		);

		// Enqueue CSS.
		wp_enqueue_style( 'zipwp-images-style', ZIPWP_IMAGES_URL . 'dist/style-main.css', array(), ZIPWP_IMAGES_VER );
		wp_enqueue_style( 'zipwp-images-google-fonts', $this->google_fonts_url(), array(), 'all' );
	}

	/**
	 * Generate and return the Google fonts url.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function google_fonts_url() {

		$fonts_url     = '';
		$font_families = array(
			'Figtree:400,500,600,700',
		);

		$query_args = array(
			'family' => rawurlencode( implode( '|', $font_families ) ),
			'subset' => rawurlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );

		return $fonts_url;
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Zipwp_Images_Script::get_instance();

