<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @var mixed[] $attr The block attributes.
 * @since 2.0.0
 *
 * @package uagb
 */

// Adds Fonts.
UAGB_Block_JS::blocks_inline_notice_gfont( $attr );

$t_selectors = array();
$m_selectors = array();
$selectors   = array();

$left_padding         = 0;
$right_padding        = 0;
$left_padding_mobile  = 0;
$right_padding_mobile = 0;
$left_padding_tablet  = 0;
$right_padding_tablet = 0;

$title_top_padding    = ( isset( $attr['titleTopPadding'] ) && is_numeric( $attr['titleTopPadding'] ) ) ? $attr['titleTopPadding'] : $attr['titleVrPadding'];
$title_bottom_padding = ( isset( $attr['titleBottomPadding'] ) && is_numeric( $attr['titleBottomPadding'] ) ) ? $attr['titleBottomPadding'] : $attr['titleVrPadding'];
$title_left_padding   = ( isset( $attr['titleLeftPadding'] ) && is_numeric( $attr['titleLeftPadding'] ) ) ? $attr['titleLeftPadding'] : $attr['titleHrPadding'];
$title_right_padding  = ( isset( $attr['titleRightPadding'] ) && is_numeric( $attr['titleRightPadding'] ) ) ? $attr['titleRightPadding'] : $attr['titleHrPadding'];

$title_top_padding_mobile    = ( isset( $attr['titleTopPaddingMobile'] ) && is_numeric( $attr['titleTopPaddingMobile'] ) ) ? $attr['titleTopPaddingMobile'] : $attr['titleVrPadding'];
$title_bottom_padding_mobile = ( isset( $attr['titleBottomPaddingMobile'] ) && is_numeric( $attr['titleBottomPaddingMobile'] ) ) ? $attr['titleBottomPaddingMobile'] : $attr['titleVrPadding'];
$title_left_padding_mobile   = ( isset( $attr['titleLeftPaddingMobile'] ) && is_numeric( $attr['titleLeftPaddingMobile'] ) ) ? $attr['titleLeftPaddingMobile'] : $attr['titleHrPadding'];
$title_right_padding_mobile  = ( isset( $attr['titleRightPaddingMobile'] ) && is_numeric( $attr['titleRightPaddingMobile'] ) ) ? $attr['titleRightPaddingMobile'] : $attr['titleHrPadding'];

$title_top_padding_tablet    = ( isset( $attr['titleTopPaddingTablet'] ) && is_numeric( $attr['titleTopPaddingTablet'] ) ) ? $attr['titleTopPaddingTablet'] : $attr['titleVrPadding'];
$title_bottom_padding_tablet = ( isset( $attr['titleBottomPaddingTablet'] ) && is_numeric( $attr['titleBottomPaddingTablet'] ) ) ? $attr['titleBottomPaddingTablet'] : $attr['titleVrPadding'];
$title_left_padding_tablet   = ( isset( $attr['titleLeftPaddingTablet'] ) && is_numeric( $attr['titleLeftPaddingTablet'] ) ) ? $attr['titleLeftPaddingTablet'] : $attr['titleHrPadding'];
$title_right_padding_tablet  = ( isset( $attr['titleRightPaddingTablet'] ) && is_numeric( $attr['titleRightPaddingTablet'] ) ) ? $attr['titleRightPaddingTablet'] : $attr['titleHrPadding'];

$content_top_padding    = ( isset( $attr['contentTopPadding'] ) && is_numeric( $attr['contentTopPadding'] ) ) ? $attr['contentTopPadding'] : $attr['contentVrPadding'];
$content_bottom_padding = ( isset( $attr['contentBottomPadding'] ) && is_numeric( $attr['contentBottomPadding'] ) ) ? $attr['contentBottomPadding'] : $attr['contentVrPadding'];
$content_left_padding   = ( isset( $attr['contentLeftPadding'] ) && is_numeric( $attr['contentLeftPadding'] ) ) ? $attr['contentLeftPadding'] : $attr['contentHrPadding'];
$content_right_padding  = ( isset( $attr['contentRightPadding'] ) && is_numeric( $attr['contentRightPadding'] ) ) ? $attr['contentRightPadding'] : $attr['contentHrPadding'];

$content_top_padding_mobile    = ( isset( $attr['contentTopPaddingMobile'] ) && is_numeric( $attr['contentTopPaddingMobile'] ) ) ? $attr['contentTopPaddingMobile'] : $attr['contentVrPadding'];
$content_bottom_padding_mobile = ( isset( $attr['contentBottomPaddingMobile'] ) && is_numeric( $attr['contentBottomPaddingMobile'] ) ) ? $attr['contentBottomPaddingMobile'] : $attr['contentVrPadding'];
$content_left_padding_mobile   = ( isset( $attr['contentLeftPaddingMobile'] ) && is_numeric( $attr['contentLeftPaddingMobile'] ) ) ? $attr['contentLeftPaddingMobile'] : $attr['contentHrPadding'];
$content_right_padding_mobile  = ( isset( $attr['contentRightPaddingMobile'] ) && is_numeric( $attr['contentRightPaddingMobile'] ) ) ? $attr['contentRightPaddingMobile'] : $attr['contentHrPadding'];

$content_top_padding_tablet    = ( isset( $attr['contentTopPaddingTablet'] ) && is_numeric( $attr['contentTopPaddingTablet'] ) ) ? $attr['contentTopPaddingTablet'] : $attr['contentVrPadding'];
$content_bottom_padding_tablet = ( isset( $attr['contentBottomPaddingTablet'] ) && is_numeric( $attr['contentBottomPaddingTablet'] ) ) ? $attr['contentBottomPaddingTablet'] : $attr['contentVrPadding'];
$content_left_padding_tablet   = ( isset( $attr['contentLeftPaddingTablet'] ) && is_numeric( $attr['contentLeftPaddingTablet'] ) ) ? $attr['contentLeftPaddingTablet'] : $attr['contentHrPadding'];
$content_right_padding_tablet  = ( isset( $attr['contentRightPaddingTablet'] ) && is_numeric( $attr['contentRightPaddingTablet'] ) ) ? $attr['contentRightPaddingTablet'] : $attr['contentHrPadding'];

$pos_top_tab        = isset( $attr['titleTopPaddingTablet'] ) ? $attr['titleTopPaddingTablet'] : $attr['titleTopPadding'];
$pos_left_tab       = isset( $attr['titleLeftPaddingTablet'] ) ? $attr['titleLeftPaddingTablet'] : $attr['titleLeftPadding'];
$pos_right_tab      = isset( $attr['titleRightPaddingTablet'] ) ? $attr['titleRightPaddingTablet'] : $attr['titleRightPadding'];
$pos_classic_tab    = isset( $attr['highlightWidthTablet'] ) ? $attr['highlightWidthTablet'] : $attr['highlightWidth'];
$pos_top_unit_tab   = isset( $attr['titleTopPaddingTablet'] ) ? $attr['tabletTitlePaddingUnit'] : $attr['titlePaddingUnit'];
$pos_left_unit_tab  = isset( $attr['titleLeftPaddingTablet'] ) ? $attr['tabletTitlePaddingUnit'] : $attr['titlePaddingUnit'];
$pos_right_unit_tab = isset( $attr['titleRightPaddingTablet'] ) ? $attr['tabletTitlePaddingUnit'] : $attr['titlePaddingUnit'];

$pos_top_mob        = isset( $attr['titleTopPaddingMobile'] ) ? $attr['titleTopPaddingMobile'] : $pos_top_tab;
$pos_left_mob       = isset( $attr['titleLeftPaddingMobile'] ) ? $attr['titleLeftPaddingMobile'] : $pos_left_tab;
$pos_right_mob      = isset( $attr['titleRightPaddingMobile'] ) ? $attr['titleRightPaddingMobile'] : $pos_right_tab;
$pos_classic_mob    = isset( $attr['highlightWidthMobile'] ) ? $attr['highlightWidthMobile'] : $pos_classic_tab;
$pos_top_unit_mob   = isset( $attr['titleTopPaddingMobile'] ) ? $attr['mobileTitlePaddingUnit'] : $pos_top_unit_tab;
$pos_left_unit_mob  = isset( $attr['titleLeftPaddingMobile'] ) ? $attr['mobileTitlePaddingUnit'] : $pos_left_unit_tab;
$pos_right_unit_mob = isset( $attr['titleRightPaddingMobile'] ) ? $attr['mobileTitlePaddingUnit'] : $pos_right_unit_tab;

if ( $attr['noticeDismiss'] ) {
	if ( 'left' === $attr['noticeAlignment'] || 'center' === $attr['noticeAlignment'] ) {
		$right_padding        = $title_right_padding;
		$left_padding         = $title_left_padding;
		$left_padding_mobile  = $title_left_padding_mobile;
		$right_padding_mobile = $title_right_padding_mobile;
		$left_padding_tablet  = $title_left_padding_tablet;
		$right_padding_tablet = $title_right_padding_tablet;
	} else {
		$left_padding         = $title_left_padding;
		$right_padding        = $title_right_padding;
		$left_padding_mobile  = $title_left_padding_mobile;
		$right_padding_mobile = $title_right_padding_mobile;
		$left_padding_tablet  = $title_left_padding_tablet;
		$right_padding_tablet = $title_right_padding_tablet;
	}
} else {
	$left_padding         = $title_left_padding;
	$right_padding        = $title_right_padding;
	$left_padding_mobile  = $title_left_padding_mobile;
	$right_padding_mobile = $title_right_padding_mobile;
	$left_padding_tablet  = $title_left_padding_tablet;
	$right_padding_tablet = $title_right_padding_tablet;
}

$selectors = array(
	'.wp-block-uagb-inline-notice .uagb-notice-title' => array(
		'padding-left'   => UAGB_Helper::get_css_value( $left_padding, $attr['titlePaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $right_padding, $attr['titlePaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $title_top_padding, $attr['titlePaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $title_bottom_padding, $attr['titlePaddingUnit'] ),
		'color'          => $attr['titleColor'],
	),
	' .uagb-notice-text'                              => array(
		'color'          => $attr['textColor'],
		'padding-left'   => UAGB_Helper::get_css_value( $content_left_padding, $attr['contentPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $content_right_padding, $attr['contentPaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $content_top_padding, $attr['contentPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $content_bottom_padding, $attr['contentPaddingUnit'] ),
	),
	' span.uagb-notice-dismiss svg'                   => array( // For Backward.
		'fill'  => $attr['noticeDismissColor'],
		'color' => $attr['noticeDismissColor'],
	),
	' svg'                                            => array( // For Backward.
		'fill'  => $attr['noticeDismissColor'],
		'color' => $attr['noticeDismissColor'],
	),
	' button[type="button"] svg'                      => array(
		'fill'  => $attr['noticeDismissColor'],
		'color' => $attr['noticeDismissColor'],
	),
	'.uagb-dismissable button[type="button"] svg'     => array(
		'width'  => UAGB_Helper::get_css_value( $attr['iconSize'], $attr['iconSizeUnit'] ),
		'height' => UAGB_Helper::get_css_value( $attr['iconSize'], $attr['iconSizeUnit'] ),
		'top'    => UAGB_Helper::get_css_value( $attr['titleTopPadding'], $attr['titlePaddingUnit'] ),
	),
	'.uagb-dismissable > svg'                         => array( // For Backward.
		'width'  => UAGB_Helper::get_css_value( $attr['iconSize'], $attr['iconSizeUnit'] ),
		'height' => UAGB_Helper::get_css_value( $attr['iconSize'], $attr['iconSizeUnit'] ),
		'top'    => UAGB_Helper::get_css_value( $attr['titleTopPadding'], $attr['titlePaddingUnit'] ),
	),
	'.uagb-inline_notice__align-left button[type="button"] svg' => array(
		'right' => UAGB_Helper::get_css_value( $attr['titleRightPadding'], $attr['titlePaddingUnit'] ),
	),
	'.uagb-inline_notice__align-left svg'             => array( // For Backward.
		'right' => UAGB_Helper::get_css_value( $attr['titleRightPadding'], $attr['titlePaddingUnit'] ),
	),
	'.uagb-inline_notice__align-center button[type="button"] svg' => array(
		'right' => UAGB_Helper::get_css_value( $attr['titleRightPadding'], $attr['titlePaddingUnit'] ),
	),
	'.uagb-inline_notice__align-center svg'           => array( // For Backward.
		'right' => UAGB_Helper::get_css_value( $attr['titleRightPadding'], $attr['titlePaddingUnit'] ),
	),
);

$m_selectors = array(
	' .uagb-notice-text'                          => array(
		'color'          => $attr['textColor'],
		'padding-left'   => UAGB_Helper::get_css_value( $content_left_padding_mobile, $attr['mobileContentPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $content_right_padding_mobile, $attr['mobileContentPaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $content_top_padding_mobile, $attr['mobileContentPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $content_bottom_padding_mobile, $attr['mobileContentPaddingUnit'] ),
	),
	' .uagb-notice-title'                         => array(
		'padding-left'   => UAGB_Helper::get_css_value( $left_padding_mobile, $attr['mobileTitlePaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $right_padding_mobile, $attr['mobileTitlePaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $title_top_padding_mobile, $attr['mobileTitlePaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $title_bottom_padding_mobile, $attr['mobileTitlePaddingUnit'] ),
	),
	'.uagb-dismissable button[type="button"] svg' => array(
		'width'  => UAGB_Helper::get_css_value( $attr['iconSizeMob'], $attr['iconSizeUnit'] ),
		'height' => UAGB_Helper::get_css_value( $attr['iconSizeMob'], $attr['iconSizeUnit'] ),
		'top'    => UAGB_Helper::get_css_value( $pos_top_mob, $pos_top_unit_mob ),
	),
	'.uagb-dismissable > svg'                     => array( // For Backward.
		'width'  => UAGB_Helper::get_css_value( $attr['iconSizeMob'], $attr['iconSizeUnit'] ),
		'height' => UAGB_Helper::get_css_value( $attr['iconSizeMob'], $attr['iconSizeUnit'] ),
		'top'    => UAGB_Helper::get_css_value( $pos_top_mob, $pos_top_unit_mob ),
	),
	'.uagb-inline_notice__align-left button[type="button"] svg' => array(
		'right' => UAGB_Helper::get_css_value( $pos_right_mob, $pos_right_unit_mob ),
	),
	'.uagb-inline_notice__align-left svg'         => array( // For Backward.
		'right' => UAGB_Helper::get_css_value( $pos_right_mob, $pos_right_unit_mob ),
	),
	'.uagb-inline_notice__align-center button[type="button"] svg' => array(
		'right' => UAGB_Helper::get_css_value( $pos_right_mob, $pos_right_unit_mob ),
	),
	'.uagb-inline_notice__align-center svg'       => array( // For Backward.
		'right' => UAGB_Helper::get_css_value( $pos_right_mob, $pos_right_unit_mob ),
	),
);

$t_selectors = array(
	' .uagb-notice-text'                          => array(
		'padding-left'   => UAGB_Helper::get_css_value( $content_left_padding_tablet, $attr['tabletContentPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $content_right_padding_tablet, $attr['tabletContentPaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $content_top_padding_tablet, $attr['tabletContentPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $content_bottom_padding_tablet, $attr['tabletContentPaddingUnit'] ),
	),
	' .uagb-notice-title'                         => array(
		'padding-left'   => UAGB_Helper::get_css_value( $left_padding_tablet, $attr['tabletTitlePaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $right_padding_tablet, $attr['tabletTitlePaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $title_top_padding_tablet, $attr['tabletTitlePaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $title_bottom_padding_tablet, $attr['tabletTitlePaddingUnit'] ),
	),
	'.uagb-dismissable button[type="button"] svg' => array(
		'width'  => UAGB_Helper::get_css_value( $attr['iconSizeTab'], $attr['iconSizeUnit'] ),
		'height' => UAGB_Helper::get_css_value( $attr['iconSizeTab'], $attr['iconSizeUnit'] ),
		'top'    => UAGB_Helper::get_css_value( $pos_top_tab, $pos_top_unit_tab ),
	),
	'.uagb-dismissable > svg'                     => array( // For Backward.
		'width'  => UAGB_Helper::get_css_value( $attr['iconSizeTab'], $attr['iconSizeUnit'] ),
		'height' => UAGB_Helper::get_css_value( $attr['iconSizeTab'], $attr['iconSizeUnit'] ),
		'top'    => UAGB_Helper::get_css_value( $pos_top_tab, $pos_top_unit_tab ),
	),
	'.uagb-inline_notice__align-left button[type="button"] svg' => array(
		'right' => UAGB_Helper::get_css_value( $pos_right_tab, $pos_right_unit_tab ),
	),
	'.uagb-inline_notice__align-left svg'         => array( // For Backward.
		'right' => UAGB_Helper::get_css_value( $pos_right_tab, $pos_right_unit_tab ),
	),
	'.uagb-inline_notice__align-center button[type="button"] svg' => array(
		'right' => UAGB_Helper::get_css_value( $pos_right_tab, $pos_right_unit_tab ),
	),
	'.uagb-inline_notice__align-center svg'       => array( // For Backward.
		'right' => UAGB_Helper::get_css_value( $pos_right_tab, $pos_right_unit_tab ),
	),
);

if ( 'modern' === $attr['layout'] ) {

	$selectors[' .uagb-notice-title']['background-color']        = $attr['noticeColor'];
	$selectors[' .uagb-notice-title']['border-top-right-radius'] = '3px';
	$selectors[' .uagb-notice-title']['border-top-left-radius']  = '3px';

	$selectors[' .uagb-notice-text']['background-color']           = $attr['contentBgColor'];
	$selectors[' .uagb-notice-text']['border']                     = '2px solid ' . $attr['noticeColor'];
	$selectors[' .uagb-notice-text']['border-bottom-left-radius']  = '3px';
	$selectors[' .uagb-notice-text']['border-bottom-right-radius'] = '3px';

	$selectors['.uagb-inline_notice__align-right button[type="button"] svg']['left']   = UAGB_Helper::get_css_value( $attr['titleLeftPadding'], $attr['titlePaddingUnit'] );
	$t_selectors['.uagb-inline_notice__align-right button[type="button"] svg']['left'] = UAGB_Helper::get_css_value( $pos_left_tab, $pos_left_unit_tab );
	$m_selectors['.uagb-inline_notice__align-right button[type="button"] svg']['left'] = UAGB_Helper::get_css_value( $pos_left_mob, $pos_left_unit_mob );

} elseif ( 'simple' === $attr['layout'] ) {

	$selectors[' .uagb-notice-title']['background-color'] = $attr['contentBgColor'];
	$selectors[' .uagb-notice-title']['border-left']      = UAGB_Helper::get_css_value( $attr['highlightWidth'], 'px' ) . ' solid ' . $attr['noticeColor'];
	$t_selectors[' .uagb-notice-title']['border-left']    = UAGB_Helper::get_css_value( $attr['highlightWidthTablet'], 'px' ) . ' solid ' . $attr['noticeColor'];
	$m_selectors[' .uagb-notice-title']['border-left']    = UAGB_Helper::get_css_value( $attr['highlightWidthMobile'], 'px' ) . ' solid ' . $attr['noticeColor'];

	$selectors[' .uagb-notice-text']['background-color'] = $attr['contentBgColor'];
	$selectors[' .uagb-notice-text']['border-left']      = UAGB_Helper::get_css_value( $attr['highlightWidth'], 'px' ) . ' solid ' . $attr['noticeColor'];
	$t_selectors[' .uagb-notice-text']['border-left']    = UAGB_Helper::get_css_value( $attr['highlightWidthTablet'], 'px' ) . ' solid ' . $attr['noticeColor'];
	$m_selectors[' .uagb-notice-text']['border-left']    = UAGB_Helper::get_css_value( $attr['highlightWidthMobile'], 'px' ) . ' solid ' . $attr['noticeColor'];

	$selectors['.uagb-inline_notice__align-right button[type="button"] svg']['left']   = 'calc(' . $attr['titleLeftPadding'] . $attr['titlePaddingUnit'] . ' + ' . $attr['highlightWidth'] . 'px)';
	$t_selectors['.uagb-inline_notice__align-right button[type="button"] svg']['left'] = 'calc(' . $pos_left_tab . $pos_left_unit_tab . ' + ' . $pos_classic_tab . 'px)';
	$m_selectors['.uagb-inline_notice__align-right button[type="button"] svg']['left'] = 'calc(' . $pos_left_mob . $pos_left_unit_mob . ' + ' . $pos_classic_mob . 'px)';

}

$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);

$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'title', ' .uagb-notice-title', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'desc', ' .uagb-notice-text', $combined_selectors );

return UAGB_Helper::generate_all_css( $combined_selectors, ' .uagb-block-' . $id );
