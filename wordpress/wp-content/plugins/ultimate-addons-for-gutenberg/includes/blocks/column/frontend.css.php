<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

global $content_width;

$bg_type      = ( isset( $attr['backgroundType'] ) ) ? $attr['backgroundType'] : 'none';
$overlay_type = ( isset( $attr['overlayType'] ) ) ? $attr['overlayType'] : 'none';

$border        = UAGB_Block_Helper::uag_generate_border_css( $attr, 'column' );
$border        = UAGB_Block_Helper::uag_generate_deprecated_border_css(
	$border,
	( isset( $attr['borderWidth'] ) ? $attr['borderWidth'] : '' ),
	( isset( $attr['borderRadius'] ) ? $attr['borderRadius'] : '' ),
	( isset( $attr['borderColor'] ) ? $attr['borderColor'] : '' ),
	( isset( $attr['borderStyle'] ) ? $attr['borderStyle'] : '' )
);
$border_tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'column', 'tablet' );
$border_mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'column', 'mobile' );

$style = array_merge(
	array(
		'padding-top'    => UAGB_Helper::get_css_value( $attr['topPadding'], $attr['desktopPaddingType'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['bottomPadding'], $attr['desktopPaddingType'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['leftPadding'], $attr['desktopPaddingType'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['rightPadding'], $attr['desktopPaddingType'] ),
		'margin-top'     => UAGB_Helper::get_css_value( $attr['topMargin'], $attr['desktopMarginType'] ),
		'margin-bottom'  => UAGB_Helper::get_css_value( $attr['bottomMargin'], $attr['desktopMarginType'] ),
		'margin-left'    => UAGB_Helper::get_css_value( $attr['leftMargin'], $attr['desktopMarginType'] ),
		'margin-right'   => UAGB_Helper::get_css_value( $attr['rightMargin'], $attr['desktopMarginType'] ),
	),
	$border
);

$m_selectors = array();
$t_selectors = array();

$position = str_replace( '-', ' ', $attr['backgroundPosition'] );

if ( 'image' === $bg_type ) {

	$style['background-image']      = ( isset( $attr['backgroundImage'] ) && isset( $attr['backgroundImage']['url'] ) ) ? "url('" . $attr['backgroundImage']['url'] . "' )" : null;
	$style['background-position']   = $position;
	$style['background-attachment'] = $attr['backgroundAttachment'];
	$style['background-repeat']     = $attr['backgroundRepeat'];
	$style['background-size']       = $attr['backgroundSize'];

}

$selectors = array(
	'.uagb-column__wrap' => $style,
);

$selectors['.uagb-column__wrap:hover'] = array(
	'border-color' => $attr['columnBorderHColor'],
);


if ( 'image' === $bg_type ) {
	if ( 'color' === $overlay_type ) {
		$selectors[' > .uagb-column__overlay'] = array(
			'background-color' => $attr['backgroundImageColor'],
			'opacity'          => ( isset( $attr['backgroundOpacity'] ) && '' !== $attr['backgroundOpacity'] && 101 !== $attr['backgroundOpacity'] ) ? $attr['backgroundOpacity'] / 100 : '',
		);
	} else {
		if ( $attr['gradientValue'] ) {
			$selectors[' > .uagb-column__overlay']['background-image'] = $attr['gradientValue'];
		} else {
			$selectors[' > .uagb-column__overlay']['background-color'] = 'transparent';
			$selectors[' > .uagb-column__overlay']['opacity']          = ( isset( $attr['backgroundOpacity'] ) && '' !== $attr['backgroundOpacity'] ) ? $attr['backgroundOpacity'] / 100 : '';
			if ( 'linear' === $attr['gradientOverlayType'] ) {

				$selectors[' > .uagb-column__overlay']['background-image'] = 'linear-gradient(' . $attr['gradientOverlayAngle'] . 'deg, ' . $attr['gradientOverlayColor1'] . ' ' . $attr['gradientOverlayLocation1'] . '%, ' . $attr['gradientOverlayColor2'] . ' ' . $attr['gradientOverlayLocation2'] . '%)';
			} else {

				$selectors[' > .uagb-column__overlay']['background-image'] = 'radial-gradient( at center center, ' . $attr['gradientOverlayColor1'] . ' ' . $attr['gradientOverlayLocation1'] . '%, ' . $attr['gradientOverlayColor2'] . ' ' . $attr['gradientOverlayLocation2'] . '%)';
			}
		}
	}
} elseif ( 'color' === $bg_type ) {
	$selectors[' > .uagb-column__overlay'] = array(
		'background-color' => $attr['backgroundColor'],
		'opacity'          => ( isset( $attr['backgroundOpacity'] ) && '' !== $attr['backgroundOpacity'] && 101 !== $attr['backgroundOpacity'] ) ? $attr['backgroundOpacity'] / 100 : '',
	);
} elseif ( 'gradient' === $bg_type ) {

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
	$selectors[' > .uagb-column__overlay']['background-image'] = $gradient;
}

$selectors[' > .uagb-column__overlay']['border-radius'] = $attr['columnBorderTopLeftRadius'] . ' ' . $attr['columnBorderTopRightRadius'] . ' ' . $attr['columnBorderBottomLeftRadius'] . ' ' . $attr['columnBorderBottomRightRadius'];

if ( '' !== $attr['colWidth'] && 0 !== $attr['colWidth'] ) {

	$selectors['.uagb-column__wrap']['width'] = UAGB_Helper::get_css_value( $attr['colWidth'], '%' );
}

$m_selectors = array(
	'.uagb-column__wrap' => array_merge(
		array(
			'padding-top'    => UAGB_Helper::get_css_value( $attr['topPaddingMobile'], $attr['mobilePaddingType'] ),
			'padding-bottom' => UAGB_Helper::get_css_value( $attr['bottomPaddingMobile'], $attr['mobilePaddingType'] ),
			'padding-left'   => UAGB_Helper::get_css_value( $attr['leftPaddingMobile'], $attr['mobilePaddingType'] ),
			'padding-right'  => UAGB_Helper::get_css_value( $attr['rightPaddingMobile'], $attr['mobilePaddingType'] ),
			'margin-top'     => UAGB_Helper::get_css_value( $attr['topMarginMobile'], $attr['mobileMarginType'] ),
			'margin-bottom'  => UAGB_Helper::get_css_value( $attr['bottomMarginMobile'], $attr['mobileMarginType'] ),
			'margin-left'    => UAGB_Helper::get_css_value( $attr['leftMarginMobile'], $attr['mobileMarginType'] ),
			'margin-right'   => UAGB_Helper::get_css_value( $attr['rightMarginMobile'], $attr['mobileMarginType'] ),
		),
		$border_mobile
	),
);

$t_selectors = array(
	'.uagb-column__wrap' => array_merge(
		array(
			'padding-top'    => UAGB_Helper::get_css_value( $attr['topPaddingTablet'], $attr['tabletPaddingType'] ),
			'padding-bottom' => UAGB_Helper::get_css_value( $attr['bottomPaddingTablet'], $attr['tabletPaddingType'] ),
			'padding-left'   => UAGB_Helper::get_css_value( $attr['leftPaddingTablet'], $attr['tabletPaddingType'] ),
			'padding-right'  => UAGB_Helper::get_css_value( $attr['rightPaddingTablet'], $attr['tabletPaddingType'] ),
			'margin-top'     => UAGB_Helper::get_css_value( $attr['topMarginTablet'], $attr['tabletMarginType'] ),
			'margin-bottom'  => UAGB_Helper::get_css_value( $attr['bottomMarginTablet'], $attr['tabletMarginType'] ),
			'margin-left'    => UAGB_Helper::get_css_value( $attr['leftMarginTablet'], $attr['tabletMarginType'] ),
			'margin-right'   => UAGB_Helper::get_css_value( $attr['rightMarginTablet'], $attr['tabletMarginType'] ),
		),
		$border_tablet
	),
);

if ( '' !== $attr['colWidthTablet'] && 0 !== $attr['colWidthTablet'] ) {

	$t_selectors['.uagb-column__wrap']['width'] = UAGB_Helper::get_css_value( $attr['colWidthTablet'], '%' );
}

if ( '' !== $attr['colWidthMobile'] && 0 !== $attr['colWidthMobile'] ) {

	$m_selectors['.uagb-column__wrap']['width'] = UAGB_Helper::get_css_value( $attr['colWidthMobile'], '%' );
}

$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);

$base_selector = ( $attr['classMigrate'] ) ? '.wp-block-uagb-column.uagb-block-' : '#uagb-column-';

return UAGB_Helper::generate_all_css( $combined_selectors, $base_selector . $id );
