<?php
/**
 * Swatches.
 *
 * @package variation-swatches-woo
 * @since 1.0.0
 */

namespace CFVSW\Inc;

use CFVSW\Inc\Traits\Get_Instance;
use CFVSW\Inc\Helper;
use CFVSW\Compatibility\Astra;
use CFVSW\Compatibility\Cartflows_Pro;
use WC_AJAX;


/**
 * Admin menu
 *
 * @since 1.0.0
 */
class Swatches {

	use Get_Instance;

	/**
	 * Instance of Helper class
	 *
	 * @var Helper
	 * @since  1.0.0
	 */
	private $helper;

	/**
	 * Stores global and store settings
	 *
	 * @var array
	 * @since  1.0.0
	 */
	private $settings = [];

	/**
	 * Post meta variation swatches type.
	 *
	 * @var boolean
	 * @since  1.0.2
	 */
	private $product_option_type = false;

	/**
	 * Postmeta variation attributes swatches value product lavel.
	 *
	 * @var array
	 * @since  1.0.2
	 */
	private $product_option_swatches = [];

	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		$this->helper                   = new Helper();
		$this->settings[ CFVSW_GLOBAL ] = $this->helper->get_option( CFVSW_GLOBAL );
		$this->settings[ CFVSW_SHOP ]   = $this->helper->get_option( CFVSW_SHOP );
		$this->settings[ CFVSW_STYLE ]  = $this->helper->get_option( CFVSW_STYLE );
		if ( class_exists( 'Cartflows_Pro_Loader' ) ) {
			new Cartflows_Pro();
		}
		add_filter( 'woocommerce_ajax_variation_threshold', [ $this, 'cfvsw_ajax_variation_threshold' ], 100, 2 );

		add_action( 'template_redirect', [ $this, 'shortcode_functionality' ], 10 );
		if (
			$this->settings[ CFVSW_GLOBAL ]['enable_swatches'] || $this->settings[ CFVSW_GLOBAL ]['enable_swatches_shop']
		) {
			add_filter( 'woocommerce_dropdown_variation_attribute_options_html', [ $this, 'variation_attribute_custom_html' ], 999, 2 );
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

			$position = $this->get_swatches_position();
			add_action( $position['action'], [ $this, 'variation_attribute_html_shop_page' ], $position['priority'] );
			add_filter( 'body_class', [ $this, 'label_position_class' ], 10, 2 );
			add_filter( 'woocommerce_loop_add_to_cart_args', [ $this, 'shop_page_add_to_cart_args' ], 10, 2 );
		}

		add_action( 'wp_ajax_cfvsw_ajax_add_to_cart', [ $this, 'cfvsw_ajax_add_to_cart' ] );
		add_action( 'wp_ajax_nopriv_cfvsw_ajax_add_to_cart', [ $this, 'cfvsw_ajax_add_to_cart' ] );
		add_filter( 'woocommerce_layered_nav_term_html', [ $this, 'filters_html' ], 10, 4 );
	}

	/**
	 * Add variation in product shortcode functionality.
	 *
	 * @return void
	 * @since 1.0.4
	 */
	public function shortcode_functionality() {
		$add_filter             = false;
		$woocommerce_shortcodes = [ 'products', 'featured_products', 'sale_products', 'best_selling_products', 'recent_products', 'product_attribute', 'top_rated_products', 'product_category' ];
		foreach ( $woocommerce_shortcodes as $value ) {
			if ( wc_post_content_has_shortcode( $value ) ) {
				$add_filter = true;
				break;
			}
		}

		if ( $add_filter ) {
			add_filter( 'cfvsw_requires_shop_settings', '__return_true' );
		} elseif ( wc_post_content_has_shortcode( 'product_page' ) ) {
			add_filter( 'cfvsw_requires_global_settings', '__return_true' );
		}
	}

	/**
	 * Get swatches postmeta options.
	 *
	 * @param integer $product_id Product id.
	 * @param array   $args_attribute Attribute arguments.
	 * @param boolean $attribute_id Attribute id.
	 * @return void
	 * @since  1.0.2
	 */
	public function get_product_swatches( $product_id, $args_attribute, $attribute_id ) {
		// Clean variables.
		if ( $this->product_option_type ) {
			$this->product_option_type     = false;
			$this->product_option_swatches = [];
		}
		$product_meta_slug = $attribute_id ? CFVSW_PRODUCT_ATTR . '_' . $args_attribute : CFVSW_PRODUCT_ATTR . '_' . $this->helper->create_slug( $args_attribute );
		$get_meta_options  = get_post_meta( $product_id, $product_meta_slug, true );
		if ( empty( $get_meta_options['type'] ) ) {
			return;
		}
		$this->product_option_type = $get_meta_options['type'];
		unset( $get_meta_options['type'] );
		if ( count( $get_meta_options ) > 0 ) {
			if ( 'image' === $this->product_option_type ) {
				foreach ( $get_meta_options as $key => $value ) {
					if ( empty( $value[ $this->product_option_type ] ) ) {
						continue;
					}
					$image_id      = intval( $value[ $this->product_option_type ] );
					$get_image_url = $image_id ? wp_get_attachment_url( $image_id ) : false;
					if ( ! $get_image_url ) {
						continue;
					}
					$this->product_option_swatches[ $key ] = $get_image_url;
				}
			} else {
				foreach ( $get_meta_options as $key => $value ) {
					if ( empty( $value[ $this->product_option_type ] ) ) {
						continue;
					}
					$custom_product_meta                   = $value[ $this->product_option_type ];
					$this->product_option_swatches[ $key ] = $custom_product_meta;
				}
			}
		}
	}

	/**
	 * Get attribute term color.
	 *
	 * @param integer $attr_id Attribute id.
	 * @param string  $slug Term slug.
	 * @param string  $args Attribute options.
	 * @param string  $for It should be color or image.
	 * @return array
	 * @since  1.0.2
	 */
	public function get_attr_term_color_image( $attr_id, $slug, $args, $for ) {
		$default_value = 'color' === $for ? '#fff' : CFVSW_URL . '/admin/assets/img/wc-placeholder.png';
		$term_name     = $slug;
		if ( $attr_id ) {
			$term = get_term_by( 'slug', $slug, $args['attribute'] );
			if ( $this->product_option_type ) {
				$get_value = ! empty( $this->product_option_swatches[ $term->term_id ] ) ? $this->product_option_swatches[ $term->term_id ] : $default_value;
			} else {
				$meta_name = 'color' === $for ? 'cfvsw_color' : 'cfvsw_image';
				$get_value = get_term_meta( $term->term_id, $meta_name, true );
			}
			$term_name = $term->name;
		} else {
			$get_value_by_slug = $this->helper->create_slug( $slug );
			$get_value         = ! empty( $this->product_option_swatches[ $get_value_by_slug ] ) ? $this->product_option_swatches[ $get_value_by_slug ] : $default_value;
		}
		$return = [
			'term_name' => $term_name,
			$for        => $get_value,
		];
		return $return;
	}

	/**
	 * Get attribute term label.
	 *
	 * @param integer $attr_id Attribute id.
	 * @param string  $slug Term slug.
	 * @param string  $args Attribute options.
	 * @return string
	 * @since  1.0.2
	 */
	public function get_attr_term_label( $attr_id, $slug, $args ) {
		$term = get_term_by( 'slug', $slug, $args['attribute'] );
		$name = ! empty( $term->name ) ? $term->name : $slug;
		if ( $attr_id && $this->product_option_type ) {
			$name = ! empty( $this->product_option_swatches[ $term->term_id ] ) ? $this->product_option_swatches[ $term->term_id ] : $name;
		} elseif ( $this->product_option_type ) {
			$get_label_slug = $this->helper->create_slug( $slug );
			$name           = ! empty( $this->product_option_swatches[ $get_label_slug ] ) ? $this->product_option_swatches[ $get_label_slug ] : $name;
		}
		return $name;
	}

	/**
	 * Return variation product attributes.
	 *
	 * @param string $select_html Default attribute template.
	 * @param array  $args Attributes settings options.
	 * @return string
	 * @since  1.0.0
	 */
	public function variation_attribute_custom_html( $select_html, $args ) {
		global $product;
		$settings        = [];
		$container_class = '';
		if ( ! $this->is_required_page() ) {
			return $select_html;
		}
		if ( $this->requires_shop_settings() ) {
			if ( ! $this->settings[ CFVSW_GLOBAL ]['enable_swatches_shop'] ) {
				return $select_html;
			}
			$settings                 = $this->settings[ CFVSW_SHOP ]['override_global'] ? $this->settings[ CFVSW_SHOP ] : array_merge( $this->settings[ CFVSW_SHOP ], $this->settings[ CFVSW_GLOBAL ] );
			$settings['auto_convert'] = true;
			if ( ! isset( $settings['tooltip'] ) ) {
				$settings['tooltip'] = $this->settings[ CFVSW_GLOBAL ]['tooltip'];
			}
			$container_class = 'cfvsw-shop-container';
		}
		if ( $this->requires_global_settings() ) {
			if ( ! $this->settings[ CFVSW_GLOBAL ]['enable_swatches'] ) {
				return $select_html;
			}
			$settings        = $this->settings[ CFVSW_GLOBAL ];
			$container_class = 'cfvsw-product-container';
		}

		if ( empty( $settings ) ) {
			return $select_html;
		}
		$attribute     = $product->get_attributes();
		$attr_id       = isset( $attribute[ strtolower( $args['attribute'] ) ] ) ? $attribute[ strtolower( $args['attribute'] ) ]->get_id() : 0;
		$shape         = get_option( "cfvsw_product_attribute_shape-$attr_id", 'default' );
		$size          = absint( get_option( "cfvsw_product_attribute_size-$attr_id", '' ) );
		$height        = absint( get_option( "cfvsw_product_attribute_height-$attr_id", '' ) );
		$width         = absint( get_option( "cfvsw_product_attribute_width-$attr_id", '' ) );
		$min_width     = ! empty( $settings['min_width'] ) ? $settings['min_width'] . 'px' : '24px';
		$min_height    = ! empty( $settings['min_height'] ) ? $settings['min_height'] . 'px' : '24px';
		$border_radius = $settings['border_radius'] . 'px';
		switch ( $shape ) {
			case 'circle':
				$min_width     = $size ? $size . 'px' : '24px';
				$min_height    = $size ? $size . 'px' : '24px';
				$border_radius = '100%';
				break;
			case 'square':
				$min_width     = $size ? $size . 'px' : '24px';
				$min_height    = $size ? $size . 'px' : '24px';
				$border_radius = '0px';
				break;
			case 'rounded':
				$min_width     = $size ? $size . 'px' : '24px';
				$min_height    = $size ? $size . 'px' : '24px';
				$border_radius = '3px';
				break;
			case 'custom':
				$min_width     = $width ? $width . 'px' : '24px';
				$min_height    = $height ? $height . 'px' : '24px';
				$border_radius = '0px';
				break;
			default:
				break;
		}

		$product_id = ! empty( $args['product'] ) ? intval( $args['product']->get_id() ) : false;

		if ( $product_id ) {
			$this->get_product_swatches( $product_id, $args['attribute'], $attr_id );
		}
		$type               = $this->product_option_type ? $this->product_option_type : $this->helper->get_attr_type_by_name( $args['attribute'] );
		$limit              = isset( $settings['limit'] ) ? intval( $settings['limit'] ) : 0;
		$attr_options_mix   = $this->get_attr_option_by_sorting( $product_id, $args['attribute'], $limit, $args['options'] );
		$attr_options       = $attr_options_mix['options'];
		$more               = $attr_options_mix['more'];
		$get_attribute_name = wc_variation_attribute_name( $args['attribute'] );
		$common_style       = 'min-width:' . $min_width . ';';
		$common_style      .= 'min-height:' . $min_height . ';';
		$common_style      .= 'border-radius:' . $border_radius . ';';

		switch ( $type ) {
			case 'color':
				$html = "<div class='cfvsw-swatches-container " . esc_attr( $container_class ) . "' swatches-attr='" . esc_attr( $get_attribute_name ) . "'>";
				foreach ( $attr_options as $slug ) {
					$get_term_data = $this->get_attr_term_color_image( $attr_id, $slug, $args, 'color' );
					$term_name     = $get_term_data['term_name'];
					$color         = $get_term_data['color'];
					$tooltip       = $settings['tooltip'] ? $term_name : '';
					$style         = $common_style;
					$inner_style   = 'background-color:' . $color . ';';
					$html         .= "<div class='cfvsw-swatches-option' data-slug='" . esc_attr( $slug ) . "' data-title='" . esc_attr( $term_name ) . "' data-tooltip='" . esc_attr( $tooltip ) . "' style=" . esc_attr( $style ) . '><div class="cfvsw-swatch-inner" style="' . esc_attr( $inner_style ) . '"></div></div>';
				}
				$html .= $more ? '<span class="cfvsw-more-link" style="line-height:' . esc_attr( $min_height ) . '">' . $more . '</span' : '';
				$html .= '</div>';
				break;
			case 'image':
				$html = "<div class='cfvsw-swatches-container " . esc_attr( $container_class ) . "' swatches-attr='" . esc_attr( $get_attribute_name ) . "'>";
				foreach ( $attr_options as $slug ) {
					$get_term_data = $this->get_attr_term_color_image( $attr_id, $slug, $args, 'image' );
					$term_name     = $get_term_data['term_name'];
					$image         = $get_term_data['image'];
					$tooltip       = $settings['tooltip'] ? $term_name : '';
					$style         = $common_style;
					$inner_style   = "background-image:url('" . esc_url( $image ) . "');background-size:cover;";
					$html         .= "<div class='cfvsw-swatches-option cfvsw-image-option' data-slug='" . esc_attr( $slug ) . "' data-title='" . esc_attr( $term_name ) . "' data-tooltip='" . esc_attr( $tooltip ) . "' style=" . esc_attr( $style ) . '>';
					$html         .= '<div class="cfvsw-swatch-inner" style="' . $inner_style . '"></div></div>';
				}
				$html .= $more ? '<span class="cfvsw-more-link" style="line-height:' . esc_attr( $min_height ) . '">' . $more . '</span' : '';
				$html .= '</div>';
				break;
			default:
				if ( 'label' !== $type && ! $settings['auto_convert'] ) {
					break;
				}
				$html = "<div class='cfvsw-swatches-container " . esc_attr( $container_class ) . "' swatches-attr='" . esc_attr( $get_attribute_name ) . "'>";
				foreach ( $attr_options as $slug ) {
					$style = $common_style;
					$name  = $this->get_attr_term_label( $attr_id, $slug, $args );
					$html .= "<div class='cfvsw-swatches-option cfvsw-label-option' data-slug='" . esc_attr( $slug ) . "' data-title='" . esc_attr( $name ) . "' style=" . esc_attr( $style ) . '><div class="cfvsw-swatch-inner">' . esc_html( $name ) . '</div></div>';
				}
				$html .= $more ? '<span class="cfvsw-more-link" style="line-height:' . esc_attr( $min_height ) . '">' . $more . '</span' : '';
				$html .= '</div>';
				break;
		}
		if ( ! empty( $html ) ) {
			return '<div class="cfvsw-hidden-select">' . $select_html . '</div>' . $html;
		}
		return $select_html;
	}

	/**
	 * Get attribute options.
	 *
	 * @param integer $product_id Current product id.
	 * @param string  $attribute Attribute slug.
	 * @param integer $limit Show swatches limit.
	 * @param integer $options For custom attribute options.
	 * @return array
	 * @since  1.0.2
	 */
	public function get_attr_option_by_sorting( $product_id, $attribute, $limit, $options ) {
		$attr_terms = wc_get_product_terms( $product_id, $attribute, [ 'fields' => 'all' ] );
		if ( ! empty( $attr_terms ) ) {
			$attr_terms = array_map(
				function ( $value ) {
					return $value->slug;
				},
				$attr_terms
			);
			$attr_terms = array_intersect( $attr_terms, $options );
		} else {
			$attr_terms = $options;
		}

		if ( $limit > 0 && $limit < count( $attr_terms ) ) {
			$permalink = get_permalink( $product_id );
			/* translators: %1$1s, %3$3s: Html Tag, %2$2s: Extra attribute count */
			$more = sprintf( __( '%1$1s %2$2s More %3$3s', 'variation-swatches-woo' ), '<a href="' . esc_url( $permalink ) . '">', ( count( $attr_terms ) - $limit ), '</a>' );
			return [
				'options' => array_splice( $attr_terms, 0, $limit ),
				'more'    => $more,
			];
		}
		return [
			'options' => $attr_terms,
			'more'    => '',
		];
	}

	/**
	 * Add catalog attribute funationality on shop page.
	 *
	 * @param array $settings Product settings.
	 * @param int   $product_id Product id.
	 * @param array $attributes Product attributes.
	 * @return array
	 * @since 1.0.3
	 */
	public function catalog_show_attr_shop_page( $settings, $product_id, $attributes ) {
		// Get global level attr.
		$get_global_saved_attr = ! empty( $settings['special_attr_choose'] ) ? sanitize_text_field( $settings['special_attr_choose'] ) : '';
		// Verify global attr exist or not.
		if ( '' !== $get_global_saved_attr ) {
			if ( ! taxonomy_is_product_attribute( $get_global_saved_attr ) ) {
				$get_global_saved_attr = '';
			}
		}

		// Get product level attr.
		$get_product_saved_attr = get_post_meta( $product_id, sanitize_text_field( CFVSW_PRODUCT_ATTR . '_catalog_attr' ), true );

		// Compare and show attr.
		$show_attr_name = ! empty( $get_product_saved_attr ) && ! empty( $attributes[ $get_product_saved_attr ] ) ? $get_product_saved_attr : $get_global_saved_attr;

		// Show first attr.
		if ( empty( $show_attr_name ) ) {
			$show_attr_name = array_key_first( $attributes );
		}
		return ! empty( $attributes[ $show_attr_name ] ) ? [ $show_attr_name => $attributes[ $show_attr_name ] ] : false;
	}

	/**
	 * Generates variation attributes for shop page
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public function variation_attribute_html_shop_page() {
		global $product;
		if ( ! $this->settings[ CFVSW_GLOBAL ]['enable_swatches_shop'] ) {
			return;
		}

		if ( ! $this->requires_shop_settings() ) {
			return;
		}

		if ( ! $product->is_type( 'variable' ) ) {
			return;
		}

		if ( ! $product->get_available_variations() ) {
			return;
		}
		$product_id = $product->get_id();
		$settings   = $this->settings[ CFVSW_SHOP ];
		// Get Available variations?
		$get_variations       = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
		$available_variations = $get_variations ? $product->get_available_variations() : false;
		$attributes           = $product->get_variation_attributes();
		// Catlog mode functionality.
		$count_attr_for_catalog = '';
		if ( ! empty( $settings['special_attr_archive'] ) ) {
			$count_attr_for_catalog = count( $attributes ) > 1 ? 'data-cfvsw-catalog=1' : '';
			$attributes             = $this->catalog_show_attr_shop_page( $settings, $product_id, $attributes );
			if ( empty( $attributes ) ) {
				return;
			}
		}

		$attribute_keys  = array_keys( $attributes );
		$variations_json = wp_json_encode( $available_variations );
		?>
		<div class="cfvsw_variations_form variations_form cfvsw_shop_align_<?php echo esc_attr( $settings['alignment'] ); ?>" data-product_variations="<?php echo esc_attr( $variations_json ); ?>" data-product_id="<?php echo absint( $product_id ); ?>" <?php echo esc_attr( $count_attr_for_catalog ); ?>>
			<?php if ( empty( $available_variations ) && false !== $available_variations ) { ?>
				<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'variation-swatches-woo' ) ) ); ?></p>
			<?php } else { ?>
				<table class="cfvsw-shop-variations variations" cellspacing="0">
					<tbody>
						<?php foreach ( $attributes as $attribute_name => $options ) { ?>
							<tr>
								<?php if ( $settings['label'] ) { ?>
									<td class="label woocommerce-loop-product__title"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>">
											<?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?>
										</label>
									</td>
								<?php } ?>
							</tr>
							<tr>
								<td class="value">
									<?php
									wc_dropdown_variation_attribute_options(
										array(
											'options'   => $options,
											'attribute' => $attribute_name,
											'product'   => $product,
										)
									);
									echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'variation-swatches-woo' ) . '</a>' ) ) : '';
									?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>
		</div>
		<?php
	}


	/**
	 * Enqueue scripts and style for frontend
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public function enqueue_scripts() {
		if ( ! $this->is_required_page() ) {
			return;
		}

		wp_register_style( 'cfvsw_swatches_product', CFVSW_URL . 'assets/css/swatches.css', [ 'dashicons' ], CFVSW_VER );
		wp_enqueue_style( 'cfvsw_swatches_product' );
		$this->inline_css();

		wp_register_script( 'cfvsw_swatches_product', CFVSW_URL . 'assets/js/swatches.js', [ 'jquery', 'wc-add-to-cart-variation' ], CFVSW_VER, true );
		wp_enqueue_script( 'cfvsw_swatches_product' );
		wp_localize_script(
			'cfvsw_swatches_product',
			'cfvsw_swatches_settings',
			[
				'ajax_url'               => admin_url( 'admin-ajax.php' ),
				'admin_url'              => admin_url( 'admin.php' ),
				'remove_attr_class'      => $this->get_remove_attr_class(),
				'html_design'            => $this->settings[ CFVSW_GLOBAL ]['html_design'],
				'unavailable_text'       => __( 'Selected variant is unavailable.', 'variation-swatches-woo' ),
				'ajax_add_to_cart_nonce' => wp_create_nonce( 'cfvsw_ajax_add_to_cart' ),
				'tooltip_image'          => $this->settings[ CFVSW_STYLE ]['tooltip_image'],
				'disable_out_of_stock'   => $this->settings[ CFVSW_GLOBAL ]['disable_out_of_stock'],
			]
		);
	}

	/**
	 * Adds inline css
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function inline_css() {
		$style = $this->settings[ CFVSW_STYLE ];

		if ( $this->requires_shop_settings() ) {
			$settings = $this->settings[ CFVSW_SHOP ]['override_global'] ? $this->settings[ CFVSW_SHOP ] : array_merge( $this->settings[ CFVSW_SHOP ], $this->settings[ CFVSW_GLOBAL ] );
		}

		if ( $this->requires_global_settings() ) {
			$settings = $this->settings[ CFVSW_GLOBAL ];
		}

		$custom_css = '';

		if ( ! empty( $style['tooltip_background'] ) && ! empty( $style['tooltip_font_color'] ) ) {
			$custom_css .= '.cfvsw-tooltip{background:' . $style['tooltip_background'] . ';color:' . $style['tooltip_font_color'] . ';}';
			$custom_css .= ' .cfvsw-tooltip:before{background:' . $style['tooltip_background'] . ';}';
		}

		$custom_css .= ':root {';
		$custom_css .= "--cfvsw-swatches-font-size: {$settings['font_size']}px;";
		$custom_css .= "--cfvsw-swatches-border-color: {$style['border_color']};";
		$custom_css .= "--cfvsw-swatches-border-color-hover: {$style['border_color']}80;";
		$custom_css .= ! empty( $settings['border_width'] ) ? "--cfvsw-swatches-border-width: {$settings['border_width']}px;" : '';
		$custom_css .= ! empty( $style['label_font_size'] ) ? "--cfvsw-swatches-label-font-size: {$style['label_font_size']}px;" : '';
		$custom_css .= "--cfvsw-swatches-tooltip-font-size: {$style['tooltip_font_size']}px;";
		$custom_css .= '}';

		if ( ! empty( $custom_css ) ) {
			wp_add_inline_style( 'cfvsw_swatches_product', $custom_css );
		}
	}

	/**
	 * Class for disable attribute type
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function get_remove_attr_class() {
		$disable_class = '';
		$settings      = [];
		if ( $this->requires_shop_settings() ) {
			$settings = $this->settings[ CFVSW_SHOP ]['override_global'] ? $this->settings[ CFVSW_SHOP ] : $this->settings[ CFVSW_GLOBAL ];
		}
		if ( $this->requires_global_settings() ) {
			$settings = $this->settings[ CFVSW_GLOBAL ];
		}

		switch ( $settings['disable_attr_type'] ) {
			case 'blurCross':
				$disable_class = 'cfvsw-swatches-blur-cross';
				break;

			default:
				$disable_class = 'cfvsw-swatches-' . $settings['disable_attr_type'];
				break;
		}

		return $disable_class;
	}

	/**
	 * Returns the position of swatches on shop page
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_swatches_position() {
		$this->check_theme_compatibility();
		$position = apply_filters(
			'cfvsw_swatches_shop_page_position',
			[
				'before_title' => [
					'action'   => 'woocommerce_shop_loop_item_title',
					'priority' => 0,
				],
				'after_title'  => [
					'action'   => 'woocommerce_shop_loop_item_title',
					'priority' => 9999,
				],
				'before_price' => [
					'action'   => 'woocommerce_after_shop_loop_item_title',
					'priority' => 9,
				],
				'after_price'  => [
					'action'   => 'woocommerce_after_shop_loop_item_title',
					'priority' => 11,
				],
			]
		);
		$key      = ! empty( $this->settings[ CFVSW_SHOP ]['position'] ) ? $this->settings[ CFVSW_SHOP ]['position'] : 'before_title';
		return ! empty( $position[ $key ] ) ? $position[ $key ] : $position['before_title'];
	}

	/**
	 * Enqueues compatibility files only if particular theme is active
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function check_theme_compatibility() {
		$current_theme = wp_get_theme();
		if ( ! empty( $current_theme->template ) && 'astra' === $current_theme->template ) {
			$astra = new Astra();
			$astra->get_shop_positions();
		}
	}

	/**
	 * Adds class to WooCommerce wrapper
	 *
	 * @param array $classes existing classes.
	 * @return array
	 * @since 1.0.0
	 */
	public function label_position_class( $classes ) {
		if ( ! $this->requires_global_settings() ) {
			return $classes;
		}

		$settings = $this->settings[ CFVSW_GLOBAL ];

		if ( $settings['enable_swatches'] && isset( $settings['html_design'] ) ) {
			$classes[] = 'cfvsw-label-' . esc_html( strtolower( $settings['html_design'] ) );
			$classes[] = 'cfvsw-product-page';
			return $classes;
		}

		return $classes;
	}

	/**
	 * Arguments for shop page add to cart button
	 *
	 * @param array  $args array of button arguments.
	 * @param object $product curreent product object.
	 * @return array
	 * @since 1.0.0
	 */
	public function shop_page_add_to_cart_args( $args, $product ) {
		if ( $product->is_type( 'variable' ) ) {
			$args['class']                                 .= ' cfvsw_ajax_add_to_cart';
			$args['attributes']['data-add_to_cart_text']    = esc_html__( 'Add to Cart', 'variation-swatches-woo' );
			$args['attributes']['data-select_options_text'] = apply_filters( 'woocommerce_product_add_to_cart_text', $product->add_to_cart_text(), $product );
		}

		return $args;
	}

	/**
	 * Add to cart functionality for shop page
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function cfvsw_ajax_add_to_cart() {
		check_ajax_referer( 'cfvsw_ajax_add_to_cart', 'security' );

		if ( empty( $_POST['product_id'] ) ) {
			return;
		}

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		$product_title     = get_the_title( $product_id );
		$quantity          = ! empty( $_POST['quantity'] ) ? wc_stock_amount( absint( $_POST['quantity'] ) ) : 1;
		$product_status    = get_post_status( $product_id );
		$variation_id      = ! empty( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
		$variation         = ! empty( $_POST['variation'] ) ? array_map( 'sanitize_text_field', $_POST['variation'] ) : array();
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation );
		$cart_page_url     = wc_get_cart_url();

		if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wc_add_to_cart_message( array( $product_id => $quantity ), true );
			} else {
				$added_to_cart_notice = sprintf(
					/* translators: %s: Product title */
					esc_html__( '"%1$s" has been added to your cart. %2$s', 'variation-swatches-woo' ),
					esc_html( $product_title ),
					'<a href="' . esc_url( $cart_page_url ) . '">' . esc_html__( 'View Cart', 'variation-swatches-woo' ) . '</a>'
				);

				wc_add_notice( $added_to_cart_notice );
			}

			WC_AJAX::get_refreshed_fragments();
		} else {

			// If there was an error adding to the cart, redirect to the product page to show any errors.
			$data = array(
				'error'       => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
			);

			wp_send_json( $data );
		}
	}

	/**
	 * This function returns true if current page is compatible for variation swatches
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function is_required_page() {
		return apply_filters(
			'cfvsw_is_required_page',
			$this->requires_global_settings() || $this->requires_shop_settings()
		);
	}

	/**
	 * This function returns true if current page is product type page
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function requires_global_settings() {
		return apply_filters(
			'cfvsw_requires_global_settings',
			is_product()
		);
	}

	/**
	 * This function returns true if current page is shop / archieve type page
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function requires_shop_settings() {
		return apply_filters(
			'cfvsw_requires_shop_settings',
			is_shop() || is_product_category() || is_product_taxonomy()
		);
	}

	/**
	 * Generates swatches html for filters
	 *
	 * @param string  $term_html default html.
	 * @param object  $term current term object.
	 * @param string  $link filter link.
	 * @param integer $count total product associated with term count.
	 * @return string
	 * @since 1.0.0
	 */
	public function filters_html( $term_html, $term, $link, $count ) {
		if ( empty( $this->settings[ CFVSW_STYLE ]['filters'] ) ) {
			return $term_html;
		}
		$type            = $this->helper->get_attr_type_by_name( $term->taxonomy );
		$settings        = [];
		$container_class = '';

		if ( ! $this->is_required_page() ) {
			return $term_html;
		}
		if ( $this->requires_shop_settings() ) {
			if ( ! $this->settings[ CFVSW_GLOBAL ]['enable_swatches_shop'] ) {
				return $term_html;
			}
			$settings = $this->settings[ CFVSW_SHOP ]['override_global'] ? $this->settings[ CFVSW_SHOP ] : array_merge( $this->settings[ CFVSW_SHOP ], $this->settings[ CFVSW_GLOBAL ] );
			if ( ! isset( $settings['tooltip'] ) ) {
				$settings['tooltip'] = $this->settings[ CFVSW_GLOBAL ]['tooltip'];
			}
			$settings['auto_convert'] = true;
		}

		if ( $this->requires_global_settings() ) {
			if ( ! $this->settings[ CFVSW_GLOBAL ]['enable_swatches'] ) {
				return $term_html;
			}
			$settings = $this->settings[ CFVSW_GLOBAL ];
		}

		if ( empty( $settings ) ) {
			return $term_html;
		}

		$attr_id       = $term->term_id;
		$shape         = get_option( "cfvsw_product_attribute_shape-$attr_id", 'default' );
		$size          = absint( get_option( "cfvsw_product_attribute_size-$attr_id", '' ) );
		$height        = absint( get_option( "cfvsw_product_attribute_height-$attr_id", '' ) );
		$width         = absint( get_option( "cfvsw_product_attribute_width-$attr_id", '' ) );
		$min_width     = ! empty( $settings['min_width'] ) ? $settings['min_width'] . 'px' : '24px';
		$min_height    = ! empty( $settings['min_height'] ) ? $settings['min_height'] . 'px' : '24px';
		$border_radius = ! empty( $settings['border_radius'] ) ? $settings['border_radius'] . 'px' : '0';
		switch ( $shape ) {
			case 'circle':
				$min_width     = $size ? $size . 'px' : '24px';
				$min_height    = $size ? $size . 'px' : '24px';
				$border_radius = '100%';
				break;
			case 'square':
				$min_width     = $size ? $size . 'px' : '24px';
				$min_height    = $size ? $size . 'px' : '24px';
				$border_radius = '0px';
				break;
			case 'rounded':
				$min_width     = $size ? $size . 'px' : '24px';
				$min_height    = $size ? $size . 'px' : '24px';
				$border_radius = '3px';
				break;
			case 'custom':
				$min_width     = $width ? $width . 'px' : '24px';
				$min_height    = $height ? $height . 'px' : '24px';
				$border_radius = '0px';
				break;
			default:
				break;
		}

		$type = $this->helper->get_attr_type_by_name( $term->taxonomy );
		switch ( $type ) {
			case 'color':
				$html    = "<div class='cfvsw-swatches-container " . esc_attr( $container_class ) . "'>";
				$color   = get_term_meta( $term->term_id, 'cfvsw_color', true );
				$tooltip = $settings['tooltip'] ? $term->name : '';
				$style   = '';
				$style  .= 'min-width:' . $min_width . ';';
				$style  .= 'min-height:' . $min_height . ';';
				$style  .= 'border-radius:' . $border_radius . ';';
				$style  .= 'background-color:' . $color . ';';
				$html   .= "<div class='cfvsw-swatches-option' data-slug='" . esc_attr( $term->slug ) . "' data-title='" . esc_attr( $term->name ) . "' data-tooltip='" . esc_attr( $tooltip ) . "' style=" . esc_attr( $style ) . '></div>';
				$html   .= '</div>';
				break;
			case 'image':
				$html    = "<div class='cfvsw-swatches-container " . esc_attr( $container_class ) . "'>";
				$image   = get_term_meta( $term->term_id, 'cfvsw_image', true );
				$tooltip = $settings['tooltip'] ? $term->name : '';
				$style   = '';
				$style  .= 'min-width:' . $min_width . ';';
				$style  .= 'min-height:' . $min_height . ';';
				$style  .= 'border-radius:' . $border_radius . ';';
				$style  .= "background-image:url('" . $image . "');background-size:cover;";
				$html   .= "<div class='cfvsw-swatches-option cfvsw-image-option' data-slug='" . esc_attr( $term->slug ) . "' data-title='" . esc_attr( $term->name ) . "' data-tooltip='" . esc_attr( $tooltip ) . "' style=" . esc_attr( $style ) . '>';
				$html   .= '</div>';
				$html   .= '</div>';
				break;
			default:
				if ( 'label' !== $type && ! $settings['auto_convert'] ) {
					break;
				}
				$html   = "<div class='cfvsw-swatches-container " . esc_attr( $container_class ) . "'>";
				$style  = '';
				$style .= 'min-width:' . $min_width . ';';
				$style .= 'min-height:' . $min_height . ';';
				$style .= 'border-radius:' . $border_radius . ';';
				$name   = ! empty( $term->name ) ? $term->name : $term->slug;
				$html  .= "<div class='cfvsw-swatches-option cfvsw-label-option' data-slug='" . esc_attr( $term->slug ) . "' data-title='" . esc_attr( $name ) . "' style=" . esc_attr( $style ) . '>' . esc_html( $name ) . '</div>';
				$html  .= '</div>';
				break;
		}

		if ( ! empty( $html ) ) {
			return '<a ref="nofollow" style="border-radius:' . esc_attr( $border_radius ) . '" href=' . esc_url( $link ) . '>' . $html . '</a>';
		}
		return $term_html;
	}

	/**
	 * Rewrite woocommerce threshold.
	 *
	 * @since 1.0.4
	 * @return int
	 */
	public function cfvsw_ajax_variation_threshold() {
		return 200;
	}
}
