<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 * @var string[] $attr
 * @var int $id
 * @package uagb
 */

// Adds Fonts.
UAGB_Block_JS::blocks_taxonomy_list_gfont( $attr );

$selectors   = array();
$t_selectors = array();
$m_selectors = array();

$overall_border_css       = UAGB_Block_Helper::uag_generate_border_css( $attr, 'overall' );
$overall_border_css       = UAGB_Block_Helper::uag_generate_deprecated_border_css(
	$overall_border_css,
	( isset( $attr['borderThickness'] ) ? $attr['borderThickness'] : '' ),
	( isset( $attr['borderRadius'] ) ? $attr['borderRadius'] : '' ),
	( isset( $attr['borderColor'] ) ? $attr['borderColor'] : '' ),
	( isset( $attr['borderStyle'] ) ? $attr['borderStyle'] : '' )
);
$overall_border_cssTablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'overall', 'tablet' );
$overall_border_cssMobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'overall', 'mobile' );

$top_padding    = isset( $attr['contentTopPadding'] ) ? $attr['contentTopPadding'] : $attr['contentPadding'];
$bottom_padding = isset( $attr['contentBottomPadding'] ) ? $attr['contentBottomPadding'] : $attr['contentPadding'];
$left_padding   = isset( $attr['contentLeftPadding'] ) ? $attr['contentLeftPadding'] : $attr['contentPadding'];
$right_padding  = isset( $attr['contentRightPadding'] ) ? $attr['contentRightPadding'] : $attr['contentPadding'];

$top_padding_mobile    = isset( $attr['contentTopPaddingMobile'] ) ? $attr['contentTopPaddingMobile'] : $attr['contentPaddingMobile'];
$bottom_padding_mobile = isset( $attr['contentBottomPaddingMobile'] ) ? $attr['contentBottomPaddingMobile'] : $attr['contentPaddingMobile'];
$left_padding_mobile   = isset( $attr['contentLeftPaddingMobile'] ) ? $attr['contentLeftPaddingMobile'] : $attr['contentPaddingMobile'];
$right_padding_mobile  = isset( $attr['contentRightPaddingMobile'] ) ? $attr['contentRightPaddingMobile'] : $attr['contentPaddingMobile'];

$top_padding_tablet    = isset( $attr['contentTopPaddingTablet'] ) ? $attr['contentTopPaddingTablet'] : $attr['contentPaddingTablet'];
$bottom_padding_tablet = isset( $attr['contentBottomPaddingTablet'] ) ? $attr['contentBottomPaddingTablet'] : $attr['contentPaddingTablet'];
$left_padding_tablet   = isset( $attr['contentLeftPaddingTablet'] ) ? $attr['contentLeftPaddingTablet'] : $attr['contentPaddingTablet'];
$right_padding_tablet  = isset( $attr['contentRightPaddingTablet'] ) ? $attr['contentRightPaddingTablet'] : $attr['contentPaddingTablet'];

$boxShadowPositionCSS = $attr['boxShadowPosition'];

if ( 'outset' === $attr['boxShadowPosition'] ) {
	$boxShadowPositionCSS = '';
}

$selectors = array(
	'.uagb-taxonomy__outer-wrap.uagb-layout-grid'          => array(
		'display'               => 'grid',
		'grid-template-columns' => 'repeat(' . $attr['columns'] . ', 1fr)',
		'grid-column-gap'       => UAGB_Helper::get_css_value( $attr['columnGap'], 'px' ),
		'grid-row-gap'          => UAGB_Helper::get_css_value( $attr['rowGap'], 'px' ),

	),
	'.uagb-layout-grid .uagb-taxomony-box'                 => array(
		'padding-left'     => UAGB_Helper::get_css_value( $left_padding, $attr['contentPaddingUnit'] ),
		'padding-right'    => UAGB_Helper::get_css_value( $right_padding, $attr['contentPaddingUnit'] ),
		'padding-top'      => UAGB_Helper::get_css_value( $top_padding, $attr['contentPaddingUnit'] ),
		'padding-bottom'   => UAGB_Helper::get_css_value( $bottom_padding, $attr['contentPaddingUnit'] ),
		'grid-column-gap'  => UAGB_Helper::get_css_value( $attr['columnGap'], 'px' ),
		'background-color' => $attr['bgColor'],
		'text-align'       => $attr['alignment'],
		'box-shadow'       => UAGB_Helper::get_css_value( $attr['boxShadowHOffset'], 'px' ) . ' ' . UAGB_Helper::get_css_value( $attr['boxShadowVOffset'], 'px' ) . ' ' . UAGB_Helper::get_css_value( $attr['boxShadowBlur'], 'px' ) . ' ' . UAGB_Helper::get_css_value( $attr['boxShadowSpread'], 'px' ) . ' ' . $attr['boxShadowColor'] . ' ' . $boxShadowPositionCSS,

	),
	'.uagb-layout-grid .uagb-tax-title'                    => array(
		'color'         => $attr['titleColor'],
		'margin-top'    => '0',
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['titleBottomSpace'], 'px' ),
	),
	'.uagb-layout-grid .uagb-tax-link'                     => array(
		'color' => $attr['countColor'],
	),
	// List layout styling.
	'.uagb-layout-list .uagb-tax-list'                     => array(
		'list-style' => $attr['listStyle'],
		'color'      => $attr['listStyleColor'],
	),
	'.uagb-layout-list .uagb-tax-list:hover'               => array( // For bullet.
		'color' => $attr['hoverlistStyleColor'],
	),
	'.uagb-layout-list .uagb-tax-link-wrap:hover'          => array( // For Number.
		'color' => $attr['hoverlistStyleColor'],
	),
	'.uagb-layout-list .uagb-tax-list a.uagb-tax-link'     => array(
		'color' => $attr['listTextColor'],
	),
	'.uagb-layout-list .uagb-tax-list a.uagb-tax-link:hover' => array(
		'color' => $attr['hoverlistTextColor'],
	),
	'.uagb-layout-list .uagb-tax-list .uagb-tax-link-wrap' => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['listBottomMargin'], 'px' ),
	),
	/* For Backword */
	' .uagb-taxonomy-wrap.uagb-layout-grid'                => array(
		'display'               => 'grid',
		'grid-template-columns' => 'repeat(' . $attr['columns'] . ', 1fr)',
		'grid-column-gap'       => UAGB_Helper::get_css_value( $attr['columnGap'], 'px' ),
		'grid-row-gap'          => UAGB_Helper::get_css_value( $attr['rowGap'], 'px' ),

	),
	' .uagb-layout-grid .uagb-taxomony-box'                => array(
		'padding-left'     => UAGB_Helper::get_css_value( $left_padding, $attr['contentPaddingUnit'] ),
		'padding-right'    => UAGB_Helper::get_css_value( $right_padding, $attr['contentPaddingUnit'] ),
		'padding-top'      => UAGB_Helper::get_css_value( $top_padding, $attr['contentPaddingUnit'] ),
		'padding-bottom'   => UAGB_Helper::get_css_value( $bottom_padding, $attr['contentPaddingUnit'] ),
		'grid-column-gap'  => UAGB_Helper::get_css_value( $attr['columnGap'], 'px' ),
		'background-color' => $attr['bgColor'],
		'text-align'       => $attr['alignment'],
		'box-shadow'       => UAGB_Helper::get_css_value( $attr['boxShadowHOffset'], 'px' ) . ' ' . UAGB_Helper::get_css_value( $attr['boxShadowVOffset'], 'px' ) . ' ' . UAGB_Helper::get_css_value( $attr['boxShadowBlur'], 'px' ) . ' ' . UAGB_Helper::get_css_value( $attr['boxShadowSpread'], 'px' ) . ' ' . $attr['boxShadowColor'] . ' ' . $boxShadowPositionCSS,

	),
	' .uagb-layout-grid .uagb-tax-title'                   => array(
		'color'         => $attr['titleColor'],
		'margin-top'    => '0',
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['titleBottomSpace'], 'px' ),
	),
	' .uagb-layout-grid .uagb-tax-link'                    => array(
		'color' => $attr['countColor'],
	),
	// List layout styling.
	' .uagb-layout-list .uagb-tax-list'                    => array(
		'list-style' => $attr['listStyle'],
		'color'      => $attr['listStyleColor'],
	),
	' .uagb-layout-list .uagb-tax-list:hover'              => array(
		'color' => $attr['hoverlistStyleColor'],
	),
	' .uagb-layout-list .uagb-tax-list a.uagb-tax-link'    => array(
		'color' => $attr['listTextColor'],
	),
	' .uagb-layout-list .uagb-tax-list a.uagb-tax-link:hover' => array(
		'color' => $attr['hoverlistTextColor'],
	),
	' .uagb-layout-list .uagb-tax-list .uagb-tax-link-wrap' => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['listBottomMargin'], 'px' ),
	),
	/* End Backword */

);
if ( 'none' !== $attr['seperatorStyle'] ) {
	$selectors['.uagb-layout-list .uagb-tax-separator']        = array(
		'border-top-color' => $attr['seperatorColor'],
		'border-top-style' => $attr['seperatorStyle'],
		'border-top-width' => UAGB_Helper::get_css_value( $attr['seperatorThickness'], 'px' ),
		'width'            => UAGB_Helper::get_css_value( $attr['seperatorWidth'], '%' ),
	);
	$selectors['.uagb-layout-list .uagb-tax-separator:hover']  = array(
		'border-top-color' => $attr['seperatorHoverColor'],
	);
	$selectors[' .uagb-layout-list .uagb-tax-separator']       = array(
		'border-top-color' => $attr['seperatorColor'],
		'border-top-style' => $attr['seperatorStyle'],
		'border-top-width' => UAGB_Helper::get_css_value( $attr['seperatorThickness'], 'px' ),
		'width'            => UAGB_Helper::get_css_value( $attr['seperatorWidth'], '%' ),
	);
	$selectors[' .uagb-layout-list .uagb-tax-separator:hover'] = array(
		'border-top-color' => $attr['seperatorHoverColor'],
	);
}
$selectors['.uagb-layout-list .uagb-tax-separator:hover'] = array(
	'border-top-color' => $attr['seperatorHoverColor'],
);
$selectors[' .uagb-taxomony-box']                         = $overall_border_css;
$selectors[' .uagb-taxomony-box:hover']                   = array(
	'border-color' => $attr['overallBorderHColor'],
);

$t_selectors = array(
	'.uagb-taxonomy-wrap.uagb-layout-grid'        => array( // For Backword.
		'grid-template-columns' => 'repeat(' . $attr['tcolumns'] . ', 1fr)',
	),
	'.uagb-taxonomy__outer-wrap.uagb-layout-grid' => array(
		'grid-template-columns' => 'repeat(' . $attr['tcolumns'] . ', 1fr)',
		'grid-column-gap'       => UAGB_Helper::get_css_value( $attr['columnGapTablet'], 'px' ),
		'grid-row-gap'          => UAGB_Helper::get_css_value( $attr['rowGapTablet'], 'px' ),
	),
	'.uagb-layout-grid .uagb-taxomony-box'        => array(
		'padding-left'   => UAGB_Helper::get_css_value( $left_padding_tablet, $attr['tabletContentPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $right_padding_tablet, $attr['tabletContentPaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $top_padding_tablet, $attr['tabletContentPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $bottom_padding_tablet, $attr['tabletContentPaddingUnit'] ),
	),
	'.uagb-layout-grid .uagb-tax-title'           => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['titleBottomSpaceTablet'], 'px' ),
	),
);

$m_selectors = array(
	'.uagb-taxonomy__outer-wrap.uagb-layout-grid' => array(
		'grid-template-columns' => 'repeat(' . $attr['mcolumns'] . ', 1fr)',
		'grid-column-gap'       => UAGB_Helper::get_css_value( $attr['columnGapMobile'], 'px' ),
		'grid-row-gap'          => UAGB_Helper::get_css_value( $attr['rowGapMobile'], 'px' ),
	),
	'.uagb-layout-grid .uagb-taxomony-box'        => array(
		'padding-left'   => UAGB_Helper::get_css_value( $left_padding_mobile, $attr['mobileContentPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $right_padding_mobile, $attr['mobileContentPaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $top_padding_mobile, $attr['mobileContentPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $bottom_padding_mobile, $attr['mobileContentPaddingUnit'] ),
	),
	'.uagb-layout-grid .uagb-tax-title'           => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['titleBottomSpaceMobile'], 'px' ),
	),
);

$t_selectors[' .uagb-taxomony-box'] = $overall_border_cssTablet;
$m_selectors[' .uagb-taxomony-box'] = $overall_border_cssMobile;

$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);
/* For Backword */
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'title', ' .uagb-layout-grid .uagb-tax-title', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'count', ' .uagb-layout-grid .uagb-tax-link', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'list', ' .uagb-layout-list .uagb-tax-list', $combined_selectors );
/* End Backword */
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'title', '.uagb-layout-grid .uagb-tax-title', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'count', '.uagb-layout-grid .uagb-tax-link', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'list', '.uagb-layout-list .uagb-tax-list', $combined_selectors );

return UAGB_Helper::generate_all_css( $combined_selectors, '.uagb-block-' . $id );
