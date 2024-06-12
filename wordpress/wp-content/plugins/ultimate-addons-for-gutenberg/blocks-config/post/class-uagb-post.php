<?php
/**
 * UAGB Post.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UAGB_Post' ) ) {

	/**
	 * Class UAGB_Post.
	 */
	class UAGB_Post {


		/**
		 * Member Variable
		 *
		 * @since 1.18.1
		 * @var instance
		 */
		private static $instance;

		/**
		 * Member Variable
		 *
		 * @since 1.18.1
		 * @var settings
		 */
		private static $settings;

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
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_blocks' ) );
			add_action( 'wp_ajax_uagb_post_pagination', array( $this, 'post_pagination' ) );
			add_action( 'wp_ajax_nopriv_uagb_post_pagination', array( $this, 'post_pagination' ) );
			add_action( 'wp_ajax_uagb_post_pagination_grid', array( $this, 'post_grid_pagination_ajax_callback' ) );
			add_action( 'wp_ajax_nopriv_uagb_post_pagination_grid', array( $this, 'post_grid_pagination_ajax_callback' ) );
			add_action( 'wp_ajax_uagb_get_posts', array( $this, 'masonry_pagination' ) );
			add_action( 'wp_ajax_nopriv_uagb_get_posts', array( $this, 'masonry_pagination' ) );
			add_action( 'wp_footer', array( $this, 'add_post_dynamic_script' ), 1000 );
			add_filter( 'redirect_canonical', array( $this, 'override_canonical' ), 1, 2 );
		}

		/**
		 * Registers the `core/latest-posts` block on server.
		 *
		 * @since 0.0.1
		 */
		public function register_blocks() {
			// Check if the register function exists.
			if ( ! function_exists( 'register_block_type' ) ) {
				return;
			}

			$pagination_masonry_border_attribute = array();

			if ( method_exists( 'UAGB_Block_Helper', 'uag_generate_php_border_attribute' ) ) {

				$pagination_masonry_border_attribute = UAGB_Block_Helper::uag_generate_php_border_attribute( 'paginationMasonry' );

			}

			$common_attributes = $this->get_post_attributes();

			register_block_type(
				'uagb/post-grid',
				array(
					'attributes'      => array_merge(
						$common_attributes,
						array(
							'blockName'                   => array(
								'type'    => 'string',
								'default' => 'post-grid',
							),
							'equalHeight'                 => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'postPagination'              => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'pageLimit'                   => array(
								'type'    => 'number',
								'default' => 10,
							),
							'paginationBgActiveColor'     => array(
								'type'    => 'string',
								'default' => '#e4e4e4',
							),
							'paginationActiveColor'       => array(
								'type'    => 'string',
								'default' => '#333333',
							),
							'paginationBgColor'           => array(
								'type'    => 'string',
								'default' => '#e4e4e4',
							),
							'paginationColor'             => array(
								'type'    => 'string',
								'default' => '#777777',
							),
							'paginationMarkup'            => array(
								'type'    => 'string',
								'default' => '',
							),
							'paginationLayout'            => array(
								'type'    => 'string',
								'default' => 'filled',
							),
							'paginationBorderActiveColor' => array(
								'type' => 'string',
							),
							'paginationBorderColor'       => array(
								'type'    => 'string',
								'default' => '#888686',
							),
							'paginationBorderRadius'      => array(
								'type' => 'number',
							),
							'paginationBorderSize'        => array(
								'type'    => 'number',
								'default' => 1,
							),
							'paginationSpacing'           => array(
								'type'    => 'number',
								'default' => 20,
							),
							'paginationAlignment'         => array(
								'type'    => 'string',
								'default' => 'left',
							),
							'paginationPrevText'          => array(
								'type'    => 'string',
								'default' => '« Previous',
							),
							'paginationNextText'          => array(
								'type'    => 'string',
								'default' => 'Next »',
							),
							'layoutConfig'                => array(
								'type'    => 'array',
								'default' => array(
									array( 'uagb/post-image' ),
									array( 'uagb/post-taxonomy' ),
									array( 'uagb/post-title' ),
									array( 'uagb/post-meta' ),
									array( 'uagb/post-excerpt' ),
									array( 'uagb/post-button' ),
								),
							),
							'post_type'                   => array(
								'type'    => 'string',
								'default' => 'grid',
							),
							'equalHeightInlineButtons'    => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'imageRatio'                  => array(
								'type'    => 'string',
								'default' => 'inherit',
							),
							'imgEqualHeight'              => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'paginationType'              => array(
								'type'    => 'string',
								'default' => 'ajax',
							),
							'isLeftToRightLayout'         => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'wrapperTopPadding'           => array(
								'type'    => 'number',
								'default' => '',
							),
							'wrapperRightPadding'         => array(
								'type'    => 'number',
								'default' => '',
							),
							'wrapperLeftPadding'          => array(
								'type'    => 'number',
								'default' => '',
							),
							'wrapperBottomPadding'        => array(
								'type'    => 'number',
								'default' => '',
							),
							'wrapperTopPaddingTablet'     => array(
								'type'    => 'number',
								'default' => '',
							),
							'wrapperRightPaddingTablet'   => array(
								'type'    => 'number',
								'default' => '',
							),
							'wrapperLeftPaddingTablet'    => array(
								'type'    => 'number',
								'default' => '',
							),
							'wrapperBottomPaddingTablet'  => array(
								'type'    => 'number',
								'default' => '',
							),
							'wrapperTopPaddingMobile'     => array(
								'type'    => 'number',
								'default' => '',
							),
							'wrapperRightPaddingMobile'   => array(
								'type'    => 'number',
								'default' => '',
							),
							'wrapperLeftPaddingMobile'    => array(
								'type'    => 'number',
								'default' => '',
							),
							'wrapperBottomPaddingMobile'  => array(
								'type'    => 'number',
								'default' => '',
							),
							'wrapperPaddingUnit'          => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'wrapperPaddingUnitTablet'    => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'wrapperPaddingUnitMobile'    => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'wrapperPaddingLink'          => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'wrapperAlign'                => array(
								'type'    => 'string',
								'default' => 'row',
							),
							'wrapperAlignPosition'        => array(
								'type'    => 'string',
								'default' => 'center',
							),
						)
					),
					'render_callback' => array( $this, 'post_grid_callback' ),
				)
			);

			register_block_type(
				'uagb/post-carousel',
				array(
					'attributes'      => array_merge(
						$common_attributes,
						array(
							'blockName'           => array(
								'type'    => 'string',
								'default' => 'post-carousel',
							),
							'pauseOnHover'        => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'infiniteLoop'        => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'transitionSpeed'     => array(
								'type'    => 'number',
								'default' => 500,
							),
							'arrowDots'           => array(
								'type'    => 'string',
								'default' => 'arrows_dots',
							),
							'autoplay'            => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'autoplaySpeed'       => array(
								'type'    => 'number',
								'default' => 2000,
							),
							'arrowSize'           => array(
								'type'    => 'number',
								'default' => 24,
							),
							'arrowBorderSize'     => array(
								'type'    => 'number',
								'default' => 0,
							),
							'arrowBorderRadius'   => array(
								'type'    => 'number',
								'default' => 0,
							),
							'arrowColor'          => array(
								'type'    => 'string',
								'default' => '#000',
							),
							'arrowDistance'       => array(
								'type' => 'number',
							),
							'arrowDistanceTablet' => array(
								'type' => 'number',
							),
							'arrowDistanceMobile' => array(
								'type' => 'number',
							),
							'equalHeight'         => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'layoutConfig'        => array(
								'type'    => 'array',
								'default' => array(
									array( 'uagb/post-image' ),
									array( 'uagb/post-taxonomy' ),
									array( 'uagb/post-title' ),
									array( 'uagb/post-meta' ),
									array( 'uagb/post-excerpt' ),
									array( 'uagb/post-button' ),
								),
							),
							'post_type'           => array(
								'type'    => 'string',
								'default' => 'carousel',
							),
							'dotsMarginTop'       => array(
								'type'    => 'number',
								'default' => '20',
							),
							'dotsMarginTopTablet' => array(
								'type'    => 'number',
								'default' => '20',
							),
							'dotsMarginTopMobile' => array(
								'type'    => 'number',
								'default' => '20',
							),
							'dotsMarginTopUnit'   => array(
								'type'    => 'string',
								'default' => 'px',
							),
						)
					),
					'render_callback' => array( $this, 'post_carousel_callback' ),
				)
			);

			$enable_legacy_blocks = UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_legacy_blocks', ( 'yes' === get_option( 'uagb-old-user-less-than-2' ) ) ? 'yes' : 'no' );

			if ( 'yes' === get_option( 'uagb-old-user-less-than-2' ) || 'yes' === $enable_legacy_blocks ) {
				register_block_type(
					'uagb/post-masonry',
					array(
						'attributes'      => array_merge(
							$common_attributes,
							array(
								'blockName'                => array(
									'type'    => 'string',
									'default' => 'post-masonry',
								),
								'paginationType'           => array(
									'type'    => 'string',
									'default' => 'none',
								),
								'paginationEventType'      => array(
									'type'    => 'string',
									'default' => 'button',
								),
								'buttonText'               => array(
									'type'    => 'string',
									'default' => 'Load More',
								),
								'paginationAlign'          => array(
									'type'    => 'string',
									'default' => 'center',
								),
								'paginationTextColor'      => array(
									'type'    => 'string',
									'default' => '',
								),
								'paginationMasonryBgColor' => array(
									'type'    => 'string',
									'default' => '',
								),
								'paginationBgHoverColor'   => array(
									'type' => 'string',
								),
								'paginationTextHoverColor' => array(
									'type' => 'string',
								),
								'paginationMasonryBorderHColor' => array(
									'type'    => 'string',
									'default' => '',
								),
								'paginationFontSize'       => array(
									'type'    => 'number',
									'default' => 13,
								),
								'loaderColor'              => array(
									'type'    => 'string',
									'default' => '#0085ba',
								),
								'loaderSize'               => array(
									'type'    => 'number',
									'default' => 18,
								),
								'paginationButtonPaddingType' => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'vpaginationButtonPaddingMobile' => array(
									'type'    => 'number',
									'default' => 8,
								),
								'vpaginationButtonPaddingTablet' => array(
									'type'    => 'number',
									'default' => 8,
								),
								'vpaginationButtonPaddingDesktop' => array(
									'type'    => 'number',
									'default' => 8,
								),
								'hpaginationButtonPaddingMobile' => array(
									'type'    => 'number',
									'default' => 12,
								),
								'hpaginationButtonPaddingTablet' => array(
									'type'    => 'number',
									'default' => 12,
								),
								'hpaginationButtonPaddingDesktop' => array(
									'type'    => 'number',
									'default' => 12,
								),
								'layoutConfig'             => array(
									'type'    => 'array',
									'default' => array(
										array( 'uagb/post-image' ),
										array( 'uagb/post-taxonomy' ),
										array( 'uagb/post-title' ),
										array( 'uagb/post-meta' ),
										array( 'uagb/post-excerpt' ),
										array( 'uagb/post-button' ),
									),
								),
								'post_type'                => array(
									'type'    => 'string',
									'default' => 'masonry',
								),
								'mobilepaginationButtonPaddingType' => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'tabletpaginationButtonPaddingType' => array(
									'type'    => 'string',
									'default' => 'px',
								),
							),
							$pagination_masonry_border_attribute
						),
						'render_callback' => array( $this, 'post_masonry_callback' ),
					)
				);
			}

		}

		/**
		 * Get Post common attributes for all Post Grid, Masonry and Carousel.
		 *
		 * @since 0.0.1
		 */
		public function get_post_attributes() {

			$btn_border_attribute     = array();
			$overall_border_attribute = array();

			if ( method_exists( 'UAGB_Block_Helper', 'uag_generate_php_border_attribute' ) ) {

				$btn_border_attribute     = UAGB_Block_Helper::uag_generate_php_border_attribute( 'btn' );
				$overall_border_attribute = UAGB_Block_Helper::uag_generate_php_border_attribute( 'overall' );

			}

			$inherit_from_theme = 'enabled' === ( 'deleted' !== UAGB_Admin_Helper::get_admin_settings_option( 'uag_btn_inherit_from_theme_fallback', 'deleted' ) ? 'disabled' : UAGB_Admin_Helper::get_admin_settings_option( 'uag_btn_inherit_from_theme', 'disabled' ) );

			return array_merge(
				$btn_border_attribute,
				$overall_border_attribute,
				array(
					'inheritFromTheme'              => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'block_id'                      => array(
						'type'    => 'string',
						'default' => 'not_set',
					),
					'categories'                    => array(
						'type' => 'string',
					),
					'postType'                      => array(
						'type'    => 'string',
						'default' => 'post',
					),
					'postDisplaytext'               => array(
						'type'    => 'string',
						'default' => 'No post found!',
					),
					'taxonomyType'                  => array(
						'type'    => 'string',
						'default' => 'category',
					),
					'postsToShow'                   => array(
						'type'    => 'number',
						'default' => 6,
					),
					'enableOffset'                  => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'postsOffset'                   => array(
						'type'    => 'number',
						'default' => 0,
					),
					'displayPostDate'               => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'displayPostExcerpt'            => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'excerptLength'                 => array(
						'type'    => 'number',
						'default' => 15,
					),
					'displayPostAuthor'             => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'displayPostTitle'              => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'displayPostComment'            => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'displayPostTaxonomy'           => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'hideTaxonomyIcon'              => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'taxStyle'                      => array(
						'type'    => 'string',
						'default' => 'default',
					),
					'displayPostTaxonomyAboveTitle' => array(
						'type'    => 'string',
						'default' => 'withMeta',
					),
					'displayPostImage'              => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'imgSize'                       => array(
						'type'    => 'string',
						'default' => 'large',
					),
					'imgPosition'                   => array(
						'type'    => 'string',
						'default' => 'top',
					),
					'linkBox'                       => array(
						'type' => 'boolean',
					),
					'bgOverlayColor'                => array(
						'type'    => 'string',
						'default' => '#000000',
					),
					'overlayOpacity'                => array(
						'type'    => 'number',
						'default' => '50',
					),
					'displayPostLink'               => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'newTab'                        => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'ctaText'                       => array(
						'type'    => 'string',
						'default' => __( 'Read More', 'ultimate-addons-for-gutenberg' ),
					),
					'inheritFromThemeBtn'           => array(
						'type'    => 'boolean',
						'default' => $inherit_from_theme,
					),
					'buttonType'                    => array(
						'type'    => 'string',
						'default' => 'primary',
					),
					'btnHPadding'                   => array(
						'type'    => 'number',
						'default' => '',
					),
					'btnVPadding'                   => array(
						'type'    => 'number',
						'default' => '',
					),
					'columns'                       => array(
						'type'    => 'number',
						'default' => 3,
					),
					'tcolumns'                      => array(
						'type'    => 'number',
						'default' => 2,
					),
					'mcolumns'                      => array(
						'type'    => 'number',
						'default' => 1,
					),
					'align'                         => array(
						'type'    => 'string',
						'default' => 'left',
					),
					'width'                         => array(
						'type'    => 'string',
						'default' => 'wide',
					),
					'order'                         => array(
						'type'    => 'string',
						'default' => 'desc',
					),
					'orderBy'                       => array(
						'type'    => 'string',
						'default' => 'date',
					),
					'rowGap'                        => array(
						'type'    => 'number',
						'default' => 20,
					),
					'rowGapTablet'                  => array(
						'type'    => 'number',
						'default' => 20,
					),
					'rowGapMobile'                  => array(
						'type'    => 'number',
						'default' => 20,
					),
					'columnGap'                     => array(
						'type'    => 'number',
						'default' => 20,
					),
					'columnGapTablet'               => array(
						'type' => 'number',
					),
					'columnGapMobile'               => array(
						'type' => 'number',
					),
					'bgType'                        => array(
						'type'    => 'string',
						'default' => 'color',
					),
					'bgColor'                       => array(
						'type'    => 'string',
						'default' => '#f6f6f6',
					),

					// Title Attributes.
					'titleColor'                    => array(
						'type' => 'string',
					),
					'titleTag'                      => array(
						'type'    => 'string',
						'default' => 'h4',
					),
					'titleFontSize'                 => array(
						'type'    => 'number',
						'default' => '',
					),
					'titleFontSizeType'             => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'titleFontSizeMobile'           => array(
						'type' => 'number',
					),
					'titleFontSizeTablet'           => array(
						'type' => 'number',
					),
					'titleFontFamily'               => array(
						'type'    => 'string',
						'default' => '',
					),
					'titleFontWeight'               => array(
						'type' => 'string',
					),
					'titleFontStyle'                => array(
						'type' => 'string',
					),
					'titleLineHeightType'           => array(
						'type'    => 'string',
						'default' => 'em',
					),
					'titleLineHeight'               => array(
						'type' => 'number',
					),
					'titleLineHeightTablet'         => array(
						'type' => 'number',
					),
					'titleLineHeightMobile'         => array(
						'type' => 'number',
					),
					'titleLoadGoogleFonts'          => array(
						'type'    => 'boolean',
						'default' => false,
					),

					// Meta attributes.
					'metaColor'                     => array(
						'type'    => 'string',
						'default' => '',
					),
					'highlightedTextColor'          => array(
						'type'    => 'string',
						'default' => '#fff',
					),
					'highlightedTextBgColor'        => array(
						'type'    => 'string',
						'default' => '#3182ce',
					),
					'metaFontSize'                  => array(
						'type'    => 'number',
						'default' => '',
					),
					'metaFontSizeType'              => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'metaFontSizeMobile'            => array(
						'type' => 'number',
					),
					'metaFontSizeTablet'            => array(
						'type' => 'number',
					),
					'metaFontFamily'                => array(
						'type'    => 'string',
						'default' => '',
					),
					'metaFontWeight'                => array(
						'type' => 'string',
					),
					'metaFontStyle'                 => array(
						'type' => 'string',
					),
					'metaLineHeightType'            => array(
						'type'    => 'string',
						'default' => 'em',
					),
					'metaLineHeight'                => array(
						'type' => 'number',
					),
					'metaLineHeightTablet'          => array(
						'type' => 'number',
					),
					'metaLineHeightMobile'          => array(
						'type' => 'number',
					),
					'metaLoadGoogleFonts'           => array(
						'type'    => 'boolean',
						'default' => false,
					),

					// Excerpt Attributes.
					'excerptColor'                  => array(
						'type'    => 'string',
						'default' => '',
					),
					'excerptFontSize'               => array(
						'type'    => 'number',
						'default' => '',
					),
					'excerptFontSizeType'           => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'excerptFontSizeMobile'         => array(
						'type' => 'number',
					),
					'excerptFontSizeTablet'         => array(
						'type' => 'number',
					),
					'excerptFontFamily'             => array(
						'type'    => 'string',
						'default' => '',
					),
					'excerptFontWeight'             => array(
						'type' => 'string',
					),
					'excerptFontStyle'              => array(
						'type' => 'string',
					),
					'excerptLineHeightType'         => array(
						'type'    => 'string',
						'default' => 'em',
					),
					'excerptLineHeight'             => array(
						'type' => 'number',
					),
					'excerptLineHeightTablet'       => array(
						'type' => 'number',
					),
					'excerptLineHeightMobile'       => array(
						'type' => 'number',
					),
					'excerptLoadGoogleFonts'        => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'displayPostContentRadio'       => array(
						'type'    => 'string',
						'default' => 'excerpt',
					),

					// CTA attributes.
					'ctaColor'                      => array(
						'type' => 'string',
					),
					'ctaBgType'                     => array(
						'type'    => 'string',
						'default' => 'color',
					),
					'ctaBgHType'                    => array(
						'type'    => 'string',
						'default' => 'color',
					),
					'ctaBgColor'                    => array(
						'type' => 'string',
					),
					'ctaHColor'                     => array(
						'type' => 'string',
					),
					'ctaBgHColor'                   => array(
						'type' => 'string',
					),
					'ctaFontSize'                   => array(
						'type'    => 'number',
						'default' => '',
					),
					'ctaFontSizeType'               => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'ctaFontSizeMobile'             => array(
						'type' => 'number',
					),
					'ctaFontSizeTablet'             => array(
						'type' => 'number',
					),
					'ctaFontFamily'                 => array(
						'type'    => 'string',
						'default' => '',
					),
					'ctaFontWeight'                 => array(
						'type' => 'string',
					),
					'ctaFontStyle'                  => array(
						'type' => 'string',
					),
					'ctaLineHeightType'             => array(
						'type'    => 'string',
						'default' => 'em',
					),
					'ctaLineHeight'                 => array(
						'type' => 'number',
					),
					'ctaLineHeightTablet'           => array(
						'type' => 'number',
					),
					'ctaLineHeightMobile'           => array(
						'type' => 'number',
					),
					'ctaLoadGoogleFonts'            => array(
						'type'    => 'boolean',
						'default' => false,
					),

					// Spacing Attributes.
					'paddingTop'                    => array(
						'type'    => 'number',
						'default' => 20,
					),
					'paddingBottom'                 => array(
						'type'    => 'number',
						'default' => 20,
					),
					'paddingRight'                  => array(
						'type'    => 'number',
						'default' => 20,
					),
					'paddingLeft'                   => array(
						'type'    => 'number',
						'default' => 20,
					),
					'paddingTopMobile'              => array(
						'type' => 'number',
					),
					'paddingBottomMobile'           => array(
						'type' => 'number',
					),
					'paddingRightMobile'            => array(
						'type' => 'number',
					),
					'paddingLeftMobile'             => array(
						'type' => 'number',
					),
					'paddingTopTablet'              => array(
						'type' => 'number',
					),
					'paddingBottomTablet'           => array(
						'type' => 'number',
					),
					'paddingRightTablet'            => array(
						'type' => 'number',
					),
					'paddingLeftTablet'             => array(
						'type' => 'number',
					),
					'paddingBtnTop'                 => array(
						'type' => 'number',
					),
					'paddingBtnBottom'              => array(
						'type' => 'number',
					),
					'paddingBtnRight'               => array(
						'type' => 'number',
					),
					'paddingBtnLeft'                => array(
						'type' => 'number',
					),
					'contentPadding'                => array(
						'type'    => 'number',
						'default' => 20,
					),
					'contentPaddingMobile'          => array(
						'type' => 'number',
					),
					'ctaBottomSpace'                => array(
						'type'    => 'number',
						'default' => 0,
					),
					'ctaBottomSpaceTablet'          => array(
						'type'    => 'number',
						'default' => 0,
					),
					'ctaBottomSpaceMobile'          => array(
						'type'    => 'number',
						'default' => 0,
					),
					'imageBottomSpace'              => array(
						'type'    => 'number',
						'default' => 15,
					),
					'imageBottomSpaceTablet'        => array(
						'type' => 'number',
					),
					'imageBottomSpaceMobiile'       => array(
						'type' => 'number',
					),
					'titleBottomSpace'              => array(
						'type'    => 'number',
						'default' => 15,
					),
					'titleBottomSpaceTablet'        => array(
						'type' => 'number',
					),
					'titleBottomSpaceMobile'        => array(
						'type' => 'number',
					),
					'metaBottomSpace'               => array(
						'type'    => 'number',
						'default' => 15,
					),
					'metaBottomSpaceTablet'         => array(
						'type' => 'number',
					),
					'metaBottomSpaceMobile'         => array(
						'type' => 'number',
					),
					'excerptBottomSpace'            => array(
						'type'    => 'number',
						'default' => 25,
					),
					'excerptBottomSpaceTablet'      => array(
						'type' => 'number',
					),
					'excerptBottomSpaceMobile'      => array(
						'type' => 'number',
					),
					// Exclude Current Post.
					'excludeCurrentPost'            => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'titleTransform'                => array(
						'type' => 'string',
					),
					'metaTransform'                 => array(
						'type' => 'string',
					),
					'excerptTransform'              => array(
						'type' => 'string',
					),
					'ctaTransform'                  => array(
						'type' => 'string',
					),
					'titleDecoration'               => array(
						'type' => 'string',
					),
					'metaDecoration'                => array(
						'type' => 'string',
					),
					'excerptDecoration'             => array(
						'type' => 'string',
					),
					'ctaDecoration'                 => array(
						'type' => 'string',
					),
					'contentPaddingUnit'            => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'rowGapUnit'                    => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'columnGapUnit'                 => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'excerptBottomSpaceUnit'        => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'paginationSpacingUnit'         => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'imageBottomSpaceUnit'          => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'titleBottomSpaceUnit'          => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'metaBottomSpaceUnit'           => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'ctaBottomSpaceUnit'            => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'paddingBtnUnit'                => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'mobilePaddingBtnUnit'          => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'tabletPaddingBtnUnit'          => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'paddingUnit'                   => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'mobilePaddingUnit'             => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'tabletPaddingUnit'             => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'isPreview'                     => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'taxDivider'                    => array(
						'type'    => 'string',
						'default' => ', ',
					),
					'titleLetterSpacing'            => array(
						'type'    => 'number',
						'default' => '',
					),
					'titleLetterSpacingType'        => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'titleLetterSpacingMobile'      => array(
						'type' => 'number',
					),
					'titleLetterSpacingTablet'      => array(
						'type' => 'number',
					),
					'metaLetterSpacing'             => array(
						'type'    => 'number',
						'default' => '',
					),
					'metaLetterSpacingType'         => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'metaLetterSpacingMobile'       => array(
						'type' => 'number',
					),
					'metaLetterSpacingTablet'       => array(
						'type' => 'number',
					),
					'ctaLetterSpacing'              => array(
						'type'    => 'number',
						'default' => '',
					),
					'ctaLetterSpacingType'          => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'ctaLetterSpacingMobile'        => array(
						'type' => 'number',
					),
					'ctaLetterSpacingTablet'        => array(
						'type' => 'number',
					),
					'excerptLetterSpacing'          => array(
						'type'    => 'number',
						'default' => '',
					),
					'excerptLetterSpacingType'      => array(
						'type'    => 'string',
						'default' => 'px',
					),
					'excerptLetterSpacingMobile'    => array(
						'type' => 'number',
					),
					'excerptLetterSpacingTablet'    => array(
						'type' => 'number',
					),
					'useSeparateBoxShadows'         => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'boxShadowColor'                => array(
						'type'    => 'string',
						'default' => '#00000070',
					),
					'boxShadowHOffset'              => array(
						'type'    => 'number',
						'default' => 0,
					),
					'boxShadowVOffset'              => array(
						'type'    => 'number',
						'default' => 0,
					),
					'boxShadowBlur'                 => array(
						'type'    => 'number',
						'default' => '',
					),
					'boxShadowSpread'               => array(
						'type'    => 'number',
						'default' => '',
					),
					'boxShadowPosition'             => array(
						'type'    => 'string',
						'default' => 'outset',
					),
					'boxShadowColorHover'           => array(
						'type'    => 'string',
						'default' => '',
					),
					'boxShadowHOffsetHover'         => array(
						'type'    => 'number',
						'default' => 0,
					),
					'boxShadowVOffsetHover'         => array(
						'type'    => 'number',
						'default' => 0,
					),
					'boxShadowBlurHover'            => array(
						'type'    => 'number',
						'default' => '',
					),
					'boxShadowSpreadHover'          => array(
						'type'    => 'number',
						'default' => '',
					),
					'boxShadowPositionHover'        => array(
						'type'    => 'string',
						'default' => 'outset',
					),
					'overallBorderHColor'           => array(
						'type' => 'string',
					),
					'borderWidth'                   => array(
						'type'    => 'number',
						'default' => '',
					),
					'borderStyle'                   => array(
						'type'    => 'string',
						'default' => 'none',
					),
					'borderColor'                   => array(
						'type'    => 'string',
						'default' => '',
					),
					'borderHColor'                  => array(
						'type' => 'string',
					),
					'borderRadius'                  => array(
						'type'    => 'number',
						'default' => '',
					),
				)
			);
		}

		/**
		 * Renders the post grid block on server.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 0.0.1
		 */
		public function post_grid_callback( $attributes ) {
			
			// Render query.
			$query = UAGB_Helper::get_query( $attributes, 'grid' );

			// Cache the settings.
			self::$settings['grid'][ $attributes['block_id'] ] = $attributes;

			ob_start();
			$this->get_post_html( $attributes, $query, 'grid' );
			// Output the post markup.
			return ob_get_clean();
		}

		/**
		 * Renders the post grid block on pagination clicks.
		 *
		 * @since 2.6.0
		 * 
		 * @return void
		 */
		public function post_grid_pagination_ajax_callback() {
			check_ajax_referer( 'uagb_grid_ajax_nonce', 'nonce' );

			if ( isset( $_POST['attr'] ) ) {

				$attr          = json_decode( stripslashes( sanitize_text_field( $_POST['attr'] ) ), true );
				$attr['paged'] = isset( $_POST['page_number'] ) ? sanitize_text_field( $_POST['page_number'] ) : '';
				$html          = $this->post_grid_callback( $attr );
				wp_send_json_success( $html );
				
			}

			wp_send_json_error( ' Something went wrong, failed to load pagination data! ' );
		}

		/**
		 * Renders the post carousel block on server.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 0.0.1
		 */
		public function post_carousel_callback( $attributes ) {

			// Render query.
			$query = UAGB_Helper::get_query( $attributes, 'carousel' );

			// Cache the settings.
			self::$settings['carousel'][ $attributes['block_id'] ] = $attributes;

			ob_start();
			$this->get_post_html( $attributes, $query, 'carousel' );
			// Output the post markup.
			return ob_get_clean();
		}

		/**
		 * Renders the post masonry block on server.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 0.0.1
		 */
		public function post_masonry_callback( $attributes ) {

			// Render query.
			$query = UAGB_Helper::get_query( $attributes, 'masonry' );

			// Cache the settings.
			self::$settings['masonry'][ $attributes['block_id'] ] = $attributes;

			ob_start();
			$this->get_post_html( $attributes, $query, 'masonry' );
			// Output the post markup.
			return ob_get_clean();
		}

		/**
		 * Renders the post grid block on server.
		 *
		 * @param array  $attributes Array of block attributes.
		 *
		 * @param object $query WP_Query object.
		 * @param string $layout post grid/masonry/carousel layout.
		 * @since 0.0.1
		 */
		public function get_post_html( $attributes, $query, $layout ) {
			// Common Post Attributes.
			$attributes['post_type']          = $layout;
			$attributes['postsToShow']        = UAGB_Block_Helper::get_fallback_number( $attributes['postsToShow'], 'postsToShow', $attributes['blockName'] );
			$attributes['postsOffset']        = UAGB_Block_Helper::get_fallback_number( $attributes['postsOffset'], 'postsOffset', $attributes['blockName'] );
			$attributes['columns']            = UAGB_Block_Helper::get_fallback_number( $attributes['columns'], 'columns', $attributes['blockName'] );
			$attributes['tcolumns']           = UAGB_Block_Helper::get_fallback_number( $attributes['tcolumns'], 'columns', $attributes['blockName'] );
			$attributes['mcolumns']           = UAGB_Block_Helper::get_fallback_number( $attributes['mcolumns'], 'mcolumns', $attributes['blockName'] );
			$attributes['excerptLength']      = UAGB_Block_Helper::get_fallback_number( $attributes['excerptLength'], 'excerptLength', $attributes['blockName'] );
			$attributes['overlayOpacity']     = UAGB_Block_Helper::get_fallback_number( $attributes['overlayOpacity'], 'overlayOpacity', $attributes['blockName'] );
			$attributes['columnGap']          = UAGB_Block_Helper::get_fallback_number( $attributes['columnGap'], 'columnGap', $attributes['blockName'] );
			$attributes['rowGap']             = UAGB_Block_Helper::get_fallback_number( $attributes['rowGap'], 'rowGap', $attributes['blockName'] );
			$attributes['imageBottomSpace']   = UAGB_Block_Helper::get_fallback_number( $attributes['imageBottomSpace'], 'imageBottomSpace', $attributes['blockName'] );
			$attributes['titleBottomSpace']   = UAGB_Block_Helper::get_fallback_number( $attributes['titleBottomSpace'], 'titleBottomSpace', $attributes['blockName'] );
			$attributes['metaBottomSpace']    = UAGB_Block_Helper::get_fallback_number( $attributes['metaBottomSpace'], 'metaBottomSpace', $attributes['blockName'] );
			$attributes['excerptBottomSpace'] = UAGB_Block_Helper::get_fallback_number( $attributes['excerptBottomSpace'], 'excerptBottomSpace', $attributes['blockName'] );
			$attributes['ctaBottomSpace']     = UAGB_Block_Helper::get_fallback_number( $attributes['ctaBottomSpace'], 'ctaBottomSpace', $attributes['blockName'] );
			// Unique Responsive Attributes.
			$attributes['rowGapTablet'] = is_numeric( $attributes['rowGapTablet'] ) ? $attributes['rowGapTablet'] : $attributes['rowGap'];
			$attributes['rowGapMobile'] = is_numeric( $attributes['rowGapMobile'] ) ? $attributes['rowGapMobile'] : $attributes['rowGapTablet'];
			// Grid / Carousel / Masonry Specific Attributes.
			if ( isset( $attributes['autoplaySpeed'] ) ) {
				$attributes['autoplaySpeed'] = UAGB_Block_Helper::get_fallback_number( $attributes['autoplaySpeed'], 'autoplaySpeed', $attributes['blockName'] );
			}
			if ( isset( $attributes['transitionSpeed'] ) ) {
				$attributes['transitionSpeed'] = UAGB_Block_Helper::get_fallback_number( $attributes['transitionSpeed'], 'transitionSpeed', $attributes['blockName'] );
			}
			if ( isset( $attributes['arrowSize'] ) ) {
				$attributes['arrowSize'] = UAGB_Block_Helper::get_fallback_number( $attributes['arrowSize'], 'arrowSize', $attributes['blockName'] );
			}
			if ( isset( $attributes['arrowDistance'] ) ) {
				$attributes['arrowDistance'] = UAGB_Block_Helper::get_fallback_number( $attributes['arrowDistance'], 'arrowDistance', $attributes['blockName'] );
			}
			if ( isset( $attributes['arrowDistanceTablet'] ) ) {
				$attributes['arrowDistanceTablet'] = UAGB_Block_Helper::get_fallback_number( $attributes['arrowDistanceTablet'], 'arrowDistanceTablet', $attributes['blockName'] );
			}
			if ( isset( $attributes['arrowDistanceMobile'] ) ) {
				$attributes['arrowDistanceMobile'] = UAGB_Block_Helper::get_fallback_number( $attributes['arrowDistanceMobile'], 'arrowDistanceMobile', $attributes['blockName'] );
			}
			if ( isset( $attributes['arrowBorderSize'] ) ) {
				$attributes['arrowBorderSize'] = UAGB_Block_Helper::get_fallback_number( $attributes['arrowBorderSize'], 'arrowBorderSize', $attributes['blockName'] );
			}
			if ( isset( $attributes['paginationSpacing'] ) ) {
				$attributes['paginationSpacing'] = UAGB_Block_Helper::get_fallback_number( $attributes['paginationSpacing'], 'paginationSpacing', $attributes['blockName'] );
			}
			if ( isset( $attributes['paginationBorderRadius'] ) ) {
				$attributes['paginationBorderRadius'] = UAGB_Block_Helper::get_fallback_number( $attributes['paginationBorderRadius'], 'paginationBorderRadius', $attributes['blockName'] );
			}
			if ( isset( $attributes['paginationBorderSize'] ) ) {
				$attributes['paginationBorderSize'] = UAGB_Block_Helper::get_fallback_number( $attributes['paginationBorderSize'], 'paginationBorderSize', $attributes['blockName'] );
			}
			if ( isset( $attributes['paginationFontSize'] ) ) {
				$attributes['paginationFontSize'] = UAGB_Block_Helper::get_fallback_number( $attributes['paginationFontSize'], 'paginationFontSize', $attributes['blockName'] );
			}
			if ( isset( $attributes['loaderSize'] ) ) {
				$attributes['loaderSize'] = UAGB_Block_Helper::get_fallback_number( $attributes['loaderSize'], 'loaderSize', $attributes['blockName'] );
			}

			$wrap = array(
				'uagb-post__items uagb-post__columns-' . $attributes['columns'],
				'is-' . $layout,
				'uagb-post__columns-tablet-' . $attributes['tcolumns'],
				'uagb-post__columns-mobile-' . $attributes['mcolumns'],
			);

			$block_id = 'uagb-block-' . $attributes['block_id'];

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

			$is_image_enabled = ( true === $attributes['displayPostImage'] ) ? 'uagb-post__image-enabled' : 'uagb-post__image-disabled';

			$outerwrap = array(
				'wp-block-uagb-post-' . $layout,
				'uagb-post-grid',
				( isset( $attributes['className'] ) ) ? $attributes['className'] : '',
				'uagb-post__image-position-' . $attributes['imgPosition'],
				$is_image_enabled,
				$block_id,
				$desktop_class,
				$tab_class,
				$mob_class,
				$zindex_extention_enabled ? 'uag-blocks-common-selector' : '',
			);

			switch ( $layout ) {
				case 'masonry':
					break;

				case 'grid':
					if ( $attributes['equalHeight'] ) {
						array_push( $wrap, 'uagb-post__equal-height' );
					}
					if ( $attributes['equalHeightInlineButtons'] ) {
						array_push( $wrap, 'uagb-equal_height_inline-read-more-buttons' );
					}
					break;

				case 'carousel':
					array_push( $outerwrap, 'uagb-post__arrow-outside' );

					if ( $attributes['equalHeight'] ) {
						array_push( $wrap, 'uagb-post__carousel_equal-height' );
					}

					if ( $query->post_count > $attributes['columns'] ) {
						array_push( $outerwrap, 'uagb-slick-carousel' );
					}
					break;

				default:
					// Nothing to do here.
					break;
			}

			$common_classes = array_merge( $outerwrap, $wrap );

			$total = $query->max_num_pages;

			?>

			<div class="<?php echo esc_attr( implode( ' ', $common_classes ) ); ?>" data-total="<?php echo esc_attr( $total ); ?>" style="<?php echo esc_attr( implode( '', $zindex_wrap ) ); ?>">

				<?php

				$this->posts_articles_markup( $query, $attributes );

				$post_not_found = $query->found_posts;

				if ( 0 === $post_not_found ) {
					?>
					<p class="uagb-post__no-posts">
						<?php echo esc_html( $attributes['postDisplaytext'] ); ?>
					</p>
					<?php
				}

				if ( ( isset( $attributes['postPagination'] ) && true === $attributes['postPagination'] ) ) {

					?>
					<div class="uagb-post-pagination-wrap">
						<?php
							// content already escaped using wp_kses_post.
							echo $this->render_pagination( $query, $attributes ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</div>
					<?php
				}
				if ( 'masonry' === $layout && 'infinite' === $attributes['paginationType'] ) {

					if ( 'scroll' === $attributes['paginationEventType'] ) {
						?>
							<div class="uagb-post-inf-loader" style="display: none;">
								<div class="uagb-post-loader-1"></div>
								<div class="uagb-post-loader-2"></div>
								<div class="uagb-post-loader-3"></div>
							</div>
							<?php

					}
					if ( 'button' === $attributes['paginationEventType'] ) {
						?>
							<div class="uagb-post__load-more-wrap">
								<span class="uagb-post-pagination-button">
									<a class="uagb-post__load-more" href="javascript:void(0);">
									<?php echo esc_html( $attributes['buttonText'] ); ?>
									</a>
								</span>
							</div>
							<?php
					}
				}
				?>
			</div>
			<?php
		}

		/**
		 * Renders the post post pagination on server.
		 *
		 * @param object $query WP_Query object.
		 * @param array  $attributes Array of block attributes.
		 * @since 1.18.1
		 */
		public function render_pagination( $query, $attributes ) {

			$permalink_structure = get_option( 'permalink_structure' );
			$base                = untrailingslashit( wp_specialchars_decode( get_pagenum_link() ) );
			$base                = UAGB_Helper::build_base_url( $permalink_structure, $base );
			$format              = UAGB_Helper::paged_format( $permalink_structure, $base );
			$paged               = UAGB_Helper::get_paged( $query );
			$p_limit             = UAGB_Block_Helper::get_fallback_number( $attributes['pageLimit'], 'pageLimit', $attributes['blockName'] );
			$page_limit          = min( $p_limit, $query->max_num_pages );
			$page_limit          = isset( $page_limit ) ? $page_limit : UAGB_Block_Helper::get_fallback_number( $attributes['postsToShow'], 'postsToShow', $attributes['blockName'] );

			$links = paginate_links(
				array(
					'base'      => $base . '%_%',
					'format'    => $format,
					'current'   => ( ! $paged ) ? 1 : $paged,
					'total'     => $page_limit,
					'type'      => 'array',
					'mid_size'  => 4,
					'end_size'  => 4,
					'prev_text' => $attributes['paginationPrevText'],
					'next_text' => $attributes['paginationNextText'],
				)
			);

			if ( isset( $links ) ) {

				return wp_kses_post( implode( PHP_EOL, $links ) );
			}

			return '';
		}

		/**
		 * Sends the Post pagination markup to edit.js
		 *
		 * @since 1.14.9
		 */
		public function post_pagination() {

			check_ajax_referer( 'uagb_ajax_nonce', 'nonce' );

			$post_attribute_array = array();

			if ( isset( $_POST['attributes'] ) ) {

				// $_POST['attributes'] is sanitized in later stage.
				$attr = isset( $_POST['attributes'] ) ? json_decode( stripslashes( $_POST['attributes'] ), true ) : array(); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				$post_attribute_array = $this->required_attribute_for_query( $attr );

				$query = UAGB_Helper::get_query( $post_attribute_array, 'grid' );

				$pagination_markup = $this->render_pagination( $query, $attr );

				wp_send_json_success( $pagination_markup );
			}

			wp_send_json_error( ' No attributes received' );
		}

		/**
		 * Required attribute for query.
		 *
		 * @param array $attributes plugin.
		 * @return array of requred query attributes.
		 * @since 2.0.0
		 */
		public function required_attribute_for_query( $attributes ) {
			return array(
				'postsOffset'        => UAGB_Block_Helper::get_fallback_number( sanitize_text_field( $attributes['postsOffset'] ), 'postsOffset', sanitize_text_field( $attributes['blockName'] ) ),
				'postsToShow'        => UAGB_Block_Helper::get_fallback_number( sanitize_text_field( $attributes['postsToShow'] ), 'postsToShow', sanitize_text_field( $attributes['blockName'] ) ),
				'postType'           => ( isset( $attributes['postType'] ) ) ? sanitize_text_field( $attributes['postType'] ) : 'post',
				'order'              => ( isset( $attributes['order'] ) ) ? sanitize_text_field( $attributes['order'] ) : 'desc',
				'orderBy'            => ( isset( $attributes['orderBy'] ) ) ? sanitize_text_field( $attributes['orderBy'] ) : 'date',
				'excludeCurrentPost' => ( ! empty( $attr['excludeCurrentPost'] ) ) ? sanitize_text_field( $attributes['excludeCurrentPost'] ) : false,
				'categories'         => ( isset( $attributes['categories'] ) && '' !== $attributes['categories'] ) ? sanitize_text_field( $attributes['categories'] ) : '',
				'taxonomyType'       => ( isset( $attributes['taxonomyType'] ) ) ? sanitize_text_field( $attributes['taxonomyType'] ) : 'category',
				'postPagination'     => ( isset( $attributes['postPagination'] ) && true === $attributes['postPagination'] ) ? sanitize_text_field( $attributes['postPagination'] ) : false,
				'paginationType'     => ( isset( $attributes['paginationType'] ) && 'none' !== $attributes['paginationType'] ) ? sanitize_text_field( $attributes['paginationType'] ) : 'none',
				'paged'              => ( isset( $attributes['paged'] ) ) ? sanitize_text_field( $attributes['paged'] ) : '',
				'blockName'          => ( isset( $attributes['blockName'] ) ) ? sanitize_text_field( $attributes['blockName'] ) : '',
			);
		}

		/**
		 * Sends the Posts to Masonry AJAX.
		 *
		 * @since 1.18.1
		 */
		public function masonry_pagination() {

			check_ajax_referer( 'uagb_masonry_ajax_nonce', 'nonce' );
			
			$post_attribute_array = array();
			// $_POST['attr'] is sanitized in later stage.
			$attr = isset( $_POST['attr'] ) ? json_decode( stripslashes( $_POST['attr'] ), true ) : array(); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$attr['paged'] = isset( $_POST['page_number'] ) ? sanitize_text_field( $_POST['page_number'] ) : '';

			$post_attribute_array = $this->required_attribute_for_query( $attr );

			$query = UAGB_Helper::get_query( $post_attribute_array, 'masonry' );

			foreach ( $attr as $key => $attribute ) {
				$attr[ $key ] = ( 'false' === $attribute ) ? false : ( ( 'true' === $attribute ) ? true : $attribute );
			}

			ob_start();
			$this->posts_articles_markup( $query, $attr );
			$html = ob_get_clean();

			wp_send_json_success( $html );
		}

		/**
		 * Render Posts HTML for Masonry Pagination.
		 *
		 * @param object $query WP_Query object.
		 * @param array  $attributes Array of block attributes.
		 * @since 1.18.1
		 */
		public function posts_articles_markup( $query, $attributes ) {

			while ( $query->have_posts() ) {

				$query->the_post();
				// Filter to modify the attributes based on content requirement.
				$attributes         = apply_filters( 'uagb_post_alter_attributes', $attributes, get_the_ID() );
				$post_class_enabled = apply_filters( 'uagb_enable_post_class', false, $attributes );

				do_action( "uagb_post_before_article_{$attributes['post_type']}", get_the_ID(), $attributes );
				$post_classes = ( $post_class_enabled ) ? implode( ' ', get_post_class( 'uagb-post__inner-wrap' ) ) : 'uagb-post__inner-wrap';
				$isLeftRight  = ( is_array( $attributes ) && isset( $attributes['isLeftToRightLayout'] ) ) ? $attributes['isLeftToRightLayout'] : false;
				?>
				<?php do_action( "uagb_post_before_inner_wrap_{$attributes['post_type']}", get_the_ID(), $attributes ); ?>
				<?php
				echo sprintf(
					'<article class="%1$s">',
					esc_attr( $post_classes )
				);
				?>
					<?php
					if ( $isLeftRight ) {
						$this->render_innerblocks_with_wrapper( $attributes );
					} else {
						$this->render_innerblocks( $attributes );
					}
					?>

					<?php $this->render_complete_box_link( $attributes ); ?>
				</article>
				<?php do_action( "uagb_post_after_inner_wrap_{$attributes['post_type']}", get_the_ID(), $attributes ); ?>
				<?php

				do_action( "uagb_post_after_article_{$attributes['post_type']}", get_the_ID(), $attributes );

			}

			wp_reset_postdata();
		}
		/**
		 * Render layout.
		 *
		 * @param array $fname to get the block.
		 * @param array $attr Array of block attributes.
		 *
		 * @since 1.20.0
		 */
		public function render_layout( $fname, $attr ) {
			switch ( $fname ) {
				case 'uagb/post-button':
					return $this->render_button( $attr );
				case 'uagb/post-image':
					return $this->render_image( $attr );
				case 'uagb/post-taxonomy':
					return ( 'aboveTitle' === $attr['displayPostTaxonomyAboveTitle'] ) ? $this->render_meta_taxonomy( $attr ) : '';
				case 'uagb/post-title':
					return $this->render_title( $attr );
				case 'uagb/post-meta':
					return $this->render_meta( $attr );
				case 'uagb/post-excerpt':
					return $this->render_excerpt( $attr );
				default:
					return '';
			}
		}

		/**
		 * Render Inner blocks with a wrapper.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 2.13.3
		 * @return void
		 */
		public function render_innerblocks_with_wrapper( $attributes ) {
			$length   = count( $attributes['layoutConfig'] );
			$img_atts = array();
		
			// Iterate through the blocks and find the uagb/post-image block.
			for ( $i = 0; $i < $length; $i++ ) {
				if ( 'uagb/post-image' === $attributes['layoutConfig'][ $i ][0] ) {
					// Store the image attributes for later rendering.
					$img_atts[] = $attributes['layoutConfig'][ $i ];
					// Remove the uagb/post-image block from the layoutConfig array.
					array_splice( $attributes['layoutConfig'], $i, 1 );
					$i--;
					$length--;
				}
			}
		
			// Render the uagb/post-image block(s) outside the wrapper, if it exists.
			foreach ( $img_atts as $img_att ) {
				echo esc_html( $this->render_layout( $img_att[0], $attributes ) );
			}
		
			// Render all blocks except for the uagb/post-image block inside the wrapper.
			echo '<div class="uag-post-grid-wrapper">';
			for ( $i = 0; $i < $length; $i++ ) {
				echo esc_html( $this->render_layout( $attributes['layoutConfig'][ $i ][0], $attributes ) );
			}
			echo '</div>';
		}

		/**
		 * Render Inner blocks.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 1.20.0
		 * @return void
		 */
		public function render_innerblocks( $attributes ) {
			$length   = count( $attributes['layoutConfig'] );
			$img_atts = array();
			for ( $i = 0; $i < $length; $i++ ) {
				if ( 'background' === $attributes['imgPosition'] && 'uagb/post-image' === $attributes['layoutConfig'][ $i ][0] ) {
					// This is to avoid background image container as first child as we are targetting first child for top margin property.
					$img_atts = $attributes['layoutConfig'][ $i ][0];
					continue;
				}
				$this->render_layout( $attributes['layoutConfig'][ $i ][0], $attributes );
			}
			// Render background image container as a last child.
			if ( ! empty( $img_atts ) ) {
				$this->render_layout( $img_atts, $attributes );
			}
		}
		/**
		 * Renders the post masonry related script.
		 *
		 * @since 0.0.1
		 */
		public function add_post_dynamic_script() {
			if ( isset( self::$settings['masonry'] ) && ! empty( self::$settings['masonry'] ) ) {
				foreach ( self::$settings['masonry'] as $key => $value ) {
					?>
					<script type="text/javascript" id="uagb-post-masonry-script-<?php echo esc_html( $key ); ?>">
						document.addEventListener("DOMContentLoaded", function(){
							let scope = document.querySelector( '.uagb-block-<?php echo esc_html( $key ); ?>' );
							if (scope.classList.contains( 'is-masonry' )) {
								setTimeout( function() {
									const isotope = new Isotope( scope, { // eslint-disable-line no-undef
											itemSelector: 'article',
										} );
									imagesLoaded( scope, function() { isotope	});
									window.addEventListener( 'resize', function() {	isotope	});
								}, 500 );
							}
							// This CSS is for Post BG Image Spacing
							let articles = document.querySelectorAll( '.wp-block-uagb-post-masonry.uagb-post__image-position-background .uagb-post__inner-wrap' );

							for( let article of articles ) {
								let articleWidth = article.offsetWidth;
								let rowGap = <?php echo esc_html( $value['rowGap'] ); ?>;
								let imageWidth = 100 - ( rowGap / articleWidth ) * 100;
								let image = article.getElementsByClassName('uagb-post__image');
								if ( image[0] ) {
									image[0].style.width = imageWidth + '%';
									image[0].style.marginLeft = rowGap / 2 + 'px';
								}

							}
						});
						<?php $selector = '.uagb-block-' . $key; ?>
						window.addEventListener( 'DOMContentLoaded', function() {
							UAGBPostMasonry._init( <?php echo wp_json_encode( $value ); ?>, '<?php echo esc_attr( $selector ); ?>' );
						});
					</script>
					<?php
				}
			}

			if ( isset( self::$settings['carousel'] ) && ! empty( self::$settings['carousel'] ) ) {
				foreach ( self::$settings['carousel'] as $key => $value ) {

					$dots         = ( 'dots' === $value['arrowDots'] || 'arrows_dots' === $value['arrowDots'] ) ? true : false;
					$arrows       = ( 'arrows' === $value['arrowDots'] || 'arrows_dots' === $value['arrowDots'] ) ? true : false;
					$equal_height = isset( $value['equalHeight'] ) ? $value['equalHeight'] : '';
					$tcolumns     = ( isset( $value['tcolumns'] ) ) ? $value['tcolumns'] : 2;
					$mcolumns     = ( isset( $value['mcolumns'] ) ) ? $value['mcolumns'] : 1;
					$is_rtl       = is_rtl();

					?>
					<script type="text/javascript" id="<?php echo esc_attr( $key ); ?>">
						document.addEventListener("DOMContentLoaded", function(){
							( function( $ ) {
								var cols = parseInt( '<?php echo esc_html( $value['columns'] ); ?>' );
								var $scope = $( '.uagb-block-<?php echo esc_html( $key ); ?>' );
								let imagePosition = '<?php echo esc_html( $value['imgPosition'] ); ?>';

								if( 'top' !== imagePosition ){
									// This CSS is for Post BG Image Spacing
									let articles = document.querySelectorAll( '.uagb-post__image-position-background .uagb-post__inner-wrap' );
									if( articles.length ) {
										for( let article of articles ) {
											let image = article.getElementsByClassName('uagb-post__image');
											if ( image[0] ) {
												let articleWidth = article.offsetWidth;
												let rowGap = <?php echo esc_html( $value['rowGap'] ); ?>;
												let imageWidth = 100 - ( rowGap / articleWidth ) * 100;
												image[0].style.width = imageWidth + '%';
												image[0].style.marginLeft = rowGap / 2 + 'px';
											}
										}
									}
								}
								// If this is not a Post Carousel, return.
								// Else if it is a carousel but has less posts than the number of columns, return after setting visibility.
								if ( ! $scope.hasClass('is-carousel') ) {
									return;
								} else if ( cols >= $scope.children('article.uagb-post__inner-wrap').length ) {
									$scope.css( 'visibility', 'visible' );
									return;
								}
								var slider_options = {
									'slidesToShow' : cols,
									'slidesToScroll' : 1,
									'autoplaySpeed' : <?php echo esc_html( $value['autoplaySpeed'] ); ?>,
									'autoplay' : Boolean( '<?php echo esc_html( $value['autoplay'] ); ?>' ),
									'infinite' : Boolean( '<?php echo esc_html( $value['infiniteLoop'] ); ?>' ),
									'pauseOnHover' : Boolean( '<?php echo esc_html( $value['pauseOnHover'] ); ?>' ),
									'speed' : <?php echo esc_html( $value['transitionSpeed'] ); ?>,
									'arrows' : Boolean( '<?php echo esc_html( $arrows ); ?>' ),
									'dots' : Boolean( '<?php echo esc_html( $dots ); ?>' ),
									'rtl' : Boolean( '<?php echo esc_html( $is_rtl ); ?>' ),
									'prevArrow' : '<button type=\"button\" data-role=\"none\" class=\"slick-prev\" aria-label=\"Previous\" tabindex=\"0\" role=\"button\"><svg width=\"20\" height=\"20\" viewBox=\"0 0 256 512\"><path d=\"M31.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L127.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L201.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34z\"></path></svg><\/button>',
									'nextArrow' : '<button type=\"button\" data-role=\"none\" class=\"slick-next\" aria-label=\"Next\" tabindex=\"0\" role=\"button\"><svg width=\"20\" height=\"20\" viewBox=\"0 0 256 512\"><path d=\"M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z\"></path></svg><\/button>',
									'responsive' : [
										{
											'breakpoint' : 1024,
											'settings' : {
												'slidesToShow' : <?php echo esc_html( $tcolumns ); ?>,
												'slidesToScroll' : 1,
											}
										},
										{
											'breakpoint' : 767,
											'settings' : {
												'slidesToShow' : <?php echo esc_html( $mcolumns ); ?>,
												'slidesToScroll' : 1,
											}
										}
									]
								};

								$scope.imagesLoaded( function() {
									$scope.slick( slider_options );
								}).always( function() {
									$scope.css( 'visibility', 'visible' );
								} );

								var enableEqualHeight = ( '<?php echo esc_html( $equal_height ); ?>' );

								if( enableEqualHeight ){
									$scope.imagesLoaded( function() {
										UAGBPostCarousel?._setHeight( $scope );
									});

									$scope.on( 'afterChange', function() {
										UAGBPostCarousel?._setHeight( $scope );
									} );
								}

							} )( jQuery );
						});
					</script>
					<?php
				}
			}

			if ( ! empty( self::$settings['grid'] ) && is_array( self::$settings['grid'] ) ) {
				foreach ( self::$settings['grid'] as $key => $value ) {
					if ( empty( $value ) || ! is_array( $value ) ) {
						return; // Exit early if this is not the attributes array.
					}
					if ( ! empty( $value['paginationType'] ) && 'ajax' !== $value['paginationType'] ) { 
						return; // Early return when pagination type exists and is not ajax.
					}
					?>

					<script type="text/javascript" id="<?php echo esc_attr( $key ); ?>">
						( function() {
							let elements = document.querySelectorAll( '.uagb-post-grid.uagb-block-<?php echo esc_html( $key ); ?> .uagb-post-pagination-wrap a' );
							elements.forEach(function(element) {
								element.addEventListener("click", function(event){
									event.preventDefault();
									const link = event.target.getAttribute('href').match( /\/page\/\d+\// )?.[0] || '';
									const regex = /\d+/; // regular expression to match a number at the end of the string
									const match = link.match( regex ) ? link.match( regex )[0] : 1; // match the regular expression with the link
									const pageNumber = parseInt( match ); // extract the number and parse it to an integer
									window.UAGBPostGrid._callAjax(<?php echo wp_json_encode( $value ); ?>, pageNumber, '<?php echo esc_attr( $key ); ?>');
								});
							});
						} )();
					</script>

					<?php
				}
			}
		}

		/**
		 * Render Image HTML.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 0.0.1
		 */
		public function render_image( $attributes ) {
			if ( ! $attributes['displayPostImage'] ) {
				return;
			}

			if ( ! get_the_post_thumbnail_url() && ( 'background' !== $attributes['imgPosition'] ) ) {
				return;
			}

			$target = ( $attributes['newTab'] ) ? '_blank' : '_self';
			do_action( "uagb_single_post_before_featured_image_{$attributes['post_type']}", get_the_ID(), $attributes );
			?>
			<div class='uagb-post__image'>
				<?php
				if ( get_the_post_thumbnail_url() ) {
					if ( 'post-grid' === $attributes['blockName'] && 'background' !== $attributes['imgPosition'] ) {
						?>
					<a href="<?php echo esc_url( apply_filters( "uagb_single_post_link_{$attributes['post_type']}", get_the_permalink(), get_the_ID(), $attributes ) ); ?>" target="<?php echo esc_attr( $target ); ?>" rel="bookmark noopener noreferrer" class='uagb-image-ratio-<?php echo esc_attr( $attributes['imageRatio'] ); ?>'><?php echo wp_get_attachment_image( get_post_thumbnail_id(), $attributes['imgSize'] ); ?>
					</a>
				<?php } else { ?>
					<a href="<?php echo esc_url( apply_filters( "uagb_single_post_link_{$attributes['post_type']}", get_the_permalink(), get_the_ID(), $attributes ) ); ?>" target="<?php echo esc_attr( $target ); ?>" rel="bookmark noopener noreferrer"><?php echo wp_get_attachment_image( get_post_thumbnail_id(), $attributes['imgSize'] ); ?>
					</a>
						<?php
				}
				}
				?>
			</div>
			<?php
			do_action( "uagb_single_post_after_featured_image_{$attributes['post_type']}", get_the_ID(), $attributes );
		}

		/**
		 * Render Post Title HTML.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 0.0.1
		 */
		public function render_title( $attributes ) {

			if ( ! $attributes['displayPostTitle'] ) {
				return;
			}

			$target = ( $attributes['newTab'] ) ? '_blank' : '_self';
			do_action( "uagb_single_post_before_title_{$attributes['post_type']}", get_the_ID(), $attributes );
			$array_of_allowed_HTML = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'span', 'p' );
			$title_tag             = UAGB_Helper::title_tag_allowed_html( $attributes['titleTag'], $array_of_allowed_HTML, 'h4' );
			?>
			<<?php echo esc_html( $title_tag ); ?> class="uagb-post__title uagb-post__text">
				<a href="<?php echo esc_url( apply_filters( "uagb_single_post_link_{$attributes['post_type']}", get_the_permalink(), get_the_ID(), $attributes ) ); ?>" target="<?php echo esc_attr( $target ); ?>" rel="bookmark noopener noreferrer"><?php the_title(); ?></a>
			</<?php echo esc_html( $title_tag ); ?>>
			<?php
			do_action( "uagb_single_post_after_title_{$attributes['post_type']}", get_the_ID(), $attributes );
		}

		/**
		 * Render Post Meta - Author HTML.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 1.14.0
		 */
		public function render_meta_author( $attributes ) {

			if ( ! $attributes['displayPostAuthor'] ) {
				return;
			}
			?>
				<span class="uagb-post__author">
				<?php echo ( true === $attributes['hideTaxonomyIcon'] ) ? '<span class="dashicons-admin-users dashicons"></span>' : ''; ?>
					<?php the_author_posts_link(); ?>
				</span>
			<?php
		}

		/**
		 * Render Post Meta - Date HTML.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 1.14.0
		 */
		public function render_meta_date( $attributes ) {

			if ( ! $attributes['displayPostDate'] ) {
				return;
			}
			global $post;
			?>
				<time datetime="<?php echo esc_attr( get_the_date( 'c', $post->ID ) ); ?>" class="uagb-post__date">
				<?php echo ( true === $attributes['hideTaxonomyIcon'] ) ? '<span class="dashicons-calendar dashicons"></span>' : ''; ?>
					<?php echo esc_html( get_the_date( '', $post->ID ) ); ?>
				</time>
			<?php
		}

		/**
		 * Render Post Meta - Comment HTML.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 1.14.0
		 */
		public function render_meta_comment( $attributes ) {

			if ( ! $attributes['displayPostComment'] ) {
				return;
			}
			?>
				<span class="uagb-post__comment">
				<?php echo ( true === $attributes['hideTaxonomyIcon'] ) ? '<span class="dashicons-admin-comments dashicons"></span>' : ''; ?>
					<?php comments_number(); ?>
				</span>
			<?php
		}

		/**
		 * Render Post Meta - Comment HTML.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 1.14.0
		 */
		public function render_meta_taxonomy( $attributes ) {

			if ( ! $attributes['displayPostTaxonomy'] ) {
				return;
			}
			global $post;

			$terms = get_the_terms( $post->ID, $attributes['taxonomyType'] );
			if ( is_wp_error( $terms ) ) {
				return;
			}

			if ( ! isset( $terms[0] ) ) {
				return;
			}
			$wrap = ( 'aboveTitle' === $attributes['displayPostTaxonomyAboveTitle'] ) ? array(
				'uagb-post__taxonomy',
				$attributes['taxStyle'],
			) : array( 'uagb-post__taxonomy' );

			if ( ( 'default' === $attributes['taxStyle'] && 'aboveTitle' === $attributes['displayPostTaxonomyAboveTitle'] ) || 'withMeta' === $attributes['displayPostTaxonomyAboveTitle'] ) {
				?>
				<div class="uagb-post__text">
					<span class='<?php echo esc_attr( implode( ' ', $wrap ) ); ?>'>
						<?php echo ( true === $attributes['hideTaxonomyIcon'] ) ? '<span class="dashicons-tag dashicons"></span>' : ''; ?>
						<?php
						$terms_list = array();
						foreach ( $terms as $key => $value ) {
							// Get the URL of this category.
							$category_link = get_category_link( $value->term_id );
							array_push( $terms_list, '<a href="' . esc_url( $category_link ) . '">' . esc_html( $value->name ) . '</a>' );
						}
						echo esc_attr( ( 'aboveTitle' === $attributes['displayPostTaxonomyAboveTitle'] ) && 'default' === $attributes['taxStyle'] ) ? wp_kses_post( implode( esc_html( $attributes['taxDivider'] ) . '&nbsp;', $terms_list ) ) : wp_kses_post( implode( ',&nbsp;', $terms_list ) );
						?>
					</span>
				</div>
				<?php
			}
			if ( 'highlighted' === $attributes['taxStyle'] && 'aboveTitle' === $attributes['displayPostTaxonomyAboveTitle'] ) {
				$terms_list = array();
				echo sprintf( '<div class="uagb-post__text">' );
				foreach ( $terms as $key => $value ) {
					// Get the URL of this category.
					$category_link = get_category_link( $value->term_id );
					echo sprintf(
						'<span class="%s">%s<a href="%s">%s</a></span>',
						esc_html( implode( ' ', $wrap ) ),
						( ( true === $attributes['hideTaxonomyIcon'] ) ? '<span class="dashicons-tag dashicons"></span>' : '' ),
						esc_url( $category_link ),
						esc_html( $value->name )
					);
				}
				echo sprintf( '</div>' );
			}
		}

		/**
		 * Render Post Meta HTML.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 0.0.1
		 */
		public function render_meta( $attributes ) {

			global $post;
			do_action( "uagb_single_post_before_meta_{$attributes['post_type']}", get_the_ID(), $attributes );

			$meta_sequence = array( 'author', 'date', 'comment', 'taxonomy' );
			$meta_sequence = apply_filters( "uagb_single_post_meta_sequence_{$attributes['post_type']}", $meta_sequence, get_the_ID(), $attributes );
			?>
			<div class='uagb-post__text uagb-post-grid-byline'>
				<?php
				foreach ( $meta_sequence as $key => $sequence ) {
					switch ( $sequence ) {
						case 'author':
							$this->render_meta_author( $attributes );
							break;

						case 'date':
							$this->render_meta_date( $attributes );
							break;

						case 'comment':
							$this->render_meta_comment( $attributes );
							break;

						case 'taxonomy':
							( 'withMeta' === $attributes['displayPostTaxonomyAboveTitle'] ) ? $this->render_meta_taxonomy( $attributes ) : '';
							break;

						default:
							break;
					}
				}
				?>
			</div>
			<?php
			do_action( "uagb_single_post_after_meta_{$attributes['post_type']}", get_the_ID(), $attributes );

		}

		/**
		 * Render Post Excerpt HTML.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 0.0.1
		 */
		public function render_excerpt( $attributes ) {

			if ( ! $attributes['displayPostExcerpt'] ) {
				return;
			}

			global $post;

			if ( 'full_post' === $attributes['displayPostContentRadio'] ) {
				$excerpt = get_the_content();
			} else {
				$excerpt_length_fallback = UAGB_Block_Helper::get_fallback_number( $attributes['excerptLength'], 'excerptLength', 'post-timeline' );
				$excerpt                 = UAGB_Helper::uagb_get_excerpt( $post->ID, $post->post_content, $excerpt_length_fallback );
			}

			$excerpt = apply_filters( "uagb_single_post_excerpt_{$attributes['post_type']}", $excerpt, get_the_ID(), $attributes );
			do_action( "uagb_single_post_before_excerpt_{$attributes['post_type']}", get_the_ID(), $attributes );
			?>
				<div class='uagb-post__text uagb-post__excerpt'>
					<?php echo wp_kses_post( $excerpt ); ?>
				</div>
			<?php
			do_action( "uagb_single_post_after_excerpt_{$attributes['post_type']}", get_the_ID(), $attributes );
		}

		/**
		 * Render Post CTA button HTML.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 0.0.1
		 */
		public function render_button( $attributes ) {
			$inherit_astra_secondary = $attributes['inheritFromThemeBtn'] && 'secondary' === $attributes['buttonType'];
			$button_type_class       = $inherit_astra_secondary ? 'ast-outline-button' : 'wp-block-button__link';

			// Initialize an empty string for border style.
			$border_style = $inherit_astra_secondary ? 'border-width: revert-layer;' : '';

			if ( ! $attributes['displayPostLink'] ) {
				return;
			}
			$target   = ( $attributes['newTab'] ) ? '_blank' : '_self';
			$cta_text = ( $attributes['ctaText'] ) ? $attributes['ctaText'] : __( 'Read More', 'ultimate-addons-for-gutenberg' );
			do_action( "uagb_single_post_before_cta_{$attributes['post_type']}", get_the_ID(), $attributes );
			$wrap_classes = 'uagb-post__text uagb-post__cta wp-block-button';
			$link_classes = $button_type_class . ' uagb-text-link';
			?>
			<div class="<?php echo esc_attr( $wrap_classes ); ?>">
				<a class="<?php echo esc_attr( $link_classes ); ?>" style="<?php echo esc_attr( $border_style ); ?>" href="<?php echo esc_url( apply_filters( "uagb_single_post_link_{$attributes['post_type']}", get_the_permalink(), get_the_ID(), $attributes ) ); ?>" target="<?php echo esc_attr( $target ); ?>" rel="bookmark noopener noreferrer"><?php echo wp_kses_post( $cta_text ); ?></a>
			</div>
			<?php
			do_action( "uagb_single_post_after_cta_{$attributes['post_type']}", get_the_ID(), $attributes );
		}

		/**
		 * Render Complete Box Link HTML.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 1.7.0
		 */
		public function render_complete_box_link( $attributes ) {
			if ( ! ( isset( $attributes['linkBox'] ) && $attributes['linkBox'] ) ) {
				return;
			}
			$target = ( $attributes['newTab'] ) ? '_blank' : '_self';
			?>
			<a class="uagb-post__link-complete-box" href="<?php echo esc_url( apply_filters( "uagb_single_post_link_{$attributes['post_type']}", get_the_permalink(), get_the_ID(), $attributes ) ); ?>" target="<?php echo esc_attr( $target ); ?>" rel="bookmark noopener noreferrer"></a>
			<?php
		}

		/**
		 * Disable canonical on Single Post.
		 *
		 * @param  string $redirect_url  The redirect URL.
		 * @param  string $requested_url The requested URL.
		 * @since  1.14.9
		 * @return bool|string
		 */
		public function override_canonical( $redirect_url, $requested_url ) {

			global $wp_query;

			if ( is_array( $wp_query->query ) ) {

				if ( true === $wp_query->is_singular
					&& - 1 === $wp_query->current_post
					&& true === $wp_query->is_paged
				) {
					$redirect_url = false;
				}
			}

			return $redirect_url;
		}
	}

	/**
	 *  Prepare if class 'UAGB_Post' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	UAGB_Post::get_instance();
}
