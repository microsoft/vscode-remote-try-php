<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 * @var mixed[] $attr
 * @var int $id
 * @package uagb
 */

// Adds Fonts.
UAGB_Block_JS::blocks_restaurant_menu_gfont( $attr );

$m_selectors = array();
$t_selectors = array();

$row_gap_tablet_fallback = is_numeric( $attr['rowGapTablet'] ) ? $attr['rowGapTablet'] : $attr['rowGap'];
$row_gap_mobile_fallback = is_numeric( $attr['rowGapMobile'] ) ? $attr['rowGapMobile'] : $row_gap_tablet_fallback;

$attr['columnGapTablet'] = is_numeric( $attr['columnGapTablet'] ) ? $attr['columnGapTablet'] : $attr['columnGap'];
$attr['columnGapMobile'] = is_numeric( $attr['columnGapMobile'] ) ? $attr['columnGapMobile'] : $attr['columnGapTablet'];

$attr['imageWidthTablet'] = is_numeric( $attr['imageWidthTablet'] ) ? $attr['imageWidthTablet'] : $attr['imageWidth'];
$attr['imageWidthMobile'] = is_numeric( $attr['imageWidthMobile'] ) ? $attr['imageWidthMobile'] : $attr['imageWidthTablet'];

$align = $attr['headingAlign'];
if ( 'left' === $align ) {
	$align = 'flex-start';
} elseif ( 'right' === $align ) {
	$align = 'flex-end';
}
$imgPaddingTop    = isset( $attr['imgPaddingTop'] ) ? $attr['imgPaddingTop'] : $attr['imgVrPadding'];
$imgPaddingRight  = isset( $attr['imgPaddingRight'] ) ? $attr['imgPaddingRight'] : $attr['imgHrPadding'];
$imgPaddingBottom = isset( $attr['imgPaddingBottom'] ) ? $attr['imgPaddingBottom'] : $attr['imgVrPadding'];
$imgPaddingLeft   = isset( $attr['imgPaddingLeft'] ) ? $attr['imgPaddingLeft'] : $attr['imgHrPadding'];

$contentPaddingTop    = isset( $attr['contentPaddingTop'] ) ? $attr['contentPaddingTop'] : $attr['contentVrPadding'];
$contentPaddingRight  = isset( $attr['contentPaddingRight'] ) ? $attr['contentPaddingRight'] : $attr['contentHrPadding'];
$contentPaddingBottom = isset( $attr['contentPaddingBottom'] ) ? $attr['contentPaddingBottom'] : $attr['contentVrPadding'];
$contentPaddingLeft   = isset( $attr['contentPaddingLeft'] ) ? $attr['contentPaddingLeft'] : $attr['contentHrPadding'];

$selectors = array(
	'.wp-block-uagb-restaurant-menu' => array(
		'column-gap' => UAGB_Helper::get_css_value( $attr['columnGap'], $attr['columnGapType'] ),
		'row-gap'    => UAGB_Helper::get_css_value( $attr['rowGap'], $attr['rowGapType'] ),
	),
	' .uagb-rest_menu__wrap img'     => array(
		'padding-left'   => UAGB_Helper::get_css_value( $imgPaddingLeft, $attr['imgPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $imgPaddingRight, $attr['imgPaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $imgPaddingTop, $attr['imgPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $imgPaddingBottom, $attr['imgPaddingUnit'] ),
		'width'          => UAGB_Helper::get_css_value( $attr['imageWidth'], $attr['imageWidthType'] ),
		'max-width'      => UAGB_Helper::get_css_value( $attr['imageWidth'], $attr['imageWidthType'] ),
	),
	// Backward.
	' .uagb-rm__separator-parent'    => array(
		'justify-content' => $align,
	),
	' .uagb-rm__content'             => array(
		'padding-left'   => UAGB_Helper::get_css_value( $contentPaddingLeft, $attr['contentPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $contentPaddingRight, $attr['contentPaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $contentPaddingTop, $attr['contentPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $contentPaddingBottom, $attr['contentPaddingUnit'] ),
	),
	' .uagb-rest_menu__wrap .uagb-rm__content .uagb-rm-details .uagb-rm__title' => array(
		'color'         => $attr['titleColor'],
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['titleSpace'], $attr['titleSpaceType'] ),
	),
	' .uagb-rm__price'               => array(
		'color' => $attr['priceColor'],
	),
	' .uagb-rm__desc'                => array(
		'color'         => $attr['descColor'],
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['descSpace'], 'px' ),
	),
);

$t_selectors = array(
	' .uagb-rest_menu__wrap .uagb-rm__content .uagb-rm-details .uagb-rm__title' => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['titleSpaceTablet'], $attr['titleSpaceType'] ),
	),
	'.wp-block-uagb-restaurant-menu' => array(
		'column-gap' => UAGB_Helper::get_css_value( $attr['columnGapTablet'], $attr['columnGapType'] ),
		'row-gap'    => UAGB_Helper::get_css_value( $row_gap_tablet_fallback, $attr['rowGapType'] ),
	),
	' .uagb-rest_menu__wrap img'     => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['imgPaddingLeftTablet'], $attr['imgTabletPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['imgPaddingRightTablet'], $attr['imgTabletPaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['imgPaddingTopTablet'], $attr['imgTabletPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['imgPaddingBottomTablet'], $attr['imgTabletPaddingUnit'] ),
		'width'          => UAGB_Helper::get_css_value( $attr['imageWidthTablet'], $attr['imageWidthType'] ),
		'max-width'      => UAGB_Helper::get_css_value( $attr['imageWidthTablet'], $attr['imageWidthType'] ),
	),
	' .uagb-rm__content'             => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['contentPaddingLeftTablet'], $attr['contentTabletPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['contentPaddingRightTablet'], $attr['contentTabletPaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['contentPaddingTopTablet'], $attr['contentTabletPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['contentPaddingBottomTablet'], $attr['contentTabletPaddingUnit'] ),
	),
);

$m_selectors = array(
	' .uagb-rest_menu__wrap .uagb-rm__content .uagb-rm-details .uagb-rm__title' => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['titleSpaceMobile'], $attr['titleSpaceType'] ),
	),
	'.wp-block-uagb-restaurant-menu' => array(
		'column-gap' => UAGB_Helper::get_css_value( $attr['columnGapMobile'], $attr['columnGapType'] ),
		'row-gap'    => UAGB_Helper::get_css_value( $row_gap_mobile_fallback, $attr['rowGapType'] ),
	),
	' .uagb-rest_menu__wrap img'     => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['imgPaddingLeftMobile'], $attr['imgMobilePaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['imgPaddingRightMobile'], $attr['imgMobilePaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['imgPaddingTopMobile'], $attr['imgMobilePaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['imgPaddingBottomMobile'], $attr['imgMobilePaddingUnit'] ),
		'width'          => UAGB_Helper::get_css_value( $attr['imageWidthMobile'], $attr['imageWidthType'] ),
		'max-width'      => UAGB_Helper::get_css_value( $attr['imageWidthMobile'], $attr['imageWidthType'] ),
	),
	' .uagb-rm__content'             => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['contentPaddingLeftMobile'], $attr['contentMobilePaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['contentPaddingRightMobile'], $attr['contentMobilePaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['contentPaddingTopMobile'], $attr['contentMobilePaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['contentPaddingBottomMobile'], $attr['contentMobilePaddingUnit'] ),
	),
);

if ( 'none' !== $attr['seperatorStyle'] ) {
	$selectors[' .uagb-rest_menu__wrap .uagb-rm__separator']   = array(
		'border-top-color' => $attr['seperatorColor'],
		'border-top-style' => $attr['seperatorStyle'],
		'border-top-width' => UAGB_Helper::get_css_value( $attr['seperatorThickness'], 'px' ),
		'width'            => UAGB_Helper::get_css_value( $attr['seperatorWidth'], $attr['seperatorWidthType'] ),
	);
	$t_selectors[' .uagb-rest_menu__wrap .uagb-rm__separator'] = array(
		'width' => UAGB_Helper::get_css_value( $attr['seperatorWidthTablet'], $attr['seperatorWidthType'] ),
	);
	$m_selectors[' .uagb-rest_menu__wrap .uagb-rm__separator'] = array(
		'width' => UAGB_Helper::get_css_value( $attr['seperatorWidthMobile'], $attr['seperatorWidthType'] ),
	);
}
if ( 1 === $attr['columns'] ) {
	$selectors['.wp-block-uagb-restaurant-menu.uagb-rest_menu__outer-wrap '] = array(
		'grid-template-columns' => 'auto',
	);
}
if ( 2 === $attr['columns'] ) {
	$selectors['.wp-block-uagb-restaurant-menu.uagb-rest_menu__outer-wrap '] = array(
		'grid-template-columns' => 'auto auto',
	);
}
if ( 3 === $attr['columns'] ) {
	$selectors['.wp-block-uagb-restaurant-menu.uagb-rest_menu__outer-wrap '] = array(
		'grid-template-columns' => 'auto auto auto',
	);
}
if ( 4 === $attr['columns'] ) {
	$selectors['.wp-block-uagb-restaurant-menu.uagb-rest_menu__outer-wrap '] = array(
		'grid-template-columns' => 'auto auto auto auto',
	);
}

if ( 1 === $attr['tcolumns'] ) {
	$t_selectors['.wp-block-uagb-restaurant-menu.uagb-rest_menu__outer-wrap '] = array(
		'grid-template-columns' => 'auto',
	);
}
if ( 2 === $attr['tcolumns'] ) {
	$t_selectors['.wp-block-uagb-restaurant-menu.uagb-rest_menu__outer-wrap '] = array(
		'grid-template-columns' => 'auto auto',
	);
}
if ( 3 === $attr['tcolumns'] ) {
	$t_selectors['.wp-block-uagb-restaurant-menu.uagb-rest_menu__outer-wrap '] = array(
		'grid-template-columns' => 'auto auto auto',
	);
}
if ( 4 === $attr['tcolumns'] ) {
	$t_selectors['.wp-block-uagb-restaurant-menu.uagb-rest_menu__outer-wrap '] = array(
		'grid-template-columns' => 'auto auto auto auto',
	);
}
if ( 1 === $attr['mcolumns'] ) {
	$m_selectors['.wp-block-uagb-restaurant-menu.uagb-rest_menu__outer-wrap '] = array(
		'grid-template-columns' => 'auto',
	);
}
if ( 2 === $attr['mcolumns'] ) {
	$m_selectors['.wp-block-uagb-restaurant-menu.uagb-rest_menu__outer-wrap '] = array(
		'grid-template-columns' => 'auto auto',
	);
}
if ( 3 === $attr['mcolumns'] ) {
	$m_selectors['.wp-block-uagb-restaurant-menu.uagb-rest_menu__outer-wrap '] = array(
		'grid-template-columns' => 'auto auto auto',
	);
}
if ( 4 === $attr['mcolumns'] ) {
	$m_selectors['.wp-block-uagb-restaurant-menu.uagb-rest_menu__outer-wrap '] = array(
		'grid-template-columns' => 'auto auto auto auto',
	);
}
if ( 'side' === $attr['imgAlign'] ) {
	$selectors[' .wp-block-uagb-restaurant-menu-child .uagb-rm__content'] = array(
		'align-items' => 'top' === $attr['imageAlignment'] ? 'flex-start' : 'center',
		'display'     => 'inline-flex',
	);

	if ( 'tablet' === $attr['stack'] ) {
		if ( 'left' === $attr['imagePosition'] ) {
			$t_selectors[' .wp-block-uagb-restaurant-menu-child .uagb-rm__content'] = array(
				'display'    => 'block',
				'text-align' => 'left',
			);
			$m_selectors[' .wp-block-uagb-restaurant-menu-child .uagb-rm__content'] = array(
				'display'    => 'block',
				'text-align' => 'left',
			);
		} else {
			$t_selectors[' .wp-block-uagb-restaurant-menu-child .uagb-rm__content'] = array(
				'display'        => 'flex',
				'flex-direction' => 'column-reverse',
				'align-items'    => 'flex-end',
			);
			$m_selectors[' .wp-block-uagb-restaurant-menu-child .uagb-rm__content'] = array(
				'display'        => 'flex',
				'flex-direction' => 'column-reverse',
				'align-items'    => 'flex-end',
			);
		}
	} elseif ( 'mobile' === $attr['stack'] ) {
		if ( 'left' === $attr['imagePosition'] ) {
			$m_selectors[' .wp-block-uagb-restaurant-menu-child .uagb-rm__content'] = array(
				'display'    => 'block',
				'text-align' => 'left',
			);
		} else {
			$m_selectors[' .wp-block-uagb-restaurant-menu-child .uagb-rm__content'] = array(
				'display'        => 'flex',
				'flex-direction' => 'column-reverse',
				'align-items'    => 'flex-end',
			);
		}
	}
	if ( 'left' === $attr['imagePosition'] ) {
		$selectors[' .uagb-rm-details'] = array(
			'text-align' => 'left',
		);
	} elseif ( 'right' === $attr['imagePosition'] ) {
		$selectors[' .uagb-rm-details'] = array(
			'text-align' => 'right',
		);
		$selectors[' .wp-block-uagb-restaurant-menu-child .uagb-rm__separator'] = array(
			'margin-left' => 'auto',
		);
	}
}

if ( 'top' === $attr['imgAlign'] ) {
	$selectors[' .wp-block-uagb-restaurant-menu-child ']                  = array(
		'text-align' => $attr['headingAlign'],
		'display'    => 'block',
	);
	$selectors[' .wp-block-uagb-restaurant-menu-child .uagb-rm__content'] = array(
		'text-align' => $attr['headingAlign'],
		'display'    => 'inline-flex',
	);
	if ( 'center' === $attr['headingAlign'] ) {
		$selectors[' .uagb-rm__content ']                                        = array(
			'display' => 'block',
		);
		$selectors[' .uagb-rm__content ']                                        = array(
			'display' => 'block',
		);
		$selectors[' .wp-block-uagb-restaurant-menu-child  .uagb-rm__separator'] = array(
			'margin' => '0 auto',
		);
	} elseif ( 'right' === $attr['headingAlign'] ) {
		$selectors[' .wp-block-uagb-restaurant-menu-child .uagb-rm__separator'] = array(
			'margin-left' => 'auto',
		);
	}
}
$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);

$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'title', ' .uagb-rm__title', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'price', ' .uagb-rm__price', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'desc', ' .uagb-rm__desc', $combined_selectors );

$base_selector = ( $attr['classMigrate'] ) ? '.wp-block-uagb-restaurant-menu.uagb-block-' : '#uagb-rm-';

return UAGB_Helper::generate_all_css( $combined_selectors, $base_selector . $id );
