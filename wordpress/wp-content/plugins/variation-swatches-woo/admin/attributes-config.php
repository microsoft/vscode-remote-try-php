<?php
/**
 * Attribute Config.
 *
 * @package variation-swatches-woo
 * @since 1.0.0
 */

namespace CFVSW\Admin;

use CFVSW\Inc\Traits\Get_Instance;
use CFVSW\Inc\Helper;


/**
 * Attribute Config
 *
 * @since 1.0.0
 */
class Attributes_Config {

	use Get_Instance;

	/**
	 * Stores attribute taxonomy id
	 *
	 * @var string
	 * @since  1.0.0
	 */
	public $taxonomy;

	/**
	 * Helper class object
	 *
	 * @var Helper
	 * @since  1.0.0
	 */
	public $helper;

	/**
	 * Contructor
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		// Adding swatch types.
		add_filter( 'product_attributes_type_selector', [ $this, 'add_swatch_types' ], 10, 1 );

		// Swatch type preview column.
		// To get taxonomy name from $_GET so nonce is not required.
		$this->taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_title( $_GET['taxonomy'] ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$this->helper   = new Helper();
		add_filter( 'manage_edit-' . $this->taxonomy . '_columns', [ $this, 'add_attribute_column' ] );
		add_filter( 'manage_' . $this->taxonomy . '_custom_column', [ $this, 'add_preview_markup' ], 10, 3 );
		add_action( 'admin_enqueue_scripts', [ $this, 'product_attribute_scripts' ] );
		add_action( 'woocommerce_after_add_attribute_fields', [ $this, 'product_attribute_shape' ] );
		add_action( 'woocommerce_after_edit_attribute_fields', [ $this, 'product_attribute_shape' ] );
		add_action( 'woocommerce_attribute_added', [ $this, 'save_product_attribute_shape' ] );
		add_action( 'woocommerce_attribute_updated', [ $this, 'save_product_attribute_shape' ] );
	}

	/**
	 * Adding taxonomy type as swatches
	 *
	 * @param array $fields default array with option 'select'.
	 * @return array
	 * @since  1.0.0
	 */
	public function add_swatch_types( $fields ) {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return $fields;
		}

		$current_screen = get_current_screen();

		if ( isset( $current_screen->base ) && 'product_page_product_attributes' === $current_screen->base ) {
			$fields = wp_parse_args(
				$fields,
				[
					'select' => esc_html__( 'Select', 'variation-swatches-woo' ),
					'label'  => esc_html__( 'Label', 'variation-swatches-woo' ),
					'color'  => esc_html__( 'Color', 'variation-swatches-woo' ),
					'image'  => esc_html__( 'Image', 'variation-swatches-woo' ),
				]
			);
		}
		return $fields;
	}

	/**
	 * Adds new column to taxonomy table
	 *
	 * @param array $columns Taxonomy header column.
	 * @return array
	 * @since  1.0.0
	 */
	public function add_attribute_column( $columns ) {
		global $taxnow;
		if ( $this->taxonomy !== $taxnow ) {
			return $columns;
		}

		$attr_type = $this->helper->get_attr_type_by_name( $this->taxonomy );
		if ( ! in_array( $attr_type, [ 'color', 'image' ], true ) ) {
			return $columns;
		}

		$new_columns = [];
		if ( isset( $columns['cb'] ) ) {
			$new_columns['cb'] = $columns['cb'];
			unset( $columns['cb'] );
		}
		$new_columns['preview'] = esc_html__( 'Preview', 'variation-swatches-woo' );

		return wp_parse_args( $columns, $new_columns );
	}

	/**
	 * Term type markup
	 *
	 * @param string $columns term columns.
	 * @param string $column current term column.
	 * @param id     $term_id current term id.
	 * @return mixed
	 * @since  1.0.0
	 */
	public function add_preview_markup( $columns, $column, $term_id ) {
		global $taxnow;

		if ( $this->taxonomy !== $taxnow || 'preview' !== $column ) {
			return $columns;
		}

		$attr_type = $this->helper->get_attr_type_by_name( $this->taxonomy );
		if ( ! in_array( $attr_type, [ 'color', 'image' ], true ) ) {
			return $columns;
		}

		switch ( $attr_type ) {
			case 'color':
				$color = get_term_meta( $term_id, 'cfvsw_color', true );
				printf( '<div class="cfvsw-preview" style="background-color:%s;width:30px;height:30px;"></div>', esc_attr( $color ) );
				break;

			case 'image':
				$image     = get_term_meta( $term_id, 'cfvsw_image', true );
				$image_url = ! empty( $image ) ? $image : wc_placeholder_img_src();
				$image_url = str_replace( ' ', '%20', $image_url );
				printf( '<img class="cfvsw-preview" src="%s" width="44px" height="44px">', esc_url( $image_url ) );
				break;
		}
	}

	/**
	 * Generates html markup for product attribute shape
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function product_attribute_shape() {
		// To get attribute shape, size, height, width of attribute by id from url. 
		// $id will be added in a suffix on the option key so nonce is not required. 
		$id            = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$value         = $id ? get_option( "cfvsw_product_attribute_shape-$id" ) : '';
		$size          = $id ? get_option( "cfvsw_product_attribute_size-$id" ) : '';
		$height        = $id ? get_option( "cfvsw_product_attribute_height-$id" ) : '';
		$width         = $id ? get_option( "cfvsw_product_attribute_width-$id" ) : '';
		$shape_options = [
			'default' => __( 'Default', 'variation-swatches-woo' ),
			'square'  => __( 'Square', 'variation-swatches-woo' ),
			'rounded' => __( 'Rounded Corner', 'variation-swatches-woo' ),
			'circle'  => __( 'Circle', 'variation-swatches-woo' ),
		];

		if ( 'custom' === $value ) {
			$shape_options['custom'] = __( 'Custom', 'variation-swatches-woo' );
		}

		if ( $id > 0 ) {
			?>
			<tr class="form-field cfvsw-product-attribute-shape">
				<th scope="row" valign="top">
					<label for="cfvsw_product_attribute_shape"><?php esc_html_e( 'Shape', 'variation-swatches-woo' ); ?></label>
				</th>
				<td>
					<select name="cfvsw_product_attribute_shape" id="cfvsw_product_attribute_shape">
						<?php
						foreach ( $shape_options as $key => $label ) {
							$selected = ( $key === $value ) ? 'selected="selected"' : '';
							echo '<option ' . esc_attr( $selected ) . ' value=' . esc_attr( $key ) . '>' . esc_html( $label ) . '</option>';
						}
						?>
					</select>
					<p class="description default"><?php esc_html_e( 'Default setting is applied from settings page.', 'variation-swatches-woo' ); ?></p>
					<p class="description square"><?php esc_html_e( 'Swatch will appear as Square.', 'variation-swatches-woo' ); ?></p>
					<p class="description rounded"><?php esc_html_e( 'Swatch will appear as Square with slight curvature at border of 3px.', 'variation-swatches-woo' ); ?></p>
					<p class="description circle"><?php esc_html_e( 'Swatch will appear as Circle', 'variation-swatches-woo' ); ?></p>
					<p class="description custom"><?php esc_html_e( 'Create custom Swatch shape by choosing different height and width', 'variation-swatches-woo' ); ?></p>
				</td>
			</tr>
			<tr class="form-field cfvsw-product-attribute-size">
				<th scope="row" valign="top">
					<label for="cfvsw_product_attribute_size"><?php esc_html_e( 'Size (Optional)', 'variation-swatches-woo' ); ?></label>
				</th>
				<td>
					<input type="number" placeholder=24 min=1 name="cfvsw_product_attribute_size" id="cfvsw_product_attribute_size" value="<?php echo esc_attr( $size ); ?>" />
					<p class="description cfvsw-size"><?php esc_html_e( 'Control the size of this particular attribute. Default size  is 24px', 'variation-swatches-woo' ); ?></p>
				</td>
			</tr>
			<tr class="form-field cfvsw-product-attribute-height">
				<th scope="row" valign="top">
					<label for="cfvsw_product_attribute_height"><?php esc_html_e( 'Height (Optional)', 'variation-swatches-woo' ); ?></label>
				</th>
				<td>
					<input type="number" placeholder=24 min=1 name="cfvsw_product_attribute_height" id="cfvsw_product_attribute_height" value="<?php echo esc_attr( $height ); ?>" />
					<p class="description cfvsw-height"><?php esc_html_e( 'Control the height of this particular attribute. Default height  is 24px', 'variation-swatches-woo' ); ?></p>
				</td>
			</tr>
			<tr class="form-field cfvsw-product-attribute-width">
				<th scope="row" valign="top">
					<label for="cfvsw_product_attribute_width"><?php esc_html_e( 'Width (Optional)', 'variation-swatches-woo' ); ?></label>
				</th>
				<td>
					<input type="number" placeholder=24 min=1 name="cfvsw_product_attribute_width" id="cfvsw_product_attribute_width" value="<?php echo esc_attr( $width ); ?>" />
					<p class="description cfvsw-width"><?php esc_html_e( 'Control the width of this particular attribute. Default width  is 24px', 'variation-swatches-woo' ); ?></p>
				</td>
			</tr>
			<tr class="form-field cfvsw-product-attribute-height">
				<th scope="row" valign="top">
					<label for="cfvsw_product_attribute_height"><?php esc_html_e( 'Height (Optional)', 'variation-swatches-woo' ); ?></label>
				</th>
				<td>
					<input type="number" placeholder=30 min=1 name="cfvsw_product_attribute_height" id="cfvsw_product_attribute_height" value="<?php echo esc_attr( $height ); ?>" />
					<p class="description cfvsw-height"><?php esc_html_e( 'Control the height of this particular attribute. Default height  is 30px', 'variation-swatches-woo' ); ?></p>
				</td>
			</tr>
			<tr class="form-field cfvsw-product-attribute-width">
				<th scope="row" valign="top">
					<label for="cfvsw_product_attribute_width"><?php esc_html_e( 'Width (Optional)', 'variation-swatches-woo' ); ?></label>
				</th>
				<td>
					<input type="number" placeholder=30 min=1 name="cfvsw_product_attribute_width" id="cfvsw_product_attribute_width" value="<?php echo esc_attr( $width ); ?>" />
					<p class="description cfvsw-width"><?php esc_html_e( 'Control the width of this particular attribute. Default width  is 30px', 'variation-swatches-woo' ); ?></p>
				</td>
			</tr>
			<?php
		} else {
			?>
			<hr />
			<h2 class="cfvsw-attribute-section"><?php esc_html_e( 'Variation Swatches', 'variation-swatches-woo' ); ?> - <span><a href="<?php echo esc_url( admin_url( 'admin.php?page=cfvsw_settings&path=settings' ) ); ?>" target="_blank"><?php esc_html_e( 'Settings', 'variation-swatches-woo' ); ?></a></span></h2>
			<div class="form-field cfvsw-product-attribute-shape">
				<label for="cfvsw_product_attribute_shape"><?php esc_html_e( 'Shape', 'variation-swatches-woo' ); ?></label>
				<select name="cfvsw_product_attribute_shape" id="cfvsw_product_attribute_shape">
					<?php
					foreach ( $shape_options as $key => $label ) {
						$selected = ( $key === $value ) ? 'selected="selected"' : '';
						echo '<option ' . esc_attr( $selected ) . ' value=' . esc_attr( $key ) . '>' . esc_html( $label ) . '</option>';
					}
					?>
				</select>
				<p class="description default"><?php esc_html_e( 'Default setting is applied from settings page.', 'variation-swatches-woo' ); ?></p>
				<p class="description square"><?php esc_html_e( 'Swatch will appear as Square.', 'variation-swatches-woo' ); ?></p>
				<p class="description rounded"><?php esc_html_e( 'Swatch will appear as Square with slight curvature at border of 3px.', 'variation-swatches-woo' ); ?></p>
				<p class="description circle"><?php esc_html_e( 'Swatch will appear as Circle', 'variation-swatches-woo' ); ?></p>
				<p class="description custom"><?php esc_html_e( 'Create custom Swatch shape by choosing different height and width', 'variation-swatches-woo' ); ?></p>
			</div>
			<div class="form-field cfvsw-product-attribute-size">
				<label for="cfvsw_product_attribute_size"><?php esc_html_e( 'Size (Optional)', 'variation-swatches-woo' ); ?></label>
				<input type="number" placeholder=24 min=1 name="cfvsw_product_attribute_size" id="cfvsw_product_attribute_size" value="<?php echo esc_attr( $size ); ?>" />
				<p class="description cfvsw-size"><?php esc_html_e( 'Control the size of this particular attribute. Default size is 24px', 'variation-swatches-woo' ); ?></p>
			</div>
			<div class="form-field cfvsw-product-attribute-height">
				<label for="cfvsw_product_attribute_height"><?php esc_html_e( 'Height (Optional)', 'variation-swatches-woo' ); ?></label>
				<input type="number" placeholder=24 min=1 name="cfvsw_product_attribute_height" id="cfvsw_product_attribute_height" value="<?php echo esc_attr( $height ); ?>" />
				<p class="description cfvsw-height"><?php esc_html_e( 'Control the height of this particular attribute. Default height is 24px', 'variation-swatches-woo' ); ?></p>
			</div>
			<div class="form-field cfvsw-product-attribute-width">
				<label for="cfvsw_product_attribute_width"><?php esc_html_e( 'Width (Optional)', 'variation-swatches-woo' ); ?></label>
				<input type="number" placeholder=24 min=1 name="cfvsw_product_attribute_width" id="cfvsw_product_attribute_width" value="<?php echo esc_attr( $width ); ?>" />
				<p class="description cfvsw-width"><?php esc_html_e( 'Control the width of this particular attribute. Default width is 24px', 'variation-swatches-woo' ); ?></p>
			</div>

			<?php
		}
	}

	/**
	 * Saves produc tattribute shape meta on add new / edit attribute save action
	 *
	 * @param int $id attribute id.
	 * @return void
	 * @since 1.0.0
	 */
	public function save_product_attribute_shape( $id ) {
		// To Save attribute save this action added in admin panel.
		// In this action nonce is already verified by woocommerce so nonce is not required.
		if ( is_admin() && isset( $_POST['cfvsw_product_attribute_shape'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
			$option = "cfvsw_product_attribute_shape-$id";
			update_option( $option, sanitize_text_field( $_POST['cfvsw_product_attribute_shape'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing

			if ( isset( $_POST['cfvsw_product_attribute_size'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
				$option = "cfvsw_product_attribute_size-$id";
				update_option( $option, sanitize_text_field( $_POST['cfvsw_product_attribute_size'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing
			}

			if ( isset( $_POST['cfvsw_product_attribute_height'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
				$option = "cfvsw_product_attribute_height-$id";
				update_option( $option, sanitize_text_field( $_POST['cfvsw_product_attribute_height'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing
			}

			if ( isset( $_POST['cfvsw_product_attribute_width'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
				$option = "cfvsw_product_attribute_width-$id";
				update_option( $option, sanitize_text_field( $_POST['cfvsw_product_attribute_width'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing
			}
		}
	}

	/**
	 * Enqueue product attribute scripts
	 *
	 * @param string $hook current page slug.
	 * @return void
	 * @since 1.0.0
	 */
	public function product_attribute_scripts( $hook ) {
		if ( 'product_page_product_attributes' !== $hook ) {
			return;
		}

		wp_register_script( 'cfvsw-product-attribute', CFVSW_URL . 'admin/assets/js/product-attribute.js', [ 'jquery' ], CFVSW_VER, true );
		wp_enqueue_script( 'cfvsw-product-attribute' );

		wp_localize_script(
			'cfvsw-product-attribute',
			'cfvsw_admin_options',
			[
				'settings_url'     => esc_url( admin_url( 'admin.php?page=cfvsw_settings&path=settings' ) ),
				'swatches_label'   => __( 'Swatches Settings', 'variation-swatches-woo' ),
				'type_description' => __( 'Choose how this attribute should appear in frontend.', 'variation-swatches-woo' ),
			]
		);
	}
}
