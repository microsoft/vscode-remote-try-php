<?php
/**
 * Templates.
 *
 * @package variation-swatches-woo
 * @since 1.0.2
 */

namespace CFVSW\Admin;

use CFVSW\Inc\Traits\Get_Instance;
use CFVSW\Inc\Helper;


/**
 * Attribute Config
 *
 * @since 1.0.2
 */
class Templates {

	use Get_Instance;

	/**
	 * WooCommerce placeholder image.
	 *
	 * @var null
	 * @since  1.0.2
	 */
	private $wc_placeholder_image;

	/**
	 * Product id.
	 *
	 * @var null
	 * @since  1.0.2
	 */
	private $product_id;

	/**
	 * Constructor
	 *
	 * @since  1.0.2
	 */
	public function __construct() {
		$this->wc_placeholder_image = CFVSW_URL . '/admin/assets/img/wc-placeholder.png';
	}

	/**
	 * Helper class object
	 *
	 * @return object Helper object.
	 * @since  1.0.2
	 */
	public function helper() {
		return new Helper();
	}

	/**
	 * Swatches attribute type.
	 *
	 * @return array
	 * @since  1.0.2
	 */
	public function type_of_attributes() {
		return [
			'label' => esc_html__( 'Label', 'variation-swatches-woo' ),
			'color' => esc_html__( 'Color', 'variation-swatches-woo' ),
			'image' => esc_html__( 'Image', 'variation-swatches-woo' ),
		];
	}

	/**
	 * WooCommerce swatches template.
	 *
	 * @param object $object Product object.
	 * @return void
	 * @since  1.0.2
	 */
	public function panel_wrapper( $object ) {
		$this->product_id = $object->get_id();
		if ( ! $this->product_id ) {
			return;
		}
		?>
		<div id="cfvsw_swatches_settings" class="cfvsw-swatches-settings panel wc-metaboxes-wrapper hidden">
		<div class="cfvsw-swatches-settings-notice">
			<p></p>
		</div>
			<?php
				$attributes = array_filter( $object->get_attributes(), [ $this, 'is_visible' ] );
				$this->product_attribute_template( $attributes );
			?>
		</div>
		<?php
	}

	/**
	 * Update or reset swatches.
	 *
	 * @param integer $product_id Current product id.
	 * @param object  $object Product object.
	 * @param boolean $reset Reset or not argument.
	 * @return void
	 * @since  1.0.2
	 */
	public function update_reset_swatches_template( $product_id, $object, $reset = false ) {
		$this->product_id = $product_id;
		if ( ! $this->product_id ) {
			return;
		}
		$attributes = array_filter( $object->get_attributes(), [ $this, 'is_visible' ] );
		ob_start();
		$this->taxonomy_section( $attributes, $reset );
		return ob_get_clean();
	}


	/**
	 * Product attribute template with require hidden inputs.
	 *
	 * @param object $attributes Product attribute.
	 * @return void
	 * @since  1.0.2
	 */
	public function product_attribute_template( $attributes ) {
		$buttons_hidden_class       = '';
		$check_attr_variation       = $this->check_attr_variation( $attributes );
		$variation_setting_page_url = add_query_arg(
			[
				'page' => 'cfvsw_settings',
			],
			admin_url( 'admin.php' )
		);

		?>
		<div class="cfvsw-swatches-input-section">
			<input type="hidden" name="product_id" value="<?php echo esc_attr( $this->product_id ); ?>" />
			<input type="hidden" name="swatches_action" value="cfvsw_save_product_swatches_data" />
			<input type="hidden" name="security" value="<?php echo esc_attr( wp_create_nonce( 'cfvsw_swatches_save_reset' ) ); ?>" />
			<div class="cfvsw-swatches-taxonomy-section">
				<?php
				if ( ! $check_attr_variation ) {
					?>
					<p class="cfvsw-swatches-no-visible-attr">
					<?php
					esc_html_e( 'No visible attribute found.', 'variation-swatches-woo' );
					?>
					</p>
					<?php
					$buttons_hidden_class = 'hidden-buttons';
				} else {
					$this->taxonomy_section( $attributes );
				}
				?>
			</div>
		</div>
		<div class="cfvsw-save-reset-swatches <?php echo esc_attr( $buttons_hidden_class ); ?>">
			<div>
				<span class="cfvsw-save-swatches button button-primary"><?php esc_html_e( 'Save', 'variation-swatches-woo' ); ?></span>
				<span class="cfvsw-reset-swatches"><?php esc_html_e( 'Reset', 'variation-swatches-woo' ); ?></span>
			</div>
			<a href="<?php echo esc_url( $variation_setting_page_url ); ?>" target="_blank" class="cfvsw-global-setting"><?php esc_html_e( 'Swatches Global Settings', 'variation-swatches-woo' ); ?></a>
		</div>
		<?php
	}

	/**
	 * Check product attribute should not empty and variation product.
	 *
	 * @param object $attributes Attribute object.
	 * @return boolean
	 * @since  1.0.2
	 */
	public function check_attr_variation( $attributes ) {
		if ( empty( $attributes ) ) {
			return false;
		}
		$return = false;
		foreach ( $attributes as $attribute ) {
			if ( $attribute->get_variation() ) {
				$return = true;
				break;
			}
		}
		return $return;
	}
	/**
	 * Get template function.
	 *
	 * @param object  $attributes Attributes object.
	 * @param boolean $reset Product attribute reset.
	 * @return void
	 * @since  1.0.2
	 */
	public function taxonomy_section( $attributes, $reset = false ) {
		$this->product_settings_wrapper( $attributes, $reset );
		foreach ( $attributes as $attribute ) {
			if ( $attribute->get_variation() ) {
				$this->attribute_wrapper( $attribute, $reset );
			}
		}
	}

	/**
	 * Product settings.
	 *
	 * @param object  $attributes Attributes object.
	 * @param boolean $reset Product attribute reset, on reset no need to get previous meta just all meta blank keep it blank.
	 * @since 1.0.3
	 * @return void
	 */
	public function product_settings_wrapper( $attributes, $reset ) {
		$get_shop_setting = $this->helper()->get_option( CFVSW_SHOP );
		if ( ! empty( $get_shop_setting['special_attr_archive'] ) ) :
			$meta_key_name          = CFVSW_PRODUCT_ATTR . '_catalog_attr';
			$input_name             = "attr[$meta_key_name]";
			$get_saved_value        = ! $reset ? get_post_meta( intval( $this->product_id ), sanitize_text_field( $meta_key_name ), true ) : false;
			$put_saved_value_hidden = '';
			?>
		<div class="cfvsw-product-settings">
			<div class="cfvsw-settings-container cfvsw-settings-special-attr cfvsw-attribute-wrapper">
			<div class="cfvsw-attribute-label">
				<h3><?php esc_html_e( 'Catalog Mode Attribute', 'variation-swatches-woo' ); ?></h3>
			</div>
			<div class="cfvsw-attribute-field">
				<select data-name="<?php echo esc_attr( $input_name ); ?>" class="select2 wc-enhanced-select cfvsw-attribute-type-select">
						<option value=""><?php esc_html_e( 'Default', 'variation-swatches-woo' ); ?></option>
						<?php
						foreach ( $attributes as $attribute ) {
							if ( $attribute->get_variation() ) {
								$attr_get_name = $attribute->get_name();
								$taxonomy      = get_taxonomy( $attr_get_name );
								$label         = $taxonomy ? $taxonomy->labels->singular_name : $attr_get_name;
								$selected      = '';
								if ( $get_saved_value && $attr_get_name === $get_saved_value ) {
									$selected               = 'selected';
									$put_saved_value_hidden = $get_saved_value;
								}
								?>
								<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $attr_get_name ); ?>"><?php echo esc_html( $label ); ?></option>
								<?php
							}
						}
						?>
					</select>
					<input type="hidden" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $put_saved_value_hidden ); ?>" />
			</div>
		</div>
		</div>
			<?php
		endif;
	}

	/**
	 * Get attribute visible or not.
	 *
	 * @param object $attribute Product attribute.
	 * @return boolean
	 * @since  1.0.2
	 */
	private function is_visible( $attribute ) {
		return true === $attribute->get_visible();
	}

	/**
	 * Attribute wrapper.
	 *
	 * @param object  $attribute Product attribute object.
	 * @param boolean $reset Product attribute reset.
	 * @return void
	 * @since  1.0.2
	 */
	public function attribute_wrapper( $attribute, $reset ) {
		$attr_get_name       = $attribute->get_name();
		$taxonomy            = get_taxonomy( $attr_get_name );
		$is_custom_attribute = false;
		if ( $taxonomy ) {
			$label = $taxonomy->labels->singular_name;
		} else {
			$label               = $attr_get_name;
			$is_custom_attribute = true;
			$attr_get_name       = $this->helper()->create_slug( $label );
		}
		$meta_key_name     = CFVSW_PRODUCT_ATTR . '_' . $attr_get_name;
		$input_name        = "attr[$meta_key_name]";
		$get_post_meta     = ! $reset ? get_post_meta( intval( $this->product_id ), sanitize_text_field( $meta_key_name ), true ) : false;
		$attribute_type    = ! empty( $get_post_meta['type'] ) ? $get_post_meta['type'] : '';
		$put_wrapper_value = ! $attribute_type ? 'attr-value-unvailable=1' : '';
		?>
		<div class='cfvsw-attribute-wrapper cfvsw-metabox' <?php echo esc_attr( $put_wrapper_value ); ?>>
			<span class="cfvsw-attribute-type-span">
					<label><?php esc_html_e( 'Attribute Type', 'variation-swatches-woo' ); ?></label>
					<select data-name="<?php echo esc_attr( $input_name ); ?>[type]" class="select2 wc-enhanced-select cfvsw-attribute-type-select cfvsw-inside-wrapper-hide-show">
						<option value=""><?php esc_html_e( 'Default', 'variation-swatches-woo' ); ?></option>
						<?php
						foreach ( $this->type_of_attributes() as $key => $value ) {
							$selected = $attribute_type && $key === $attribute_type ? 'selected' : '';
							?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
						<?php } ?>
					</select>
			</span>
			<h3 class="cfvsw-attribute-heading cfvsw-metabox-handle">
				<span><?php echo esc_html( $label ); ?></span>
				<div class="cfvsw-handlediv"></div>
				<input class="cfvsw-term-parent-attr" type="hidden" name="<?php echo esc_attr( $input_name ); ?>[type]" value="<?php echo esc_attr( $attribute_type ); ?>" />
			</h3>
			<div class="woocommerce_attribute_data cfvsw-metabox-content">
				<?php $this->attribute_type_wrapper( $attribute, $is_custom_attribute, $input_name, $get_post_meta, $attribute_type ); ?>
			</div>
		</div>
		<?php
	}


	/**
	 * Attribute type wrapper custom and global both attribute.
	 *
	 * @param object  $attribute Product attribute object.
	 * @param boolean $is_custom_attribute Custom attribute or global attribute.
	 * @param string  $input_name Term input name which is saved in post meta.
	 * @param array   $get_post_meta Product attribute post meta.
	 * @param string  $attribute_type Attribute type for now image, color.
	 * @return void
	 * @since  1.0.2
	 */
	public function attribute_type_wrapper( $attribute, $is_custom_attribute, $input_name, $get_post_meta, $attribute_type ) {
		$get_options = $attribute->get_options();
		?>
		<div class="cfvsw-attribute-itemes">
			<?php
			if ( $is_custom_attribute ) {
				foreach ( $get_options as $get_term_name ) {
					$item_id = $this->helper()->create_slug( $get_term_name );
					$this->get_term_template( $item_id, $get_term_name, $input_name, $get_post_meta, $attribute_type );
				}
			} else {
				foreach ( $get_options as $item_id ) {
					$get_term_name = get_term( $item_id )->name;
					$this->get_term_template( $item_id, $get_term_name, $input_name, $get_post_meta, $attribute_type );
				}
			}
			?>
		</div>
		<?php
	}

	/**
	 * Attribute terms template.
	 *
	 * @param string $item_id Term id, for custom attribute it will be term name in form of slug and for attribute it will be term id in number.
	 * @param string $term_name Term name.
	 * @param string $input_name Term input name which is saved in post meta.
	 * @param array  $get_post_meta Product attribute post meta.
	 * @param string $attribute_type Attribute type for now image, color.
	 * @return void
	 * @since  1.0.2
	 */
	public function get_term_template( $item_id, $term_name, $input_name, $get_post_meta, $attribute_type ) {
		$item_name_label = $input_name . "[$item_id][label]";
		$item_name_color = $input_name . "[$item_id][color]";
		$item_name_image = $input_name . "[$item_id][image]";

		$saved_label = ! empty( $get_post_meta[ $item_id ]['label'] ) ? $get_post_meta[ $item_id ]['label'] : '';
		$saved_image = ! empty( $get_post_meta[ $item_id ]['image'] ) ? intval( $get_post_meta[ $item_id ]['image'] ) : '';
		$saved_color = ! empty( $get_post_meta[ $item_id ]['color'] ) ? $get_post_meta[ $item_id ]['color'] : '';

		$get_image_url = $saved_image ? wp_get_attachment_url( $saved_image ) : '';
		$put_image_url = $get_image_url ? $get_image_url : $this->wc_placeholder_image;

		$show_label_container     = 'label' === $attribute_type ? 'display:block;' : '';
		$show_image_container     = 'image' === $attribute_type ? 'display:block;' : '';
		$show_color_container     = 'color' === $attribute_type ? 'display:block;' : '';
		$show_remove_image_button = $saved_image ? 'display:block;' : '';
		?>
		<div class='cfvsw-attribute-wrapper cfvsw-metabox cfvsw-term-box'>
			<h3 class="cfvsw-attribute-heading cfvsw-metabox-handle">
				<span><?php echo esc_html( $term_name ); ?></span>
				<div class="cfvsw-handlediv"></div>
			</h3>
			<div class="woocommerce_attribute_data cfvsw-metabox-content">
				<div data-container='label' style="<?php echo esc_attr( $show_label_container ); ?>" class="cfvsw-attribute-item-container">
					<div>
						<div class="cfvsw-attribute-label">
							<label><?php esc_html_e( 'Label Text', 'variation-swatches-woo' ); ?></label>
						</div>
						<div class="cfvsw-attribute-field">
							<input data-cfvsw-save='cfvsw-certain-type' type="text" value="<?php echo esc_attr( $saved_label ); ?>" name="<?php echo esc_attr( $item_name_label ); ?>" />
						</div>
					</div>
				</div>
				<div data-container='color' style="<?php echo esc_attr( $show_color_container ); ?>" class="cfvsw-attribute-item-container">
					<div>
						<div class="cfvsw-attribute-label">
							<label><?php esc_html_e( 'Color', 'variation-swatches-woo' ); ?></label>
						</div>
						<div class="cfvsw-attribute-field">
							<input data-cfvsw-save='cfvsw-certain-type' id="cfvsw-attribute-item-color" class="cfvsw-attribute-item-color" type="text" value="<?php echo esc_attr( $saved_color ); ?>" name="<?php echo esc_attr( $item_name_color ); ?>" />
						</div>
					</div>
				</div>
				<div data-container='image' style="<?php echo esc_attr( $show_image_container ); ?>" class="cfvsw-attribute-item-container">
					<div>
						<div class="cfvsw-attribute-label">
							<label><?php esc_html_e( 'Image', 'variation-swatches-woo' ); ?></label>
						</div>
						<div class="cfvsw-attribute-field field-image">
							<div class="cfvsw-image-preview-wrap">
								<img class="cfvsw-image-preview" data-placeholder-image="<?php echo esc_url( $this->wc_placeholder_image ); ?>" src="<?php echo esc_url( $put_image_url ); ?>" alt="<?php esc_attr_e( 'Variation swatches image preview', 'variation-swatches-woo' ); ?>" />
							</div>
							<div class="button-wrapper">
								<input type="hidden" class="cfvsw-save-image" name="<?php echo esc_attr( $item_name_image ); ?>" value="<?php echo esc_attr( $saved_image ); ?>" />
								<button type="button" class="cfvsw_upload_image_attr_item button button-primary button-small"><?php esc_html_e( 'Upload image', 'variation-swatches-woo' ); ?></button>
								<button type="button" id="cfvsw_remove_image_attr_item" style="<?php echo esc_attr( $show_remove_image_button ); ?>" class="cfvsw_remove_image_attr_item button button-small "><?php esc_html_e( 'Remove image', 'variation-swatches-woo' ); ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
