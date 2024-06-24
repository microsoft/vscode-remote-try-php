<?php
/**
 * UAGB faq.
 *
 * @package UAGB
 * @since 2.13.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UAGB_Faq' ) ) {

	/**
	 * Class UAGB_Faq.
	 *
	 * @since 2.13.5
	 */
	class UAGB_Faq {

		/**
		 * Member Variable
		 *
		 * @var UAGB_Faq
		 * @since 2.13.5
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @return UAGB_Faq
		 * @since 2.13.5
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 2.13.5
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_blocks' ) );
		}

		/**
		 * Registers the `faq` block on server.
		 *
		 * @since 2.13.5
		 * @return void
		 */
		public function register_blocks() {

			register_block_type(
				'uagb/faq',
				array(
					'attributes'      => array(
						'block_id'                     => array(
							'type' => 'string',
						),
						'layout'                       => array(
							'type'    => 'string',
							'default' => 'accordion',
						),
						'inactiveOtherItems'           => array(
							'type'    => 'boolean',
							'default' => true,
						),
						'expandFirstItem'              => array(
							'type'    => 'boolean',
							'default' => true,
						),
						'enableSchemaSupport'          => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'align'                        => array(
							'type'         => 'string',
							'default'      => 'left',
							'UAGCopyPaste' => array(
								'styleType' => 'overall-alignment',
							),
						),
						'blockTopPadding'              => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-top-padding',
							),
						),
						'blockRightPadding'            => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-right-padding',
							),
						),
						'blockLeftPadding'             => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-left-padding',
							),
						),
						'blockBottomPadding'           => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-bottom-padding',
							),
						),
						'blockTopPaddingTablet'        => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-top-padding-tablet',
							),
						),
						'blockRightPaddingTablet'      => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-right-padding-tablet',
							),
						),
						'blockLeftPaddingTablet'       => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-left-padding-tablet',
							),
						),
						'blockBottomPaddingTablet'     => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-bottom-padding-tablet',
							),
						),
						'blockTopPaddingMobile'        => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-top-padding-mobile',
							),
						),
						'blockRightPaddingMobile'      => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-right-padding-mobile',
							),
						),
						'blockLeftPaddingMobile'       => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-left-padding-mobile',
							),
						),
						'blockBottomPaddingMobile'     => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-bottom-padding-mobile',
							),
						),
						'blockPaddingUnit'             => array(
							'type'         => 'string',
							'default'      => 'px',
							'UAGCopyPaste' => array(
								'styleType' => 'block-padding-unit',
							),
						),

						'blockPaddingUnitTablet'       => array(
							'type'         => 'string',
							'default'      => 'px',
							'UAGCopyPaste' => array(
								'styleType' => 'block-padding-unit-tablet',
							),
						),
						'blockPaddingUnitMobile'       => array(
							'type'         => 'string',
							'default'      => 'px',
							'UAGCopyPaste' => array(
								'styleType' => 'block-padding-unit-mobile',
							),
						),
						'blockPaddingLink'             => array(
							'type'    => 'boolean',
							'default' => true,
						),
						'blockTopMargin'               => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-top-margin',
							),
						),
						'blockRightMargin'             => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-right-margin',
							),
						),
						'blockLeftMargin'              => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-left-margin',
							),
						),
						'blockBottomMargin'            => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-bottom-margin',
							),
						),
						'blockTopMarginTablet'         => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-top-margin-tablet',
							),
						),
						'blockRightMarginTablet'       => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-right-margin-tablet',
							),
						),
						'blockLeftMarginTablet'        => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-left-margin-tablet',
							),
						),
						'blockBottomMarginTablet'      => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-bottom-margin-tablet',
							),
						),
						'blockTopMarginMobile'         => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-top-margin-mobile',
							),
						),
						'blockRightMarginMobile'       => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-right-margin-mobile',
							),
						),
						'blockLeftMarginMobile'        => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-left-margin-mobile',
							),
						),
						'blockBottomMarginMobile'      => array(
							'type'         => 'number',
							'isGBSStyle'   => true,
							'UAGCopyPaste' => array(
								'styleType' => 'block-bottom-margin-mobile',
							),
						),
						'blockMarginUnit'              => array(
							'type'         => 'string',
							'default'      => 'px',
							'UAGCopyPaste' => array(
								'styleType' => 'block-margin-unit',
							),
						),
						'blockMarginUnitTablet'        => array(
							'type'         => 'string',
							'default'      => 'px',
							'UAGCopyPaste' => array(
								'styleType' => 'block-margin-unit-tablet',
							),
						),
						'blockMarginUnitMobile'        => array(
							'type'         => 'string',
							'default'      => 'px',
							'UAGCopyPaste' => array(
								'styleType' => 'block-margin-unit-mobile',
							),
						),
						'blockMarginLink'              => array(
							'type'    => 'boolean',
							'default' => true,
						),
						'enableSeparator'              => array(
							'type'         => 'boolean',
							'default'      => false,
							'UAGCopyPaste' => array(
								'styleType' => 'enable-separator',
							),
						),
						'rowsGap'                      => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'row-gap',
							),
						),
						'rowsGapTablet'                => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'row-gap-tablet',
							),
						),
						'rowsGapMobile'                => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'row-gap-mobile',
							),
						),
						'rowsGapUnit'                  => array(
							'type'         => 'string',
							'default'      => 'px',
							'UAGCopyPaste' => array(
								'styleType' => 'row-gap-type',
							),
						),
						'columnsGap'                   => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'column-gap',
							),
						),
						'columnsGapTablet'             => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'column-gap-tablet',
							),
						),
						'columnsGapMobile'             => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'column-gap-mobile',
							),
						),
						'columnsGapUnit'               => array(
							'type'         => 'string',
							'default'      => 'px',
							'UAGCopyPaste' => array(
								'styleType' => 'column-gap-type',
							),
						),
						'boxBgType'                    => array(
							'type'         => 'string',
							'default'      => 'color',
							'UAGCopyPaste' => array(
								'styleType' => 'faq-bg-type',
							),
						),
						'boxBgHoverType'               => array(
							'type'         => 'string',
							'default'      => 'color',
							'UAGCopyPaste' => array(
								'styleType' => 'faq-bg-hover-type',
							),
						),
						'boxBgColor'                   => array(
							'type'         => 'string',
							'default'      => '',
							'UAGCopyPaste' => array(
								'styleType' => 'faq-bg-color',
							),
						),
						'boxBgHoverColor'              => array(
							'type'         => 'string',
							'default'      => '',
							'UAGCopyPaste' => array(
								'styleType' => 'faq-bg-hover-color',
							),
						),
						'boxPaddingTypeMobile'         => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'faq-padding-type-mobile',
							),
							'default'      => 'px',
						),
						'boxPaddingTypeTablet'         => array(
							'type'         => 'string',
							'default'      => 'px',
							'UAGCopyPaste' => array(
								'styleType' => 'faq-padding-type-tablet',
							),
						),
						'boxPaddingTypeDesktop'        => array(
							'type'         => 'string',
							'default'      => 'px',
							'UAGCopyPaste' => array(
								'styleType' => 'faq-padding-type-desktop',
							),
						),
						'vBoxPaddingMobile'            => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'faq-vertical-padding-mobile',
							),
						),
						'hBoxPaddingMobile'            => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'faq-horizontal-padding-mobile',
							),
						),
						'vBoxPaddingTablet'            => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'faq-vertical-padding-tablet',
							),
						),
						'hBoxPaddingTablet'            => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'faq-horizontal-padding-tablet',
							),
						),
						'vBoxPaddingDesktop'           => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'faq-vertical-padding-desktop',
							),
						),
						'hBoxPaddingDesktop'           => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'faq-horizontal-padding-desktop',
							),
						),
						'borderHoverColor'             => array(
							'type' => 'string',
						),
						'borderStyle'                  => array(
							'type'    => 'string',
							'default' => 'solid',
						),
						'borderWidth'                  => array(
							'type'    => 'number',
							'default' => 1,
						),
						'borderRadius'                 => array(
							'type'    => 'number',
							'default' => 2,
						),
						'borderColor'                  => array(
							'type'    => 'string',
							'default' => '#D2D2D2',
						),
						'questionTextColor'            => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-color',
							),
						),
						'questionTextActiveColor'      => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-hover-color',
							),
						),
						'questionTextBgColor'          => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-bg-color',
							),
						),
						'questionTextActiveBgColor'    => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-hover-bg-color',
							),
						),
						'questionPaddingTypeDesktop'   => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-padding-type-desktop',
							),
							'default'      => 'px',
						),
						'questionPaddingTypeTablet'    => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-padding-type-tablet',
							),
							'default'      => 'px',
						),
						'questionPaddingTypeMobile'    => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-padding-type-mobile',
							),
							'default'      => 'px',
						),
						'vquestionPaddingMobile'       => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-vertical-padding-mobile',
							),
							'default'      => 10,
						),
						'vquestionPaddingTablet'       => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-vertical-padding-tablet',
							),
							'default'      => 10,
						),
						'vquestionPaddingDesktop'      => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-vertical-padding-desktop',
							),
							'default'      => 10,
						),
						'hquestionPaddingMobile'       => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-horizontal-padding-mobile',
							),
							'default'      => 10,
						),
						'hquestionPaddingTablet'       => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-horizontal-padding-tablet',
							),
							'default'      => 10,
						),
						'hquestionPaddingDesktop'      => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-horizontal-padding-desktop',
							),
							'default'      => 10,
						),
						'answerTextColor'              => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-color',
							),
						),
						'answerPaddingTypeDesktop'     => array(
							'type'         => 'string',
							'default'      => 'px',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-padding-type-desktop',
							),
						),
						'answerPaddingTypeTablet'      => array(
							'type'         => 'string',
							'default'      => 'px',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-padding-type-tablet',
							),
						),
						'answerPaddingTypeMobile'      => array(
							'type'         => 'string',
							'default'      => 'px',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-padding-type-mobile',
							),
						),
						'vanswerPaddingMobile'         => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-vertical-padding-mobile',
							),
							'default'      => 10,
						),
						'vanswerPaddingTablet'         => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-vertical-padding-tablet',
							),
							'default'      => 10,
						),
						'iconBgSize'                   => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'icon-bg-size',
							),
						),
						'iconBgSizeTablet'             => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'icon-bg-size-tablet',
							),
						),
						'iconBgSizeMobile'             => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'icon-bg-size-mobile',
							),
						),
						'iconBgSizeType'               => array(
							'type'         => 'string',
							'default'      => 'px',
							'UAGCopyPaste' => array(
								'styleType' => 'icon-bg-size-type',
							),
						),
						'columns'                      => array(
							'type'         => 'number',
							'default'      => 2,
							'UAGCopyPaste' => array(
								'styleType' => 'column-count',
							),
						),
						'tcolumns'                     => array(
							'type'         => 'number',
							'default'      => 2,
							'UAGCopyPaste' => array(
								'styleType' => 'column-count-tablet',
							),
						),
						'mcolumns'                     => array(
							'type'         => 'number',
							'default'      => 1,
							'UAGCopyPaste' => array(
								'styleType' => 'column-count-mobile',
							),
						),
						'schema'                       => array(
							'type'    => 'string',
							'default' => '',
						),
						'enableToggle'                 => array(
							'type'    => 'boolean',
							'default' => true,
						),
						'equalHeight'                  => array(
							'type'         => 'boolean',
							'default'      => true,
							'UAGCopyPaste' => array(
								'styleType' => 'equal-height',
							),
						),
						'questionLeftPaddingTablet'    => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-left-padding-tablet',
							),
						),
						'questionBottomPaddingTablet'  => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-bottom-padding-tablet',
							),
						),
						'questionLeftPaddingDesktop'   => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-left-padding-desktop',
							),
						),
						'questionBottomPaddingDesktop' => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-bottom-padding-desktop',
							),
						),
						'questionLeftPaddingMobile'    => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-left-padding-mobile',
							),
						),
						'questionBottomPaddingMobile'  => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-bottom-padding-mobile',
							),
						),
						'headingTag'                   => array(
							'type'     => 'string',
							'selector' => 'span,p,h1,h2,h3,h4,h5,h6',
							'default'  => 'span',
						),
						'questionSpacingLink'          => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'answerSpacingLink'            => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'answerTopPadding'             => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'desc-top-padding',
							),
						),
						'answerRightPadding'           => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'desc-right-padding',
							),
						),
						'answerBottomPadding'          => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'desc-bottom-padding',
							),
						),
						'answerLeftPadding'            => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'desc-left-padding',
							),
						),
						'answerTopPaddingTablet'       => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'desc-top-padding-tablet',
							),
						),
						'answerRightPaddingTablet'     => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'desc-right-padding-tablet',
							),
						),
						'answerBottomPaddingTablet'    => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'desc-bottom-padding-tablet',
							),
						),
						'answerLeftPaddingTablet'      => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'desc-left-padding-tablet',
							),
						),
						'answerTopPaddingMobile'       => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'desc-top-padding-mobile',
							),
						),
						'answerRightPaddingMobile'     => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'desc-right-padding-mobile',
							),
						),
						'answerBottomPaddingMobile'    => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'desc-bottom-padding-mobile',
							),
						),
						'answerLeftPaddingMobile'      => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'desc-left-padding-mobile',
							),
						),
						'isPreview'                    => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'questionLetterSpacing'        => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-letter-spacing',
							),
						),
						'questionLetterSpacingTablet'  => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-letter-spacing-tablet',
							),
						),
						'questionLetterSpacingMobile'  => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-letter-spacing-mobile',
							),
						),
						'questionLetterSpacingType'    => array(
							'type'         => 'string',
							'default'      => 'px',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-letter-spacing-type',
							),
						),
						'answerLetterSpacing'          => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-letter-spacing',
							),
						),
						'answerLetterSpacingTablet'    => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-letter-spacing-tablet',
							),
						),
						'answerLetterSpacingMobile'    => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-letter-spacing-mobile',
							),
						),
						'answerLetterSpacingType'      => array(
							'type'         => 'string',
							'default'      => 'px',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-letter-spacing-type',
							),
						),
						'iconBgColor'                  => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'icon-bg-color',
							),
						),
						'iconColor'                    => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'icon-color',
							),
						),
						'iconActiveColor'              => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'icon-hover-color',
							),
						),
						'gapBtwIconQUestion'           => array(
							'type'         => 'number',
							'default'      => 10,
							'UAGCopyPaste' => array(
								'styleType' => 'icon-spacing',
							),
						),
						'gapBtwIconQUestionTablet'     => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'icon-spacing-tablet',
							),
						),
						'gapBtwIconQUestionMobile'     => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'icon-spacing-mobile',
							),
						),
						'questionloadGoogleFonts'      => array(
							'type'         => 'boolean',
							'default'      => false,
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-load-google-fonts',
							),
						),
						'answerloadGoogleFonts'        => array(
							'type'         => 'boolean',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-load-google-fonts',
							),
							'default'      => false,
						),
						'questionFontFamily'           => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-font-family',
							),
							'default'      => 'Default',
						),
						'questionFontWeight'           => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-font-weight',
							),
						),
						'questionFontStyle'            => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-font-style',
							),
							'default'      => 'normal',
						),
						'questionTransform'            => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-transform',
							),
						),
						'questionDecoration'           => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-decoration',
							),
						),
						'questionFontSize'             => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-font-size',
							),
						),
						'questionFontSizeType'         => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-font-size-type',
							),
							'default'      => 'px',
						),
						'questionFontSizeTablet'       => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-font-size-tablet',
							),
						),
						'questionFontSizeMobile'       => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-font-size-mobile',
							),
						),
						'questionLineHeight'           => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-line-height',
							),
						),
						'questionLineHeightType'       => array(
							'type'         => 'string',
							'default'      => 'em',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-line-height-type',
							),
						),
						'questionLineHeightTablet'     => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-line-height-tablet',
							),
						),
						'questionLineHeightMobile'     => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'main-title-line-height-mobile',
							),
						),
						'answerFontFamily'             => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-font-family',
							),
							'default'      => 'Default',
						),
						'answerFontWeight'             => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-font-weight',
							),
						),
						'answerFontStyle'              => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-font-style',
							),
							'default'      => 'normal',
						),
						'answerTransform'              => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-transform',
							),
						),
						'answerDecoration'             => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-decoration',
							),
						),
						'answerFontSize'               => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-font-size',
							),
						),
						'answerFontSizeType'           => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-font-size-type',
							),
							'default'      => 'px',
						),
						'answerFontSizeTablet'         => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-font-size-tablet',
							),
						),
						'answerFontSizeMobile'         => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-font-size-mobile',
							),
						),
						'answerLineHeight'             => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-line-height',
							),
						),
						'answerLineHeightType'         => array(
							'type'         => 'string',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-line-height-type',
							),
							'default'      => 'em',
						),
						'answerLineHeightTablet'       => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-line-height-tablet',
							),
						),
						'answerLineHeightMobile'       => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'desc-line-height-mobile',
							),
						),
						'icon'                         => array(
							'type'    => 'string',
							'default' => 'plus',
						),
						'iconActive'                   => array(
							'type'    => 'string',
							'default' => 'minus',
						),
						'iconAlign'                    => array(
							'type'         => 'string',
							'default'      => 'row',
							'UAGCopyPaste' => array(
								'styleType' => 'icon-align',
							),
						),
						'iconSize'                     => array(
							'type'         => 'number',
							'default'      => 12,
							'UAGCopyPaste' => array(
								'styleType' => 'icon-size',
							),
						),
						'iconSizeTablet'               => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'icon-size-tablet',
							),
						),
						'iconSizeMobile'               => array(
							'type'         => 'number',
							'UAGCopyPaste' => array(
								'styleType' => 'icon-size-mobile',
							),
						),
						'iconSizeType'                 => array(
							'type'         => 'string',
							'default'      => 'px',
							'UAGCopyPaste' => array(
								'styleType' => 'icon-size-type',
							),
						),
						'question'                     => array(
							'type'    => 'string',
							'default' => __( 'What is FAQ?', 'ultimate-addons-for-gutenberg' ),
						),
						'answer'                       => array(
							'type'    => 'string',
							'default' => __(
								'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
								'ultimate-addons-for-gutenberg'
							),
						),
					),
					'render_callback' => array( $this, 'render_faq_block' ),
				)
			);

			register_block_type(
				'uagb/faq-child',
				array(
					'attributes'      => array(
						'isPreview'  => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'block_id'   => array(
							'type' => 'string',
						),
						'question'   => array(
							'type'    => 'string',
							'default' => __( 'What is FAQ?', 'ultimate-addons-for-gutenberg' ),
						),
						'answer'     => array(
							'type'    => 'string',
							'default' => __(
								'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
								'ultimate-addons-for-gutenberg'
							),
						),
						'icon'       => array(
							'type'    => 'string',
							'default' => 'plus',
						),
						'iconActive' => array(
							'type'    => 'string',
							'default' => 'minus',
						),
						'layout'     => array(
							'type'    => 'string',
							'default' => 'accordion',
						),
						'headingTag' => array(
							'type'     => 'string',
							'selector' => 'span,p,h1,h2,h3,h4,h5,h6',
							'default'  => 'span',
						),
					),
					'render_callback' => array( $this, 'render_faq_child_block' ),
				) 
			);
		}

		/**
		 * Renders the UAGB FAQ block.
		 *
		 * @param  array    $attributes Block attributes.
		 * @param  string   $content    Block default content.
		 * @param  WP_Block $block      Block instance.
		 * @since 2.13.5
		 * @return string Rendered block HTML.
		 */
		public function render_faq_block( $attributes, $content, $block ) {
			global $post; // Use the global post object to get the current post.
			$block_id            = isset( $attributes['block_id'] ) ? $attributes['block_id'] : '';
			$enable_schema       = $attributes['enableSchemaSupport'];
			$equal_height        = $attributes['equalHeight'];
			$icon_align          = $attributes['iconAlign'];
			$layout              = $attributes['layout'];
			$expand_first_item   = ( true === $attributes['expandFirstItem'] ) ? 'uagb-faq-expand-first-true' : 'uagb-faq-expand-first-false';
			$inactive_other_item = ( true === $attributes['inactiveOtherItems'] ) ? 'uagb-faq-inactive-other-true' : 'uagb-faq-inactive-other-false';
			$enable_toggle       = isset( $attributes['enableToggle'] ) ? 'true' : 'false';
			// Get the current page URL.
			$page_url = get_permalink( $post );
			// Initialize the schema JSON structure.
			$json_data = array(
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'@id'        => $page_url,
				'mainEntity' => array(),
			);
			// Collect data from inner blocks for the schema.
			$inner_blocks_html = '';
			foreach ( $block->inner_blocks as $inner_block ) {
				if ( is_object( $inner_block ) && method_exists( $inner_block, 'render' ) ) {
					$inner_blocks_html .= $inner_block->render();
					// Assuming inner blocks have 'question' and 'answer' attributes.
					if ( isset( $inner_block->attributes['question'] ) && isset( $inner_block->attributes['answer'] ) ) {
						$faq_data                  = array(
							'@type'          => 'Question',
							'name'           => $inner_block->attributes['question'],
							'acceptedAnswer' => array(
								'@type' => 'Answer',
								'text'  => $inner_block->attributes['answer'],
							),
						);
						$json_data['mainEntity'][] = $faq_data;
					}
				}
			}
			// Render schema if enabled.
			$schema_output = '';
			if ( $enable_schema && ! empty( $json_data['mainEntity'] ) ) {
				$schema_output = '<script type="application/ld+json">' . wp_json_encode( $json_data ) . '</script>';
			}
			// Add equal height class if enabled.
			$equal_height_class = $equal_height ? 'uagb-faq-equal-height' : '';
			$desktop_class      = '';
			$tab_class          = '';
			$mob_class          = '';

			if ( array_key_exists( 'UAGHideDesktop', $attributes ) || array_key_exists( 'UAGHideTab', $attributes ) || array_key_exists( 'UAGHideMob', $attributes ) ) {

				$desktop_class = ( isset( $attributes['UAGHideDesktop'] ) ) ? 'uag-hide-desktop' : '';

				$tab_class = ( isset( $attributes['UAGHideTab'] ) ) ? 'uag-hide-tab' : '';

				$mob_class = ( isset( $attributes['UAGHideMob'] ) ) ? 'uag-hide-mob' : '';
			}

			$zindex_desktop           = '';
			$zindex_tablet            = '';
			$zindex_mobile            = '';
			$zindex_wrap              = array();
			$zindex_extension_enabled = ( isset( $attributes['zIndex'] ) || isset( $attributes['zIndexTablet'] ) || isset( $attributes['zIndexMobile'] ) );

			if ( $zindex_extension_enabled ) {
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
			$zindex     = $zindex_extension_enabled ? 'uag-blocks-common-selector' : '';
			$class_name = ( isset( $attributes['className'] ) ) ? $attributes['className'] : '';
			// Build the block's HTML.
			$output  = '<div class="' . esc_attr( "wp-block-uagb-faq uagb-faq__outer-wrap uagb-block-{$block_id} uagb-faq-icon-{$icon_align} uagb-faq-layout-{$layout} {$expand_first_item} {$inactive_other_item} uagb-faq__wrap uagb-buttons-layout-wrap {$equal_height_class} {$desktop_class} {$tab_class} {$mob_class} {$zindex} {$class_name}" ) . '" data-faqtoggle="' . esc_attr( $enable_toggle ) . '" role="tablist">';
			$output .= $schema_output;
			$output .= $inner_blocks_html;
			$output .= '</div>';

			return $output;
		}

		/**
		 * Render faq icon function.
		 *
		 * @param string $icon Icon name.
		 * @param string $class Icon class.
		 * @since 2.13.5
		 * @return string|false Rendered icon HTML.
		 */
		public function faq_render_icon( $icon, $class ) {
			ob_start();
			?>
			<span class="<?php echo esc_attr( $class ); ?> uagb-faq-icon-wrap">
				<?php	
				echo UAGB_Helper::render_svg_html( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</span>
			<?php
			return ob_get_clean();
		}

		/**
		 * Renders the UAGB FAQ child block.
		 *
		 * @param  array    $attributes Block attributes.
		 * @param  string   $content    Block default content.
		 * @param  WP_Block $block      Block instance.
		 * @since 2.13.5
		 * @return string Rendered block HTML.
		 */
		public function render_faq_child_block( $attributes, $content, $block ) {
			// Extract attributes.
			$block_id    = isset( $attributes['block_id'] ) ? $attributes['block_id'] : '';
			$question    = $attributes['question'];
			$answer      = $attributes['answer'];
			$icon        = isset( $attributes['icon'] ) ? $attributes['icon'] : 'plus';
			$icon_active = isset( $attributes['iconActive'] ) ? $attributes['iconActive'] : 'minus';
			$layout      = $attributes['layout'];
			$heading_tag = $attributes['headingTag'];

			// Render icon and active icon.
			$icon_output        = $this->faq_render_icon( $icon, 'uagb-icon' );
			$icon_active_output = $this->faq_render_icon( $icon_active, 'uagb-icon-active' );
			$class_name         = ( isset( $attributes['className'] ) ) ? $attributes['className'] : '';

			// Build the block's HTML.
			$output  = '<div class="' . esc_attr( "wp-block-uagb-faq-child uagb-faq-child__outer-wrap uagb-faq-item uagb-block-{$block_id} {$class_name}" ) . '" role="tab" tabindex="0">';
			$output .= '<div class="uagb-faq-questions-button uagb-faq-questions">';
			if ( 'accordion' === $layout ) {
				$output .= $icon_output;
				$output .= $icon_active_output;
				$output .= '<' . esc_attr( $heading_tag ) . ' class="uagb-question">' . wp_kses_post( $question ) . '</' . esc_attr( $heading_tag ) . '>';
			} else {
				$output .= '<' . esc_attr( $heading_tag ) . ' class="uagb-question">' . wp_kses_post( $question ) . '</' . esc_attr( $heading_tag ) . '>';
			}
			$output .= '</div>';
			$output .= '<div class="uagb-faq-content"><p>' . wp_kses_post( $answer ) . '</p></div>';
			$output .= '</div>';

			return $output;
		}
	}

	/**
	 *  Prepare if class 'UAGB_Faq' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	UAGB_Faq::get_instance();
}
