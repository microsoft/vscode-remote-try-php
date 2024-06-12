<?php
/**
 * Product Config.
 *
 * @package variation-swatches-woo
 * @since 1.0.2
 */

namespace CFVSW\Admin;

use CFVSW\Inc\Traits\Get_Instance;
use CFVSW\Inc\Helper;
use CFVSW\Admin\Templates;


/**
 * Attribute Config
 *
 * @since 1.0.2
 */
class Product_Config {

	use Get_Instance;

	/**
	 * Helper class object
	 *
	 * @var Helper
	 * @since  1.0.2
	 */
	public $helper;

	/**
	 * Helper class object
	 *
	 * @var Templates
	 * @since  1.0.2
	 */
	public $templates;

	/**
	 * Constructor
	 *
	 * @since  1.0.2
	 */
	public function __construct() {
		$this->helper    = new Helper();
		$this->templates = new Templates();
		add_filter( 'woocommerce_product_data_tabs', [ $this, 'swatches_tab' ] );
		add_action( 'woocommerce_product_data_panels', [ $this, 'swatches_panel' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'product_enqueue_scripts' ] );
		add_action( 'wp_ajax_cfvsw_save_product_swatches_data', [ $this, 'save_swatches' ] );
		add_action( 'wp_ajax_cfvsw_update_product_swatches_data', [ $this, 'update_swatches' ] );
		add_action( 'wp_ajax_cfvsw_reset_product_swatches_data', [ $this, 'reset_swatches' ] );
	}

	/**
	 * Update attribute swatches postmeta.
	 *
	 * @return void
	 * @since  1.0.2
	 */
	public function update_swatches() {
		check_ajax_referer( 'cfvsw_swatches_save_reset', 'security' );
		$product_id = ! empty( $_POST['product_id'] ) ? intval( sanitize_text_field( $_POST['product_id'] ) ) : false;
		if ( ! $product_id ) {
			wp_send_json_error( [ 'message' => __( 'Invalid product id.', 'variation-swatches-woo' ) ] );
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid user.', 'variation-swatches-woo' ) ] );
		}

		$product_object = wc_get_product( $product_id );
		if ( ! $product_object->is_type( 'variable' ) ) {
			wp_send_json_error( [ 'message' => __( 'Product type is not variable.', 'variation-swatches-woo' ) ] );
		}
		$this->return_swatches_template( $product_id, $product_object );
	}

	/**
	 * Reset attribute swatches postmeta.
	 *
	 * @return void
	 * @since  1.0.2
	 */
	public function reset_swatches() {
		check_ajax_referer( 'cfvsw_swatches_save_reset', 'security' );
		$product_id = ! empty( $_POST['product_id'] ) ? intval( sanitize_text_field( $_POST['product_id'] ) ) : false;
		if ( ! $product_id ) {
			wp_send_json_error( [ 'message' => __( 'Invalid product id.', 'variation-swatches-woo' ) ] );
		}


		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid user.', 'variation-swatches-woo' ) ] );
		}

		$product_object = wc_get_product( $product_id );
		if ( ! $product_object->is_type( 'variable' ) ) {
			wp_send_json_error( [ 'message' => __( 'Product type is not variable.', 'variation-swatches-woo' ) ] );
		}
		// Cleanup previous meta.
		$this->clean_up_previous_swatches_data( $product_id );
		$this->return_swatches_template( $product_id, $product_object, true );
	}

	/**
	 * Update and reset ajax response.
	 *
	 * @param integer $product_id Current product id.
	 * @param object  $product_object Product object.
	 * @param boolean $need_to_reset Want to reset or not.
	 * @return void
	 * @since  1.0.2
	 */
	public function return_swatches_template( $product_id, $product_object, $need_to_reset = false ) {
		$template    = $this->templates->update_reset_swatches_template( $product_id, $product_object, $need_to_reset );
		$message     = $need_to_reset ? __( 'Swatches setting have been reset.', 'variation-swatches-woo' ) : __( 'Settings saved.', 'variation-swatches-woo' );
		$message     = '' === $template ? __( 'No visible attribute found.', 'variation-swatches-woo' ) : $message;
		$return_data = [
			'template' => $template,
			'message'  => $message,
		];
		wp_send_json_success( $return_data );
	}

	/**
	 * Save attribute swatches postmeta.
	 *
	 * @return void
	 * @since  1.0.2
	 */
	public function save_swatches() {
		check_ajax_referer( 'cfvsw_swatches_save_reset', 'security' );
		// Check admin or not here.
		$product_id = ! empty( $_POST['product_id'] ) ? intval( sanitize_text_field( $_POST['product_id'] ) ) : false;
		if ( ! $product_id || empty( $_POST['attr'] ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid product id.', 'variation-swatches-woo' ) ] );
		}


		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid user.', 'variation-swatches-woo' ) ] );
		}

		// $_POST['attr'] is sanitized in later stage using sanitize_recursively function so sanitization is not required.
		$check_blank_array = $this->helper->remove_blank_array( $_POST['attr'] ); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		// Cleanup previous meta.
		$this->clean_up_previous_swatches_data( $product_id );
		if ( empty( $check_blank_array ) ) {
			wp_send_json_error( [ 'message' => __( 'Settings saved.', 'variation-swatches-woo' ) ] );
		}
		$sanitize_array = $this->helper->sanitize_recursively( 'sanitize_text_field', $check_blank_array );

		foreach ( $sanitize_array as $key => $value ) {
			update_post_meta( $product_id, $key, $value );
		}
		wp_send_json_success(
			[ 'message' => __( 'Settings saved.', 'variation-swatches-woo' ) ]
		);
	}

	/**
	 * Remove previous postmeta data.
	 *
	 * @param integer $product_id Current product id.
	 * @return void
	 * @since  1.0.2
	 */
	public function clean_up_previous_swatches_data( $product_id ) {
		global $wpdb;
		$meta_key = '%' . $wpdb->esc_like( CFVSW_PRODUCT_ATTR ) . '%';
		// Required custom result from database, was not possible with regular WordPress call.
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE %s AND post_id = %d ", $meta_key, $product_id ) );  //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	}

	/**
	 * Swatches tab template.
	 *
	 * @param array $tabs Woocommerce panel tab.
	 * @return array
	 * @since  1.0.2
	 */
	public function swatches_tab( $tabs ) {
		$tabs['cfvsw_tab'] = array(
			'label'    => esc_html__( 'Swatches', 'variation-swatches-woo' ),
			'target'   => 'cfvsw_swatches_settings',
			'class'    => [ 'show_if_variable', 'cfvsw_tab' ],
			'priority' => 61,
		);

		return $tabs;
	}

	/**
	 * Swatches settings template.
	 *
	 * @return void
	 * @since  1.0.2
	 */
	public function swatches_panel() {
		global $product_object;
		$product_id = $product_object->get_id();
		$this->swatches_settings( $product_id );
	}

	/**
	 * Swatches settings template part.
	 *
	 * @param integer $product_id Current product id.
	 * @return void
	 * @since  1.0.2
	 */
	public function swatches_settings( $product_id ) {
		$product_id     = sanitize_text_field( $product_id );
		$product_object = wc_get_product( $product_id );
		if ( ! $product_object->is_type( 'variable' ) ) {
			?>
			<div id="cfvsw_swatches_settings" class="panel wc-metaboxes-wrapper hidden">
		<div class="cfvsw-swatches-settings-notice no-product">
			<p><?php esc_html_e( 'Please save this product to enable custom settings for Variation Swatches.', 'variation-swatches-woo' ); ?></p>
		</div>
		</div>
			<?php
			return;
		}
		$this->templates->panel_wrapper( $product_object );
	}

	/**
	 * Enqueue scripts and style files.
	 *
	 * @return void
	 * @since  1.0.2
	 */
	public function product_enqueue_scripts() {
		$screen = get_current_screen();
		if ( isset( $screen->id ) && 'product' === $screen->id ) {
			wp_register_script( 'cfvsw-product-config-js', CFVSW_URL . 'admin/assets/js/product-config.js', [ 'jquery', 'wc-enhanced-select' ], CFVSW_VER, true );
			wp_register_style( 'cfvsw-product', CFVSW_URL . 'admin/assets/css/product-config.css', [], CFVSW_VER, 'all' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'cfvsw-product-config-js' );

			wp_localize_script(
				'cfvsw-product-config-js',
				'cfvsw_swatches_product',
				[
					'ajax_url'          => admin_url( 'admin-ajax.php' ),
					'image_upload_text' => [
						'title'        => __(
							'Select a image to upload',
							'variation-swatches-woo'
						),
						'button_title' => __(
							'Use this image',
							'variation-swatches-woo'
						),
					],
				]
			);

			wp_enqueue_style( 'cfvsw-product' );
		}
	}
}
