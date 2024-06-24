<?php
/**
 * UAGB Post.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UAGB_Post_Timeline' ) ) {

	/**
	 * Class UAGB_Post_Timeline.
	 */
	class UAGB_Post_Timeline {


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

			$inherit_from_theme = 'enabled' === ( 'deleted' !== UAGB_Admin_Helper::get_admin_settings_option( 'uag_btn_inherit_from_theme_fallback', 'deleted' ) ? 'disabled' : UAGB_Admin_Helper::get_admin_settings_option( 'uag_btn_inherit_from_theme', 'disabled' ) );

			register_block_type(
				'uagb/post-timeline',
				array(
					'attributes'      => array(
						'blockName'                  => array(
							'type'    => 'string',
							'default' => 'post-timeline',
						),
						'align'                      => array(
							'type'    => 'string',
							'default' => '',
						),
						'alignTablet'                => array(
							'type'    => 'string',
							'default' => '',
						),
						'alignMobile'                => array(
							'type'    => 'string',
							'default' => '',
						),
						'headingColor'               => array(
							'type'    => 'string',
							'default' => '#333',
						),
						'subHeadingColor'            => array(
							'type'    => 'string',
							'default' => '#333',
						),
						'separatorBg'                => array(
							'type'    => 'string',
							'default' => '#eee',
						),
						'backgroundColor'            => array(
							'type'    => 'string',
							'default' => '#eee',
						),
						'separatorColor'             => array(
							'type'    => 'string',
							'default' => '#eee',
						),
						'separatorFillColor'         => array(
							'type'    => 'string',
							'default' => '#0693e3',
						),
						'separatorBorder'            => array(
							'type'    => 'string',
							'default' => '#eee',
						),
						'borderFocus'                => array(
							'type'    => 'string',
							'default' => '#5cb85c',
						),
						'headingTag'                 => array(
							'type'    => 'string',
							'default' => 'h3',
						),
						'horizontalSpace'            => array(
							'type'    => 'number',
							'default' => 10,
						),
						'horizontalSpaceTablet'      => array(
							'type' => 'number',
						),
						'horizontalSpaceMobile'      => array(
							'type' => 'number',
						),
						'horizontalSpaceUnit'        => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'horizontalSpaceUnitTablet'  => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'horizontalSpaceUnitMobile'  => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'verticalSpace'              => array(
							'type'    => 'number',
							'default' => 15,
						),
						'verticalSpaceTablet'        => array(
							'type' => 'number',
						),
						'verticalSpaceMobile'        => array(
							'type' => 'number',
						),
						'verticalSpaceUnit'          => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'verticalSpaceUnitTablet'    => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'verticalSpaceUnitMobile'    => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'timelinAlignment'           => array(
							'type'    => 'string',
							'default' => 'center',
						),
						'timelinAlignmentTablet'     => array(
							'type' => 'string',
						),
						'timelinAlignmentMobile'     => array(
							'type' => 'string',
						),
						'arrowlinAlignment'          => array(
							'type'    => 'string',
							'default' => 'center',
						),
						'subHeadFontSizeType'        => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'subHeadFontSize'            => array(
							'type' => 'number',
						),
						'subHeadFontSizeTablet'      => array(
							'type' => 'number',
						),
						'subHeadFontSizeMobile'      => array(
							'type' => 'number',
						),
						'subHeadFontFamily'          => array(
							'type'    => 'string',
							'default' => '',
						),
						'subHeadFontWeight'          => array(
							'type' => 'string',
						),
						'subHeadFontStyle'           => array(
							'type' => 'string',
						),
						'subHeadLineHeightType'      => array(
							'type'    => 'string',
							'default' => 'em',
						),
						'subHeadLineHeight'          => array(
							'type' => 'number',
						),
						'subHeadLineHeightTablet'    => array(
							'type' => 'number',
						),
						'subHeadLineHeightMobile'    => array(
							'type' => 'number',
						),
						'subHeadLoadGoogleFonts'     => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'headSpace'                  => array(
							'type'    => 'number',
							'default' => 5,
						),
						'headSpaceTablet'            => array(
							'type' => 'number',
						),
						'headSpaceMobile'            => array(
							'type' => 'number',
						),
						'authorSpace'                => array(
							'type'    => 'number',
							'default' => 5,
						),
						'authorSpaceTablet'          => array(
							'type' => 'number',
						),
						'authorSpaceMobile'          => array(
							'type' => 'number',
						),
						'contentSpace'               => array(
							'type'    => 'number',
							'default' => 15,
						),
						'separatorwidth'             => array(
							'type'    => 'number',
							'default' => 3,
						),
						'borderwidth'                => array(
							'type'    => 'number',
							'default' => 0,
						),
						'iconColor'                  => array(
							'type'    => 'string',
							'default' => '#333',
						),
						'iconFocus'                  => array(
							'type'    => 'string',
							'default' => '#fff',
						),
						'iconBgFocus'                => array(
							'type'    => 'string',
							'default' => '#0693e3',
						),
						'authorColor'                => array(
							'type'    => 'string',
							'default' => '#333',
						),
						'authorFontSizeType'         => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'authorFontSize'             => array(
							'type'    => 'number',
							'default' => 11,
						),
						'authorFontSizeTablet'       => array(
							'type' => 'number',
						),
						'authorFontSizeMobile'       => array(
							'type' => 'number',
						),
						'authorFontFamily'           => array(
							'type'    => 'string',
							'default' => '',
						),
						'authorFontWeight'           => array(
							'type' => 'string',
						),
						'authorFontStyle'            => array(
							'type' => 'string',
						),
						'authorLineHeightType'       => array(
							'type'    => 'string',
							'default' => 'em',
						),
						'authorLineHeight'           => array(
							'type' => 'number',
						),
						'authorLineHeightTablet'     => array(
							'type' => 'number',
						),
						'authorLineHeightMobile'     => array(
							'type' => 'number',
						),
						'authorLoadGoogleFonts'      => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'ctaFontSizeType'            => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'ctaFontSize'                => array(
							'type'    => 'number',
							'default' => '',
						),
						'ctaFontSizeTablet'          => array(
							'type' => 'number',
						),
						'ctaFontSizeMobile'          => array(
							'type' => 'number',
						),
						'ctaFontFamily'              => array(
							'type'    => 'string',
							'default' => '',
						),
						'ctaFontWeight'              => array(
							'type' => 'string',
						),
						'ctaFontStyle'               => array(
							'type' => 'string',
						),
						'ctaLineHeightType'          => array(
							'type'    => 'string',
							'default' => 'em',
						),
						'ctaLineHeight'              => array(
							'type' => 'number',
						),
						'ctaLineHeightTablet'        => array(
							'type' => 'number',
						),
						'ctaLineHeightMobile'        => array(
							'type' => 'number',
						),
						'ctaLoadGoogleFonts'         => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'dateColor'                  => array(
							'type'    => 'string',
							'default' => '#333',
						),
						'dateFontsizeType'           => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'dateFontsize'               => array(
							'type'    => 'number',
							'default' => 12,
						),
						'dateFontsizeTablet'         => array(
							'type' => 'number',
						),
						'dateFontsizeMobile'         => array(
							'type' => 'number',
						),
						'dateFontSizeType'           => array(
							'type' => 'string',
						),
						'dateFontSize'               => array(
							'type' => 'number',
						),
						'dateFontSizeTablet'         => array(
							'type' => 'number',
						),
						'dateFontSizeMobile'         => array(
							'type' => 'number',
						),
						'dateFontFamily'             => array(
							'type'    => 'string',
							'default' => '',
						),
						'dateFontWeight'             => array(
							'type' => 'string',
						),
						'dateFontStyle'              => array(
							'type' => 'string',
						),
						'dateLineHeightType'         => array(
							'type'    => 'string',
							'default' => 'em',
						),
						'dateLineHeight'             => array(
							'type' => 'number',
						),
						'dateLineHeightTablet'       => array(
							'type' => 'number',
						),
						'dateLineHeightMobile'       => array(
							'type' => 'number',
						),
						'dateLoadGoogleFonts'        => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'connectorBgsize'            => array(
							'type'    => 'number',
							'default' => 35,
						),
						'dateBottomspace'            => array(
							'type'    => 'number',
							'default' => 5,
						),
						'dateBottomspaceMobile'      => array(
							'type' => 'number',
						),
						'dateBottomspaceTablet'      => array(
							'type' => 'number',
						),
						'headFontSizeType'           => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'headFontSize'               => array(
							'type' => 'number',
						),
						'headFontSizeTablet'         => array(
							'type' => 'number',
						),
						'headFontSizeMobile'         => array(
							'type' => 'number',
						),
						'headFontFamily'             => array(
							'type'    => 'string',
							'default' => '',
						),
						'headFontWeight'             => array(
							'type' => 'string',
						),
						'headFontStyle'              => array(
							'type' => 'string',
						),
						'headLineHeightType'         => array(
							'type'    => 'string',
							'default' => 'em',
						),
						'headLineHeight'             => array(
							'type' => 'number',
						),
						'headLineHeightTablet'       => array(
							'type' => 'number',
						),
						'headLineHeightMobile'       => array(
							'type' => 'number',
						),
						'headLoadGoogleFonts'        => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'categories'                 => array(
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
						'postsToShow'                => array(
							'type'    => 'number',
							'default' => 6,
						),
						'postsOffset'                => array(
							'type'    => 'number',
							'default' => 0,
						),
						'displayPostDate'            => array(
							'type'    => 'boolean',
							'default' => true,
						),
						'dateFormat'                 => array(
							'type'    => 'string',
							'default' => 'F j, Y',
						),
						'displayPostExcerpt'         => array(
							'type'    => 'boolean',
							'default' => true,
						),
						'displayPostAuthor'          => array(
							'type'    => 'boolean',
							'default' => true,
						),
						'displayPostImage'           => array(
							'type'    => 'boolean',
							'default' => true,
						),
						'displayPostLink'            => array(
							'type'    => 'boolean',
							'default' => true,
						),
						'inheritFromTheme'           => array(
							'type'    => 'boolean',
							'default' => $inherit_from_theme,
						),
						'buttonType'                 => array(
							'type'    => 'string',
							'default' => 'primary',
						),
						'exerptLength'               => array(
							'type'    => 'number',
							'default' => 15,
						),
						'postLayout'                 => array(
							'type'    => 'string',
							'default' => 'grid',
						),
						'columns'                    => array(
							'type'    => 'number',
							'default' => 2,
						),
						'width'                      => array(
							'type'    => 'string',
							'default' => 'wide',
						),
						'order'                      => array(
							'type'    => 'string',
							'default' => 'desc',
						),
						'orderBy'                    => array(
							'type'    => 'string',
							'default' => 'date',
						),
						'imageSize'                  => array(
							'type'    => 'string',
							'default' => 'large',
						),
						'readMoreText'               => array(
							'type'    => 'string',
							'default' => __( 'Read More', 'ultimate-addons-for-gutenberg' ),
						),
						'block_id'                   => array(
							'type'    => 'string',
							'default' => 'not_set',
						),
						'icon'                       => array(
							'type'    => 'string',
							'default' => 'calendar-days',
						),
						'borderRadius'               => array(
							'type'    => 'number',
							'default' => 2,
						),
						'borderRadiusTablet'         => array(
							'type' => 'number',
						),
						'borderRadiusMobile'         => array(
							'type' => 'number',
						),
						'bgPadding'                  => array(
							'type'    => 'number',
							'default' => 20,
						),
						'contentPadding'             => array(
							'type'    => 'number',
							'default' => 10,
						),
						'ctaBottomSpacing'           => array(
							'type'    => 'number',
							'default' => 0,
						),
						'ctaBottomSpacingTablet'     => array(
							'type' => 'number',
						),
						'ctaBottomSpacingMobile'     => array(
							'type' => 'number',
						),
						'headTopSpacing'             => array(
							'type'    => 'number',
							'default' => 0,
						),
						'headTopSpacingTablet'       => array(
							'type' => 'number',
						),
						'headTopSpacingMobile'       => array(
							'type' => 'number',
						),
						'iconSize'                   => array(
							'type'    => 'number',
							'default' => 15,
						),
						'ctaColor'                   => array(
							'type' => 'string',
						),
						'ctaBackground'              => array(
							'type' => 'string',
						),
						'stack'                      => array(
							'type'    => 'string',
							'default' => 'tablet',
						),
						'linkTarget'                 => array(
							'type'    => 'boolean',
							'default' => false,
						),
						// Exclude Current Post.
						'excludeCurrentPost'         => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'leftMargin'                 => array(
							'type' => 'number',
						),
						'rightMargin'                => array(
							'type' => 'number',
						),
						'topMargin'                  => array(
							'type' => 'number',
						),
						'bottomMargin'               => array(
							'type' => 'number',
						),
						'leftMarginTablet'           => array(
							'type' => 'number',
						),
						'rightMarginTablet'          => array(
							'type' => 'number',
						),
						'topMarginTablet'            => array(
							'type' => 'number',
						),
						'bottomMarginTablet'         => array(
							'type' => 'number',
						),
						'leftMarginMobile'           => array(
							'type' => 'number',
						),
						'rightMarginMobile'          => array(
							'type' => 'number',
						),
						'topMarginMobile'            => array(
							'type' => 'number',
						),
						'bottomMarginMobile'         => array(
							'type' => 'number',
						),
						'marginUnit'                 => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'mobileMarginUnit'           => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'tabletMarginUnit'           => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'marginLink'                 => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'leftPadding'                => array(
							'type' => 'number',
						),
						'rightPadding'               => array(
							'type' => 'number',
						),
						'topPadding'                 => array(
							'type' => 'number',
						),
						'bottomPadding'              => array(
							'type' => 'number',
						),
						'leftPaddingTablet'          => array(
							'type' => 'number',
						),
						'rightPaddingTablet'         => array(
							'type' => 'number',
						),
						'topPaddingTablet'           => array(
							'type' => 'number',
						),
						'bottomPaddingTablet'        => array(
							'type' => 'number',
						),
						'leftPaddingMobile'          => array(
							'type' => 'number',
						),
						'rightPaddingMobile'         => array(
							'type' => 'number',
						),
						'topPaddingMobile'           => array(
							'type' => 'number',
						),
						'bottomPaddingMobile'        => array(
							'type' => 'number',
						),
						'paddingUnit'                => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'mobilePaddingUnit'          => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'tabletPaddingUnit'          => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'paddingLink'                => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'headTransform'              => array(
							'type' => 'string',
						),
						'authorTransform'            => array(
							'type' => 'string',
						),
						'subHeadTransform'           => array(
							'type' => 'string',
						),
						'dateTransform'              => array(
							'type' => 'string',
						),
						'ctaTransform'               => array(
							'type' => 'string',
						),
						'headDecoration'             => array(
							'type' => 'string',
						),
						'authorDecoration'           => array(
							'type' => 'string',
						),
						'subHeadDecoration'          => array(
							'type' => 'string',
						),
						'dateDecoration'             => array(
							'type' => 'string',
						),
						'ctaDecoration'              => array(
							'type' => 'string',
						),
						'isPreview'                  => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'headLetterSpacing'          => array(
							'type'    => 'number',
							'default' => '',
						),
						'headLetterSpacingType'      => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'headLetterSpacingMobile'    => array(
							'type' => 'number',
						),
						'headLetterSpacingTablet'    => array(
							'type' => 'number',
						),
						'subHeadLetterSpacing'       => array(
							'type'    => 'number',
							'default' => '',
						),
						'subHeadLetterSpacingType'   => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'subHeadLetterSpacingMobile' => array(
							'type' => 'number',
						),
						'subHeadLetterSpacingTablet' => array(
							'type' => 'number',
						),
						'ctaLetterSpacing'           => array(
							'type'    => 'number',
							'default' => '',
						),
						'ctaLetterSpacingType'       => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'ctaLetterSpacingMobile'     => array(
							'type' => 'number',
						),
						'ctaLetterSpacingTablet'     => array(
							'type' => 'number',
						),
						'dateLetterSpacing'          => array(
							'type'    => 'number',
							'default' => '',
						),
						'dateLetterSpacingType'      => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'dateLetterSpacingMobile'    => array(
							'type' => 'number',
						),
						'dateLetterSpacingTablet'    => array(
							'type' => 'number',
						),
						'authorLetterSpacing'        => array(
							'type'    => 'number',
							'default' => '',
						),
						'authorLetterSpacingType'    => array(
							'type'    => 'string',
							'default' => 'px',
						),
						'authorLetterSpacingMobile'  => array(
							'type' => 'number',
						),
						'authorLetterSpacingTablet'  => array(
							'type' => 'number',
						),
					),
					'render_callback' => array( $this, 'post_timeline_callback' ),
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
		public function post_timeline_callback( $attributes ) {

			$attributes['post_type'] = 'timeline';

			$recent_posts = UAGB_Helper::get_query( $attributes, 'timeline' );
			$block_id     = 'uagb-block-' . $attributes['block_id'];

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

			$outer_class = 'uagb-timeline__outer-wrap';

			$main_classes = array(
				'wp-block-uagb-post-timeline',
				$outer_class,
				$block_id,
				$desktop_class,
				$tab_class,
				$mob_class,
				$zindex_extention_enabled ? 'uag-blocks-common-selector' : '',
			);

			ob_start();
			?>
			<div class = "
			<?php
			echo esc_attr( implode( ' ', $main_classes ) );
			echo esc_html( $this->get_classes( $attributes ) );
			?>
			<?php
			echo esc_html( $this->get_classes( $attributes ) );
			?>
			"
			style="<?php echo esc_attr( implode( '', $zindex_wrap ) ); ?>" >
				<?php
				if ( empty( $recent_posts ) ) {
					esc_html_e( 'No posts found', 'ultimate-addons-for-gutenberg' );
				} else {
					$this->get_post_html( $attributes, $recent_posts );
				}
				?>
				<div class = "uagb-timeline__line" >
					<div class = "uagb-timeline__line__inner"></div>
				</div>
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Renders the post timeline block on server.
		 *
		 * @param array  $attributes Array of block attributes.
		 *
		 * @param object $query WP_Query object.
		 * @since 0.0.1
		 */
		public function get_post_html( $attributes, $query ) {
			?>
				<?php
				$index = 0;
				while ( $query->have_posts() ) {
					$query->the_post();
					global $post;
					$this->render_single( $attributes, $index, $post );
					$index++;
				}
				wp_reset_postdata();
				?>
			<?php
		}

		/**
		 * Renders the post timeline single block.
		 *
		 * @param array  $attributes Array of block attributes.
		 * @param int    $index Index value of current post.
		 * @param object $post Current Post object.
		 *
		 * @since 0.0.1
		 */
		public function render_single( $attributes, $index, $post ) {

			$display_inner_date  = ( 'center' === $attributes['timelinAlignment'] ) ? true : false;
			$content_align_class = $this->get_align_classes( $attributes, $index );
			$day_align_class     = $this->get_day_align_classes( $attributes, $index );

			?>
			<article class = "uagb-timeline__field <?php echo esc_html( $content_align_class ); ?>">
					<?php $this->get_icon( $attributes ); ?>
					<div class = "<?php echo esc_html( $day_align_class ); ?> uagb-timeline__events-inner-new" >
						<div class="uagb-timeline__events-inner--content">
								<?php $this->get_date( $attributes, 'uagb-timeline__date-hide uagb-timeline__inner-date-new' ); ?>
							<?php ( $attributes['displayPostImage'] ) ? $this->get_image( $attributes ) : ''; ?>
								<?php
									$this->get_title( $attributes );
									$this->get_author( $attributes, $post->post_author );
									$this->get_excerpt( $attributes );
									$this->get_cta( $attributes );
								?>
								<div class = "uagb-timeline__arrow"></div>
						</div>
					</div>
					<?php if ( $display_inner_date ) { ?>
						<?php $this->get_date( $attributes, 'uagb-timeline__date-new' ); ?>
					<?php } ?>
			</article>
			<?php
		}

		/**
		 * Function Name: get_icon.
		 *
		 * @param  array $attributes attribute array.
		 */
		public function get_icon( $attributes ) {
			?>
			<div class = "uagb-timeline__marker uagb-timeline__out-view-icon" >
				<span class = "uagb-timeline__icon-new uagb-timeline__out-view-icon" ><?php UAGB_Helper::render_svg_html( $attributes['icon'] ); ?></span>
			</div>
			<?php
		}

		/**
		 * Function Name: get_image.
		 *
		 * @param  array $attributes attribute array.
		 */
		public function get_image( $attributes ) {

			if ( ! get_the_post_thumbnail_url() ) {
				return;
			}

			$target = ( isset( $attributes['linkTarget'] ) && ( true === $attributes['linkTarget'] ) ) ? '_blank' : '_self';
			do_action( "uagb_single_post_before_featured_image_{$attributes['post_type']}", get_the_ID(), $attributes );
			?>
				<a class='uagb-timeline__image' href="<?php echo esc_url( apply_filters( "uagb_single_post_link_{$attributes['post_type']}", get_the_permalink(), get_the_ID(), $attributes ) ); ?>" target="<?php echo esc_attr( $target ); ?>" rel="noopener noreferrer"><?php echo wp_get_attachment_image( get_post_thumbnail_id(), $attributes['imageSize'] ); ?>
				</a>
			<?php
			do_action( "uagb_single_post_after_featured_image_{$attributes['post_type']}", get_the_ID(), $attributes );
		}

		/**
		 * Function Name: get_date.
		 *
		 * @param  array  $attributes attribute array.
		 * @param  string $classname attribute string.
		 */
		public function get_date( $attributes, $classname ) {

			global $post;
			$post_id = $post->ID;
			?>
			<div datetime="<?php echo esc_attr( get_the_date( 'c', $post_id ) ); ?>" class="<?php echo esc_attr( $classname ); ?>">
				<?php
				if ( isset( $attributes['displayPostDate'] ) && $attributes['displayPostDate'] ) {
					echo esc_html( get_the_date( $attributes['dateFormat'], $post_id ) );
				}
				?>
			</div>
			<?php
		}

		/**
		 * Function Name: get_title.
		 *
		 * @param  array $attributes attribute array.
		 */
		public function get_title( $attributes ) {

			$target = ( isset( $attributes['linkTarget'] ) && ( true === $attributes['linkTarget'] ) ) ? '_blank' : '_self';

			$array_of_allowed_HTML = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'span', 'p' );
			$tag                   = UAGB_Helper::title_tag_allowed_html( $attributes['headingTag'], $array_of_allowed_HTML, 'h3' );
			global $post;
			?>
				<?php do_action( "uagb_single_post_before_title_{$attributes['post_type']}", get_the_ID(), $attributes ); ?>
				<<?php echo esc_html( $tag ); ?> class="uagb-timeline__heading" >
					<a href="<?php echo esc_url( apply_filters( "uagb_single_post_link_{$attributes['post_type']}", get_the_permalink(), get_the_ID(), $attributes ) ); ?>" target="<?php echo esc_attr( $target ); ?>" rel="noopener noreferrer"><?php the_title(); ?></a>
				</<?php echo esc_html( $tag ); ?>>
				<?php do_action( "uagb_single_post_after_title_{$attributes['post_type']}", get_the_ID(), $attributes ); ?>
			<?php
		}

		/**
		 * Function Name: get_cta.
		 *
		 * @param  array $attributes attribute array.
		 */
		public function get_cta( $attributes ) {

			$inherit_astra_secondary = $attributes['inheritFromTheme'] && 'secondary' === $attributes['buttonType'];
			$button_type_class       = $inherit_astra_secondary ? 'ast-outline-button' : 'wp-block-button__link';

			// Initialize an empty string for border style.
			$border_style  = $inherit_astra_secondary ? 'border-width: revert-layer;' : '';
			$cta_btn_class = "uagb-timeline__link $button_type_class";

			if ( ! $attributes['displayPostLink'] ) {
				return;
			}
			$target = ( isset( $attributes['linkTarget'] ) && ( true === $attributes['linkTarget'] ) ) ? '_blank' : '_self';
			do_action( "uagb_single_post_before_cta_{$attributes['post_type']}", get_the_ID(), $attributes );
			?>
			<div class="uagb-timeline__link_parent wp-block-button">
				<a class="<?php echo esc_attr( $cta_btn_class ); ?>" style="<?php echo esc_attr( $border_style ); ?>" href="<?php echo esc_url( apply_filters( "uagb_single_post_link_{$attributes['post_type']}", get_the_permalink(), get_the_ID(), $attributes ) ); ?>" target="<?php echo esc_attr( $target ); ?>" rel=" noopener noreferrer"><?php echo wp_kses_post( $attributes['readMoreText'] ); ?></a>
			</div>
			<?php
			do_action( "uagb_single_post_after_cta_{$attributes['post_type']}", get_the_ID(), $attributes );
		}

		/**
		 * Function get_author.
		 *
		 * @param  array $attributes attribute.
		 * @param  array $author attribute.
		 */
		public function get_author( $attributes, $author ) {

			$output = '';
			do_action( "uagb_single_post_before_meta_{$attributes['post_type']}", get_the_ID(), $attributes );
			if ( isset( $attributes['displayPostAuthor'] ) && $attributes['displayPostAuthor'] ) {
				?>
				<span class="dashicons-admin-users dashicons"></span>
				<a class="uagb-timeline__author-link" href="<?php echo esc_url( get_author_posts_url( $author ) ); ?>"><?php echo esc_html( get_the_author_meta( 'display_name', $author ) ); ?></a>
				<?php
			}
			do_action( "uagb_single_post_after_meta_{$attributes['post_type']}", get_the_ID(), $attributes );
		}

		/**
		 * Function get_excerpt.
		 *
		 * @param  array $attributes attribute.
		 */
		public function get_excerpt( $attributes ) {

			if ( ! $attributes['displayPostExcerpt'] ) {
				return;
			}

			global $post;

			$excerpt_length_fallback = UAGB_Block_Helper::get_fallback_number( $attributes['exerptLength'], 'exerptLength', $attributes['blockName'] );

			$excerpt = UAGB_Helper::uagb_get_excerpt( $post->ID, $post->post_content, $excerpt_length_fallback );

			$excerpt = apply_filters( "uagb_single_post_excerpt_{$attributes['post_type']}", $excerpt, get_the_ID(), $attributes );
			do_action( "uagb_single_post_before_excerpt_{$attributes['post_type']}", get_the_ID(), $attributes );
			?>
			<div class="uagb-timeline-desc-content">
				<?php echo wp_kses_post( $excerpt ); ?>
			</div>
			<?php
			do_action( "uagb_single_post_after_excerpt_{$attributes['post_type']}", get_the_ID(), $attributes );
		}

		/**
		 * Function Name: get_classes .
		 *
		 * @param  array $attributes array of setting.
		 * @return string             class name.
		 */
		public function get_classes( $attributes ) {

			// Arrow position.
			$classes = array();
			if ( isset( $attributes['arrowlinAlignment'] ) && '' !== $attributes['arrowlinAlignment'] ) {
				$classes[] = 'uagb-timeline__arrow-' . $attributes['arrowlinAlignment'];
			}
			// Alignmnet.
			if ( isset( $attributes['timelinAlignment'] ) && '' !== $attributes['timelinAlignment'] ) {
				$classes[] = 'uagb-timeline__' . $attributes['timelinAlignment'] . '-block';
			}

			if ( isset( $attributes['displayPostLink'] ) && '' !== $attributes['displayPostLink'] ) {
				$classes[] = 'uagb_timeline__cta-enable';
			}

			$classes[] = 'uagb-timeline';
			$classes[] = 'uagb-timeline__content-wrap';

			return implode( ' ', $classes );
		}

		/**
		 * Function Name: get_align_classes description.
		 *
		 * @param array  $attributes attribute array.
		 * @param string $index_val  post index.
		 * @return string            output HTML/String.
		 */
		public function get_align_classes( $attributes, $index_val ) {

			$classes   = array();
			$classes[] = '';
			if ( isset( $attributes['timelinAlignment'] ) && '' !== $attributes['timelinAlignment'] ) {
				if ( 'center' !== $attributes['timelinAlignment'] ) {
					$classes[] = 'uagb-timeline__' . $attributes['timelinAlignment'];
				} else {
					$classes[] = ( 0 === $index_val % 2 ) ? 'uagb-timeline__right' : 'uagb-timeline__left';
				}
			}

			return implode( ' ', $classes );
		}

		/**
		 * Function Name: get_day_align_classes description.
		 *
		 * @param array  $attributes attribute array.
		 * @param string $index_val  post index.
		 * @return string            output HTML/String.
		 */
		public function get_day_align_classes( $attributes, $index_val ) {

			$classes   = array();
			$classes[] = 'uagb-timeline__day-new';
			if ( isset( $attributes['timelinAlignment'] ) && '' !== $attributes['timelinAlignment'] ) {
				if ( 'center' === $attributes['timelinAlignment'] ) {
					$classes[] = ( 0 === $index_val % 2 ) ? 'uagb-timeline__day-right' : 'uagb-timeline__day-left';
				} else {
					$classes[] = 'uagb-timeline__day-' . $attributes['timelinAlignment'];
				}
			}

			return implode( ' ', $classes );
		}

	}

	/**
	 *  Prepare if class 'UAGB_Post_Timeline' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	UAGB_Post_Timeline::get_instance();
}
