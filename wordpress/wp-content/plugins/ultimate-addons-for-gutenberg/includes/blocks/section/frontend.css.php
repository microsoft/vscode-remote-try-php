<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 * @package uagb
 */

global $content_width;

/**
 * Note: Fixing issue due to constraints on variable usage before a global declaration.
 * 
 * @var mixed[] $attr
 * @var int $id
 * @var string $gradient
 */

$overall_border_css = UAGB_Block_Helper::uag_generate_border_css( $attr, 'overall' );
$overall_border_css = UAGB_Block_Helper::uag_generate_deprecated_border_css(
	$overall_border_css,
	( isset( $attr['borderWidth'] ) && is_string( $attr['borderWidth'] ) ? $attr['borderWidth'] : '' ),
	( isset( $attr['borderRadius'] ) && is_string( $attr['borderRadius'] ) ? $attr['borderRadius'] : '' ),
	( isset( $attr['borderColor'] ) && is_string( $attr['borderColor'] ) ? $attr['borderColor'] : '' ),
	( isset( $attr['borderStyle'] ) && is_string( $attr['borderStyle'] ) ? $attr['borderStyle'] : '' )
);

$overall_border_css_tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'overall', 'tablet' );
$overall_border_css_mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'overall', 'mobile' );


$bg_type                 = ( isset( $attr['backgroundType'] ) ) ? $attr['backgroundType'] : 'none';
$overlay_type            = ( isset( $attr['overlayType'] ) ) ? $attr['overlayType'] : 'color';
$gradientOverlayPosition = ( isset( $attr['gradientOverlayPosition'] ) ) ? $attr['gradientOverlayPosition'] : 'center center';
$gradientPosition        = ( isset( $attr['gradientPosition'] ) ) ? $attr['gradientPosition'] : 'center center';

$boxShadowPositionCSS = $attr['boxShadowPosition'];
if ( 'outset' === $attr['boxShadowPosition'] ) {
	$boxShadowPositionCSS = '';
}

$style  = array(
	'padding-top'    => UAGB_Helper::get_css_value( $attr['topPadding'], $attr['desktopPaddingType'] ),
	'padding-bottom' => UAGB_Helper::get_css_value( $attr['bottomPadding'], $attr['desktopPaddingType'] ),
	'padding-left'   => UAGB_Helper::get_css_value( $attr['leftPadding'], $attr['desktopPaddingType'] ),
	'padding-right'  => UAGB_Helper::get_css_value( $attr['rightPadding'], $attr['desktopPaddingType'] ),
	'margin-top'     => UAGB_Helper::get_css_value( $attr['topMargin'], $attr['desktopMarginType'] ),
	'margin-bottom'  => UAGB_Helper::get_css_value( $attr['bottomMargin'], $attr['desktopMarginType'] ),
);
$style += $overall_border_css;

$m_selectors = array();
$t_selectors = array();
if ( 'boxed' === $attr['contentWidth'] ) {
	switch ( $attr['align'] ) {
		case 'right':
			$style['margin-right'] = UAGB_Helper::get_css_value( $attr['rightMargin'], $attr['desktopMarginType'] );
			$style['margin-left']  = 'auto';
			break;
		case 'left':
			$style['margin-right'] = 'auto';
			$style['margin-left']  = UAGB_Helper::get_css_value( $attr['leftMargin'], $attr['desktopMarginType'] );
			break;
		case 'center':
			$style['margin-right'] = 'auto';
			$style['margin-left']  = 'auto';
			break;
	}
}
if ( 'full_width' === $attr['contentWidth'] ) {
	$style['margin-right'] = UAGB_Helper::get_css_value( $attr['rightMargin'], $attr['desktopMarginType'] );
	$style['margin-left']  = UAGB_Helper::get_css_value( $attr['leftMargin'], $attr['desktopMarginType'] );
}

$position = str_replace( '-', ' ', $attr['backgroundPosition'] );

$section_width = '100%';

if ( isset( $attr['contentWidth'] ) && ( 'boxed' === $attr['contentWidth'] && isset( $attr['width'] ) ) ) {
	$section_width = UAGB_Helper::get_css_value( $attr['width'], 'px' );
}

if ( 'wide' !== $attr['align'] && 'full' !== $attr['align'] ) {
	$style['max-width'] = $section_width;
}

if ( 'image' === $bg_type ) {

	$style['background-image']      = ( isset( $attr['backgroundImage'] ) && isset( $attr['backgroundImage']['url'] ) ) ? "url('" . $attr['backgroundImage']['url'] . "' )" : null;
	$style['background-position']   = $position;
	$style['background-attachment'] = $attr['backgroundAttachment'];
	$style['background-repeat']     = $attr['backgroundRepeat'];
	$style['background-size']       = $attr['backgroundSize'];

}

$inner_width = '100%';

if ( isset( $attr['contentWidth'] ) ) {
	if ( 'boxed' !== $attr['contentWidth'] ) {
		if ( isset( $attr['themeWidth'] ) && $attr['themeWidth'] ) {
			$inner_width = UAGB_Helper::get_css_value( $content_width, 'px' );
		} else {
			if ( isset( $attr['innerWidth'] ) ) {
				$inner_width = UAGB_Helper::get_css_value( $attr['innerWidth'], $attr['innerWidthType'] );
			}
		}
	}
}

$video_opacity = 0.5;
if ( isset( $attr['backgroundVideoOpacity'] ) && '' !== $attr['backgroundVideoOpacity'] ) {
	$video_opacity = ( 1 < $attr['backgroundVideoOpacity'] ) ? ( ( 100 - $attr['backgroundVideoOpacity'] ) / 100 ) : ( ( 1 - $attr['backgroundVideoOpacity'] ) );
}

$selectors = array(
	'.uagb-section__wrap'          => $style,
	' > .uagb-section__video-wrap' => array(
		'opacity' => $video_opacity,
	),
	' > .uagb-section__inner-wrap' => array(
		'max-width' => $inner_width,
	),
	'.wp-block-uagb-section'       => array(
		'box-shadow' => UAGB_Helper::get_css_value( $attr['boxShadowHOffset'], 'px' ) . ' ' . UAGB_Helper::get_css_value( $attr['boxShadowVOffset'], 'px' ) . ' ' . UAGB_Helper::get_css_value( $attr['boxShadowBlur'], 'px' ) . ' ' . UAGB_Helper::get_css_value( $attr['boxShadowSpread'], 'px' ) . ' ' . $attr['boxShadowColor'] . ' ' . $boxShadowPositionCSS,
	),
	'.uagb-section__wrap:hover'    => array(
		'border-color' => $attr['overallBorderHColor'],
	),
);

if ( 'video' === $bg_type ) {
	if ( 'color' === $overlay_type ) {
		$selectors[' > .uagb-section__overlay'] = array(
			'background-color' => $attr['backgroundVideoColor'],
		);
	} else {
		$selectors[' > .uagb-section__overlay']['background-image'] = $attr['gradientValue'];
	}
} elseif ( 'image' === $bg_type ) {
	if ( 'color' === $overlay_type ) {
		$selectors[' > .uagb-section__overlay'] = array(
			'background-color' => $attr['backgroundImageColor'],
			'opacity'          => ( isset( $attr['backgroundOpacity'] ) && '' !== $attr['backgroundOpacity'] && 101 !== $attr['backgroundOpacity'] && 0 !== $attr['backgroundOpacity'] ) ? $attr['backgroundOpacity'] / 100 : '',
		);
	} else {
		if ( $attr['gradientValue'] ) {
			$selectors[' > .uagb-section__overlay']['background-image'] = $attr['gradientValue'];
		} else {
			$selectors[' > .uagb-section__overlay']['background-color'] = 'transparent';
			$selectors[' > .uagb-section__overlay']['opacity']          = ( isset( $attr['backgroundOpacity'] ) && '' !== $attr['backgroundOpacity'] && 101 !== $attr['backgroundOpacity'] && 0 !== $attr['backgroundOpacity'] ) ? $attr['backgroundOpacity'] / 100 : '';
			if ( 'linear' === $attr['gradientOverlayType'] ) {

				$selectors[' > .uagb-section__overlay']['background-image'] = 'linear-gradient(' . $attr['gradientOverlayAngle'] . 'deg, ' . $attr['gradientOverlayColor1'] . ' ' . $attr['gradientOverlayLocation1'] . '%, ' . $attr['gradientOverlayColor2'] . ' ' . $attr['gradientOverlayLocation2'] . '%)';
			} else {

				$selectors[' > .uagb-section__overlay']['background-image'] = 'radial-gradient( at ' . $gradientOverlayPosition . ', ' . $attr['gradientOverlayColor1'] . ' ' . $attr['gradientOverlayLocation1'] . '%, ' . $attr['gradientOverlayColor2'] . ' ' . $attr['gradientOverlayLocation2'] . '%)';
			}
		}
	}
} elseif ( 'color' === $bg_type ) {
	$selectors[' > .uagb-section__overlay'] = array(
		'background-color' => $attr['backgroundColor'],
		'opacity'          => ( isset( $attr['backgroundOpacity'] ) && '' !== $attr['backgroundOpacity'] && 101 !== $attr['backgroundOpacity'] && 0 !== $attr['backgroundOpacity'] ) ? $attr['backgroundOpacity'] / 100 : '',
	);
} elseif ( 'gradient' === $bg_type ) {
	$selectors[' > .uagb-section__overlay']['background-color'] = 'transparent';
	$selectors[' > .uagb-section__overlay']['opacity']          = ( isset( $attr['backgroundOpacity'] ) && '' !== $attr['backgroundOpacity'] && 101 !== $attr['backgroundOpacity'] && 0 !== $attr['backgroundOpacity'] ) ? $attr['backgroundOpacity'] / 100 : '';

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
	$selectors[' > .uagb-section__overlay']['background-image'] = $gradient;
}

$selectors[' > .uagb-section__overlay']['border-radius'] = $attr['overallBorderTopLeftRadius'] . ' ' . $attr['overallBorderTopRightRadius'] . ' ' . $attr['overallBorderBottomLeftRadius'] . ' ' . $attr['overallBorderBottomRightRadius'];

$m_selectors = array(
	'.uagb-section__wrap' => array_merge(
		array(
			'padding-top'    => UAGB_Helper::get_css_value( $attr['topPaddingMobile'], $attr['mobilePaddingType'] ),
			'padding-bottom' => UAGB_Helper::get_css_value( $attr['bottomPaddingMobile'], $attr['mobilePaddingType'] ),
			'padding-left'   => UAGB_Helper::get_css_value( $attr['leftPaddingMobile'], $attr['mobilePaddingType'] ),
			'padding-right'  => UAGB_Helper::get_css_value( $attr['rightPaddingMobile'], $attr['mobilePaddingType'] ),
		),
		$overall_border_css_mobile
),
);

$t_selectors                                      = array(
	'.uagb-section__wrap' => array_merge(
		array(
			'padding-top'    => UAGB_Helper::get_css_value( $attr['topPaddingTablet'], $attr['tabletPaddingType'] ),
			'padding-bottom' => UAGB_Helper::get_css_value( $attr['bottomPaddingTablet'], $attr['tabletPaddingType'] ),
			'padding-left'   => UAGB_Helper::get_css_value( $attr['leftPaddingTablet'], $attr['tabletPaddingType'] ),
			'padding-right'  => UAGB_Helper::get_css_value( $attr['rightPaddingTablet'], $attr['tabletPaddingType'] ),
		),
		$overall_border_css_tablet
	),
);
$m_selectors['.uagb-section__wrap']['margin-top'] = UAGB_Helper::get_css_value( $attr['topMarginMobile'], $attr['mobileMarginType'] );
$m_selectors['.uagb-section__wrap']['margin-bottom'] = UAGB_Helper::get_css_value( $attr['bottomMarginMobile'], $attr['mobileMarginType'] );
$t_selectors['.uagb-section__wrap']['margin-top']    = UAGB_Helper::get_css_value( $attr['topMarginTablet'], $attr['tabletMarginType'] );
$t_selectors['.uagb-section__wrap']['margin-bottom'] = UAGB_Helper::get_css_value( $attr['bottomMarginTablet'], $attr['tabletMarginType'] );
if ( 'boxed' === $attr['contentWidth'] ) {
	if ( 'right' === $attr['align'] ) {
		$t_selectors['.uagb-section__wrap']['margin-right'] = UAGB_Helper::get_css_value( $attr['rightMarginTablet'], $attr['tabletMarginType'] );
		$m_selectors['.uagb-section__wrap']['margin-right'] = UAGB_Helper::get_css_value( $attr['rightMarginMobile'], $attr['mobileMarginType'] );
	} elseif ( 'left' === $attr['align'] ) {
		$t_selectors['.uagb-section__wrap']['margin-left'] = UAGB_Helper::get_css_value( $attr['leftMarginTablet'], $attr['tabletMarginType'] );
		$m_selectors['.uagb-section__wrap']['margin-left'] = UAGB_Helper::get_css_value( $attr['leftMarginMobile'], $attr['mobileMarginType'] );
	}
}

$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);

$base_selector = ( $attr['classMigrate'] ) ? '.uagb-block-' : '#uagb-section-';

return UAGB_Helper::generate_all_css( $combined_selectors, $base_selector . $id );
