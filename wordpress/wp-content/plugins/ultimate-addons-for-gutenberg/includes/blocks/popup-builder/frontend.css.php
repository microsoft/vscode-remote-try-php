<?php
/**
 * Frontend CSS File.
 *
 * @since 2.6.0
 *
 * @package uagb
 */

/**
 * Adding this comment to avoid PHPStan errors of undefined variable as these variables are defined else where.
 *
 * @var mixed[] $attr
 */

// Setup Defaults for Variants.
if ( 'banner' === $attr['variantType'] ) {
	$popup_position_v = ! empty( $attr['popupPositionV'] ) ? $attr['popupPositionV'] : 'flex-start';
	$popup_position_h = '';
} else {
	$popup_position_v = ! empty( $attr['popupPositionV'] ) ? $attr['popupPositionV'] : 'center';
	$popup_position_h = ! empty( $attr['popupPositionH'] ) ? $attr['popupPositionH'] : 'center';
}

// Border Attributes.
$content_border_css        = UAGB_Block_Helper::uag_generate_border_css( $attr, 'content' );
$content_border_css_tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'content', 'tablet' );
$content_border_css_mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'content', 'mobile' );

// Background CSS.
$bg_obj_desktop = array(
	'backgroundType'           => $attr['backgroundType'],
	'backgroundImage'          => $attr['backgroundImageDesktop'],
	'backgroundColor'          => $attr['backgroundColor'],
	'selectGradient'           => $attr['selectGradient'],
	'gradientValue'            => $attr['gradientValue'],
	'gradientColor1'           => $attr['gradientColor1'],
	'gradientColor2'           => $attr['gradientColor2'],
	'gradientLocation1'        => $attr['gradientLocation1'],
	'gradientLocation2'        => $attr['gradientLocation2'],
	'gradientType'             => $attr['gradientType'],
	'gradientAngle'            => $attr['gradientAngle'],
	'backgroundRepeat'         => $attr['backgroundRepeatDesktop'],
	'backgroundPosition'       => $attr['backgroundPositionDesktop'],
	'backgroundSize'           => $attr['backgroundSizeDesktop'],
	'backgroundAttachment'     => $attr['backgroundAttachmentDesktop'],
	'backgroundImageColor'     => $attr['backgroundImageColor'],
	'overlayType'              => $attr['overlayType'],
	'backgroundCustomSize'     => $attr['backgroundCustomSizeDesktop'],
	'backgroundCustomSizeType' => $attr['backgroundCustomSizeType'],
	'customPosition'           => $attr['customPosition'],
	'xPosition'                => $attr['xPositionDesktop'],
	'xPositionType'            => $attr['xPositionType'],
	'yPosition'                => $attr['yPositionDesktop'],
	'yPositionType'            => $attr['yPositionType'],
);
$bg_obj_tablet  = array(
	'backgroundType'           => $attr['backgroundType'],
	'backgroundImage'          => $attr['backgroundImageTablet'],
	'backgroundColor'          => $attr['backgroundColor'],
	'selectGradient'           => $attr['selectGradient'],
	'gradientValue'            => $attr['gradientValue'],
	'gradientColor1'           => $attr['gradientColor1'],
	'gradientColor2'           => $attr['gradientColor2'],
	'gradientLocation1'        => $attr['gradientLocation1'],
	'gradientLocation2'        => $attr['gradientLocation2'],
	'gradientType'             => $attr['gradientType'],
	'gradientAngle'            => $attr['gradientAngle'],
	'backgroundRepeat'         => $attr['backgroundRepeatTablet'],
	'backgroundPosition'       => $attr['backgroundPositionTablet'],
	'backgroundSize'           => $attr['backgroundSizeTablet'],
	'backgroundAttachment'     => $attr['backgroundAttachmentTablet'],
	'backgroundImageColor'     => $attr['backgroundImageColor'],
	'overlayType'              => $attr['overlayType'],
	'backgroundCustomSize'     => $attr['backgroundCustomSizeTablet'],
	'backgroundCustomSizeType' => $attr['backgroundCustomSizeType'],
	'customPosition'           => $attr['customPosition'],
	'xPosition'                => $attr['xPositionTablet'],
	'xPositionType'            => $attr['xPositionTypeTablet'],
	'yPosition'                => $attr['yPositionTablet'],
	'yPositionType'            => $attr['yPositionTypeTablet'],
);
$bg_obj_mobile  = array(
	'backgroundType'           => $attr['backgroundType'],
	'backgroundImage'          => $attr['backgroundImageMobile'],
	'backgroundColor'          => $attr['backgroundColor'],
	'selectGradient'           => $attr['selectGradient'],
	'gradientValue'            => $attr['gradientValue'],
	'gradientColor1'           => $attr['gradientColor1'],
	'gradientColor2'           => $attr['gradientColor2'],
	'gradientLocation1'        => $attr['gradientLocation1'],
	'gradientLocation2'        => $attr['gradientLocation2'],
	'gradientType'             => $attr['gradientType'],
	'gradientAngle'            => $attr['gradientAngle'],
	'backgroundRepeat'         => $attr['backgroundRepeatMobile'],
	'backgroundPosition'       => $attr['backgroundPositionMobile'],
	'backgroundSize'           => $attr['backgroundSizeMobile'],
	'backgroundAttachment'     => $attr['backgroundAttachmentMobile'],
	'backgroundImageColor'     => $attr['backgroundImageColor'],
	'overlayType'              => $attr['overlayType'],
	'backgroundCustomSize'     => $attr['backgroundCustomSizeMobile'],
	'backgroundCustomSizeType' => $attr['backgroundCustomSizeType'],
	'customPosition'           => $attr['customPosition'],
	'xPosition'                => $attr['xPositionMobile'],
	'xPositionType'            => $attr['xPositionTypeMobile'],
	'yPosition'                => $attr['yPositionMobile'],
	'yPositionType'            => $attr['yPositionTypeMobile'],
);

$popup_bg_css        = UAGB_Block_Helper::uag_get_background_obj( $bg_obj_desktop );
$popup_bg_css_tablet = UAGB_Block_Helper::uag_get_background_obj( $bg_obj_tablet );
$popup_bg_css_mobile = UAGB_Block_Helper::uag_get_background_obj( $bg_obj_mobile );

// Box Shadow CSS.
$box_shadow_properties       = array(
	'horizontal' => $attr['boxShadowHOffset'],
	'vertical'   => $attr['boxShadowVOffset'],
	'blur'       => $attr['boxShadowBlur'],
	'spread'     => $attr['boxShadowSpread'],
	'color'      => $attr['boxShadowColor'],
	'position'   => $attr['boxShadowPosition'],
);
$box_shadow_hover_properties = array(
	'horizontal' => $attr['boxShadowHOffsetHover'],
	'vertical'   => $attr['boxShadowVOffsetHover'],
	'blur'       => $attr['boxShadowBlurHover'],
	'spread'     => $attr['boxShadowSpreadHover'],
	'color'      => $attr['boxShadowColorHover'],
	'position'   => $attr['boxShadowPositionHover'],
	'alt_color'  => $attr['boxShadowColor'],
);

$box_shadow_css       = UAGB_Block_Helper::generate_shadow_css( $box_shadow_properties );
$box_shadow_hover_css = UAGB_Block_Helper::generate_shadow_css( $box_shadow_hover_properties );

$selectors = array(
	'.uagb-popup-builder'                         => array(
		'align-items'      => $popup_position_v,
		'justify-content'  => $popup_position_h,
		'background-color' => $attr['hasOverlay'] ? $attr['popupOverlayColor'] : '',
		'pointer-events'   => ( 'banner' === $attr['variantType'] || ( 'popup' === $attr['variantType'] && ! $attr['haltBackgroundInteraction'] ) ) ? 'none' : '',
	),
	' .uagb-popup-builder__wrapper'               => array(
		'pointer-events' => 'auto',
	),
	' .uagb-popup-builder__wrapper--banner'       => array(
		'height'     => $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeight'], $attr['popupHeightUnit'] ) : 'auto',
		'min-height' => ! $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeight'], $attr['popupHeightUnit'] ) : '',
	),
	' .uagb-popup-builder__wrapper--popup'        => array(
		'height'     => $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeight'], $attr['popupHeightUnit'] ) : '',
		'max-height' => ! $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeight'], $attr['popupHeightUnit'] ) : '',
		'width'      => UAGB_Helper::get_css_value( $attr['popupWidth'], $attr['popupWidthUnit'] ),
		'margin'     => UAGB_Block_Helper::generate_spacing(
			$attr['popupMarginUnit'],
			$attr['popupMarginTop'],
			$attr['popupMarginRight'],
			$attr['popupMarginBottom'],
			$attr['popupMarginLeft']
		),
	),
	// Backward Compatibility - Close button CSS for v2.12.2 and below.
	' .uagb-popup-builder__close'                 => array(
		'left'    => ( ( 'top-left' === $attr['closeIconPosition'] && ! is_rtl() ) || ( 'top-right' === $attr['closeIconPosition'] && is_rtl() ) ) ? 0 : '',
		'right'   => ( ( 'top-right' === $attr['closeIconPosition'] && ! is_rtl() ) || ( 'top-left' === $attr['closeIconPosition'] && is_rtl() ) ) ? 0 : '',
		'padding' => UAGB_Block_Helper::generate_spacing(
			$attr['closePaddingUnit'],
			$attr['closePaddingTop'],
			$attr['closePaddingRight'],
			$attr['closePaddingBottom'],
			$attr['closePaddingLeft']
		),
	),
	// Backward Compatibility - Close button CSS for v2.12.2 and below.
	' .uagb-popup-builder__close svg'             => array(
		'width'       => UAGB_Helper::get_css_value( $attr['closeIconSize'], 'px' ),
		'height'      => UAGB_Helper::get_css_value( $attr['closeIconSize'], 'px' ),
		'line-height' => UAGB_Helper::get_css_value( $attr['closeIconSize'], 'px' ),
		'font-size'   => UAGB_Helper::get_css_value( $attr['closeIconSize'], 'px' ),
		'fill'        => $attr['closeIconColor'],
	),
	' button.uagb-popup-builder__close'           => array(
		'left'    => ( ( 'top-left' === $attr['closeIconPosition'] && ! is_rtl() ) || ( 'top-right' === $attr['closeIconPosition'] && is_rtl() ) ) ? 0 : '',
		'right'   => ( ( 'top-right' === $attr['closeIconPosition'] && ! is_rtl() ) || ( 'top-left' === $attr['closeIconPosition'] && is_rtl() ) ) ? 0 : '',
		'padding' => UAGB_Block_Helper::generate_spacing(
			$attr['closePaddingUnit'],
			$attr['closePaddingTop'],
			$attr['closePaddingRight'],
			$attr['closePaddingBottom'],
			$attr['closePaddingLeft']
		),
	),
	' button.uagb-popup-builder__close svg'       => array(
		'width'       => UAGB_Helper::get_css_value( $attr['closeIconSize'], 'px' ),
		'height'      => UAGB_Helper::get_css_value( $attr['closeIconSize'], 'px' ),
		'line-height' => UAGB_Helper::get_css_value( $attr['closeIconSize'], 'px' ),
		'font-size'   => UAGB_Helper::get_css_value( $attr['closeIconSize'], 'px' ),
		'fill'        => $attr['closeIconColor'],
	),
	' button.uagb-popup-builder__close:hover svg' => array(
		'fill' => $attr['closeIconColorHover'],
	),
	' button.uagb-popup-builder__close:focus svg' => array(
		'fill' => $attr['closeIconColorHover'],
	),
	' .uagb-popup-builder__container'             => array_merge(
		array(
			'justify-content' => $attr['hasFixedHeight'] ? $attr['popupContentAlignmentV'] : '',
			'overflow-y'      => $attr['hasFixedHeight'] && ( 'center' === $attr['popupContentAlignmentV'] || 'flex-end' === $attr['popupContentAlignmentV'] ) ? 'hidden' : '',
			'box-shadow'      => $box_shadow_css,
			'padding'         => UAGB_Block_Helper::generate_spacing(
				$attr['popupPaddingUnit'],
				$attr['popupPaddingTop'],
				$attr['popupPaddingRight'],
				$attr['popupPaddingBottom'],
				$attr['popupPaddingLeft']
			),
		),
		$popup_bg_css,
		$content_border_css
	),
	' .uagb-popup-builder__container:hover'       => array(
		'box-shadow'   => $attr['useSeparateBoxShadows'] ? $box_shadow_hover_css : '',
		'border-color' => $attr['contentBorderHColor'],
	),
	' .uagb-popup-builder__container--banner'     => array(
		'min-height' => ! $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeight'], $attr['popupHeightUnit'] ) : '',
	),
	' .uagb-popup-builder__container--popup'      => array(
		'max-height' => ! $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeight'], $attr['popupHeightUnit'] ) : '',
	),
);

$t_selectors = array(
	' .uagb-popup-builder__wrapper--banner'   => array(
		'height'     => $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeightTablet'], $attr['popupHeightUnitTablet'] ) : 'auto',
		'min-height' => ! $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeightTablet'], $attr['popupHeightUnitTablet'] ) : '',
	),
	' .uagb-popup-builder__wrapper--popup'    => array(
		'height'     => $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeightTablet'], $attr['popupHeightUnitTablet'] ) : '',
		'max-height' => ! $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeightTablet'], $attr['popupHeightUnitTablet'] ) : '',
		'width'      => UAGB_Helper::get_css_value( $attr['popupWidthTablet'], $attr['popupWidthUnitTablet'] ),
		'margin'     => UAGB_Block_Helper::generate_spacing(
			$attr['popupMarginUnitTablet'],
			$attr['popupMarginTopTablet'],
			$attr['popupMarginRightTablet'],
			$attr['popupMarginBottomTablet'],
			$attr['popupMarginLeftTablet']
		),
	),
	// Backward Compatibility - Close button CSS for v2.12.2 and below.
	' .uagb-popup-builder__close'             => array(
		'padding' => UAGB_Block_Helper::generate_spacing(
			$attr['closePaddingUnitTablet'],
			$attr['closePaddingTopTablet'],
			$attr['closePaddingRightTablet'],
			$attr['closePaddingBottomTablet'],
			$attr['closePaddingLeftTablet']
		),
	),
	// Backward Compatibility - Close button CSS for v2.12.2 and below.
	' .uagb-popup-builder__close svg'         => array(
		'width'       => UAGB_Helper::get_css_value( $attr['closeIconSizeTablet'], 'px' ),
		'height'      => UAGB_Helper::get_css_value( $attr['closeIconSizeTablet'], 'px' ),
		'line-height' => UAGB_Helper::get_css_value( $attr['closeIconSizeTablet'], 'px' ),
		'font-size'   => UAGB_Helper::get_css_value( $attr['closeIconSizeTablet'], 'px' ),
	),
	' button.uagb-popup-builder__close'       => array(
		'padding' => UAGB_Block_Helper::generate_spacing(
			$attr['closePaddingUnitTablet'],
			$attr['closePaddingTopTablet'],
			$attr['closePaddingRightTablet'],
			$attr['closePaddingBottomTablet'],
			$attr['closePaddingLeftTablet']
		),
	),
	' button.uagb-popup-builder__close svg'   => array(
		'width'       => UAGB_Helper::get_css_value( $attr['closeIconSizeTablet'], 'px' ),
		'height'      => UAGB_Helper::get_css_value( $attr['closeIconSizeTablet'], 'px' ),
		'line-height' => UAGB_Helper::get_css_value( $attr['closeIconSizeTablet'], 'px' ),
		'font-size'   => UAGB_Helper::get_css_value( $attr['closeIconSizeTablet'], 'px' ),
	),
	' .uagb-popup-builder__container'         => array_merge(
		array(
			'padding' => UAGB_Block_Helper::generate_spacing(
				$attr['popupPaddingUnitTablet'],
				$attr['popupPaddingTopTablet'],
				$attr['popupPaddingRightTablet'],
				$attr['popupPaddingBottomTablet'],
				$attr['popupPaddingLeftTablet']
			),
		),
		$popup_bg_css_tablet,
		$content_border_css_tablet
	),
	' .uagb-popup-builder__container--banner' => array(
		'min-height' => ! $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeightTablet'], $attr['popupHeightUnitTablet'] ) : '',
	),
	' .uagb-popup-builder__container--popup'  => array(
		'max-height' => ! $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeightTablet'], $attr['popupHeightUnitTablet'] ) : '',
	),
);
$m_selectors = array(
	' .uagb-popup-builder__wrapper--banner'   => array(
		'height'     => $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeightMobile'], $attr['popupHeightUnitMobile'] ) : 'auto',
		'min-height' => ! $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeightMobile'], $attr['popupHeightUnitMobile'] ) : '',
	),
	' .uagb-popup-builder__wrapper--popup'    => array(
		'height'     => $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeightMobile'], $attr['popupHeightUnitMobile'] ) : '',
		'max-height' => ! $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeightMobile'], $attr['popupHeightUnitMobile'] ) : '',
		'width'      => UAGB_Helper::get_css_value( $attr['popupWidthMobile'], $attr['popupWidthUnitMobile'] ),
		'margin'     => UAGB_Block_Helper::generate_spacing(
			$attr['popupMarginUnitMobile'],
			$attr['popupMarginTopMobile'],
			$attr['popupMarginRightMobile'],
			$attr['popupMarginBottomMobile'],
			$attr['popupMarginLeftMobile']
		),
	),
	// Backward Compatibility - Close button CSS for v2.12.2 and below.
	' .uagb-popup-builder__close'             => array(
		'padding' => UAGB_Block_Helper::generate_spacing(
			$attr['closePaddingUnitMobile'],
			$attr['closePaddingTopMobile'],
			$attr['closePaddingRightMobile'],
			$attr['closePaddingBottomMobile'],
			$attr['closePaddingLeftMobile']
		),
	),
	// Backward Compatibility - Close button CSS for v2.12.2 and below.
	' .uagb-popup-builder__close svg'         => array(
		'width'       => UAGB_Helper::get_css_value( $attr['closeIconSizeMobile'], 'px' ),
		'height'      => UAGB_Helper::get_css_value( $attr['closeIconSizeMobile'], 'px' ),
		'line-height' => UAGB_Helper::get_css_value( $attr['closeIconSizeMobile'], 'px' ),
		'font-size'   => UAGB_Helper::get_css_value( $attr['closeIconSizeMobile'], 'px' ),
	),
	' button.uagb-popup-builder__close'       => array(
		'padding' => UAGB_Block_Helper::generate_spacing(
			$attr['closePaddingUnitMobile'],
			$attr['closePaddingTopMobile'],
			$attr['closePaddingRightMobile'],
			$attr['closePaddingBottomMobile'],
			$attr['closePaddingLeftMobile']
		),
	),
	' button.uagb-popup-builder__close svg'   => array(
		'width'       => UAGB_Helper::get_css_value( $attr['closeIconSizeMobile'], 'px' ),
		'height'      => UAGB_Helper::get_css_value( $attr['closeIconSizeMobile'], 'px' ),
		'line-height' => UAGB_Helper::get_css_value( $attr['closeIconSizeMobile'], 'px' ),
		'font-size'   => UAGB_Helper::get_css_value( $attr['closeIconSizeMobile'], 'px' ),
	),
	' .uagb-popup-builder__container'         => array_merge(
		array(
			'padding' => UAGB_Block_Helper::generate_spacing(
				$attr['popupPaddingUnitMobile'],
				$attr['popupPaddingTopMobile'],
				$attr['popupPaddingRightMobile'],
				$attr['popupPaddingBottomMobile'],
				$attr['popupPaddingLeftMobile']
			),
		),
		$popup_bg_css_mobile,
		$content_border_css_mobile
	),
	' .uagb-popup-builder__container--banner' => array(
		'min-height' => ! $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeightMobile'], $attr['popupHeightUnitMobile'] ) : '',
	),
	' .uagb-popup-builder__container--popup'  => array(
		'max-height' => ! $attr['hasFixedHeight'] ? UAGB_Helper::get_css_value( $attr['popupHeightMobile'], $attr['popupHeightUnitMobile'] ) : '',
	),
);

// If Background Type or Background Image is not set, add the default background color.
// Tablet and Mobile Image Backgrounds are handled by the device hierarchy.
if ( 'none' === $attr['backgroundType'] || ( 'image' === $attr['backgroundType'] && ! $attr['backgroundImageDesktop'] ) ) {
	$selectors[' .uagb-popup-builder__container']['background-color'] = '#fff';
}

// If this is a Banner, add the required static CSS overrides.
if ( 'banner' === $attr['variantType'] ) {
	$selectors['.uagb-popup-builder']['width']  = '100%';
	$selectors['.uagb-popup-builder']['height'] = 'unset';
	// If this is a Push Banner, add the Push Banner CSS as well.
	if ( $attr['willPushContent'] ) {
		$selectors['.uagb-popup-builder']['align-items'] = 'flex-start';
		$selectors['.uagb-popup-builder']['position']    = 'relative';
		$selectors['.uagb-popup-builder']['transition']  = 'max-height 0.5s cubic-bezier(1, 0, 1, 1)';
		$selectors['.uagb-popup-builder']['max-height']  = 0;
		$selectors['.uagb-popup-builder']['opacity']     = 1;
		$selectors['.uagb-popup-builder']['z-index']     = 9999;
		// Else if this is not a Push Banner, add the Bottom Banner overrides if needed.
	} elseif ( 'flex-end' === $attr['popupPositionV'] ) {
		$selectors['.uagb-popup-builder']['top']    = 'unset';
		$selectors['.uagb-popup-builder']['bottom'] = 0;
	}
}

$combined_selectors = UAGB_Helper::get_combined_selectors(
	'popup-builder', 
	array(
		'desktop' => $selectors,
		'tablet'  => $t_selectors,
		'mobile'  => $m_selectors,
	),
	$attr
);

$block_selector = '.uagb-block-' . $id;

return UAGB_Helper::generate_all_css( $combined_selectors, $block_selector );
