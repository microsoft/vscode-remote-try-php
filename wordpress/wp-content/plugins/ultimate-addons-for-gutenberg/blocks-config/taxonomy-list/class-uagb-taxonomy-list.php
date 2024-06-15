<?php
/**
 * UAGB - Taxonomy-List
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UAGB_Taxonomy_List' ) ) {

	/**
	 * Class UAGB_Taxonomy_List.
	 *
	 * @since 1.18.1
	 */
	class UAGB_Taxonomy_List {

		/**
		 * Member Variable
		 *
		 * @since 1.18.1
		 * @var instance
		 */
		private static $instance;

		/**
		 *  Initiator
		 *
		 * @since 1.18.1
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
		 * @since 1.18.1
		 */
		public function __construct() {

			// Activation hook.
			add_action( 'init', array( $this, 'register_blocks' ) );
		}

		/**
		 * Registers the `uagb/taxonomy-list` block on server.
		 *
		 * @since 1.18.1
		 */
		public function register_blocks() {

			// Check if the register function exists.
			if ( ! function_exists( 'register_block_type' ) ) {
				return;
			}
			$border_attribute = array();

			if ( method_exists( 'UAGB_Block_Helper', 'uag_generate_php_border_attribute' ) ) {

				$border_attribute = UAGB_Block_Helper::uag_generate_php_border_attribute( 'overall' );

			}

			register_block_type(
				'uagb/taxonomy-list',
				array(
					'attributes'      => array_merge(
						$border_attribute,
						array(
							'block_id'                   => array(
								'type' => 'string',
							),
							'postType'                   => array(
								'type'    => 'string',
								'default' => 'post',
							),
							'taxonomyType'               => array(
								'type'    => 'string',
								'default' => 'category',
							),
							'categories'                 => array(
								'type' => 'string',
							),
							'order'                      => array(
								'type'    => 'string',
								'default' => 'desc',
							),
							'orderBy'                    => array(
								'type'    => 'string',
								'default' => 'date',
							),
							'postsToShow'                => array(
								'type'    => 'number',
								'default' => '8',
							),
							'layout'                     => array(
								'type'    => 'string',
								'default' => 'grid',
							),
							'columns'                    => array(
								'type'    => 'number',
								'default' => 3,
							),
							'tcolumns'                   => array(
								'type'    => 'number',
								'default' => 2,
							),
							'mcolumns'                   => array(
								'type'    => 'number',
								'default' => 1,
							),
							'noTaxDisplaytext'           => array(
								'type'    => 'string',
								'default' => __( 'Taxonomy Not Available.', 'ultimate-addons-for-gutenberg' ),
							),
							'boxShadowColor'             => array(
								'type'    => 'string',
								'default' => '#00000070',
							),
							'boxShadowHOffset'           => array(
								'type'    => 'number',
								'default' => 0,
							),
							'boxShadowVOffset'           => array(
								'type'    => 'number',
								'default' => 0,
							),
							'boxShadowBlur'              => array(
								'type' => 'number',
							),
							'boxShadowSpread'            => array(
								'type' => 'number',
							),
							'boxShadowPosition'          => array(
								'type'    => 'string',
								'default' => 'outset',
							),
							'showCount'                  => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'showEmptyTaxonomy'          => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'showhierarchy'              => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'titleTag'                   => array(
								'type'    => 'string',
								'default' => '',
							),
							// Color Attributes.
							'bgColor'                    => array(
								'type'    => 'string',
								'default' => '#f5f5f5',
							),
							'titleColor'                 => array(
								'type'    => 'string',
								'default' => '#3b3b3b',
							),
							'countColor'                 => array(
								'type'    => 'string',
								'default' => '#777777',
							),
							'listTextColor'              => array(
								'type'    => 'string',
								'default' => '#3b3b3b',
							),
							'hoverlistTextColor'         => array(
								'type'    => 'string',
								'default' => '#3b3b3b',
							),
							'listStyleColor'             => array(
								'type'    => 'string',
								'default' => '#3b3b3b',
							),
							'hoverlistStyleColor'        => array(
								'type'    => 'string',
								'default' => '#3b3b3b',
							),

							// Spacing Attributes.
							'rowGap'                     => array(
								'type'    => 'number',
								'default' => 20,
							),
							'columnGap'                  => array(
								'type'    => 'number',
								'default' => 20,
							),
							'contentPadding'             => array(
								'type'    => 'number',
								'default' => 20,
							),
							'contentPaddingTablet'       => array(
								'type'    => 'number',
								'default' => 15,
							),
							'contentPaddingMobile'       => array(
								'type'    => 'number',
								'default' => 15,
							),
							'titleBottomSpace'           => array(
								'type'    => 'number',
								'default' => 5,
							),
							'listBottomMargin'           => array(
								'type'    => 'number',
								'default' => 10,
							),

							// ALignment Attributes.
							'alignment'                  => array(
								'type'    => 'string',
								'default' => 'center',
							),

							// List Attributes.
							'listStyle'                  => array(
								'type'    => 'string',
								'default' => 'disc',
							),
							'listDisplayStyle'           => array(
								'type'    => 'string',
								'default' => 'list',
							),

							// Seperator Attributes.
							'seperatorStyle'             => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'seperatorWidth'             => array(
								'type'    => 'number',
								'default' => 100,
							),
							'seperatorThickness'         => array(
								'type'    => 'number',
								'default' => 1,
							),
							'seperatorColor'             => array(
								'type'    => 'string',
								'default' => '#b2b4b5',
							),
							'seperatorHoverColor'        => array(
								'type'    => 'string',
								'default' => '#b2b4b5',
							),

							// Typograpghy attributes.
							'titleFontSize'              => array(
								'type' => 'number',
							),
							'titleFontSizeType'          => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'titleFontSizeMobile'        => array(
								'type' => 'number',
							),
							'titleFontSizeTablet'        => array(
								'type' => 'number',
							),
							'titleFontFamily'            => array(
								'type'    => 'string',
								'default' => 'Default',
							),
							'titleFontWeight'            => array(
								'type' => 'string',
							),
							'titleFontStyle'             => array(
								'type' => 'string',
							),
							'titleLineHeightType'        => array(
								'type'    => 'string',
								'default' => 'em',
							),
							'titleLineHeight'            => array(
								'type' => 'number',
							),
							'titleLineHeightTablet'      => array(
								'type' => 'number',
							),
							'titleLineHeightMobile'      => array(
								'type' => 'number',
							),
							'titleLoadGoogleFonts'       => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'countFontSize'              => array(
								'type' => 'number',
							),
							'countFontSizeType'          => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'countFontSizeMobile'        => array(
								'type' => 'number',
							),
							'countFontSizeTablet'        => array(
								'type' => 'number',
							),
							'countFontFamily'            => array(
								'type'    => 'string',
								'default' => 'Default',
							),
							'countFontWeight'            => array(
								'type' => 'string',
							),
							'countFontStyle'             => array(
								'type' => 'string',
							),
							'countLineHeightType'        => array(
								'type'    => 'string',
								'default' => 'em',
							),
							'countLineHeight'            => array(
								'type' => 'number',
							),
							'countLineHeightTablet'      => array(
								'type' => 'number',
							),
							'countLineHeightMobile'      => array(
								'type' => 'number',
							),
							'countLoadGoogleFonts'       => array(
								'type'    => 'boolean',
								'default' => false,
							),

							'listFontSize'               => array(
								'type' => 'number',
							),
							'listFontSizeType'           => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'listFontSizeMobile'         => array(
								'type' => 'number',
							),
							'listFontSizeTablet'         => array(
								'type' => 'number',
							),
							'listFontFamily'             => array(
								'type'    => 'string',
								'default' => 'Default',
							),
							'listFontWeight'             => array(
								'type' => 'string',
							),
							'listFontStyle'              => array(
								'type' => 'string',
							),
							'listLineHeightType'         => array(
								'type'    => 'string',
								'default' => 'em',
							),
							'listLineHeight'             => array(
								'type' => 'number',
							),
							'listLineHeightTablet'       => array(
								'type' => 'number',
							),
							'listLineHeightMobile'       => array(
								'type' => 'number',
							),
							'listLoadGoogleFonts'        => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'contentLeftPadding'         => array(
								'type' => 'number',
							),
							'contentRightPadding'        => array(
								'type' => 'number',
							),
							'contentTopPadding'          => array(
								'type' => 'number',
							),
							'contentBottomPadding'       => array(
								'type' => 'number',
							),
							'contentLeftPaddingTablet'   => array(
								'type' => 'number',
							),
							'contentRightPaddingTablet'  => array(
								'type' => 'number',
							),
							'contentTopPaddingTablet'    => array(
								'type' => 'number',
							),
							'contentBottomPaddingTablet' => array(
								'type' => 'number',
							),
							'contentLeftPaddingMobile'   => array(
								'type' => 'number',
							),
							'contentRightPaddingMobile'  => array(
								'type' => 'number',
							),
							'contentTopPaddingMobile'    => array(
								'type' => 'number',
							),
							'contentBottomPaddingMobile' => array(
								'type' => 'number',
							),
							'contentPaddingUnit'         => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'mobileContentPaddingUnit'   => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'tabletContentPaddingUnit'   => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'contentPaddingLink'         => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'titleTransform'             => array(
								'type' => 'string',
							),
							'countTransform'             => array(
								'type' => 'string',
							),
							'listTransform'              => array(
								'type' => 'string',
							),
							'titleDecoration'            => array(
								'type' => 'string',
							),
							'countDecoration'            => array(
								'type' => 'string',
							),
							'listDecoration'             => array(
								'type' => 'string',
							),
							'isPreview'                  => array(
								'type'    => 'boolean',
								'default' => false,
							),
							// letter spacing.
							'titleLetterSpacing'         => array(
								'type'    => 'number',
								'default' => 0,
							),
							'titleLetterSpacingTablet'   => array(
								'type' => 'number',
							),
							'titleLetterSpacingMobile'   => array(
								'type' => 'number',
							),
							'titleLetterSpacingType'     => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'countLetterSpacing'         => array(
								'type' => 'number',
							),
							'countLetterSpacingTablet'   => array(
								'type' => 'number',
							),
							'countLetterSpacingMobile'   => array(
								'type' => 'number',
							),
							'countLetterSpacingType'     => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'listLetterSpacing'          => array(
								'type' => 'number',
							),
							'listLetterSpacingTablet'    => array(
								'type' => 'number',
							),
							'listLetterSpacingMobile'    => array(
								'type' => 'number',
							),
							'listLetterSpacingType'      => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'borderColor'                => array(
								'type'    => 'string',
								'default' => '#E0E0E0',
							),
							'borderThickness'            => array(
								'type'    => 'number',
								'default' => 1,
							),
							'borderRadius'               => array(
								'type'    => 'number',
								'default' => 3,
							),
							'borderStyle'                => array(
								'type'    => 'string',
								'default' => 'solid',
							),
							'borderHoverColor'           => array(
								'type'    => 'string',
								'default' => '#E0E0E0',
							),
						)
					),
					'render_callback' => array( $this, 'render_html' ),
				)
			);
		}

		/**
		 * Render Grid HTML.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 1.18.1
		 */
		public function grid_html( $attributes ) {
			$block_id         = $attributes['block_id'];
			$postType         = $attributes['postType'];
			$taxonomyType     = $attributes['taxonomyType'];
			$layout           = $attributes['layout'];
			$seperatorStyle   = $attributes['seperatorStyle'];
			$noTaxDisplaytext = $attributes['noTaxDisplaytext'];
			$showCount        = $attributes['showCount'];
			$titleTag         = $attributes['titleTag'];

			if ( 'grid' === $layout ) {

				$array_of_allowed_HTML = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div' );
				$title_tag             = UAGB_Helper::title_tag_allowed_html( $titleTag, $array_of_allowed_HTML, 'h4' );

				$pt            = get_post_type_object( $postType );
				$singular_name = $pt->labels->singular_name;

				$args                = array(
					'hide_empty' => ! $attributes['showEmptyTaxonomy'],
					'parent'     => 0,
				);
				$new_categories_list = get_terms( $attributes['taxonomyType'], $args );

				if ( is_array( $new_categories_list ) ) {
					foreach ( $new_categories_list as $value ) {
						$link = get_term_link( $value->slug, $attributes['taxonomyType'] );
						if ( ! is_wp_error( $link ) ) {
							?>

						<div class="uagb-taxomony-box">
							<a class="uagb-tax-link" href= "<?php echo esc_url( $link ); ?>">
								<<?php echo esc_html( $title_tag ); ?> class="uagb-tax-title"><?php echo esc_html( $value->name ); ?>
								</<?php echo esc_html( $title_tag ); ?>>
								<?php if ( $showCount ) { ?>
										<?php echo esc_attr( $value->count ); ?>
										<?php $countName = ( $value->count > 1 ) ? esc_attr( $singular_name ) . 's' : esc_attr( $singular_name ); ?>
										<?php echo esc_attr( apply_filters( 'uagb_taxonomy_count_text', $countName, $value->count ) ); ?>
								<?php } ?>
							</a>
						</div>
							<?php
						}
					}
				}
			}
		}
		/**
		 * Return link for individual category.
		 *
		 * @param string $slug of category.
		 * @param string $taxonomy_type attributes.
		 *
		 * @since 2.6.0
		 * @return string link using slug.
		 */
		public function get_link_of_individual_categories( $slug, $taxonomy_type ) {
			if ( ! is_string( $slug ) ) {
				return '#';
			}
			$link = get_term_link( $slug, $taxonomy_type );
			if ( is_wp_error( $link ) ) {
				$link = '#';
			}
			return $link;
		}

		/**
		 * Get terms hierarchical.
		 *
		 * @param string  $taxonomy_type of taxonomy type.
		 * @param integer $parent_id of parent id.
		 * @param bool    $show_empty_taxonomy of show empty taxonomy.
		 * @since 2.10.4
		 * @return array of terms.
		 */
		public function get_terms_hierarchical( $taxonomy_type, $parent_id, $show_empty_taxonomy ) {
			$args = array(
				'taxonomy'   => $taxonomy_type,
				'parent'     => $parent_id,
				'hide_empty' => ! $show_empty_taxonomy,
			);

			$terms = get_terms( $args );

			if ( is_wp_error( $terms ) || empty( $terms ) || ! is_array( $terms ) ) {
				return array();
			}

			foreach ( $terms as $term ) {
				$term->children = $this->get_terms_hierarchical( $taxonomy_type, $term->term_id, $show_empty_taxonomy );
			}

			return $terms;
		}

		/**
		 * Display terms recursive.
		 *
		 * @param object $term of terms.
		 * @param string $taxonomy_type of taxonomy type.
		 * @param bool   $showCount of show count.
		 * @param string $seperatorStyle of separator style.
		 * @param string $title_tag of title tag.
		 * @param bool   $show_hierarchy of show hierarchy.
		 * @since 2.10.4
		 * @return void
		 */
		public function display_terms_recursive( $term, $taxonomy_type, $showCount, $seperatorStyle, $title_tag, $show_hierarchy ) {
			if ( $term instanceof WP_Term ) {
				$child_link = $this->get_link_of_individual_categories( $term->slug, $taxonomy_type );
				echo sprintf(
					'<li class="uagb-tax-list"><%s class="uagb-tax-link-wrap"><a class="uagb-tax-link" href="%s">%s</a> %s</%s><div class="uagb-tax-separator"></div></li>',
					esc_html( $title_tag ),
					esc_url( $child_link ),
					esc_html( $term->name ),
					( boolval( $showCount ) ? ' (' . esc_attr( $term->count ) . ') ' : '' ),
					esc_html( $title_tag )
				);

				if ( $show_hierarchy && ! empty( $term->children ) && is_array( $term->children ) ) {
					foreach ( $term->children as $child ) {
						echo sprintf( '<ul class="uagb-taxonomy-list-children">' );
						$this->display_terms_recursive( $child, $taxonomy_type, $showCount, $seperatorStyle, $title_tag, $show_hierarchy );
						echo sprintf( '</ul>' );
					}
				}
			}
		}

		/**
		 * Render List HTML.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 1.18.1
		 */
		public function list_html( $attributes ) {
			$block_id         = $attributes['block_id'];
			$postType         = $attributes['postType'];
			$taxonomyType     = $attributes['taxonomyType'];
			$layout           = $attributes['layout'];
			$seperatorStyle   = $attributes['seperatorStyle'];
			$noTaxDisplaytext = $attributes['noTaxDisplaytext'];
			$showCount        = $attributes['showCount'];
			$titleTag         = $attributes['titleTag'];

			if ( 'list' === $layout ) {

				$array_of_allowed_HTML = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div' );
				$title_tag             = UAGB_Helper::title_tag_allowed_html( $titleTag, $array_of_allowed_HTML, 'div' );

				$pt            = get_post_type_object( $postType );
				$singular_name = $pt->labels->singular_name;

				$args = array(
					'hide_empty' => ! $attributes['showEmptyTaxonomy'],
					'parent'     => 0,
				);

				$taxonomy_type       = $attributes['taxonomyType'];
				$show_empty_taxonomy = $attributes['showEmptyTaxonomy'];
				$new_categories_list = $this->get_terms_hierarchical( $taxonomy_type, 0, $show_empty_taxonomy );
				?>
				<?php if ( 'dropdown' !== $attributes['listDisplayStyle'] && ! empty( $new_categories_list ) && is_array( $new_categories_list ) ) { ?>
					<ul class="uagb-list-wrap">
						<?php
						foreach ( $new_categories_list as $term ) {
							?>
							<?php
							if ( $term instanceof WP_Term ) {
								$this->display_terms_recursive( $term, $taxonomy_type, $showCount, $seperatorStyle, $title_tag, $attributes['showhierarchy'] );
							}
							?>
						<?php } ?>
						</ul>
				<?php } else { ?>
					<select class="uagb-list-dropdown-wrap" onchange="redirectToTaxonomyLink(this)">
						<option selected value=""> -- <?php esc_html_e( 'Select', 'ultimate-addons-for-gutenberg' ); ?> -- </option>
						<?php
						if ( is_array( $new_categories_list ) ) {
							foreach ( $new_categories_list as $key => $value ) {
								$link = $this->get_link_of_individual_categories( $value->slug, $attributes['taxonomyType'] );
								?>
							<option value="<?php echo esc_url( $link ); ?>" >
								<?php echo esc_attr( $value->name ); ?>
								<?php if ( $showCount ) { ?>
									<?php echo ' (' . esc_attr( $value->count ) . ')'; ?>
								<?php } ?>
							</option>
								<?php
							}
						}
						?>
					</select>
					<script type="text/javascript">
						function redirectToTaxonomyLink( selectedOption ) {
							var selectedValue = selectedOption.value;
							if ( selectedValue ) {
								location.href = selectedValue;
							}
						}
					</script>
				<?php } ?>
				<?php
			}
		}

		/**
		 * Render Taxonomy List HTML.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 1.18.1
		 */
		public function render_html( $attributes ) {

			$block_id         = $attributes['block_id'];
			$postType         = $attributes['postType'];
			$taxonomyType     = $attributes['taxonomyType'];
			$layout           = $attributes['layout'];
			$seperatorStyle   = $attributes['seperatorStyle'];
			$noTaxDisplaytext = $attributes['noTaxDisplaytext'];
			$showCount        = $attributes['showCount'];

			$desktop_class = '';
			$tab_class     = '';
			$mob_class     = '';

			if ( array_key_exists( 'UAGHideDesktop', $attributes ) || array_key_exists( 'UAGHideTab', $attributes ) || array_key_exists( 'UAGHideMob', $attributes ) ) {

				$desktop_class = ( isset( $attributes['UAGHideDesktop'] ) ) ? 'uag-hide-desktop' : '';

				$tab_class = ( isset( $attributes['UAGHideTab'] ) ) ? 'uag-hide-tab' : '';

				$mob_class = ( isset( $attributes['UAGHideMob'] ) ) ? 'uag-hide-mob' : '';
			}

			$zindex_desktop           = '';
			$zindex_tablet            = '';
			$zindex_mobile            = '';
			$zindex_wrap              = array();
			$zindex_extention_enabled = ( isset( $attributes['zIndex'] ) || isset( $attributes['zIndexTablet'] ) || isset( $attributes['zIndexMobile'] ) );

			if ( $zindex_extention_enabled ) {
				$zindex_desktop = ( isset( $attributes['zIndex'] ) ) ? '--z-index-desktop:' . $attributes['zIndex'] . ';' : false;
				$zindex_tablet  = ( isset( $attributes['zIndexTablet'] ) ) ? '--z-index-tablet:' . $attributes['zIndexTablet'] . ';' : false;
				$zindex_mobile  = ( isset( $attributes['zIndexMobile'] ) ) ? '--z-index-mobile:' . $attributes['zIndexMobile'] . ';' : false;

				if ( $zindex_desktop ) {
					array_push( $zindex_wrap, $zindex_desktop );
				}

				if ( $zindex_tablet ) {
					array_push( $zindex_wrap, $zindex_tablet );
				}

				if ( $zindex_mobile ) {
					array_push( $zindex_wrap, $zindex_mobile );
				}
			}

			$main_classes = array(
				'wp-block-uagb-taxonomy-list',
				'uagb-taxonomy__outer-wrap',
				'uagb-layout-' . $layout,
				'uagb-block-' . $block_id,
				$desktop_class,
				$tab_class,
				$mob_class,
				$zindex_extention_enabled ? 'uag-blocks-common-selector' : '',
			);

			$args = array(
				'hide_empty' => ! $attributes['showEmptyTaxonomy'],
			);

			if ( $taxonomyType ) {
				$new_categories_list = get_terms( $taxonomyType, $args );
			}

			ob_start();

			?>
				<div class = "<?php echo esc_attr( implode( ' ', $main_classes ) ); ?>" style="<?php echo esc_attr( implode( '', $zindex_wrap ) ); ?>">
					<?php if ( ! empty( $new_categories_list ) ) { ?>
							<?php $this->grid_html( $attributes ); ?>
							<?php $this->list_html( $attributes ); ?>
					<?php } else { ?>
							<div class="uagb-tax-not-available"><?php echo esc_html( $noTaxDisplaytext ); ?></div>
					<?php } ?>
				</div>

			<?php

			return ob_get_clean();
		}
	}

	/**
	 *  Prepare if class 'UAGB_Taxonomy_List' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	UAGB_Taxonomy_List::get_instance();
}
