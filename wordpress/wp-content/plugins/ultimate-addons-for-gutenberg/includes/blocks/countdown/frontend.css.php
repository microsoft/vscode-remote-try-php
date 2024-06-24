<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.4.0
 *
 * @package uagb
 */

$attr = isset( $attr ) ? $attr : array();

UAGB_Block_JS::blocks_countdown_gfont( $attr );

$is_rtl = is_rtl();

$child_selector_type          = $is_rtl ? 'first' : 'last';
$pseudo_element_selector_type = $is_rtl ? 'before' : 'after';

$separator_selector = '.wp-block-uagb-countdown .wp-block-uagb-countdown__box:not(:' . $child_selector_type . '-child) .wp-block-uagb-countdown__time::' . $pseudo_element_selector_type;

// On showSeconds disable this selector is used to remove the separator after minutes.
$min_separator_removal_selector = '.wp-block-uagb-countdown .wp-block-uagb-countdown__box.wp-block-uagb-countdown__box-minutes:not(:' .
$child_selector_type .
'-child) .wp-block-uagb-countdown__time.wp-block-uagb-countdown__time-minutes::' .
$pseudo_element_selector_type;

// On showSeconds and showMinutes disable this selector is used to remove the separator after hours.
$hour_separator_removal_selector = '.wp-block-uagb-countdown .wp-block-uagb-countdown__box.wp-block-uagb-countdown__box-hours:not(:' .
$child_selector_type .
'-child) .wp-block-uagb-countdown__time.wp-block-uagb-countdown__time-hours::' .
$pseudo_element_selector_type;

// On showSeconds, showMinutes and showHours disable this selector is used to remove the separator after days.
$days_separator_removal_selector = '.wp-block-uagb-countdown .wp-block-uagb-countdown__box.wp-block-uagb-countdown__box-days:not(:' .
$child_selector_type .
'-child) .wp-block-uagb-countdown__time.wp-block-uagb-countdown__time-days::' .
$pseudo_element_selector_type;

// Box Border CSS.
$box_border_css        = UAGB_Block_Helper::uag_generate_border_css( $attr, 'box' );
$box_border_css_tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'box', 'tablet' );
$box_border_css_mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'box', 'mobile' );

// Box Shadow.
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

$m_selectors = array();
$t_selectors = array();

$selectors = array(

	'.wp-block-uagb-countdown' => array(
		'justify-content' => $attr['align'],
		'margin-top'      => UAGB_Helper::get_css_value( $attr['blockTopMargin'], $attr['blockMarginUnit'] ),
		'margin-right'    => UAGB_Helper::get_css_value( $attr['blockRightMargin'], $attr['blockMarginUnit'] ),
		'margin-bottom'   => UAGB_Helper::get_css_value( $attr['blockBottomMargin'], $attr['blockMarginUnit'] ),
		'margin-left'     => UAGB_Helper::get_css_value( $attr['blockLeftMargin'], $attr['blockMarginUnit'] ),
		'padding-top'     => UAGB_Helper::get_css_value( $attr['blockTopPadding'], $attr['blockPaddingUnit'] ),
		'padding-right'   => UAGB_Helper::get_css_value( $attr['blockRightPadding'], $attr['blockPaddingUnit'] ),
		'padding-bottom'  => UAGB_Helper::get_css_value( $attr['blockBottomPadding'], $attr['blockPaddingUnit'] ),
		'padding-left'    => UAGB_Helper::get_css_value( $attr['blockLeftPadding'], $attr['blockPaddingUnit'] ),
	),

	'.wp-block-uagb-countdown .wp-block-uagb-countdown__box-days' => array(
		'display' => $attr['showDays'] ? '' : 'none',
	),

	'.wp-block-uagb-countdown .wp-block-uagb-countdown__box-hours' => array(
		'display' => $attr['showHours'] ? '' : 'none',
	),

	'.wp-block-uagb-countdown .wp-block-uagb-countdown__box-minutes' => array(
		'display' => $attr['showMinutes'] ? '' : 'none',
	),

	'.wp-block-uagb-countdown .wp-block-uagb-countdown__box-seconds' => array(
		'display' => $attr['showSeconds'] ? '' : 'none',
	),

	'.wp-block-uagb-countdown .wp-block-uagb-countdown__box' => array_merge(
		array(
			'aspect-ratio'     => $attr['isSquareBox'] ? 1 : 'auto',
			'width'            => UAGB_Helper::get_css_value( $attr['boxWidth'], 'px' ),
			'height'           => $attr['isSquareBox'] ? UAGB_Helper::get_css_value( $attr['boxWidth'], 'px' ) : 'auto',
			'flex-direction'   => $attr['boxFlex'],
			'justify-content'  => ( 'column' !== $attr['boxFlex'] ) ? $attr['boxAlign'] : 'center',
			'align-items'      => ( 'row' !== $attr['boxFlex'] ) ? $attr['boxAlign'] : 'center',
			'background-color' => ( 'transparent' !== $attr['boxBgType'] ) ? $attr['boxBgColor'] : 'transparent',
			'padding-top'      => UAGB_Helper::get_css_value( $attr['boxTopPadding'], $attr['boxPaddingUnit'] ),
			'padding-right'    => UAGB_Helper::get_css_value( $attr['boxRightPadding'], $attr['boxPaddingUnit'] ),
			'padding-bottom'   => UAGB_Helper::get_css_value( $attr['boxBottomPadding'], $attr['boxPaddingUnit'] ),
			'padding-left'     => UAGB_Helper::get_css_value( $attr['boxLeftPadding'], $attr['boxPaddingUnit'] ),
			'row-gap'          => UAGB_Helper::get_css_value( $attr['internalBoxSpacing'], 'px' ),
			'column-gap'       => UAGB_Helper::get_css_value( $attr['internalBoxSpacing'], 'px' ),
			'box-shadow'       => $box_shadow_css,
		),
		$box_border_css
	),

	'.wp-block-uagb-countdown:hover .wp-block-uagb-countdown__box' => array(
		'border-color' => $attr['boxBorderHColor'],
	),

	'.wp-block-uagb-countdown .wp-block-uagb-countdown__box.wp-block-uagb-countdown__box-minutes:not(:last-child)' => array(
		'margin-right' => $attr['showSeconds'] ? UAGB_Helper::get_css_value( $attr['boxSpacing'], 'px' ) : 'unset',
	),

	'.wp-block-uagb-countdown .wp-block-uagb-countdown__box.wp-block-uagb-countdown__box-hours:not(:last-child)' => array(
		'margin-right' => ( $attr['showSeconds'] || $attr['showMinutes'] ) ? UAGB_Helper::get_css_value( $attr['boxSpacing'], 'px' ) : 'unset',
	),

	'.wp-block-uagb-countdown .wp-block-uagb-countdown__box.wp-block-uagb-countdown__box-days:not(:last-child)' => array(
		'margin-right' => ( $attr['showSeconds'] || $attr['showMinutes'] || $attr['showHours'] ) ? UAGB_Helper::get_css_value( $attr['boxSpacing'], 'px' ) : 'unset',
	),

	'.wp-block-uagb-countdown .wp-block-uagb-countdown__time' => array(
		'font-family'     => $attr['digitFontFamily'],
		'font-style'      => $attr['digitFontStyle'],
		'text-decoration' => $attr['digitDecoration'],
		'font-weight'     => $attr['digitFontWeight'],
		'font-size'       => UAGB_Helper::get_css_value( $attr['digitFontSize'], $attr['digitFontSizeType'] ),
		'line-height'     => UAGB_Helper::get_css_value( $attr['digitLineHeight'], $attr['digitLineHeightType'] ),
		'letter-spacing'  => UAGB_Helper::get_css_value( $attr['digitLetterSpacing'], $attr['digitLetterSpacingType'] ),
		'color'           => $attr['digitColor'],
	),

	'.wp-block-uagb-countdown div.wp-block-uagb-countdown__label' => array(
		'align-self'      => ( ! $attr['isSquareBox'] && ( 'row' === $attr['boxFlex'] ) ) ? $attr['labelVerticalAlignment'] : 'unset',
		'font-family'     => $attr['labelFontFamily'],
		'font-style'      => $attr['labelFontStyle'],
		'text-decoration' => $attr['labelDecoration'],
		'text-transform'  => $attr['labelTransform'],
		'font-weight'     => $attr['labelFontWeight'],
		'font-size'       => UAGB_Helper::get_css_value( $attr['labelFontSize'], $attr['labelFontSizeType'] ),
		'line-height'     => UAGB_Helper::get_css_value( $attr['labelLineHeight'], $attr['labelLineHeightType'] ),
		'letter-spacing'  => UAGB_Helper::get_css_value( $attr['labelLetterSpacing'], $attr['labelLetterSpacingType'] ),
		'color'           => $attr['labelColor'],
	),

);

// If using separate box shadow hover settings, then generate CSS for it.
if ( $attr['useSeparateBoxShadows'] ) {
	$selectors['.wp-block-uagb-countdown:hover .wp-block-uagb-countdown__box']['box-shadow'] = $box_shadow_hover_css;
}

// TABLET SELECTORS.

$t_selectors['.wp-block-uagb-countdown'] = array(
	'justify-content' => $attr['alignTablet'],
	'margin-top'      => UAGB_Helper::get_css_value( $attr['blockTopMarginTablet'], $attr['blockMarginUnitTablet'] ),
	'margin-right'    => UAGB_Helper::get_css_value( $attr['blockRightMarginTablet'], $attr['blockMarginUnitTablet'] ),
	'margin-bottom'   => UAGB_Helper::get_css_value( $attr['blockBottomMarginTablet'], $attr['blockMarginUnitTablet'] ),
	'margin-left'     => UAGB_Helper::get_css_value( $attr['blockLeftMarginTablet'], $attr['blockMarginUnitTablet'] ),
	'padding-top'     => UAGB_Helper::get_css_value( $attr['blockTopPaddingTablet'], $attr['blockPaddingUnitTablet'] ),
	'padding-right'   => UAGB_Helper::get_css_value( $attr['blockRightPaddingTablet'], $attr['blockPaddingUnitTablet'] ),
	'padding-bottom'  => UAGB_Helper::get_css_value( $attr['blockBottomPaddingTablet'], $attr['blockPaddingUnitTablet'] ),
	'padding-left'    => UAGB_Helper::get_css_value( $attr['blockLeftPaddingTablet'], $attr['blockPaddingUnitTablet'] ),
);

$t_selectors['.wp-block-uagb-countdown .wp-block-uagb-countdown__box'] = array_merge(
	array(
		'width'           => UAGB_Helper::get_css_value( $attr['boxWidthTablet'], 'px' ),
		'height'          => $attr['isSquareBox'] ? UAGB_Helper::get_css_value( $attr['boxWidthTablet'], 'px' ) : 'auto',
		'flex-direction'  => $attr['boxFlexTablet'],
		'justify-content' => ( 'column' !== $attr['boxFlexTablet'] ) ? $attr['boxAlignTablet'] : 'center',
		'align-items'     => ( 'row' !== $attr['boxFlexTablet'] ) ? $attr['boxAlignTablet'] : 'center',
		'padding-top'     => UAGB_Helper::get_css_value( $attr['boxTopPaddingTablet'], $attr['boxPaddingUnitTablet'] ),
		'padding-right'   => UAGB_Helper::get_css_value( $attr['boxRightPaddingTablet'], $attr['boxPaddingUnitTablet'] ),
		'padding-bottom'  => UAGB_Helper::get_css_value( $attr['boxBottomPaddingTablet'], $attr['boxPaddingUnitTablet'] ),
		'padding-left'    => UAGB_Helper::get_css_value( $attr['boxLeftPaddingTablet'], $attr['boxPaddingUnitTablet'] ),
		'row-gap'         => UAGB_Helper::get_css_value( $attr['internalBoxSpacingTablet'], 'px' ),
		'column-gap'      => UAGB_Helper::get_css_value( $attr['internalBoxSpacingTablet'], 'px' ),
	),
	$box_border_css_tablet
);

$t_selectors['.wp-block-uagb-countdown .wp-block-uagb-countdown__box.wp-block-uagb-countdown__box-minutes:not(:last-child)'] = array(
	'margin-right' => $attr['showSeconds'] ? UAGB_Helper::get_css_value( $attr['boxSpacingTablet'], 'px' ) : 'unset',
);

$t_selectors['.wp-block-uagb-countdown .wp-block-uagb-countdown__box.wp-block-uagb-countdown__box-hours:not(:last-child)'] = array(
	'margin-right' => ( $attr['showSeconds'] || $attr['showMinutes'] ) ? UAGB_Helper::get_css_value( $attr['boxSpacingTablet'], 'px' ) : 'unset',
);

$t_selectors['.wp-block-uagb-countdown .wp-block-uagb-countdown__box.wp-block-uagb-countdown__box-days:not(:last-child)'] = array(
	'margin-right' => ( $attr['showSeconds'] || $attr['showMinutes'] || $attr['showHours'] ) ? UAGB_Helper::get_css_value( $attr['boxSpacingTablet'], 'px' ) : 'unset',
);

$t_selectors['.wp-block-uagb-countdown .wp-block-uagb-countdown__time'] = array(
	'font-size'      => UAGB_Helper::get_css_value( $attr['digitFontSizeTablet'], $attr['digitFontSizeTypeTablet'] ),
	'line-height'    => UAGB_Helper::get_css_value( $attr['digitLineHeightTablet'], $attr['digitLineHeightType'] ),
	'letter-spacing' => UAGB_Helper::get_css_value( $attr['digitLetterSpacingTablet'], $attr['digitLetterSpacingType'] ),
);

$t_selectors['.wp-block-uagb-countdown div.wp-block-uagb-countdown__label'] = array(
	'align-self'     => ( ! $attr['isSquareBox'] && ( 'row' === $attr['boxFlexTablet'] ) ) ? $attr['labelVerticalAlignmentTablet'] : 'unset',
	'font-size'      => UAGB_Helper::get_css_value( $attr['labelFontSizeTablet'], $attr['labelFontSizeTypeTablet'] ),
	'line-height'    => UAGB_Helper::get_css_value( $attr['labelLineHeightTablet'], $attr['labelLineHeightType'] ),
	'letter-spacing' => UAGB_Helper::get_css_value( $attr['labelLetterSpacingTablet'], $attr['labelLetterSpacingType'] ),
);

// MOBILE SELECTORS.

$m_selectors['.wp-block-uagb-countdown'] = array(
	'justify-content' => $attr['alignMobile'],
	'margin-top'      => UAGB_Helper::get_css_value( $attr['blockTopMarginMobile'], $attr['blockMarginUnitMobile'] ),
	'margin-right'    => UAGB_Helper::get_css_value( $attr['blockRightMarginMobile'], $attr['blockMarginUnitMobile'] ),
	'margin-bottom'   => UAGB_Helper::get_css_value( $attr['blockBottomMarginMobile'], $attr['blockMarginUnitMobile'] ),
	'margin-left'     => UAGB_Helper::get_css_value( $attr['blockLeftMarginMobile'], $attr['blockMarginUnitMobile'] ),
	'padding-top'     => UAGB_Helper::get_css_value( $attr['blockTopPaddingMobile'], $attr['blockPaddingUnitMobile'] ),
	'padding-right'   => UAGB_Helper::get_css_value( $attr['blockRightPaddingMobile'], $attr['blockPaddingUnitMobile'] ),
	'padding-bottom'  => UAGB_Helper::get_css_value( $attr['blockBottomPaddingMobile'], $attr['blockPaddingUnitMobile'] ),
	'padding-left'    => UAGB_Helper::get_css_value( $attr['blockLeftPaddingMobile'], $attr['blockPaddingUnitMobile'] ),
);

$m_selectors['.wp-block-uagb-countdown .wp-block-uagb-countdown__box'] = array_merge(
	array(
		'width'           => UAGB_Helper::get_css_value( $attr['boxWidthMobile'], 'px' ),
		'height'          => $attr['isSquareBox'] ? UAGB_Helper::get_css_value( $attr['boxWidthMobile'], 'px' ) : 'auto',
		'flex-direction'  => $attr['boxFlexMobile'],
		'justify-content' => ( 'column' !== $attr['boxFlexMobile'] ) ? $attr['boxAlignMobile'] : 'center',
		'align-items'     => ( 'row' !== $attr['boxFlexMobile'] ) ? $attr['boxAlignMobile'] : 'center',
		'padding-top'     => UAGB_Helper::get_css_value( $attr['boxTopPaddingMobile'], $attr['boxPaddingUnitMobile'] ),
		'padding-right'   => UAGB_Helper::get_css_value( $attr['boxRightPaddingMobile'], $attr['boxPaddingUnitMobile'] ),
		'padding-bottom'  => UAGB_Helper::get_css_value( $attr['boxBottomPaddingMobile'], $attr['boxPaddingUnitMobile'] ),
		'padding-left'    => UAGB_Helper::get_css_value( $attr['boxLeftPaddingMobile'], $attr['boxPaddingUnitMobile'] ),
		'row-gap'         => UAGB_Helper::get_css_value( $attr['internalBoxSpacingMobile'], 'px' ),
		'column-gap'      => UAGB_Helper::get_css_value( $attr['internalBoxSpacingMobile'], 'px' ),
	),
	$box_border_css_mobile
);

$m_selectors['.wp-block-uagb-countdown .wp-block-uagb-countdown__box.wp-block-uagb-countdown__box-minutes:not(:last-child)'] = array(
	'margin-right' => $attr['showSeconds'] ? UAGB_Helper::get_css_value( $attr['boxSpacingMobile'], 'px' ) : 'unset',
);

$m_selectors['.wp-block-uagb-countdown .wp-block-uagb-countdown__box.wp-block-uagb-countdown__box-hours:not(:last-child)'] = array(
	'margin-right' => ( $attr['showSeconds'] || $attr['showMinutes'] ) ? UAGB_Helper::get_css_value( $attr['boxSpacingMobile'], 'px' ) : 'unset',
);

$m_selectors['.wp-block-uagb-countdown .wp-block-uagb-countdown__box.wp-block-uagb-countdown__box-days:not(:last-child)'] = array(
	'margin-right' => ( $attr['showSeconds'] || $attr['showMinutes'] || $attr['showHours'] ) ? UAGB_Helper::get_css_value( $attr['boxSpacingMobile'], 'px' ) : 'unset',
);

$m_selectors['.wp-block-uagb-countdown .wp-block-uagb-countdown__time'] = array(
	'font-size'      => UAGB_Helper::get_css_value( $attr['digitFontSizeMobile'], $attr['digitFontSizeTypeMobile'] ),
	'line-height'    => UAGB_Helper::get_css_value( $attr['digitLineHeightMobile'], $attr['digitLineHeightType'] ),
	'letter-spacing' => UAGB_Helper::get_css_value( $attr['digitLetterSpacingMobile'], $attr['digitLetterSpacingType'] ),
);

$m_selectors['.wp-block-uagb-countdown div.wp-block-uagb-countdown__label'] = array(
	'align-self'     => ( ! $attr['isSquareBox'] && ( 'row' === $attr['boxFlexMobile'] ) ) ? $attr['labelVerticalAlignmentMobile'] : 'unset',
	'font-size'      => UAGB_Helper::get_css_value( $attr['labelFontSizeMobile'], $attr['labelFontSizeTypeMobile'] ),
	'line-height'    => UAGB_Helper::get_css_value( $attr['labelLineHeightMobile'], $attr['labelLineHeightType'] ),
	'letter-spacing' => UAGB_Helper::get_css_value( $attr['labelLetterSpacingMobile'], $attr['labelLetterSpacingType'] ),
);

if ( true === $attr['showSeparator'] ) {

	$selectors[ $separator_selector ] = array(
		'content'     => $attr['separatorType'] ? "'" . $attr['separatorType'] . "'" : '',
		'font-family' => $attr['separatorFontFamily'],
		'font-style'  => $attr['separatorFontStyle'],
		'font-weight' => $attr['separatorFontWeight'],
		'font-size'   => UAGB_Helper::get_css_value( $attr['separatorFontSize'], $attr['separatorFontSizeType'] ),
		'line-height' => UAGB_Helper::get_css_value( $attr['separatorLineHeight'], $attr['separatorLineHeightType'] ),
		'color'       => $attr['separatorColor'],
		'right'       => is_int( $attr['separatorRightSpacing'] ) ? UAGB_Helper::get_css_value( -$attr['separatorRightSpacing'], 'px' ) : '',
		'top'         => UAGB_Helper::get_css_value( $attr['separatorTopSpacing'], 'px' ),
	);

	$selectors[ $min_separator_removal_selector ] = array(
		'display' => $attr['showSeconds'] ? '' : 'none',
	);

	$selectors[ $hour_separator_removal_selector ] = array(
		'display' => ( $attr['showMinutes'] || $attr['showSeconds'] ) ? '' : 'none',
	);

	$selectors[ $days_separator_removal_selector ] = array(
		'display' => ( $attr['showHours'] || $attr['showMinutes'] || $attr['showSeconds'] ) ? '' : 'none',
	);

	$t_selectors[ $separator_selector ] = array(
		'font-size'   => UAGB_Helper::get_css_value( $attr['separatorFontSizeTablet'], $attr['separatorFontSizeTypeTablet'] ),
		'line-height' => UAGB_Helper::get_css_value( $attr['separatorLineHeightTablet'], $attr['separatorLineHeightType'] ),
		'right'       => is_int( $attr['separatorRightSpacingTablet'] ) ? UAGB_Helper::get_css_value( -$attr['separatorRightSpacingTablet'], 'px' ) : '',
		'top'         => UAGB_Helper::get_css_value( $attr['separatorTopSpacingTablet'], 'px' ),
	);

	$m_selectors[ $separator_selector ] = array(
		'font-size'   => UAGB_Helper::get_css_value( $attr['separatorFontSizeMobile'], $attr['separatorFontSizeTypeMobile'] ),
		'line-height' => UAGB_Helper::get_css_value( $attr['separatorLineHeightMobile'], $attr['separatorLineHeightType'] ),
		'right'       => is_int( $attr['separatorRightSpacingMobile'] ) ? UAGB_Helper::get_css_value( -$attr['separatorRightSpacingMobile'], 'px' ) : '',
		'top'         => UAGB_Helper::get_css_value( $attr['separatorTopSpacingMobile'], 'px' ),
	);
}

// RTL support for box gap.
if ( $is_rtl ) {
	$boxGapSelectorLTR = '.wp-block-uagb-countdown .wp-block-uagb-countdown__box:not(:last-child)';
	$boxGapSelectorRTL = '.wp-block-uagb-countdown .wp-block-uagb-countdown__box:not(:first-child)';

	$selectors[ $boxGapSelectorLTR ]['margin-right']   = 'unset';
	$t_selectors[ $boxGapSelectorLTR ]['margin-right'] = 'unset';
	$m_selectors[ $boxGapSelectorLTR ]['margin-right'] = 'unset';

	$selectors[ $boxGapSelectorRTL ]['margin-right']   = UAGB_Helper::get_css_value( $attr['boxSpacing'], 'px' );
	$t_selectors[ $boxGapSelectorRTL ]['margin-right'] = UAGB_Helper::get_css_value( $attr['boxSpacingTablet'], 'px' );
	$m_selectors[ $boxGapSelectorRTL ]['margin-right'] = UAGB_Helper::get_css_value( $attr['boxSpacingMobile'], 'px' );
}


if ( ! empty( $attr['globalBlockStyleId'] ) && empty( $attr['isSquareBox'] ) ) {
	
	$selectors['.wp-block-uagb-countdown .wp-block-uagb-countdown__box']['aspect-ratio'] = '';
	$selectors['.wp-block-uagb-countdown .wp-block-uagb-countdown__box']['height']       = '';

	// For Tablet.
	$t_selectors['.wp-block-uagb-countdown .wp-block-uagb-countdown__box']['aspect-ratio'] = '';
	$t_selectors['.wp-block-uagb-countdown .wp-block-uagb-countdown__box']['height']       = '';

	// For Mobile.
	$m_selectors['.wp-block-uagb-countdown .wp-block-uagb-countdown__box']['aspect-ratio'] = '';
	$m_selectors['.wp-block-uagb-countdown .wp-block-uagb-countdown__box']['height']       = '';
}

$combined_selectors = UAGB_Helper::get_combined_selectors(
	'countdown',
	array(
		'desktop' => $selectors,
		'tablet'  => $t_selectors,
		'mobile'  => $m_selectors,
	),
	$attr 
);

$base_selector = '.uagb-block-';

return UAGB_Helper::generate_all_css(
	$combined_selectors,
	$base_selector . $id,
	isset( $gbs_class ) ? $gbs_class : ''
);
