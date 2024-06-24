<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

// Adds Fonts.
UAGB_Block_JS::blocks_call_to_action_gfont( $attr );

$content_width_tablet_fallback = is_numeric( $attr['contentWidthTablet'] ) ? $attr['contentWidthTablet'] : $attr['contentWidth'];
$content_width_mobile_fallback = is_numeric( $attr['contentWidthMobile'] ) ? $attr['contentWidthMobile'] : $content_width_tablet_fallback;

$btn_content_width_tablet_fallback = is_numeric( $attr['btncontentWidthTablet'] ) ? $attr['btncontentWidthTablet'] : $attr['btncontentWidth'];
$btn_content_width_mobile_fallback = is_numeric( $attr['btncontentWidthMobile'] ) ? $attr['btncontentWidthMobile'] : $attr['btncontentWidth'];

$t_selectors = array();
$m_selectors = array();

$svg_size   = UAGB_Helper::get_css_value( $attr['ctaFontSize'], $attr['ctaFontSizeType'] );
$m_svg_size = UAGB_Helper::get_css_value( $attr['ctaFontSizeMobile'], $attr['ctaFontSizeTypeMobile'] );
$t_svg_size = UAGB_Helper::get_css_value( $attr['ctaFontSizeTablet'], $attr['ctaFontSizeTypeTablet'] );

$btn_padding_top    = isset( $attr['ctaTopPadding'] ) ? $attr['ctaTopPadding'] : $attr['ctaBtnVertPadding'];
$btn_padding_bottom = isset( $attr['ctaBottomPadding'] ) ? $attr['ctaBottomPadding'] : $attr['ctaBtnVertPadding'];
$btn_padding_left   = isset( $attr['ctaLeftPadding'] ) ? $attr['ctaLeftPadding'] : $attr['ctaBtnHrPadding'];
$btn_padding_right  = isset( $attr['ctaRightPadding'] ) ? $attr['ctaRightPadding'] : $attr['ctaBtnHrPadding'];

if ( 'left' === $attr['textAlign'] ) {
	$alignment = 'flex-start';
} elseif ( 'right' === $attr['textAlign'] ) {
	$alignment = 'flex-end';
} else {
	$alignment = 'center';
}

if ( 'left' === $attr['textAlignTablet'] ) {
	$alignmentTablet = 'flex-start';
} elseif ( 'right' === $attr['textAlignTablet'] ) {
	$alignmentTablet = 'flex-end';
} else {
	$alignmentTablet = 'center';
}

if ( 'left' === $attr['textAlignMobile'] ) {
	$alignmentMobile = 'flex-start';
} elseif ( 'right' === $attr['textAlignMobile'] ) {

	$alignmentMobile = 'flex-end';
} else {
	$alignmentMobile = 'center';
}
$selectors = array(
	'.wp-block-uagb-call-to-action .uagb-cta__title'       => array(
		'color'         => $attr['titleColor'],
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['titleSpace'], $attr['titleSpaceType'] ),
	),
	'.wp-block-uagb-call-to-action .uagb-cta__desc'        => array(
		'color'         => $attr['descColor'],
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['descSpace'], $attr['descSpaceType'] ),
	),
	' .uagb-cta__align-button-after'                       => array(
		'margin-left' => UAGB_Helper::get_css_value( $attr['ctaIconSpace'], 'px' ),
	),
	' .uagb-cta__align-button-before'                      => array(
		'margin-right' => UAGB_Helper::get_css_value( $attr['ctaIconSpace'], 'px' ),
	),
	' .uagb-cta__button-wrapper .uagb-cta-with-svg'        => array(
		'font-size'   => $svg_size,
		'width'       => $svg_size,
		'height'      => $svg_size,
		'line-height' => $svg_size,
	),
	' .uagb-cta__button-wrapper .uagb-cta__block-link svg' => array(
		'fill' => $attr['ctaBtnLinkColor'],
	),
	'.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper > svg' => array(
		'font-size'   => $svg_size,
		'width'       => $svg_size,
		'height'      => $svg_size,
		'line-height' => $svg_size,
		'fill'        => $attr['ctaBtnLinkColor'],
	),
	'.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper:hover > svg' => array(
		'fill' => $attr['ctaLinkHoverColor'],
	),
	'.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper:focus > svg' => array(
		'fill' => $attr['ctaLinkHoverColor'],
	),
	'.wp-block-uagb-call-to-action a.uagb-cta-second__button > svg' => array(
		'font-size'   => UAGB_Helper::get_css_value( $attr['secondCtaFontSize'], $attr['secondCtaFontSizeType'] ),
		'width'       => UAGB_Helper::get_css_value( $attr['secondCtaFontSize'], $attr['secondCtaFontSizeType'] ),
		'height'      => UAGB_Helper::get_css_value( $attr['secondCtaFontSize'], $attr['secondCtaFontSizeType'] ),
		'line-height' => UAGB_Helper::get_css_value( $attr['secondCtaFontSize'], $attr['secondCtaFontSizeType'] ),
		'fill'        => $attr['secondCtaColor'],
	),
	'.wp-block-uagb-call-to-action a.uagb-cta-second__button:hover > svg' => array(
		'fill' => $attr['secondCtaHoverColor'],
	),
	'.wp-block-uagb-call-to-action a.uagb-cta-second__button:focus > svg' => array(
		'fill' => $attr['secondCtaHoverColor'],
	),
);

if ( 'text' === $attr['ctaType'] ) {
	$selectors[' .uagb-cta__button-wrapper a.uagb-cta-typeof-text']                    = array(
		'color' => $attr['ctaBtnLinkColor'],
	);
	$selectors[' .uagb-cta__button-wrapper a.uagb-cta-typeof-text:hover ']             = array(
		'color' => $attr['ctaLinkHoverColor'],
	);
	$selectors[' .uagb-cta__button-wrapper a.uagb-cta-typeof-text:focus ']             = array(
		'color' => $attr['ctaLinkHoverColor'],
	);
	$selectors['.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper']        = array(
		'color' => $attr['ctaBtnLinkColor'],
	);
	$selectors['.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper:hover '] = array(
		'color' => $attr['ctaLinkHoverColor'],
	);
	$selectors['.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper:focus '] = array(
		'color' => $attr['ctaLinkHoverColor'],
	);
}

$selectors[' .uagb-cta__content-wrap']      = array(
	'text-align' => $attr['textAlign'],
);
$selectors[' .uagb-cta__wrap']              = array(
	'width'      => UAGB_Helper::get_css_value( $attr['contentWidth'], $attr['contentWidthType'] ),
	'text-align' => $attr['textAlign'],
);
$selectors['.wp-block-uagb-call-to-action'] = array(
	'text-align'     => $attr['textAlign'],
	'padding-top'    => UAGB_Helper::get_css_value( $attr['overallBlockTopPadding'], $attr['overallBlockPaddingUnit'] ),
	'padding-bottom' => UAGB_Helper::get_css_value( $attr['overallBlockBottomPadding'], $attr['overallBlockPaddingUnit'] ),
	'padding-left'   => UAGB_Helper::get_css_value( $attr['overallBlockLeftPadding'], $attr['overallBlockPaddingUnit'] ),
	'padding-right'  => UAGB_Helper::get_css_value( $attr['overallBlockRightPadding'], $attr['overallBlockPaddingUnit'] ),
	'margin-top'     => UAGB_Helper::get_css_value( $attr['overallBlockTopMargin'], $attr['overallBlockMarginUnit'] ),
	'margin-bottom'  => UAGB_Helper::get_css_value( $attr['overallBlockBottomMargin'], $attr['overallBlockMarginUnit'] ),
	'margin-left'    => UAGB_Helper::get_css_value( $attr['overallBlockLeftMargin'], $attr['overallBlockMarginUnit'] ),
	'margin-right'   => UAGB_Helper::get_css_value( $attr['overallBlockRightMargin'], $attr['overallBlockMarginUnit'] ),
);

if ( 'left' === $attr['textAlign'] && 'right' === $attr['ctaPosition'] ) {
	$selectors[' .uagb-cta__left-right-wrap .uagb-cta__content'] = array(
		'margin-left'  => UAGB_Helper::get_css_value( $attr['ctaLeftSpace'], 'px' ),
		'margin-right' => '0',
	);
}

$t_selectors = array(
	'.wp-block-uagb-call-to-action .uagb-cta__title' => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['titleSpaceTablet'], $attr['titleSpaceType'] ),
	),
	'.wp-block-uagb-call-to-action .uagb-cta__desc'  => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['descSpaceTablet'], $attr['descSpaceType'] ),
	),
	' .uagb-cta__button-wrapper .uagb-cta-with-svg'  => array(
		'font-size'   => $t_svg_size,
		'width'       => $t_svg_size,
		'height'      => $t_svg_size,
		'line-height' => $t_svg_size,
	),
	'.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper svg' => array(
		'font-size'   => $t_svg_size,
		'width'       => $t_svg_size,
		'height'      => $t_svg_size,
		'line-height' => $t_svg_size,
	),
	'.wp-block-uagb-call-to-action a.uagb-cta-second__button svg' => array(
		'font-size'   => UAGB_Helper::get_css_value( $attr['secondCtaFontSizeTablet'], $attr['secondCtaFontSizeTypeTablet'] ),
		'width'       => UAGB_Helper::get_css_value( $attr['secondCtaFontSizeTablet'], $attr['secondCtaFontSizeTypeTablet'] ),
		'height'      => UAGB_Helper::get_css_value( $attr['secondCtaFontSizeTablet'], $attr['secondCtaFontSizeTypeTablet'] ),
		'line-height' => UAGB_Helper::get_css_value( $attr['secondCtaFontSizeTablet'], $attr['secondCtaFontSizeTypeTablet'] ),
	),
);

$t_selectors['.wp-block-uagb-call-to-action.uagb-cta__content-stacked-tablet '] = array(
	'display' => 'inherit',
);
$t_selectors['.uagb-cta__content-stacked-tablet .uagb-cta__wrap']               = array(
	'width' => '100%',
);

$m_selectors = array(
	'.wp-block-uagb-call-to-action .uagb-cta__title' => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['titleSpaceMobile'], $attr['titleSpaceType'] ),
	),
	'.wp-block-uagb-call-to-action .uagb-cta__desc'  => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['descSpaceMobile'], $attr['descSpaceType'] ),
	),
	' .uagb-cta__button-wrapper .uagb-cta-with-svg'  => array(
		'font-size'   => $m_svg_size,
		'width'       => $m_svg_size,
		'height'      => $m_svg_size,
		'line-height' => $m_svg_size,
	),
	'.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper svg' => array(
		'font-size'   => $m_svg_size,
		'width'       => $m_svg_size,
		'height'      => $m_svg_size,
		'line-height' => $m_svg_size,
	),
	'.wp-block-uagb-call-to-action a.uagb-cta-second__button svg' => array(
		'font-size'   => UAGB_Helper::get_css_value( $attr['secondCtaFontSizeMobile'], $attr['secondCtaFontSizeTypeMobile'] ),
		'width'       => UAGB_Helper::get_css_value( $attr['secondCtaFontSizeMobile'], $attr['secondCtaFontSizeTypeMobile'] ),
		'height'      => UAGB_Helper::get_css_value( $attr['secondCtaFontSizeMobile'], $attr['secondCtaFontSizeTypeMobile'] ),
		'line-height' => UAGB_Helper::get_css_value( $attr['secondCtaFontSizeMobile'], $attr['secondCtaFontSizeTypeMobile'] ),
	),
);

$m_selectors['.wp-block-uagb-call-to-action.uagb-cta__content-stacked-mobile '] = array(
	'display' => 'inherit',
);
$m_selectors['.uagb-cta__content-stacked-mobile .uagb-cta__wrap']               = array(
	'width' => '100%',
);
if ( 'desktop' === $attr['stackBtn'] ) {

	$selectors[' .uagb-cta__buttons']    = array(
		'flex-direction' => 'column',
		'row-gap'        => UAGB_Helper::get_css_value( $attr['gapBtn'], 'px' ),
	);
	$t_selectors[' .uagb-cta__buttons '] = array(
		'row-gap' => UAGB_Helper::get_css_value( $attr['gapBtnTablet'], 'px' ),
	);
	$m_selectors[' .uagb-cta__buttons '] = array(
		'row-gap' => UAGB_Helper::get_css_value( $attr['gapBtnMobile'], 'px' ),
	);

} elseif ( 'tablet' === $attr['stackBtn'] ) {

	$selectors[' .uagb-cta__buttons ']  = array(
		'column-gap' => UAGB_Helper::get_css_value( $attr['gapBtn'], 'px' ),
	);
	$t_selectors[' .uagb-cta__buttons'] = array(
		'flex-direction' => 'column',
		'row-gap'        => UAGB_Helper::get_css_value( $attr['gapBtnTablet'], 'px' ),
	);
	$m_selectors[' .uagb-cta__buttons'] = array(
		'flex-direction' => 'column',
		'row-gap'        => UAGB_Helper::get_css_value( $attr['gapBtnMobile'], 'px' ),
	);

} elseif ( 'mobile' === $attr['stackBtn'] ) {

	$selectors[' .uagb-cta__buttons ']  = array(
		'column-gap' => UAGB_Helper::get_css_value( $attr['gapBtn'], 'px' ),
	);
	$t_selectors[' .uagb-cta__buttons'] = array(
		'column-gap' => UAGB_Helper::get_css_value( $attr['gapBtnTablet'], 'px' ),
	);
	$m_selectors[' .uagb-cta__buttons'] = array(
		'flex-direction' => 'column',
		'row-gap'        => UAGB_Helper::get_css_value( $attr['gapBtnMobile'], 'px' ),
	);

} elseif ( 'none' === $attr['stackBtn'] ) {
	$selectors[' .uagb-cta__buttons']   = array(
		'column-gap' => UAGB_Helper::get_css_value( $attr['gapBtn'], 'px' ),
	);
	$t_selectors[' .uagb-cta__buttons'] = array(
		'column-gap' => UAGB_Helper::get_css_value( $attr['gapBtnTablet'], 'px' ),
	);
	$m_selectors[' .uagb-cta__buttons'] = array(
		'column-gap'      => UAGB_Helper::get_css_value( $attr['gapBtnMobile'], 'px' ),
		'justify-content' => 'center',
	);
}
if ( 'button' === $attr['ctaType'] && $attr['enabledSecondCtaButton'] ) {
	$selectors['.wp-block-button .uagb-cta__buttons']   = array(
		'width' => UAGB_Helper::get_css_value( $attr['btncontentWidth'], $attr['btncontentWidthType'] ),
	);
	$t_selectors['.wp-block-button .uagb-cta__buttons'] = array(
		'width' => UAGB_Helper::get_css_value( $btn_content_width_tablet_fallback, $attr['btncontentWidthType'] ),
	);
	$m_selectors['.wp-block-button .uagb-cta__buttons'] = array(
		'width' => UAGB_Helper::get_css_value( $btn_content_width_mobile_fallback, $attr['btncontentWidthType'] ),
	);
}

$cta_icon_spacing        = UAGB_Helper::get_css_value( $attr['ctaIconSpace'], 'px' );
$cta_icon_spacing_tablet = UAGB_Helper::get_css_value( $attr['ctaIconSpaceTablet'], 'px' );
$cta_icon_spacing_mobile = UAGB_Helper::get_css_value( $attr['ctaIconSpaceMobile'], 'px' );

$right_side_margin = 'margin-right';
$left_side_margin  = 'margin-left';

if ( ! is_rtl() ) {
	$right_side_margin = 'margin-left';
	$left_side_margin  = 'margin-right';
}

if ( 'before' === $attr['ctaIconPosition'] ) {
	$selectors['.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper > svg']   = array(
		$left_side_margin => $cta_icon_spacing,
		'font-size'       => $svg_size,
		'width'           => $svg_size,
		'height'          => $svg_size,
		'line-height'     => $svg_size,
		'fill'            => $attr['ctaBtnLinkColor'],
	);
	$t_selectors['.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper > svg'] = array(
		$left_side_margin => $cta_icon_spacing_tablet,
	);
	$m_selectors['.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper > svg'] = array(
		$left_side_margin => $cta_icon_spacing_mobile,
	);
} else {
	$selectors['.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper > svg']   = array(
		$right_side_margin => $cta_icon_spacing,
		'font-size'        => $svg_size,
		'width'            => $svg_size,
		'height'           => $svg_size,
		'line-height'      => $svg_size,
		'fill'             => $attr['ctaBtnLinkColor'],
	);
	$t_selectors['.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper > svg'] = array(
		$right_side_margin => $cta_icon_spacing_tablet,
	);
	$m_selectors['.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper > svg'] = array(
		$right_side_margin => $cta_icon_spacing_mobile,
	);
}

if ( 'none' === $attr['ctaType'] || 'all' === $attr['ctaType'] ) {
	$selectors[' .uagb-cta__wrap'] = array(
		'width' => '100%',
	);
}

if ( 'right' === $attr['ctaPosition'] && ( 'text' === $attr['ctaType'] || 'button' === $attr['ctaType'] ) ) {
	$selectors['.wp-block-uagb-call-to-action '] = array(
		'display'         => 'flex',
		'justify-content' => 'space-between',
	);
	$selectors[' .uagb-cta__content-right .uagb-cta__left-right-wrap .uagb-cta__content']        = array(
		'width' => UAGB_Helper::get_css_value( $attr['contentWidth'], $attr['contentWidthType'] ),
	);
	$selectors[' .uagb-cta__content-right .uagb-cta__left-right-wrap .uagb-cta__link-wrapper']   = array(
		'width' => UAGB_Helper::get_css_value( ( 100 - $attr['contentWidth'] ), $attr['contentWidthType'] ),
	);
	$t_selectors[' .uagb-cta__content-right .uagb-cta__left-right-wrap .uagb-cta__content']      = array(
		'width' => UAGB_Helper::get_css_value( $attr['contentWidthTablet'], $attr['contentWidthType'] ),
	);
	$t_selectors[' .uagb-cta__content-right .uagb-cta__left-right-wrap .uagb-cta__link-wrapper'] = array(
		'width' => UAGB_Helper::get_css_value( ( 100 - $content_width_tablet_fallback ), $attr['contentWidthType'] ),
	);

	$m_selectors[' .uagb-cta__content-right .uagb-cta__left-right-wrap .uagb-cta__content']      = array(
		'width' => UAGB_Helper::get_css_value( $attr['contentWidthMobile'], $attr['contentWidthType'] ),
	);
	$m_selectors[' .uagb-cta__content-right .uagb-cta__left-right-wrap .uagb-cta__link-wrapper'] = array(
		'width' => UAGB_Helper::get_css_value( ( 100 - $content_width_mobile_fallback ), $attr['contentWidthType'] ),
	);

	$selectors['.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper '] = array(
		'align-self'  => 'top' === $attr['buttonAlign'] ? 'flex-start' : 'center',
		'height'      => 'fit-content',
		'margin-left' => 'auto',
	);
}
$t_selectors[' .uagb-cta__wrap'] = array(
	'width'      => UAGB_Helper::get_css_value( $attr['contentWidthTablet'], $attr['contentWidthType'] ),
	'text-align' => $attr['textAlignTablet'],
);
$m_selectors[' .uagb-cta__wrap'] = array(
	'width'      => UAGB_Helper::get_css_value( $attr['contentWidthMobile'], $attr['contentWidthType'] ),
	'text-align' => $attr['textAlignMobile'],
);
if ( 'desktop' === $attr['stack'] ) {

	$selectors['.wp-block-uagb-call-to-action  ']   = array(
		'flex-direction' => 'column',
		'align-items'    => $alignment,
	);
	$t_selectors['.wp-block-uagb-call-to-action  '] = array(
		'flex-direction' => 'column',
		'align-items'    => $alignmentTablet,
		'padding-top'    => UAGB_Helper::get_css_value( $attr['overallBlockTopTabletPadding'], $attr['overallBlockTabletPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['overallBlockBottomTabletPadding'], $attr['overallBlockTabletPaddingUnit'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['overallBlockLeftTabletPadding'], $attr['overallBlockTabletPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['overallBlockRightTabletPadding'], $attr['overallBlockTabletPaddingUnit'] ),
		'margin-top'     => UAGB_Helper::get_css_value( $attr['overallBlockTopTabletMargin'], $attr['overallBlockTabletMarginUnit'] ),
		'margin-bottom'  => UAGB_Helper::get_css_value( $attr['overallBlockBottomTabletMargin'], $attr['overallBlockTabletMarginUnit'] ),
		'margin-left'    => UAGB_Helper::get_css_value( $attr['overallBlockLeftTabletMargin'], $attr['overallBlockTabletMarginUnit'] ),
		'margin-right'   => UAGB_Helper::get_css_value( $attr['overallBlockRightTabletMargin'], $attr['overallBlockTabletMarginUnit'] ),
	);
	$m_selectors['.wp-block-uagb-call-to-action  '] = array(
		'flex-direction' => 'column',
		'align-items'    => $alignmentMobile,
		'padding-top'    => UAGB_Helper::get_css_value( $attr['overallBlockTopMobilePadding'], $attr['overallBlockMobilePaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['overallBlockBottomMobilePadding'], $attr['overallBlockMobilePaddingUnit'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['overallBlockLeftMobilePadding'], $attr['overallBlockMobilePaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['overallBlockRightMobilePadding'], $attr['overallBlockMobilePaddingUnit'] ),
		'margin-top'     => UAGB_Helper::get_css_value( $attr['overallBlockTopMobileMargin'], $attr['overallBlockMobileMarginUnit'] ),
		'margin-bottom'  => UAGB_Helper::get_css_value( $attr['overallBlockBottomMobileMargin'], $attr['overallBlockMobileMarginUnit'] ),
		'margin-left'    => UAGB_Helper::get_css_value( $attr['overallBlockLeftMobileMargin'], $attr['overallBlockMobileMarginUnit'] ),
		'margin-right'   => UAGB_Helper::get_css_value( $attr['overallBlockRightMobileMargin'], $attr['overallBlockMobileMarginUnit'] ),
	);
} elseif ( 'tablet' === $attr['stack'] ) {

	$selectors['.wp-block-uagb-call-to-action  ']  = array(
		'flex-direction' => 'row',
		'align-items'    => 'top' === $attr['buttonAlign'] ? 'flex-start' : 'center',
	);
	$t_selectors['.wp-block-uagb-call-to-action '] = array(
		'flex-direction' => 'column',
		'align-items'    => $alignmentTablet,
		'padding-top'    => UAGB_Helper::get_css_value( $attr['overallBlockTopTabletPadding'], $attr['overallBlockTabletPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['overallBlockBottomTabletPadding'], $attr['overallBlockTabletPaddingUnit'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['overallBlockLeftTabletPadding'], $attr['overallBlockTabletPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['overallBlockRightTabletPadding'], $attr['overallBlockTabletPaddingUnit'] ),
		'margin-top'     => UAGB_Helper::get_css_value( $attr['overallBlockTopTabletMargin'], $attr['overallBlockTabletMarginUnit'] ),
		'margin-bottom'  => UAGB_Helper::get_css_value( $attr['overallBlockBottomTabletMargin'], $attr['overallBlockTabletMarginUnit'] ),
		'margin-left'    => UAGB_Helper::get_css_value( $attr['overallBlockLeftTabletMargin'], $attr['overallBlockTabletMarginUnit'] ),
		'margin-right'   => UAGB_Helper::get_css_value( $attr['overallBlockRightTabletMargin'], $attr['overallBlockTabletMarginUnit'] ),
	);
	$m_selectors['.wp-block-uagb-call-to-action '] = array(
		'flex-direction' => 'column',
		'align-items'    => $alignmentMobile,
		'padding-top'    => UAGB_Helper::get_css_value( $attr['overallBlockTopMobilePadding'], $attr['overallBlockMobilePaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['overallBlockBottomMobilePadding'], $attr['overallBlockMobilePaddingUnit'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['overallBlockLeftMobilePadding'], $attr['overallBlockMobilePaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['overallBlockRightMobilePadding'], $attr['overallBlockMobilePaddingUnit'] ),
		'margin-top'     => UAGB_Helper::get_css_value( $attr['overallBlockTopMobileMargin'], $attr['overallBlockMobileMarginUnit'] ),
		'margin-bottom'  => UAGB_Helper::get_css_value( $attr['overallBlockBottomMobileMargin'], $attr['overallBlockMobileMarginUnit'] ),
		'margin-left'    => UAGB_Helper::get_css_value( $attr['overallBlockLeftMobileMargin'], $attr['overallBlockMobileMarginUnit'] ),
		'margin-right'   => UAGB_Helper::get_css_value( $attr['overallBlockRightMobileMargin'], $attr['overallBlockMobileMarginUnit'] ),
	);

} elseif ( 'mobile' === $attr['stack'] ) {

	$selectors['.wp-block-uagb-call-to-action  ']  = array(
		'flex-direction' => 'row',
		'align-items'    => 'top' === $attr['buttonAlign'] ? 'flex-start' : 'center',
	);
	$t_selectors['.wp-block-uagb-call-to-action '] = array(
		'flex-direction' => 'row',
		'align-items'    => 'top' === $attr['buttonAlign'] ? 'flex-start' : 'center',
	);
	$m_selectors['.wp-block-uagb-call-to-action '] = array(
		'flex-direction' => 'column',
		'align-items'    => $alignmentMobile,
		'padding-top'    => UAGB_Helper::get_css_value( $attr['overallBlockTopMobilePadding'], $attr['overallBlockMobilePaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['overallBlockBottomMobilePadding'], $attr['overallBlockMobilePaddingUnit'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['overallBlockLeftMobilePadding'], $attr['overallBlockMobilePaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['overallBlockRightMobilePadding'], $attr['overallBlockMobilePaddingUnit'] ),
		'margin-top'     => UAGB_Helper::get_css_value( $attr['overallBlockTopMobileMargin'], $attr['overallBlockMobileMarginUnit'] ),
		'margin-bottom'  => UAGB_Helper::get_css_value( $attr['overallBlockBottomMobileMargin'], $attr['overallBlockMobileMarginUnit'] ),
		'margin-left'    => UAGB_Helper::get_css_value( $attr['overallBlockLeftMobileMargin'], $attr['overallBlockMobileMarginUnit'] ),
		'margin-right'   => UAGB_Helper::get_css_value( $attr['overallBlockRightMobileMargin'], $attr['overallBlockMobileMarginUnit'] ),
	);

} elseif ( 'none' === $attr['stack'] ) {
	$selectors['.wp-block-uagb-call-to-action  ']                    = array(
		'align-items'    => 'top' === $attr['buttonAlign'] ? 'flex-start' : 'center',
		'flex-direction' => 'row',
	);
	$t_selectors['.wp-block-uagb-call-to-action ']                   = array(
		'align-items'    => 'top' === $attr['buttonAlign'] ? 'flex-start' : 'center',
		'flex-direction' => 'row',
	);
	$m_selectors['.wp-block-uagb-call-to-action ']                   = array(
		'align-items'    => 'top' === $attr['buttonAlign'] ? 'flex-start' : 'center',
		'flex-direction' => 'row',
	);
	$selectors['.wp-block-uagb-call-to-action .uagb-cta__buttons']   = array(
		'margin-left' => UAGB_Helper::get_css_value( $attr['buttonRightSpace'], $attr['buttonRightSpaceType'] ),
	);
	$t_selectors['.wp-block-uagb-call-to-action .uagb-cta__buttons'] = array(
		'margin-left' => UAGB_Helper::get_css_value( $attr['buttonRightSpaceTablet'], $attr['buttonRightSpaceType'] ),
	);
	$m_selectors[' .uagb-cta__outer-wrap  .uagb-cta__buttons']       = array(
		'margin-left' => UAGB_Helper::get_css_value( $attr['buttonRightSpaceMobile'], $attr['buttonRightSpaceType'] ),
	);
}

$second_cta_icon_spacing        = UAGB_Helper::get_css_value( $attr['secondCtaIconSpace'], 'px' );
$second_cta_icon_spacing_tablet = UAGB_Helper::get_css_value( $attr['secondCtaIconSpaceTablet'], 'px' );
$second_cta_icon_spacing_mobile = UAGB_Helper::get_css_value( $attr['secondCtaIconSpaceMobile'], 'px' );

if ( 'before' === $attr['secondCtaIconPosition'] ) {
	$selectors['.wp-block-uagb-call-to-action a.uagb-cta-second__button > svg']   = array(
		$left_side_margin => $second_cta_icon_spacing,
		'font-size'       => UAGB_Helper::get_css_value( $attr['secondCtaFontSize'], $attr['secondCtaFontSizeType'] ),
		'width'           => UAGB_Helper::get_css_value( $attr['secondCtaFontSize'], $attr['secondCtaFontSizeType'] ),
		'height'          => UAGB_Helper::get_css_value( $attr['secondCtaFontSize'], $attr['secondCtaFontSizeType'] ),
		'line-height'     => UAGB_Helper::get_css_value( $attr['secondCtaFontSize'], $attr['secondCtaFontSizeType'] ),
		'fill'            => $attr['secondCtaColor'],
	);
	$t_selectors['.wp-block-uagb-call-to-action a.uagb-cta-second__button > svg'] = array(
		$left_side_margin => $second_cta_icon_spacing_tablet,
	);
	$m_selectors['.wp-block-uagb-call-to-action a.uagb-cta-second__button > svg'] = array(
		$left_side_margin => $second_cta_icon_spacing_mobile,
	);
} else {
	$selectors['.wp-block-uagb-call-to-action a.uagb-cta-second__button > svg']   = array(
		$right_side_margin => $second_cta_icon_spacing,
		'font-size'        => UAGB_Helper::get_css_value( $attr['secondCtaFontSize'], $attr['secondCtaFontSizeType'] ),
		'width'            => UAGB_Helper::get_css_value( $attr['secondCtaFontSize'], $attr['secondCtaFontSizeType'] ),
		'height'           => UAGB_Helper::get_css_value( $attr['secondCtaFontSize'], $attr['secondCtaFontSizeType'] ),
		'line-height'      => UAGB_Helper::get_css_value( $attr['secondCtaFontSize'], $attr['secondCtaFontSizeType'] ),
		'fill'             => $attr['secondCtaColor'],
	);
	$t_selectors['.wp-block-uagb-call-to-action a.uagb-cta-second__button > svg'] = array(
		$right_side_margin => $second_cta_icon_spacing_tablet,
	);
	$m_selectors['.wp-block-uagb-call-to-action a.uagb-cta-second__button > svg'] = array(
		$right_side_margin => $second_cta_icon_spacing_mobile,
	);
}

if ( ! $attr['secInheritFromTheme'] ) {
	
	$second_cta_border        = UAGB_Block_Helper::uag_generate_border_css( $attr, 'secondCta' );
	$second_cta_border_tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'secondCta', 'tablet' );
	$second_cta_border_mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'secondCta', 'mobile' );
	
	$selectors['.wp-block-uagb-call-to-action.wp-block-button a.uagb-cta-second__button']       = array_merge(
		array(
			'color'            => $attr['secondCtaColor'],
			'background-color' => ( 'color' === $attr['secondCtaBgType'] ) ? $attr['secondCtaBackground'] : 'transparent',
			'padding-top'      => UAGB_Helper::get_css_value( $attr['secondCtaTopPadding'], $attr['secondCtaPaddingUnit'] ),
			'padding-bottom'   => UAGB_Helper::get_css_value( $attr['secondCtaBottomPadding'], $attr['secondCtaPaddingUnit'] ),
			'padding-left'     => UAGB_Helper::get_css_value( $attr['secondCtaLeftPadding'], $attr['secondCtaPaddingUnit'] ),
			'padding-right'    => UAGB_Helper::get_css_value( $attr['secondCtaRightPadding'], $attr['secondCtaPaddingUnit'] ),
			'align-self'       => 'top' === $attr['buttonAlign'] ? 'flex-start' : 'center',
			'height'           => 'fit-content',
		),
		$second_cta_border
	);
	$selectors['.wp-block-uagb-call-to-action.wp-block-button a.uagb-cta-second__button:hover'] = array(
		'color'            => $attr['secondCtaHoverColor'],
		'background-color' => ( 'color' === $attr['secondCtaBgHoverType'] ) ? $attr['secondCtaHoverBackground'] . '!important' : 'transparent',
		'border-color'     => $attr['secondCtaBorderHColor'],
	);
	$selectors['.wp-block-uagb-call-to-action.wp-block-button a.uagb-cta-second__button:focus'] = array(
		'color'            => $attr['secondCtaHoverColor'],
		'background-color' => ( 'color' === $attr['secondCtaBgHoverType'] ) ? $attr['secondCtaHoverBackground'] . '!important' : 'transparent',
		'border-color'     => $attr['secondCtaBorderHColor'],
	);
	
	$t_selectors['.wp-block-uagb-call-to-action.wp-block-button a.uagb-cta-second__button'] = $second_cta_border_tablet;
	$m_selectors['.wp-block-uagb-call-to-action.wp-block-button a.uagb-cta-second__button'] = $second_cta_border_mobile;
	$m_selectors['.wp-block-uagb-call-to-action.wp-block-button a.uagb-cta-second__button'] = array(
		'padding-top'    => UAGB_Helper::get_css_value( $attr['secondCtaTopMobilePadding'], $attr['secondCtaMobilePaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['secondCtaBottomMobilePadding'], $attr['secondCtaMobilePaddingUnit'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['secondCtaLeftMobilePadding'], $attr['secondCtaMobilePaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['secondCtaRightMobilePadding'], $attr['secondCtaMobilePaddingUnit'] ),
	);
	$t_selectors['.wp-block-uagb-call-to-action.wp-block-button a.uagb-cta-second__button'] = array(
		'padding-top'    => UAGB_Helper::get_css_value( $attr['secondCtaTopTabletPadding'], $attr['secondCtaTabletPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['secondCtaBottomTabletPadding'], $attr['secondCtaTabletPaddingUnit'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['secondCtaLeftTabletPadding'], $attr['secondCtaTabletPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['secondCtaRightTabletPadding'], $attr['secondCtaTabletPaddingUnit'] ),
	);
}

if ( ! $attr['inheritFromTheme'] && 'button' === $attr['ctaType'] ) {

	$cta_border        = UAGB_Block_Helper::uag_generate_border_css( $attr, 'btn' );
	$cta_border        = UAGB_Block_Helper::uag_generate_deprecated_border_css(
		$cta_border,
		( isset( $attr['ctaBorderWidth'] ) ? $attr['ctaBorderWidth'] : '' ),
		( isset( $attr['ctaBorderRadius'] ) ? $attr['ctaBorderRadius'] : '' ),
		( isset( $attr['ctaBorderColor'] ) ? $attr['ctaBorderColor'] : '' ),
		( isset( $attr['ctaBorderStyle'] ) ? $attr['ctaBorderStyle'] : '' )
	);
	$cta_border_tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'btn', 'tablet' );
	$cta_border_mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'btn', 'mobile' );


	$selectors[' .uagb-cta__button-wrapper a.uagb-cta-typeof-button'] = array_merge(
		array(
			'color'            => $attr['ctaBtnLinkColor'] ? $attr['ctaBtnLinkColor'] : '#333',
			'background-color' => ( 'color' === $attr['ctaBgType'] ) ? $attr['ctaBgColor'] : 'transparent',
			'padding-top'      => UAGB_Helper::get_css_value( $btn_padding_top, $attr['ctaPaddingUnit'] ),
			'padding-bottom'   => UAGB_Helper::get_css_value( $btn_padding_bottom, $attr['ctaPaddingUnit'] ),
			'padding-left'     => UAGB_Helper::get_css_value( $btn_padding_left, $attr['ctaPaddingUnit'] ),
			'padding-right'    => UAGB_Helper::get_css_value( $btn_padding_right, $attr['ctaPaddingUnit'] ),
		),
		$cta_border
	);
	$selectors['.wp-block-uagb-call-to-action.wp-block-button .uagb-cta__button-wrapper a.uagb-cta-typeof-button:hover']                       = array(
		'color'            => $attr['ctaLinkHoverColor'],
		'background-color' => ( 'color' === $attr['ctaBgHoverType'] ) ? $attr['ctaBgHoverColor'] : 'transparent',
		'border-color'     => $attr['btnBorderHColor'] ? $attr['btnBorderHColor'] : $attr['ctaBorderhoverColor'],
	);
	$selectors['.wp-block-uagb-call-to-action.wp-block-button .uagb-cta__button-wrapper a.uagb-cta-typeof-button:focus']                       = array(
		'color'            => $attr['ctaLinkHoverColor'],
		'background-color' => ( 'color' === $attr['ctaBgHoverType'] ) ? $attr['ctaBgHoverColor'] : 'transparent',
		'border-color'     => $attr['btnBorderHColor'] ? $attr['btnBorderHColor'] : $attr['ctaBorderhoverColor'],
	);
	$selectors['.wp-block-uagb-call-to-action.wp-block-button .uagb-cta__buttons a.uagb-cta__button-link-wrapper.wp-block-button__link']       = array_merge(
		array(
			'color'            => $attr['ctaBtnLinkColor'],
			'background-color' => ( 'color' === $attr['ctaBgType'] ) ? $attr['ctaBgColor'] : 'transparent',
			'padding-top'      => UAGB_Helper::get_css_value( $btn_padding_top, $attr['ctaPaddingUnit'] ),
			'padding-bottom'   => UAGB_Helper::get_css_value( $btn_padding_bottom, $attr['ctaPaddingUnit'] ),
			'padding-left'     => UAGB_Helper::get_css_value( $btn_padding_left, $attr['ctaPaddingUnit'] ),
			'padding-right'    => UAGB_Helper::get_css_value( $btn_padding_right, $attr['ctaPaddingUnit'] ),
		),
		$cta_border
	);
	$selectors['.wp-block-uagb-call-to-action.wp-block-button .uagb-cta__buttons a.uagb-cta__button-link-wrapper.wp-block-button__link:hover'] = array(
		'color'            => $attr['ctaLinkHoverColor'],
		'background-color' => ( 'color' === $attr['ctaBgHoverType'] ) ? $attr['ctaBgHoverColor'] : 'transparent',
		'border-color'     => $attr['btnBorderHColor'] ? $attr['btnBorderHColor'] : $attr['ctaBorderhoverColor'],
	);
	$selectors['.wp-block-uagb-call-to-action.wp-block-button .uagb-cta__buttons a.uagb-cta__button-link-wrapper.wp-block-button__link:focus'] = array(
		'color'            => $attr['ctaLinkHoverColor'],
		'background-color' => ( 'color' === $attr['ctaBgHoverType'] ) ? $attr['ctaBgHoverColor'] : 'transparent',
		'border-color'     => $attr['btnBorderHColor'] ? $attr['btnBorderHColor'] : $attr['ctaBorderhoverColor'],
	);
	$t_selectors['.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper'] = array(
		'padding-top'    => UAGB_Helper::get_css_value( $attr['ctaTopPaddingTablet'], $attr['tabletCTAPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['ctaBottomPaddingTablet'], $attr['tabletCTAPaddingUnit'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['ctaLeftPaddingTablet'], $attr['tabletCTAPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['ctaRightPaddingTablet'], $attr['tabletCTAPaddingUnit'] ),
	);
	$t_selectors['.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper'] = $cta_border_tablet;
	$m_selectors['.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper'] = array(
		'padding-top'    => UAGB_Helper::get_css_value( $attr['ctaTopPaddingMobile'], $attr['mobileCTAPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['ctaBottomPaddingMobile'], $attr['mobileCTAPaddingUnit'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['ctaLeftPaddingMobile'], $attr['mobileCTAPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['ctaRightPaddingMobile'], $attr['mobileCTAPaddingUnit'] ),
	);
	$m_selectors['.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper'] = $cta_border_mobile;
	$t_selectors['.wp-block-uagb-call-to-action.wp-block-button .uagb-cta__buttons a.uagb-cta__button-link-wrapper.wp-block-button__link'] = array_merge(
		array(
			'padding-top'    => UAGB_Helper::get_css_value( $attr['ctaTopPaddingTablet'], $attr['tabletCTAPaddingUnit'] ),
			'padding-bottom' => UAGB_Helper::get_css_value( $attr['ctaBottomPaddingTablet'], $attr['tabletCTAPaddingUnit'] ),
			'padding-left'   => UAGB_Helper::get_css_value( $attr['ctaLeftPaddingTablet'], $attr['tabletCTAPaddingUnit'] ),
			'padding-right'  => UAGB_Helper::get_css_value( $attr['ctaRightPaddingTablet'], $attr['tabletCTAPaddingUnit'] ),
		),
		$cta_border_tablet
	);
	$m_selectors['.wp-block-uagb-call-to-action.wp-block-button .uagb-cta__buttons a.uagb-cta__button-link-wrapper.wp-block-button__link'] = array_merge(
		array(
			'padding-top'    => UAGB_Helper::get_css_value( $attr['ctaTopPaddingMobile'], $attr['mobileCTAPaddingUnit'] ),
			'padding-bottom' => UAGB_Helper::get_css_value( $attr['ctaBottomPaddingMobile'], $attr['mobileCTAPaddingUnit'] ),
			'padding-left'   => UAGB_Helper::get_css_value( $attr['ctaLeftPaddingMobile'], $attr['mobileCTAPaddingUnit'] ),
			'padding-right'  => UAGB_Helper::get_css_value( $attr['ctaRightPaddingMobile'], $attr['mobileCTAPaddingUnit'] ),
		),
		$cta_border_mobile
	);
}
$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);
if ( ! $attr['inheritFromTheme'] ) {
	$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'cta', '.wp-block-uagb-call-to-action a.uagb-cta__button-link-wrapper', $combined_selectors );
	$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'cta', ' .uagb-cta__button-wrapper a.uagb-cta__button-link-wrapper', $combined_selectors );
}

if ( ! $attr['secInheritFromTheme'] ) {
	$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'secondCta', '.wp-block-uagb-call-to-action a.uagb-cta-second__button', $combined_selectors );
}

if ( 'text' === $attr['ctaType'] ) {
	$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'cta', ' .uagb-cta__button-wrapper a.uagb-cta-typeof-text', $combined_selectors );
}

$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'title', ' .uagb-cta__title', $combined_selectors );
if ( $attr['enableMultilineParagraph'] ) {
	$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'desc', ' .uagb-cta__desc p', $combined_selectors );
} else {
	$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'desc', ' .uagb-cta__desc', $combined_selectors );
}

$base_selector = ( $attr['classMigrate'] ) ? '.uagb-block-' : '#uagb-cta-block-';

return UAGB_Helper::generate_all_css(
	$combined_selectors,
	$base_selector . $id,
	isset( $gbs_class ) ? $gbs_class : ''
);
