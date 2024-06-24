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
UAGB_Block_JS::blocks_wp_search_gfont( $attr );

$selectors            = array();
$t_selectors          = array();
$m_selectors          = array();
$boxShadowPositionCSS = $attr['boxShadowPosition'];

if ( 'outset' === $attr['boxShadowPosition'] ) {
	$boxShadowPositionCSS = '';
}
$paddingInputTop          = isset( $attr['paddingInputTop'] ) ? UAGB_Helper::get_css_value( $attr['paddingInputTop'], $attr['inputPaddingTypeDesktop'] ) : UAGB_Helper::get_css_value( $attr['vinputPaddingDesktop'], $attr['inputPaddingTypeDesktop'] );
$paddingInputRight        = isset( $attr['paddingInputRight'] ) ? UAGB_Helper::get_css_value( $attr['paddingInputRight'], $attr['inputPaddingTypeDesktop'] ) : UAGB_Helper::get_css_value( $attr['hinputPaddingDesktop'], $attr['inputPaddingTypeDesktop'] );
$paddingInputBottom       = isset( $attr['paddingInputBottom'] ) ? UAGB_Helper::get_css_value( $attr['paddingInputBottom'], $attr['inputPaddingTypeDesktop'] ) : UAGB_Helper::get_css_value( $attr['vinputPaddingDesktop'], $attr['inputPaddingTypeDesktop'] );
$paddingInputLeft         = isset( $attr['paddingInputLeft'] ) ? UAGB_Helper::get_css_value( $attr['paddingInputLeft'], $attr['inputPaddingTypeDesktop'] ) : UAGB_Helper::get_css_value( $attr['hinputPaddingDesktop'], $attr['inputPaddingTypeDesktop'] );
$paddingInputTopTablet    = isset( $attr['paddingInputTopTablet'] ) ? UAGB_Helper::get_css_value( $attr['paddingInputTopTablet'], $attr['tabletPaddingInputUnit'] ) : UAGB_Helper::get_css_value( $attr['vinputPaddingTablet'], $attr['inputPaddingTypeDesktop'] );
$paddingInputRightTablet  = isset( $attr['paddingInputRightTablet'] ) ? UAGB_Helper::get_css_value( $attr['paddingInputRightTablet'], $attr['tabletPaddingInputUnit'] ) : UAGB_Helper::get_css_value( $attr['hinputPaddingTablet'], $attr['inputPaddingTypeDesktop'] );
$paddingInputBottomTablet = isset( $attr['paddingInputBottomTablet'] ) ? UAGB_Helper::get_css_value( $attr['paddingInputBottomTablet'], $attr['tabletPaddingInputUnit'] ) : UAGB_Helper::get_css_value( $attr['vinputPaddingTablet'], $attr['inputPaddingTypeDesktop'] );
$paddingInputLeftTablet   = isset( $attr['paddingInputLeftTablet'] ) ? UAGB_Helper::get_css_value( $attr['paddingInputLeftTablet'], $attr['tabletPaddingInputUnit'] ) : UAGB_Helper::get_css_value( $attr['hinputPaddingTablet'], $attr['inputPaddingTypeDesktop'] );
$paddingInputTopMobile    = isset( $attr['paddingInputTopMobile'] ) ? UAGB_Helper::get_css_value( $attr['paddingInputTopMobile'], $attr['mobilePaddingInputUnit'] ) : UAGB_Helper::get_css_value( $attr['vinputPaddingMobile'], $attr['inputPaddingTypeDesktop'] );
$paddingInputRightMobile  = isset( $attr['paddingInputRightMobile'] ) ? UAGB_Helper::get_css_value( $attr['paddingInputRightMobile'], $attr['mobilePaddingInputUnit'] ) : UAGB_Helper::get_css_value( $attr['hinputPaddingMobile'], $attr['inputPaddingTypeDesktop'] );
$paddingInputBottomMobile = isset( $attr['paddingInputBottomMobile'] ) ? UAGB_Helper::get_css_value( $attr['paddingInputBottomMobile'], $attr['mobilePaddingInputUnit'] ) : UAGB_Helper::get_css_value( $attr['vinputPaddingMobile'], $attr['inputPaddingTypeDesktop'] );
$paddingInputLeftMobile   = isset( $attr['paddingInputLeftMobile'] ) ? UAGB_Helper::get_css_value( $attr['paddingInputLeftMobile'], $attr['mobilePaddingInputUnit'] ) : UAGB_Helper::get_css_value( $attr['hinputPaddingMobile'], $attr['inputPaddingTypeDesktop'] );

$iconSize       = UAGB_Helper::get_css_value( $attr['iconSize'], $attr['iconSizeType'] );
$buttonIconSize = UAGB_Helper::get_css_value( $attr['buttonIconSize'], $attr['buttonIconSizeType'] );
$inputCSS       = array(
	'color'            => $attr['textColor'],
	'background-color' => $attr['inputBgColor'],
	'border'           => 0,
	'border-radius'    => '0px',
	'margin'           => 0,
	'outline'          => 'unset',
	'padding-top'      => $paddingInputTop,
	'padding-bottom'   => $paddingInputBottom,
	'padding-right'    => $paddingInputRight,
	'padding-left'     => $paddingInputLeft,
);


$inputBorderCSS       = UAGB_Block_Helper::uag_generate_border_css( $attr, 'input' );
$inputBorderCSS       = UAGB_Block_Helper::uag_generate_deprecated_border_css(
	$inputBorderCSS,
	( isset( $attr['borderWidth'] ) ? $attr['borderWidth'] : '' ),
	( isset( $attr['borderRadius'] ) ? $attr['borderRadius'] : '' ),
	( isset( $attr['borderColor'] ) ? $attr['borderColor'] : '' ),
	( isset( $attr['borderStyle'] ) ? $attr['borderStyle'] : '' )
);
$inputBorderCSSTablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'input', 'tablet' );
$inputBorderCSSMobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'input', 'mobile' );

$boxCSS = array_merge(
	$inputBorderCSS,
	array(
		'outline'    => 'unset',
		'box-shadow' => UAGB_Helper::get_css_value( $attr['boxShadowHOffset'], 'px' ) . ' ' . UAGB_Helper::get_css_value( $attr['boxShadowVOffset'], 'px' ) . ' ' . UAGB_Helper::get_css_value( $attr['boxShadowBlur'], 'px' ) . ' ' . UAGB_Helper::get_css_value( $attr['boxShadowSpread'], 'px' ) . ' ' . $attr['boxShadowColor'] . ' ' . $boxShadowPositionCSS,
		'transition' => 'all .5s',
	)
);
if ( 'px' === $attr['inputSizeType'] ) {
	$boxCSS['max-width'] = UAGB_Helper::get_css_value( $attr['inputSize'], $attr['inputSizeType'] );
} else {
	$boxCSS['width'] = UAGB_Helper::get_css_value( $attr['inputSize'], $attr['inputSizeType'] );
}
$icon_color = $attr['textColor'];

if ( $attr['iconColor'] && '' !== $attr['iconColor'] ) {
	$icon_color = $attr['iconColor'];
}

$selectors = array(
	' .uagb-search-form__container .uagb-search-submit' => array(
		'width'   => UAGB_Helper::get_css_value( $attr['buttonWidth'], $attr['buttonWidthType'] ),
		'padding' => 0,
		'border'  => 0,
	),
	' .uagb-search-form__container .uagb-search-form__input::placeholder' => array(
		'color'   => $attr['textColor'],
		'opacity' => 0.6,
	),
	' .uagb-search-form__container .uagb-search-submit .uagb-wp-search-button-icon-wrap svg' => array(
		'width'     => $buttonIconSize,
		'height'    => $buttonIconSize,
		'font-size' => $buttonIconSize,
		'fill'      => $attr['buttonIconColor'],
	),
	' .uagb-search-form__container .uagb-search-submit:hover .uagb-wp-search-button-icon-wrap svg' => array(
		'fill' => $attr['buttonIconHoverColor'],
	),
	' .uagb-search-form__container .uagb-search-submit .uagb-wp-search-button-text' => array(
		'color' => $attr['buttonTextColor'],
	),
	' .uagb-search-form__container .uagb-search-submit:hover .uagb-wp-search-button-text' => array(
		'color' => $attr['buttonTextHoverColor'],
	),
	'.uagb-layout-input .uagb-wp-search-icon-wrap svg'  => array(
		'width'     => $iconSize,
		'height'    => $iconSize,
		'font-size' => $iconSize,
		'fill'      => $icon_color,
	),
);

if ( 'input-button' === $attr['layout'] || 'input' === $attr['layout'] ) {
	$selectors[' .uagb-search-form__container .uagb-search-form__input'] = $inputCSS;

	$selectors[' .uagb-search-wrapper .uagb-search-form__container']       = $boxCSS;
	$selectors[' .uagb-search-wrapper .uagb-search-form__container:hover'] = array(
		'border-color' => $attr['inputBorderHColor'],
	);

	if ( 'inset' === $attr['boxShadowPosition'] ) {
		$selectors[' .uagb-search-wrapper .uagb-search-form__input'] = array(

			'box-shadow' => UAGB_Helper::get_css_value( $attr['boxShadowHOffset'], 'px' ) . ' ' . UAGB_Helper::get_css_value( $attr['boxShadowVOffset'], 'px' ) . ' ' . UAGB_Helper::get_css_value( $attr['boxShadowBlur'], 'px' ) . ' ' . UAGB_Helper::get_css_value( $attr['boxShadowSpread'], 'px' ) . ' ' . $attr['boxShadowColor'] . ' ' . $boxShadowPositionCSS,
		);
	}

	$selectors[' .uagb-search-form__container .uagb-wp-search-icon-wrap'] = array(
		'background-color' => $attr['inputBgColor'],
		'padding-top'      => $paddingInputTop,
		'padding-bottom'   => $paddingInputBottom,
		'padding-left'     => $paddingInputLeft,
	);
}

$selectors['.uagb-layout-input-button .uagb-search-wrapper .uagb-search-form__container .uagb-search-submit']       = array(
	'background-color' => $attr['buttonBgColor'],
);
$selectors['.uagb-layout-input-button .uagb-search-wrapper .uagb-search-form__container .uagb-search-submit:hover'] = array(
	'background-color' => $attr['buttonBgHoverColor'],
);

$m_selectors = array(
	' .uagb-search-wrapper .uagb-search-form__container' => $inputBorderCSSMobile,
	' .uagb-search-wrapper .uagb-search-form__container .uagb-search-form__input' => array(
		'padding-top'    => $paddingInputTopMobile,
		'padding-bottom' => $paddingInputBottomMobile,
		'padding-right'  => $paddingInputRightMobile,
		'padding-left'   => $paddingInputLeftMobile,
	),
	' .uagb-search-form__container .uagb-wp-search-icon-wrap' => array(
		'padding-top'    => $paddingInputTopMobile,
		'padding-bottom' => $paddingInputBottomMobile,
		'padding-left'   => $paddingInputLeftMobile,
	),
);

$t_selectors        = array(
	' .uagb-search-wrapper .uagb-search-form__container' => $inputBorderCSSTablet,
	' .uagb-search-wrapper .uagb-search-form__container .uagb-search-form__input' => array(
		'padding-top'    => $paddingInputTopTablet,
		'padding-bottom' => $paddingInputBottomTablet,
		'padding-right'  => $paddingInputRightTablet,
		'padding-left'   => $paddingInputLeftTablet,
	),
	' .uagb-search-form__container .uagb-wp-search-icon-wrap' => array(
		'padding-top'    => $paddingInputTopTablet,
		'padding-bottom' => $paddingInputBottomTablet,
		'padding-left'   => $paddingInputLeftTablet,
	),
);
$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'input', ' .uagb-search-wrapper .uagb-search-form__container .uagb-search-form__input', $combined_selectors );

$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'button', ' .uagb-search-wrapper .uagb-search-form__container .uagb-search-submit .uagb-wp-search-button-text', $combined_selectors );

return UAGB_Helper::generate_all_css( $combined_selectors, '.uagb-block-' . $id );
