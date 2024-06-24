<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

// Adds Fonts.
UAGB_Block_JS::blocks_faq_gfont( $attr );

$icon_color        = $attr['iconColor'];
$icon_active_color = $attr['iconActiveColor'];

$attr['questionBottomPaddingDesktop'] = ( 10 === $attr['questionBottomPaddingDesktop'] && 10 !== $attr['vquestionPaddingDesktop'] ) ? $attr['vquestionPaddingDesktop'] : $attr['questionBottomPaddingDesktop'];

$attr['questionLeftPaddingDesktop'] = ( 10 === $attr['questionLeftPaddingDesktop'] && 10 !== $attr['hquestionPaddingDesktop'] ) ? $attr['hquestionPaddingDesktop'] : $attr['questionLeftPaddingDesktop'];

$attr['questionBottomPaddingTablet'] = ( 10 === $attr['questionBottomPaddingTablet'] && 10 !== $attr['vquestionPaddingTablet'] ) ? $attr['vquestionPaddingTablet'] : $attr['questionBottomPaddingTablet'];

$attr['questionLeftPaddingTablet'] = ( 10 === $attr['questionLeftPaddingTablet'] && 10 !== $attr['hquestionPaddingTablet'] ) ? $attr['hquestionPaddingTablet'] : $attr['questionLeftPaddingTablet'];

$attr['questionBottomPaddingMobile'] = ( 10 === $attr['questionBottomPaddingMobile'] && 10 !== $attr['vquestionPaddingMobile'] ) ? $attr['vquestionPaddingMobile'] : $attr['questionBottomPaddingMobile'];

$attr['questionLeftPaddingMobile'] = ( 10 === $attr['questionLeftPaddingMobile'] && 10 !== $attr['hquestionPaddingMobile'] ) ? $attr['hquestionPaddingMobile'] : $attr['questionLeftPaddingMobile'];

if ( ! isset( $attr['iconColor'] ) || '' === $attr['iconColor'] ) {

	$icon_color = $attr['questionTextColor'];
}
if ( ! isset( $attr['iconActiveColor'] ) || '' === $attr['iconActiveColor'] ) {

	$icon_active_color = $attr['questionTextActiveColor'];
}

$icon_size   = UAGB_Helper::get_css_value( $attr['iconSize'], $attr['iconSizeType'] );
$t_icon_size = UAGB_Helper::get_css_value( $attr['iconSizeTablet'], $attr['iconSizeType'] );
$m_icon_size = UAGB_Helper::get_css_value( $attr['iconSizeMobile'], $attr['iconSizeType'] );

$answer_top_padding_desktop    = isset( $attr['answerTopPadding'] ) ? $attr['answerTopPadding'] : $attr['vanswerPaddingDesktop'];
$answer_bottom_padding_desktop = isset( $attr['answerBottomPadding'] ) ? $attr['answerBottomPadding'] : $attr['vanswerPaddingDesktop'];
$answer_left_padding_desktop   = isset( $attr['answerLeftPadding'] ) ? $attr['answerLeftPadding'] : $attr['hanswerPaddingDesktop'];
$answer_right_padding_desktop  = isset( $attr['answerRightPadding'] ) ? $attr['answerRightPadding'] : $attr['hanswerPaddingDesktop'];

$answer_top_padding_tablet    = isset( $attr['answerTopPaddingTablet'] ) ? $attr['answerTopPaddingTablet'] : $attr['vanswerPaddingTablet'];
$answer_bottom_padding_tablet = isset( $attr['answerBottomPaddingTablet'] ) ? $attr['answerBottomPaddingTablet'] : $attr['vanswerPaddingTablet'];
$answer_left_padding_tablet   = isset( $attr['answerLeftPaddingTablet'] ) ? $attr['answerLeftPaddingTablet'] : $attr['hanswerPaddingTablet'];
$answer_right_padding_tablet  = isset( $attr['answerRightPaddingTablet'] ) ? $attr['answerRightPaddingTablet'] : $attr['hanswerPaddingTablet'];

$answer_top_padding_mobile    = isset( $attr['answerTopPaddingMobile'] ) ? $attr['answerTopPaddingMobile'] : $attr['vanswerPaddingMobile'];
$answer_bottom_padding_mobile = isset( $attr['answerBottomPaddingMobile'] ) ? $attr['answerBottomPaddingMobile'] : $attr['vanswerPaddingMobile'];
$answer_left_padding_mobile   = isset( $attr['answerLeftPaddingMobile'] ) ? $attr['answerLeftPaddingMobile'] : $attr['hanswerPaddingMobile'];
$answer_right_padding_mobile  = isset( $attr['answerRightPaddingMobile'] ) ? $attr['answerRightPaddingMobile'] : $attr['hanswerPaddingMobile'];

$border        = UAGB_Block_Helper::uag_generate_border_css( $attr, 'overall' );
$border        = UAGB_Block_Helper::uag_generate_deprecated_border_css(
	$border,
	( isset( $attr['borderWidth'] ) ? $attr['borderWidth'] : '' ),
	( isset( $attr['borderRadius'] ) ? $attr['borderRadius'] : '' ),
	( isset( $attr['borderColor'] ) ? $attr['borderColor'] : '' ),
	( isset( $attr['borderStyle'] ) ? $attr['borderStyle'] : '' )
);
$border_tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'overall', 'tablet' );
$border_mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'overall', 'mobile' );

$icon_border        = UAGB_Block_Helper::uag_generate_border_css( $attr, 'icon' );
$icon_border_tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'icon', 'tablet' );
$icon_border_mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'icon', 'mobile' );

$selectors = array(
	' .uagb-icon svg'                                     => array(
		'width'     => $icon_size,
		'height'    => $icon_size,
		'font-size' => $icon_size,
		'fill'      => $icon_color,
	),
	' .uagb-icon-active svg'                              => array(
		'width'     => $icon_size,
		'height'    => $icon_size,
		'font-size' => $icon_size,
		'fill'      => $icon_active_color,
	),
	' .uagb-faq-child__outer-wrap'                        => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['rowsGap'], $attr['rowsGapUnit'] ),
	),
	' .uagb-faq-item'                                     => array_merge(
		array(
			'background-color' => ( 'color' === $attr['boxBgType'] ) ? $attr['boxBgColor'] : 'transparent',
		),
		$border
	),
	' .uagb-faq-item:hover'                               => array(
		'background-color' => ( 'color' === $attr['boxBgHoverType'] ) ? $attr['boxBgHoverColor'] : 'transparent',
		'border-color'     => ! empty( $attr['overallBorderHColor'] ) ? $attr['overallBorderHColor'] : $attr['borderHoverColor'],
	),
	' .uagb-faq-item .uagb-question'                      => array(
		'color' => $attr['questionTextColor'],
	),
	' .uagb-faq-item.uagb-faq-item-active .uagb-question' => array(
		'color' => $attr['questionTextActiveColor'],
	),
	' .uagb-faq-item:hover .uagb-question'                => array(
		'color' => $attr['questionTextActiveColor'],
	),
	' .uagb-faq-questions-button'                         => array(
		'padding-top'      => UAGB_Helper::get_css_value( $attr['vquestionPaddingDesktop'], $attr['questionPaddingTypeDesktop'] ),
		'padding-bottom'   => UAGB_Helper::get_css_value( $attr['questionBottomPaddingDesktop'], $attr['questionPaddingTypeDesktop'] ),
		'padding-right'    => UAGB_Helper::get_css_value( $attr['hquestionPaddingDesktop'], $attr['questionPaddingTypeDesktop'] ),
		'padding-left'     => UAGB_Helper::get_css_value( $attr['questionLeftPaddingDesktop'], $attr['questionPaddingTypeDesktop'] ),
		'background-color' => $attr['questionTextBgColor'],
	),
	' .uagb-faq-item.uagb-faq-item-active .uagb-faq-questions-button' => array(
		'background-color' => $attr['questionTextActiveBgColor'],
	),
	' .uagb-faq-item:hover .uagb-faq-questions-button'    => array(
		'background-color' => $attr['questionTextActiveBgColor'],
	),
	' .uagb-faq-content'                                  => array(
		'padding-top'    => UAGB_Helper::get_css_value( $answer_top_padding_desktop, $attr['answerPaddingTypeDesktop'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $answer_bottom_padding_desktop, $attr['answerPaddingTypeDesktop'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $answer_right_padding_desktop, $attr['answerPaddingTypeDesktop'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $answer_left_padding_desktop, $attr['answerPaddingTypeDesktop'] ),
	),
	' .uagb-faq-content span'                             => array(
		'margin-top'    => UAGB_Helper::get_css_value( $answer_top_padding_desktop, $attr['answerPaddingTypeDesktop'] ),
		'margin-bottom' => UAGB_Helper::get_css_value( $answer_bottom_padding_desktop, $attr['answerPaddingTypeDesktop'] ),
		'margin-right'  => UAGB_Helper::get_css_value( $answer_right_padding_desktop, $attr['answerPaddingTypeDesktop'] ),
		'margin-left'   => UAGB_Helper::get_css_value( $answer_left_padding_desktop, $attr['answerPaddingTypeDesktop'] ),
	),
	'.uagb-faq-icon-row .uagb-faq-item .uagb-faq-icon-wrap' => array(
		'margin-right' => UAGB_Helper::get_css_value( $attr['gapBtwIconQUestion'], 'px' ),
	),
	'.uagb-faq-icon-row-reverse .uagb-faq-item .uagb-faq-icon-wrap' => array(
		'margin-left' => UAGB_Helper::get_css_value( $attr['gapBtwIconQUestion'], 'px' ),
	),
	'.wp-block-uagb-faq .uagb-faq-item .uagb-faq-icon-wrap' => array_merge(
		array(
			'padding'          => UAGB_Helper::get_css_value( $attr['iconBgSize'], $attr['iconBgSizeType'] ),
			'background-color' => $attr['iconBgColor'],
		),
		$icon_border
	),
	'.wp-block-uagb-faq .uagb-faq-item .uagb-faq-icon-wrap:hover' => array(
		'border-color' => $attr['iconBorderHColor'],
	),
	' .uagb-faq-item:hover .uagb-icon svg'                => array(
		'fill' => $icon_active_color,
	),
	' .uagb-faq-item .uagb-faq-questions-button.uagb-faq-questions' => array(
		'flex-direction' => $attr['iconAlign'],
	),
	' .uagb-faq-item .uagb-faq-content'                   => array(
		'color' => $attr['answerTextColor'],
	),
	'.uagb-faq__outer-wrap'                               => array(
		'margin-top'     => UAGB_Helper::get_css_value(
			$attr['blockTopMargin'],
			$attr['blockMarginUnit']
		),
		'margin-right'   => UAGB_Helper::get_css_value(
			$attr['blockRightMargin'],
			$attr['blockMarginUnit']
		),
		'margin-bottom'  => UAGB_Helper::get_css_value(
			$attr['blockBottomMargin'],
			$attr['blockMarginUnit']
		),
		'margin-left'    => UAGB_Helper::get_css_value(
			$attr['blockLeftMargin'],
			$attr['blockMarginUnit']
		),
		'padding-top'    => UAGB_Helper::get_css_value(
			$attr['blockTopPadding'],
			$attr['blockPaddingUnit']
		),
		'padding-right'  => UAGB_Helper::get_css_value(
			$attr['blockRightPadding'],
			$attr['blockPaddingUnit']
		),
		'padding-bottom' => UAGB_Helper::get_css_value(
			$attr['blockBottomPadding'],
			$attr['blockPaddingUnit']
		),
		'padding-left'   => UAGB_Helper::get_css_value(
			$attr['blockLeftPadding'],
			$attr['blockPaddingUnit']
		),
	),
);

$t_selectors = array(
	'.uagb-faq-icon-row .uagb-faq-item .uagb-faq-icon-wrap' => array(
		'margin-right' => UAGB_Helper::get_css_value( $attr['gapBtwIconQUestionTablet'], 'px' ),
	),
	'.uagb-faq-icon-row-reverse .uagb-faq-item .uagb-faq-icon-wrap' => array(
		'margin-left' => UAGB_Helper::get_css_value( $attr['gapBtwIconQUestionTablet'], 'px' ),
	),
	'.wp-block-uagb-faq .uagb-faq-item .uagb-faq-icon-wrap' => array_merge(
		array(
			'padding' => UAGB_Helper::get_css_value( $attr['iconBgSizeTablet'], $attr['iconBgSizeType'] ),
		),
		$icon_border_tablet
	),
	' .uagb-faq-questions-button'  => array(
		'padding-top'    => UAGB_Helper::get_css_value( $attr['vquestionPaddingTablet'], $attr['questionPaddingTypeTablet'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['questionBottomPaddingTablet'], $attr['questionPaddingTypeTablet'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['hquestionPaddingTablet'], $attr['questionPaddingTypeTablet'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['questionLeftPaddingTablet'], $attr['questionPaddingTypeTablet'] ),
	),
	' .uagb-faq-content'           => array(
		'padding-top'    => UAGB_Helper::get_css_value( $answer_top_padding_tablet, $attr['answerPaddingTypeTablet'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $answer_bottom_padding_tablet, $attr['answerPaddingTypeTablet'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $answer_right_padding_tablet, $attr['answerPaddingTypeTablet'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $answer_left_padding_tablet, $attr['answerPaddingTypeTablet'] ),
	),
	' .uagb-faq-content span'      => array(
		'margin-top'    => UAGB_Helper::get_css_value( $answer_top_padding_tablet, $attr['answerPaddingTypeTablet'] ),
		'margin-bottom' => UAGB_Helper::get_css_value( $answer_bottom_padding_tablet, $attr['answerPaddingTypeTablet'] ),
		'margin-right'  => UAGB_Helper::get_css_value( $answer_right_padding_tablet, $attr['answerPaddingTypeTablet'] ),
		'margin-left'   => UAGB_Helper::get_css_value( $answer_left_padding_tablet, $attr['answerPaddingTypeTablet'] ),
	),
	' .uagb-icon svg'              => array(
		'width'     => $t_icon_size,
		'height'    => $t_icon_size,
		'font-size' => $t_icon_size,
	),
	' .uagb-icon-active svg'       => array(
		'width'     => $t_icon_size,
		'height'    => $t_icon_size,
		'font-size' => $t_icon_size,
	),
	' .uagb-faq-child__outer-wrap' => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['rowsGapTablet'], $attr['rowsGapUnit'] ),
	),
	' .uagb-faq-item'              => $border_tablet,
	'.uagb-faq__outer-wrap'        => array(
		'padding-top'    => UAGB_Helper::get_css_value( $attr['blockTopPaddingTablet'], $attr['blockPaddingUnitTablet'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['blockRightPaddingTablet'], $attr['blockPaddingUnitTablet'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['blockBottomPaddingTablet'], $attr['blockPaddingUnitTablet'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['blockLeftPaddingTablet'], $attr['blockPaddingUnitTablet'] ),
		'margin-top'     => UAGB_Helper::get_css_value( $attr['blockTopMarginTablet'], $attr['blockMarginUnitTablet'] ),
		'margin-right'   => UAGB_Helper::get_css_value( $attr['blockRightMarginTablet'], $attr['blockMarginUnitTablet'] ),
		'margin-bottom'  => UAGB_Helper::get_css_value( $attr['blockBottomMarginTablet'], $attr['blockMarginUnitTablet'] ),
		'margin-left'    => UAGB_Helper::get_css_value( $attr['blockLeftMarginTablet'], $attr['blockMarginUnitTablet'] ),
	),
);
$m_selectors = array(
	'.uagb-faq-icon-row .uagb-faq-item .uagb-faq-icon-wrap' => array(
		'margin-right' => UAGB_Helper::get_css_value( $attr['gapBtwIconQUestionMobile'], 'px' ),
	),
	' .uagb-faq-item'              => $border_mobile,
	'.uagb-faq-icon-row-reverse .uagb-faq-item .uagb-faq-icon-wrap' => array(
		'margin-left' => UAGB_Helper::get_css_value( $attr['gapBtwIconQUestionMobile'], 'px' ),
	),
	' .uagb-faq-child__outer-wrap' => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['rowsGapMobile'], $attr['rowsGapUnit'] ),
	),
	'.wp-block-uagb-faq .uagb-faq-item .uagb-faq-icon-wrap' => array_merge(
		array(
			'padding' => UAGB_Helper::get_css_value( $attr['iconBgSizeMobile'], $attr['iconBgSizeType'] ),
		),
		$icon_border_mobile
	),
	' .uagb-faq-questions-button'  => array(
		'padding-top'    => UAGB_Helper::get_css_value( $attr['vquestionPaddingMobile'], $attr['questionPaddingTypeMobile'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['questionBottomPaddingMobile'], $attr['questionPaddingTypeMobile'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['hquestionPaddingMobile'], $attr['questionPaddingTypeMobile'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['questionLeftPaddingMobile'], $attr['questionPaddingTypeMobile'] ),
	),
	' .uagb-faq-content'           => array(
		'padding-top'    => UAGB_Helper::get_css_value( $answer_top_padding_mobile, $attr['answerPaddingTypeMobile'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $answer_bottom_padding_mobile, $attr['answerPaddingTypeMobile'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $answer_right_padding_mobile, $attr['answerPaddingTypeMobile'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $answer_left_padding_mobile, $attr['answerPaddingTypeMobile'] ),
	),
	' .uagb-faq-content span'      => array(
		'margin-top'    => UAGB_Helper::get_css_value( $answer_top_padding_mobile, $attr['answerPaddingTypeMobile'] ),
		'margin-bottom' => UAGB_Helper::get_css_value( $answer_bottom_padding_mobile, $attr['answerPaddingTypeMobile'] ),
		'margin-right'  => UAGB_Helper::get_css_value( $answer_right_padding_mobile, $attr['answerPaddingTypeMobile'] ),
		'margin-left'   => UAGB_Helper::get_css_value( $answer_left_padding_mobile, $attr['answerPaddingTypeMobile'] ),
	),
	' .uagb-icon svg'              => array(
		'width'     => $m_icon_size,
		'height'    => $m_icon_size,
		'font-size' => $m_icon_size,
	),
	' .uagb-icon-active svg'       => array(
		'width'     => $m_icon_size,
		'height'    => $m_icon_size,
		'font-size' => $m_icon_size,
	),
	'.uagb-faq__outer-wrap'        => array(
		'padding-top'    => UAGB_Helper::get_css_value( $attr['blockTopPaddingMobile'], $attr['blockPaddingUnitMobile'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['blockRightPaddingMobile'], $attr['blockPaddingUnitMobile'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['blockBottomPaddingMobile'], $attr['blockPaddingUnitMobile'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['blockLeftPaddingMobile'], $attr['blockPaddingUnitMobile'] ),
		'margin-top'     => UAGB_Helper::get_css_value( $attr['blockTopMarginMobile'], $attr['blockMarginUnitMobile'] ),
		'margin-right'   => UAGB_Helper::get_css_value( $attr['blockRightMarginMobile'], $attr['blockMarginUnitMobile'] ),
		'margin-bottom'  => UAGB_Helper::get_css_value( $attr['blockBottomMarginMobile'], $attr['blockMarginUnitMobile'] ),
		'margin-left'    => UAGB_Helper::get_css_value( $attr['blockLeftMarginMobile'], $attr['blockMarginUnitMobile'] ),
	),
);

if ( 'accordion' === $attr['layout'] && true === $attr['inactiveOtherItems'] ) {

	$selectors[' .wp-block-uagb-faq-child.uagb-faq-child__outer-wrap .uagb-faq-content '] = array(
		'display' => 'none',
	);
}
if ( 'accordion' === $attr['layout'] && true === $attr['expandFirstItem'] ) {

	$selectors['.uagb-faq__wrap.uagb-buttons-layout-wrap > .uagb-faq-child__outer-wrap:first-child.uagb-faq-item.uagb-faq-item-active .uagb-faq-content ']  = array(
		'display' => 'block',
	);
	$selectors['.uagb-faq__wrap.uagb-buttons-layout-wrap > .uagb-faq-child__outer-wrap:first-child .uagb-faq-item.uagb-faq-item-active .uagb-faq-content '] = array(
		'display' => 'block',
	);
}
if ( true === $attr['enableSeparator'] ) {

	$selectors[' .uagb-faq-child__outer-wrap .uagb-faq-content '] =
	array(
		'border-style'        => 'solid',
		'border-top-color'    => $attr['overallBorderColor'],
		'border-top-width'    => UAGB_Helper::get_css_value( $attr['overallBorderTopWidth'], 'px' ),
		'border-right-width'  => '0px',
		'border-bottom-width' => '0px',
		'border-left-width'   => '0px',
	);

	$t_selectors[' .uagb-faq-child__outer-wrap .uagb-faq-content ']     =
	array(
		'border-style'        => 'solid',
		'border-top-color'    => $attr['overallBorderColor'],
		'border-top-width'    => UAGB_Helper::get_css_value( $attr['overallBorderTopWidthTablet'], 'px' ),
		'border-right-width'  => '0px',
		'border-bottom-width' => '0px',
		'border-left-width'   => '0px',
	);
	$m_selectors[' .uagb-faq-child__outer-wrap .uagb-faq-content ']     =
	array(
		'border-style'        => 'solid',
		'border-top-color'    => $attr['overallBorderColor'],
		'border-top-width'    => UAGB_Helper::get_css_value( $attr['overallBorderTopWidthMobile'], 'px' ),
		'border-right-width'  => '0px',
		'border-bottom-width' => '0px',
		'border-left-width'   => '0px',
	);
	$selectors[' .uagb-faq-child__outer-wrap .uagb-faq-content:hover '] = array(
		'border-top-color' => ! empty( $attr['overallBorderHColor'] ) ? $attr['overallBorderHColor'] : $attr['borderHoverColor'],
	);
}
if ( 'grid' === $attr['layout'] ) {

	$selectors['.uagb-faq-layout-grid.uagb-faq__wrap .uagb-faq-child__outer-wrap '] = array(
		'text-align' => $attr['align'],
	);

	$selectors['.uagb-faq-layout-grid .uagb-faq__wrap .uagb-faq-child__outer-wrap '] = array(
		'text-align' => $attr['align'],
	);

	$selectors['.uagb-faq-layout-grid .uagb-faq__wrap.uagb-buttons-layout-wrap ']   = array(
		'grid-template-columns' => 'repeat(' . $attr['columns'] . ', 1fr)',
		'grid-column-gap'       => UAGB_Helper::get_css_value( $attr['columnsGap'], $attr['columnsGapUnit'] ),
		'grid-row-gap'          => UAGB_Helper::get_css_value( $attr['rowsGap'], $attr['rowsGapUnit'] ),
		'display'               => 'grid',
	);
	$t_selectors['.uagb-faq-layout-grid .uagb-faq__wrap.uagb-buttons-layout-wrap '] = array(
		'grid-column-gap'       => UAGB_Helper::get_css_value( $attr['columnsGapTablet'], $attr['columnsGapUnit'] ),
		'grid-template-columns' => 'repeat(' . $attr['tcolumns'] . ', 1fr)',
		'grid-row-gap'          => UAGB_Helper::get_css_value( $attr['rowsGapTablet'], $attr['rowsGapUnit'] ),
	);
	$m_selectors['.uagb-faq-layout-grid .uagb-faq__wrap.uagb-buttons-layout-wrap '] = array(
		'grid-template-columns' => 'repeat(' . $attr['mcolumns'] . ', 1fr)',
		'grid-column-gap'       => UAGB_Helper::get_css_value( $attr['columnsGapMobile'], $attr['columnsGapUnit'] ),
		'grid-row-gap'          => UAGB_Helper::get_css_value( $attr['rowsGapMobile'], $attr['rowsGapUnit'] ),
	);

	$selectors['.uagb-faq-layout-grid.uagb-faq__wrap.uagb-buttons-layout-wrap '] = array(
		'grid-template-columns' => 'repeat(' . $attr['columns'] . ', 1fr)',
		'grid-column-gap'       => UAGB_Helper::get_css_value( $attr['columnsGap'], $attr['columnsGapUnit'] ),
		'grid-row-gap'          => UAGB_Helper::get_css_value( $attr['rowsGap'], $attr['rowsGapUnit'] ),
		'display'               => 'grid',
	);

	$t_selectors['.uagb-faq-layout-grid.uagb-faq__wrap.uagb-buttons-layout-wrap '] = array(
		'grid-template-columns' => 'repeat(' . $attr['tcolumns'] . ', 1fr)',
		'grid-column-gap'       => UAGB_Helper::get_css_value( $attr['columnsGapTablet'], $attr['columnsGapUnit'] ),
		'grid-row-gap'          => UAGB_Helper::get_css_value( $attr['rowsGapTablet'], $attr['rowsGapUnit'] ),
	);
	$m_selectors['.uagb-faq-layout-grid.uagb-faq__wrap.uagb-buttons-layout-wrap '] = array(
		'grid-template-columns' => 'repeat(' . $attr['mcolumns'] . ', 1fr)',
		'grid-column-gap'       => UAGB_Helper::get_css_value( $attr['columnsGapMobile'], $attr['columnsGapUnit'] ),
		'grid-row-gap'          => UAGB_Helper::get_css_value( $attr['rowsGapMobile'], $attr['rowsGapUnit'] ),
	);
}

$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);

$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'question', ' .uagb-faq-questions-button .uagb-question', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'answer', ' .uagb-faq-item .uagb-faq-content', $combined_selectors );

return UAGB_Helper::generate_all_css( $combined_selectors, '.uagb-block-' . $id );
