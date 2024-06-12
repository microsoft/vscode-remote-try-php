<?php
/**
 * UAGB - Contact Form 7 Designer.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UAGB_CF7_Styler' ) ) {

	/**
	 * Class UAGB_CF7_Styler.
	 */
	class UAGB_CF7_Styler {

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance;

		/**
		 *  Initiator
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

			// Activation hook.
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
			$field_border_attribute = array();
			$btn_border_attribute   = array();

			if ( method_exists( 'UAGB_Block_Helper', 'uag_generate_php_border_attribute' ) ) {

				$field_border_attribute = UAGB_Block_Helper::uag_generate_php_border_attribute( 'input' );
				$btn_border_attribute   = UAGB_Block_Helper::uag_generate_php_border_attribute( 'btn' );

			}

			$enable_legacy_blocks = UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_legacy_blocks', ( 'yes' === get_option( 'uagb-old-user-less-than-2' ) ) ? 'yes' : 'no' );

			if ( 'yes' === get_option( 'uagb-old-user-less-than-2' ) || 'yes' === $enable_legacy_blocks ) {
				register_block_type(
					'uagb/cf7-styler',
					array(
						'attributes'      => array_merge(
							$field_border_attribute,
							$btn_border_attribute,
							array(
								'block_id'                 => array(
									'type' => 'string',
								),
								'align'                    => array(
									'type'    => 'string',
									'default' => 'left',
								),
								'className'                => array(
									'type' => 'string',
								),
								'formId'                   => array(
									'type'    => 'string',
									'default' => '0',
								),
								'isHtml'                   => array(
									'type' => 'boolean',
								),
								'formJson'                 => array(
									'type'    => 'object',
									'default' => null,
								),
								'fieldStyle'               => array(
									'type'    => 'string',
									'default' => 'box',
								),
								'fieldVrPadding'           => array(
									'type'    => 'number',
									'default' => 10,
								),
								'fieldHrPadding'           => array(
									'type'    => 'number',
									'default' => 10,
								),
								'fieldBgColor'             => array(
									'type'    => 'string',
									'default' => '#fafafa',
								),
								'fieldLabelColor'          => array(
									'type'    => 'string',
									'default' => '#333',
								),
								'fieldInputColor'          => array(
									'type'    => 'string',
									'default' => '#333',
								),
								'buttonAlignment'          => array(
									'type'    => 'string',
									'default' => 'left',
								),
								'buttonAlignmentTablet'    => array(
									'type'    => 'string',
									'default' => '',
								),
								'buttonAlignmentMobile'    => array(
									'type'    => 'string',
									'default' => '',
								),
								'buttonVrPadding'          => array(
									'type'    => 'number',
									'default' => 10,
								),
								'buttonHrPadding'          => array(
									'type'    => 'number',
									'default' => 25,
								),
								'buttonTextColor'          => array(
									'type'    => 'string',
									'default' => '#333',
								),
								'buttonBgColor'            => array(
									'type'    => 'string',
									'default' => '',
								),
								'buttonTextHoverColor'     => array(
									'type'    => 'string',
									'default' => '#333',
								),
								'buttonBgHoverColor'       => array(
									'type'    => 'string',
									'default' => '',
								),
								'fieldSpacing'             => array(
									'type'    => 'number',
									'default' => '',
								),
								'fieldSpacingTablet'       => array(
									'type' => 'number',
								),
								'fieldSpacingMobile'       => array(
									'type' => 'number',
								),
								'fieldLabelSpacing'        => array(
									'type'    => 'number',
									'default' => '',
								),
								'fieldLabelSpacingTablet'  => array(
									'type' => 'number',
								),
								'fieldLabelSpacingMobile'  => array(
									'type' => 'number',
								),
								'labelFontSize'            => array(
									'type'    => 'number',
									'default' => '',
								),
								'labelFontSizeType'        => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'labelFontSizeTablet'      => array(
									'type' => 'number',
								),
								'labelFontSizeMobile'      => array(
									'type' => 'number',
								),
								'labelFontFamily'          => array(
									'type'    => 'string',
									'default' => 'Default',
								),
								'labelFontWeight'          => array(
									'type' => 'string',
								),
								'labelLineHeightType'      => array(
									'type'    => 'string',
									'default' => 'em',
								),
								'labelLineHeight'          => array(
									'type' => 'number',
								),
								'labelLineHeightTablet'    => array(
									'type' => 'number',
								),
								'labelLineHeightMobile'    => array(
									'type' => 'number',
								),
								'labelLoadGoogleFonts'     => array(
									'type'    => 'boolean',
									'default' => false,
								),
								'inputFontSize'            => array(
									'type'    => 'number',
									'default' => '',
								),
								'inputFontSizeType'        => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'inputFontSizeTablet'      => array(
									'type' => 'number',
								),
								'inputFontSizeMobile'      => array(
									'type' => 'number',
								),
								'inputFontFamily'          => array(
									'type'    => 'string',
									'default' => 'Default',
								),
								'inputFontWeight'          => array(
									'type' => 'string',
								),
								'inputLineHeightType'      => array(
									'type'    => 'string',
									'default' => 'em',
								),
								'inputLineHeight'          => array(
									'type' => 'number',
								),
								'inputLineHeightTablet'    => array(
									'type' => 'number',
								),
								'inputLineHeightMobile'    => array(
									'type' => 'number',
								),
								'inputLoadGoogleFonts'     => array(
									'type'    => 'boolean',
									'default' => false,
								),
								'buttonFontSize'           => array(
									'type'    => 'number',
									'default' => '',
								),
								'buttonFontSizeType'       => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'buttonFontSizeTablet'     => array(
									'type' => 'number',
								),
								'buttonFontSizeMobile'     => array(
									'type' => 'number',
								),
								'buttonFontFamily'         => array(
									'type'    => 'string',
									'default' => 'Default',
								),
								'buttonFontWeight'         => array(
									'type' => 'string',
								),
								'buttonLineHeightType'     => array(
									'type'    => 'string',
									'default' => 'em',
								),
								'buttonLineHeight'         => array(
									'type' => 'number',
								),
								'buttonLineHeightTablet'   => array(
									'type' => 'number',
								),
								'buttonLineHeightMobile'   => array(
									'type' => 'number',
								),
								'buttonLoadGoogleFonts'    => array(
									'type'    => 'boolean',
									'default' => false,
								),
								'enableOveride'            => array(
									'type'    => 'boolean',
									'default' => true,
								),
								'radioCheckSize'           => array(
									'type'    => 'number',
									'default' => '',
								),
								'radioCheckSizeTablet'     => array(
									'type' => 'number',
								),
								'radioCheckSizeMobile'     => array(
									'type' => 'number',
								),
								'radioCheckBgColor'        => array(
									'type'    => 'string',
									'default' => '',
								),
								'radioCheckSelectColor'    => array(
									'type'    => 'string',
									'default' => '',
								),
								'radioCheckLableColor'     => array(
									'type'    => 'string',
									'default' => '',
								),
								'radioCheckBorderColor'    => array(
									'type'    => 'string',
									'default' => '#abb8c3',
								),
								'radioCheckBorderWidth'    => array(
									'type'    => 'number',
									'default' => '',
								),
								'radioCheckBorderWidthTablet' => array(
									'type'    => 'number',
									'default' => '1',
								),
								'radioCheckBorderWidthMobile' => array(
									'type'    => 'number',
									'default' => '1',
								),
								'radioCheckBorderWidthUnit' => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'radioCheckBorderRadius'   => array(
									'type'    => 'number',
									'default' => '',
								),
								'radioCheckFontSize'       => array(
									'type'    => 'number',
									'default' => '',
								),
								'radioCheckFontSizeType'   => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'radioCheckFontSizeTablet' => array(
									'type' => 'number',
								),
								'radioCheckFontSizeMobile' => array(
									'type' => 'number',
								),
								'radioCheckFontFamily'     => array(
									'type'    => 'string',
									'default' => 'Default',
								),
								'radioCheckFontWeight'     => array(
									'type' => 'string',
								),
								'radioCheckLineHeightType' => array(
									'type'    => 'string',
									'default' => 'em',
								),
								'radioCheckLineHeight'     => array(
									'type' => 'number',
								),
								'radioCheckLineHeightTablet' => array(
									'type' => 'number',
								),
								'radioCheckLineHeightMobile' => array(
									'type' => 'number',
								),
								'radioCheckLoadGoogleFonts' => array(
									'type'    => 'boolean',
									'default' => false,
								),
								'validationMsgPosition'    => array(
									'type'    => 'string',
									'default' => 'default',
								),
								'validationMsgColor'       => array(
									'type'    => 'string',
									'default' => '#ff0000',
								),
								'validationMsgBgColor'     => array(
									'type'    => 'string',
									'default' => '',
								),
								'enableHighlightBorder'    => array(
									'type'    => 'boolean',
									'default' => false,
								),
								'highlightBorderColor'     => array(
									'type'    => 'string',
									'default' => '#ff0000',
								),
								'validationMsgFontSize'    => array(
									'type'    => 'number',
									'default' => '',
								),
								'validationMsgFontSizeType' => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'validationMsgFontSizeTablet' => array(
									'type' => 'number',
								),
								'validationMsgFontSizeMobile' => array(
									'type' => 'number',
								),
								'validationMsgFontFamily'  => array(
									'type'    => 'string',
									'default' => 'Default',
								),
								'validationMsgFontWeight'  => array(
									'type' => 'string',
								),
								'validationMsgLineHeightType' => array(
									'type'    => 'string',
									'default' => 'em',
								),
								'validationMsgLineHeight'  => array(
									'type' => 'number',
								),
								'validationMsgLineHeightTablet' => array(
									'type' => 'number',
								),
								'validationMsgLineHeightMobile' => array(
									'type' => 'number',
								),
								'validationMsgLoadGoogleFonts' => array(
									'type'    => 'boolean',
									'default' => false,
								),
								'successMsgColor'          => array(
									'type'    => 'string',
									'default' => '',
								),
								'successMsgBgColor'        => array(
									'type'    => 'string',
									'default' => '',
								),
								'successMsgBorderColor'    => array(
									'type'    => 'string',
									'default' => '',
								),
								'errorMsgColor'            => array(
									'type'    => 'string',
									'default' => '',
								),
								'errorMsgBgColor'          => array(
									'type'    => 'string',
									'default' => '',
								),
								'errorMsgBorderColor'      => array(
									'type'    => 'string',
									'default' => '',
								),
								'msgBorderSize'            => array(
									'type'    => 'number',
									'default' => '',
								),
								'msgBorderSizeUnit'        => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'msgBorderRadius'          => array(
									'type'    => 'number',
									'default' => '',
								),
								'msgVrPadding'             => array(
									'type'    => 'number',
									'default' => '',
								),
								'msgHrPadding'             => array(
									'type'    => 'number',
									'default' => '',
								),
								'msgFontSize'              => array(
									'type'    => 'number',
									'default' => '',
								),
								'msgFontSizeType'          => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'msgFontSizeTablet'        => array(
									'type' => 'number',
								),
								'msgFontSizeMobile'        => array(
									'type' => 'number',
								),
								'msgFontFamily'            => array(
									'type'    => 'string',
									'default' => 'Default',
								),
								'msgFontWeight'            => array(
									'type' => 'string',
								),
								'msgLineHeightType'        => array(
									'type'    => 'string',
									'default' => 'em',
								),
								'msgLineHeight'            => array(
									'type' => 'number',
								),
								'msgLineHeightTablet'      => array(
									'type' => 'number',
								),
								'msgLineHeightMobile'      => array(
									'type' => 'number',
								),
								'msgLoadGoogleFonts'       => array(
									'type'    => 'boolean',
									'default' => false,
								),
								'radioCheckBorderRadiusType' => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'msgBorderRadiusType'      => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'fieldBorderRadiusType'    => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'buttonBorderRadiusType'   => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'messageTopPaddingDesktop' => array(
									'type' => 'number',
								),
								'messageBottomPaddingDesktop' => array(
									'type' => 'number',
								),
								'messageLeftPaddingDesktop' => array(
									'type' => 'number',
								),
								'messageRightPaddingDesktop' => array(
									'type' => 'number',
								),

								'messageTopPaddingTablet'  => array(
									'type' => 'number',
								),
								'messageBottomPaddingTablet' => array(
									'type' => 'number',
								),
								'messageLeftPaddingTablet' => array(
									'type' => 'number',
								),
								'messageRightPaddingTablet' => array(
									'type' => 'number',
								),

								'messageTopPaddingMobile'  => array(
									'type' => 'number',
								),
								'messageBottomPaddingMobile' => array(
									'type' => 'number',
								),
								'messageLeftPaddingMobile' => array(
									'type' => 'number',
								),
								'messageRightPaddingMobile' => array(
									'type' => 'number',
								),
								'messagePaddingTypeDesktop' => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'messageSpacingLink'       => array(
									'type'    => 'boolean',
									'default' => false,
								),

								'buttonTopPaddingDesktop'  => array(
									'type' => 'number',
								),
								'buttonBottomPaddingDesktop' => array(
									'type' => 'number',
								),
								'buttonLeftPaddingDesktop' => array(
									'type' => 'number',
								),
								'buttonRightPaddingDesktop' => array(
									'type' => 'number',
								),

								'buttonTopPaddingTablet'   => array(
									'type' => 'number',
								),
								'buttonBottomPaddingTablet' => array(
									'type' => 'number',
								),
								'buttonLeftPaddingTablet'  => array(
									'type' => 'number',
								),
								'buttonRightPaddingTablet' => array(
									'type' => 'number',
								),

								'buttonTopPaddingMobile'   => array(
									'type' => 'number',
								),
								'buttonBottomPaddingMobile' => array(
									'type' => 'number',
								),
								'buttonLeftPaddingMobile'  => array(
									'type' => 'number',
								),
								'buttonRightPaddingMobile' => array(
									'type' => 'number',
								),
								'buttonPaddingTypeDesktop' => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'buttonPaddingTypeTablet'  => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'buttonPaddingTypeMobile'  => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'buttonSpacingLink'        => array(
									'type'    => 'boolean',
									'default' => false,
								),

								'fieldTopPaddingDesktop'   => array(
									'type' => 'number',
								),
								'fieldBottomPaddingDesktop' => array(
									'type' => 'number',
								),
								'fieldLeftPaddingDesktop'  => array(
									'type' => 'number',
								),
								'fieldRightPaddingDesktop' => array(
									'type' => 'number',
								),

								'fieldTopPaddingTablet'    => array(
									'type' => 'number',
								),
								'fieldBottomPaddingTablet' => array(
									'type' => 'number',
								),
								'fieldLeftPaddingTablet'   => array(
									'type' => 'number',
								),
								'fieldRightPaddingTablet'  => array(
									'type' => 'number',
								),

								'fieldTopPaddingMobile'    => array(
									'type' => 'number',
								),
								'fieldBottomPaddingMobile' => array(
									'type' => 'number',
								),
								'fieldLeftPaddingMobile'   => array(
									'type' => 'number',
								),
								'fieldRightPaddingMobile'  => array(
									'type' => 'number',
								),
								'fieldPaddingTypeDesktop'  => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'fieldPaddingTypeTablet'   => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'fieldPaddingTypeMobile'   => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'fieldSpacingLink'         => array(
									'type'    => 'boolean',
									'default' => false,
								),
								'labelTransform'           => array(
									'type' => 'string',
								),
								'labelDecoration'          => array(
									'type' => 'string',
								),
								'labelFontStyle'           => array(
									'type' => 'string',
								),
								'inputTransform'           => array(
									'type' => 'string',
								),
								'inputDecoration'          => array(
									'type' => 'string',
								),
								'inputFontStyle'           => array(
									'type' => 'string',
								),
								'buttonTransform'          => array(
									'type' => 'string',
								),
								'buttonDecoration'         => array(
									'type' => 'string',
								),
								'buttonFontStyle'          => array(
									'type' => 'string',
								),
								'radioCheckTransform'      => array(
									'type' => 'string',
								),
								'radioCheckDecoration'     => array(
									'type' => 'string',
								),
								'radioCheckFontStyle'      => array(
									'type' => 'string',
								),
								'validationMsgTransform'   => array(
									'type' => 'string',
								),
								'validationMsgDecoration'  => array(
									'type' => 'string',
								),
								'validationMsgFontStyle'   => array(
									'type' => 'string',
								),
								'msgTransform'             => array(
									'type' => 'string',
								),
								'msgDecoration'            => array(
									'type' => 'string',
								),
								'msgFontStyle'             => array(
									'type' => 'string',
								),
								'isPreview'                => array(
									'type'    => 'boolean',
									'default' => false,
								),

								'labelLetterSpacing'       => array(
									'type'    => 'number',
									'default' => '',
								),
								'labelLetterSpacingType'   => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'labelLetterSpacingMobile' => array(
									'type' => 'number',
								),
								'labelLetterSpacingTablet' => array(
									'type' => 'number',
								),
								'inputLetterSpacing'       => array(
									'type'    => 'number',
									'default' => '',
								),
								'inputLetterSpacingType'   => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'inputLetterSpacingMobile' => array(
									'type' => 'number',
								),
								'inputLetterSpacingTablet' => array(
									'type' => 'number',
								),
								'buttonLetterSpacing'      => array(
									'type'    => 'number',
									'default' => '',
								),
								'buttonLetterSpacingType'  => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'buttonLetterSpacingMobile' => array(
									'type' => 'number',
								),
								'buttonLetterSpacingTablet' => array(
									'type' => 'number',
								),
								'radioCheckLetterSpacing'  => array(
									'type'    => 'number',
									'default' => '',
								),
								'radioCheckLetterSpacingType' => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'radioCheckLetterSpacingMobile' => array(
									'type' => 'number',
								),
								'radioCheckLetterSpacingTablet' => array(
									'type' => 'number',
								),
								'validationMsgLetterSpacing' => array(
									'type'    => 'number',
									'default' => '',
								),
								'validationMsgLetterSpacingType' => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'validationMsgLetterSpacingMobile' => array(
									'type' => 'number',
								),
								'validationMsgLetterSpacingTablet' => array(
									'type' => 'number',
								),

								'msgLetterSpacing'         => array(
									'type'    => 'number',
									'default' => '',
								),
								'msgLetterSpacingType'     => array(
									'type'    => 'string',
									'default' => 'px',
								),
								'msgLetterSpacingMobile'   => array(
									'type' => 'number',
								),
								'msgLetterSpacingTablet'   => array(
									'type' => 'number',
								),
								'fieldBorderStyle'         => array(
									'type'    => 'string',
									'default' => 'solid',
								),
								'fieldBorderWidth'         => array(
									'type'    => 'number',
									'default' => 1,
								),
								'fieldBorderRadius'        => array(
									'type'    => 'number',
									'default' => 0,
								),
								'fieldBorderColor'         => array(
									'type'    => 'string',
									'default' => '#eeeeee',
								),
								'fieldBorderFocusColor'    => array(
									'type'    => 'string',
									'default' => '',
								),
								'buttonBorderStyle'        => array(
									'type'    => 'string',
									'default' => 'solid',
								),
								'buttonBorderWidth'        => array(
									'type'    => 'number',
									'default' => 1,
								),
								'buttonBorderRadius'       => array(
									'type'    => 'number',
									'default' => 0,
								),
								'buttonBorderColor'        => array(
									'type'    => 'string',
									'default' => '#333',
								),
								'buttonBorderHoverColor'   => array(
									'type'    => 'string',
									'default' => '#333',
								),
							)
						),
						'render_callback' => array( $this, 'render_html' ),
					)
				);
			}

		}

		/**
		 * Render CF7 HTML.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 1.10.0
		 */
		public function render_html( $attributes ) {

			$form = $attributes['formId'];

			$classes = array(
				'uagb-cf7-styler__align-' . $attributes['align'],
				'uagb-cf7-styler__field-style-' . $attributes['fieldStyle'],
				'uagb-cf7-styler__btn-align-' . $attributes['buttonAlignment'],
				'uagb-cf7-styler__btn-align-tablet-' . $attributes['buttonAlignmentTablet'],
				'uagb-cf7-styler__btn-align-mobile-' . $attributes['buttonAlignmentMobile'],
				'uagb-cf7-styler__highlight-style-' . $attributes['validationMsgPosition'],
			);

			if ( $attributes['enableOveride'] ) {
				$classes[] = 'uagb-cf7-styler__check-style-enabled';
			}

			if ( $attributes['enableHighlightBorder'] ) {
				$classes[] = 'uagb-cf7-styler__highlight-border';
			}
			$desktop_class = '';
			$tab_class     = '';
			$mob_class     = '';

			if ( array_key_exists( 'UAGHideDesktop', $attributes ) || array_key_exists( 'UAGHideTab', $attributes ) || array_key_exists( 'UAGHideMob', $attributes ) ) {

				$desktop_class = ( isset( $attributes['UAGHideDesktop'] ) ) ? 'uag-hide-desktop' : '';

				$tab_class = ( isset( $attributes['UAGHideTab'] ) ) ? 'uag-hide-tab' : '';

				$mob_class = ( isset( $attributes['UAGHideMob'] ) ) ? 'uag-hide-mob' : '';
			}

			$main_classes = array(
				'wp-block-uagb-cf7-styler',
				'uagb-cf7-styler__outer-wrap',
				'uagb-block-' . $attributes['block_id'],
				$desktop_class,
				$tab_class,
				$mob_class,
			);

			if ( isset( $attributes['className'] ) ) {
				$main_classes[] = $attributes['className'];
			}

			ob_start();
			if ( $form && 0 !== $form && -1 !== $form ) {
				?>
				<div class = "<?php echo esc_attr( implode( ' ', $main_classes ) ); ?>">
					<div class = "<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
					<?php echo do_shortcode( '[contact-form-7 id="' . $form . '"]' ); ?>
					</div>
				</div>
				<?php
			}
			return ob_get_clean();
		}
	}

	/**
	 *  Prepare if class 'UAGB_CF7_Styler' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	UAGB_CF7_Styler::get_instance();
}
