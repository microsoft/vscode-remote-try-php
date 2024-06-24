<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

/**
 * Adding this comment to avoid PHPStan errors of undefined variable as these variables are defined else where.
 *
 * @var mixed[] $attr
 * @var int $id
 */

// Adds Fonts.
UAGB_Block_JS::blocks_testimonial_gfont( $attr );

$row_gap_tablet_fallback    = is_numeric( $attr['rowGapTablet'] ) ? $attr['rowGapTablet'] : $attr['rowGap'];
$row_gap_mobile_fallback    = is_numeric( $attr['rowGapMobile'] ) ? $attr['rowGapMobile'] : $row_gap_tablet_fallback;
$column_gap_tablet_fallback = is_numeric( $attr['columnGapTablet'] ) ? $attr['columnGapTablet'] : $attr['columnGap'];
$column_gap_mobile_fallback = is_numeric( $attr['columnGapMobile'] ) ? $attr['columnGapMobile'] : $column_gap_tablet_fallback;

$img_align = 'center';
if ( 'left' === $attr['headingAlign'] ) {
	$img_align = 'flex-start';
} elseif ( 'right' === $attr['headingAlign'] ) {
	$img_align = 'flex-end';
}

$overall_border        = UAGB_Block_Helper::uag_generate_border_css( $attr, 'overall' );
$overall_border        = UAGB_Block_Helper::uag_generate_deprecated_border_css(
	$overall_border,
	( isset( $attr['borderWidth'] ) ? $attr['borderWidth'] : '' ),
	( isset( $attr['borderRadius'] ) ? $attr['borderRadius'] : '' ),
	( isset( $attr['borderColor'] ) ? $attr['borderColor'] : '' ),
	( isset( $attr['borderStyle'] ) ? $attr['borderStyle'] : '' )
);
$overall_border_Tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'overall', 'tablet' );
$overall_border_Mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'overall', 'mobile' );

$position = str_replace( '-', ' ', $attr['backgroundPosition'] );

$t_selectors = array();
$m_selectors = array();

$paddingTop    = isset( $attr['paddingTop'] ) ? $attr['paddingTop'] : $attr['contentPadding'];
$paddingBottom = isset( $attr['paddingBottom'] ) ? $attr['paddingBottom'] : $attr['contentPadding'];
$paddingLeft   = isset( $attr['paddingLeft'] ) ? $attr['paddingLeft'] : $attr['contentPadding'];
$paddingRight  = isset( $attr['paddingRight'] ) ? $attr['paddingRight'] : $attr['contentPadding'];

$imgpaddingTop    = isset( $attr['imgpaddingTop'] ) ? $attr['imgpaddingTop'] : $attr['imgVrPadding'];
$imgpaddingRight  = isset( $attr['imgpaddingRight'] ) ? $attr['imgpaddingRight'] : $attr['imgHrPadding'];
$imgpaddingBottom = isset( $attr['imgpaddingBottom'] ) ? $attr['imgpaddingBottom'] : $attr['imgVrPadding'];
$imgpaddingLeft   = isset( $attr['imgpaddingLeft'] ) ? $attr['imgpaddingLeft'] : $attr['imgHrPadding'];

$selectors = array(
	' .uagb-testimonial__wrap'                         => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['rowGap'], $attr['rowGapType'] ),
		'padding-left'  => UAGB_Helper::get_css_value( ( ( $attr['columnGap'] ) / 2 ), $attr['columnGapType'] ),
		'padding-right' => UAGB_Helper::get_css_value( ( ( $attr['columnGap'] ) / 2 ), $attr['columnGapType'] ),
	),
	' .uagb-tm__content'                               => array(
		'text-align'     => $attr['headingAlign'],
		'padding-top'    => UAGB_Helper::get_css_value( $paddingTop, $attr['paddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $paddingBottom, $attr['paddingUnit'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $paddingLeft, $attr['paddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $paddingRight, $attr['paddingUnit'] ),
		'align-content'  => $attr['vAlignContent'],
	),
	' .uagb-testimonial__wrap .uagb-tm__image-content' => array(
		'text-align'     => $attr['headingAlign'],
		'padding-top'    => UAGB_Helper::get_css_value( $imgpaddingTop, $attr['imgpaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $imgpaddingBottom, $attr['imgpaddingUnit'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $imgpaddingLeft, $attr['imgpaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $imgpaddingRight, $attr['imgpaddingUnit'] ),
	),
	' .uagb-tm__image img'                             => array(
		'width'     => UAGB_Helper::get_css_value( $attr['imageWidth'], $attr['imageWidthType'] ),
		'max-width' => UAGB_Helper::get_css_value( $attr['imageWidth'], $attr['imageWidthType'] ),
	),

	' .uagb-tm__author-name'                           => array(
		'color'         => $attr['authorColor'],
		'margin-bottom' => $attr['nameSpace'] . $attr['nameSpaceType'],
	),
	' .uagb-tm__company'                               => array(
		'color' => $attr['companyColor'],
	),
	' .uagb-tm__desc'                                  => array(
		'color'         => $attr['descColor'],
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['descSpace'], $attr['descSpaceType'] ),
	),
	' .uagb-testimonial__wrap .uagb-tm__content'       => $overall_border,
	' .uagb-testimonial__wrap .uagb-tm__content:hover' => array(
		'border-color' => $attr['overallBorderHColor'],
	),
	' ul.slick-dots li button:before'                  => array(
		'color' => $attr['arrowColor'],
	),
	' ul.slick-dots li.slick-active button:before'     => array(
		'color' => $attr['arrowColor'],
	),
	' .uagb-tm__image-position-top .uagb-tm__image-content' => array(
		'justify-content' => $img_align,
	),
);

$gradient = '';

$gradientColor1    = isset( $attr['gradientColor1'] ) ? $attr['gradientColor1'] : '';
$gradientColor2    = isset( $attr['gradientColor2'] ) ? $attr['gradientColor2'] : '';
$gradientType      = isset( $attr['gradientType'] ) ? $attr['gradientType'] : '';
$gradientLocation1 = isset( $attr['gradientLocation1'] ) ? $attr['gradientLocation1'] : '';
$gradientLocation2 = isset( $attr['gradientLocation2'] ) ? $attr['gradientLocation2'] : '';
$gradientAngle     = isset( $attr['gradientAngle'] ) ? $attr['gradientAngle'] : '';

if ( 'basic' === $attr['selectGradient'] && $attr['gradientValue'] ) {
	$gradient = $attr['gradientValue'];
} elseif ( 'linear' === $gradientType && 'advanced' === $attr['selectGradient'] ) {
	$gradient = 'linear-gradient(' . $gradientAngle . 'deg, ' . $gradientColor1 . ' ' . $gradientLocation1 . '%, ' . $gradientColor2 . ' ' . $gradientLocation2 . '%)';
} elseif ( 'radial' === $gradientType && 'advanced' === $attr['selectGradient'] ) {
	$gradient = 'radial-gradient( at center center, ' . $gradientColor1 . ' ' . $gradientLocation1 . '%, ' . $gradientColor2 . ' ' . $gradientLocation2 . '%)';
}

if ( 'gradient' === $attr['backgroundType'] ) {
	$selectors[' .uagb-tm__content'] = array(
		'background-color' => 'transparent',
		'background-image' => $gradient,
	);
}
if ( 'image' === $attr['backgroundType'] ) {
	if ( 'color' === $attr['overlayType'] ) {
		$selectors[' .uagb-testimonial__wrap.uagb-tm__bg-type-image .uagb-tm__overlay'] = array(
			'background-color' => $attr['backgroundImageColor'],
			'opacity'          => ( isset( $attr['backgroundOpacity'] ) && '' !== $attr['backgroundOpacity'] && 101 !== $attr['backgroundOpacity'] ) ? ( ( 100 - $attr['backgroundOpacity'] ) / 100 ) : '',
		);
	} elseif ( 'gradient' === $attr['overlayType'] ) {
			$selectors[' .uagb-testimonial__wrap.uagb-tm__bg-type-image .uagb-tm__overlay']['background-image'] = $gradient;
	}
} else {
	$selectors['  .uagb-testimonial__wrap.uagb-tm__bg-type-color .uagb-tm__content'] = array(
		'background-color' => $attr['backgroundColor'],
	);
}

if ( true === $attr['equalHeight'] ) {
	$selectors['  .uagb-tm__content'] = array(
		'height' => '-webkit-fill-available',
	);
}

$selectors['  .uagb-testimonial__wrap.uagb-tm__bg-type-image .uagb-tm__content'] = array(
	'background-image'    => ( isset( $attr['backgroundImage']['url'] ) && '' !== $attr['backgroundImage']['url'] ) ? 'url("' . $attr['backgroundImage']['url'] . '")' : null,
	'background-position' => $position,
	'background-repeat'   => $attr['backgroundRepeat'],
	'background-size'     => $attr['backgroundSize'],
);
if ( 'dots' === $attr['arrowDots'] ) {
	$selectors['.uagb-slick-carousel'] = array(
		'padding' => '0 0 35px 0',
	);
}

if ( '1' === $attr['test_item_count'] || $attr['test_item_count'] === $attr['columns'] ) {
	$selectors['.uagb-slick-carousel'] = array(
		'padding' => 0,
	);
}

$m_selectors = array(
	' .uagb-testimonial__wrap'                          => array(
		'padding-left'  => UAGB_Helper::get_css_value( ( ( $column_gap_mobile_fallback ) / 2 ), $attr['columnGapType'] ),
		'padding-right' => UAGB_Helper::get_css_value( ( ( $column_gap_mobile_fallback ) / 2 ), $attr['columnGapType'] ),
		'margin-bottom' => UAGB_Helper::get_css_value( $row_gap_mobile_fallback, $attr['rowGapType'] ),
	),
	' .uagb-tm__image img'                              => array(
		'width'     => UAGB_Helper::get_css_value( $attr['imageWidthMobile'], $attr['imageWidthType'] ),
		'max-width' => UAGB_Helper::get_css_value( $attr['imageWidthMobile'], $attr['imageWidthType'] ),
	),
	' .uagb-tm__author-name'                            => array(
		'margin-bottom' => $attr['nameSpaceMobile'] . $attr['nameSpaceType'],
	),

	' .uagb-testimonial__wrap .uagb-tm__content'        => $overall_border_Mobile,
	' .uagb-tm__desc'                                   => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['descSpaceMobile'], $attr['descSpaceType'] ),
		'margin-left'   => ( ( 1 === $attr['test_item_count'] ) || ( 'dots' === $attr['arrowDots'] ) || ( 1 !== $attr['columns'] ) ) ? 'auto' : '20px',
		'margin-right'  => ( ( 1 === $attr['test_item_count'] ) || ( 'dots' === $attr['arrowDots'] ) || ( 1 !== $attr['columns'] ) ) ? 'auto' : '20px',
	),
	' .uagb-tm__content'                                => array(
		'text-align'     => $attr['headingAlignMobile'],
		'padding-top'    => UAGB_Helper::get_css_value( $attr['paddingTopMobile'], $attr['mobilePaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['paddingBottomMobile'], $attr['mobilePaddingUnit'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['paddingLeftMobile'], $attr['mobilePaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['paddingRightMobile'], $attr['mobilePaddingUnit'] ),
		'align-content'  => $attr['vAlignContent'],
	),
	'  .uagb-testimonial__wrap .uagb-tm__image-content' => array(
		'text-align'     => $attr['headingAlignMobile'],
		'padding-top'    => UAGB_Helper::get_css_value( $attr['imgpaddingTopMobile'], $attr['imgmobilePaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['imgpaddingBottomMobile'], $attr['imgmobilePaddingUnit'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['imgpaddingLeftMobile'], $attr['imgmobilePaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['imgpaddingRightMobile'], $attr['imgmobilePaddingUnit'] ),
	),
);
$t_selectors = array(
	' .uagb-testimonial__wrap'                          => array(
		'padding-left'  => UAGB_Helper::get_css_value( ( ( $column_gap_tablet_fallback ) / 2 ), $attr['columnGapType'] ),
		'padding-right' => UAGB_Helper::get_css_value( ( ( $column_gap_tablet_fallback ) / 2 ), $attr['columnGapType'] ),
		'margin-bottom' => UAGB_Helper::get_css_value( $row_gap_tablet_fallback, $attr['rowGapType'] ),
	),
	' .uagb-tm__image img'                              => array(
		'width'     => UAGB_Helper::get_css_value( $attr['imageWidthTablet'], $attr['imageWidthType'] ),
		'max-width' => UAGB_Helper::get_css_value( $attr['imageWidthTablet'], $attr['imageWidthType'] ),
	),
	' .uagb-tm__author-name'                            => array(
		'margin-bottom' => $attr['nameSpaceTablet'] . $attr['nameSpaceType'],
	),
	' .uagb-tm__desc'                                   => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['descSpaceTablet'], $attr['descSpaceType'] ),
	),

	' .uagb-testimonial__wrap .uagb-tm__content'        => $overall_border_Tablet,
	' .uagb-tm__content'                                => array(
		'text-align'     => $attr['headingAlignTablet'],
		'padding-top'    => UAGB_Helper::get_css_value( $attr['paddingTopTablet'], $attr['tabletPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['paddingBottomTablet'], $attr['tabletPaddingUnit'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['paddingLeftTablet'], $attr['tabletPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['paddingRightTablet'], $attr['tabletPaddingUnit'] ),
		'align-content'  => $attr['vAlignContent'],
	),
	'  .uagb-testimonial__wrap .uagb-tm__image-content' => array(
		'text-align'     => $attr['headingAlignTablet'],
		'padding-top'    => UAGB_Helper::get_css_value( $attr['imgpaddingTopTablet'], $attr['imgtabletPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['imgpaddingRightTablet'], $attr['imgtabletPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['imgpaddingBottomTablet'], $attr['imgtabletPaddingUnit'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['imgpaddingLeftTablet'], $attr['imgtabletPaddingUnit'] ),
	),
);

$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);

$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'name', '  .uagb-tm__author-name', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'company', ' .uagb-tm__company', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'desc', ' .uagb-tm__desc', $combined_selectors );

$base_selector = ( $attr['classMigrate'] ) ? '.uagb-block-' : '#uagb-testimonial-';

return UAGB_Helper::generate_all_css( $combined_selectors, $base_selector . $id );
