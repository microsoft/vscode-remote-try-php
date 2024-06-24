<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.3.0
 * @var mixed[] $attr
 * @var int $id
 * @package uagb
 */

$block_name = 'slider';

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

$border        = UAGB_Block_Helper::uag_generate_border_css( $attr, 'slider' );
$border_tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'slider', 'tablet' );
$border_mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'slider', 'mobile' );

$arrow_border        = UAGB_Block_Helper::uag_generate_border_css( $attr, 'slider-arrow' );
$arrow_border_tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'slider-arrow', 'tablet' );
$arrow_border_mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'slider-arrow', 'mobile' );

$bg_obj_desktop           = array(
	'backgroundType'           => $attr['backgroundType'],
	'backgroundImage'          => $attr['backgroundImageDesktop'],
	'backgroundColor'          => $attr['backgroundColor'],
	'gradientValue'            => $attr['gradientValue'],
	'gradientColor1'           => $attr['gradientColor1'],
	'gradientColor2'           => $attr['gradientColor2'],
	'gradientType'             => $attr['gradientType'],
	'gradientLocation1'        => $attr['gradientLocation1'],
	'gradientLocation2'        => $attr['gradientLocation2'],
	'gradientAngle'            => $attr['gradientAngle'],
	'selectGradient'           => $attr['selectGradient'],
	'backgroundRepeat'         => $attr['backgroundRepeatDesktop'],
	'backgroundPosition'       => $attr['backgroundPositionDesktop'],
	'backgroundSize'           => $attr['backgroundSizeDesktop'],
	'backgroundAttachment'     => $attr['backgroundAttachmentDesktop'],
	'backgroundImageColor'     => $attr['backgroundImageColor'],
	'overlayType'              => $attr['overlayType'],
	'backgroundCustomSize'     => $attr['backgroundCustomSizeDesktop'],
	'backgroundCustomSizeType' => $attr['backgroundCustomSizeType'],
	'customPosition'           => $attr['customPosition'],
	'xPosition'                => $attr['xPositionDesktop'],
	'xPositionType'            => $attr['xPositionType'],
	'yPosition'                => $attr['yPositionDesktop'],
	'yPositionType'            => $attr['yPositionType'],
);
$container_bg_css_desktop = UAGB_Block_Helper::uag_get_background_obj( $bg_obj_desktop );

// Tablet.
$left_padding_tablet   = '' !== $attr['leftPaddingTablet'] ? $attr['leftPaddingTablet'] : $attr['leftPaddingDesktop'];
$right_padding_tablet  = '' !== $attr['rightPaddingTablet'] ? $attr['rightPaddingTablet'] : $attr['rightPaddingDesktop'];
$top_padding_tablet    = '' !== $attr['topPaddingTablet'] ? $attr['topPaddingTablet'] : $attr['topPaddingDesktop'];
$bottom_padding_tablet = '' !== $attr['bottomPaddingTablet'] ? $attr['bottomPaddingTablet'] : $attr['bottomPaddingDesktop'];

$left_margin_tablet   = '' !== $attr['leftMarginTablet'] ? $attr['leftMarginTablet'] : $attr['leftMarginDesktop'];
$right_margin_tablet  = '' !== $attr['rightMarginTablet'] ? $attr['rightMarginTablet'] : $attr['rightMarginDesktop'];
$top_margin_tablet    = '' !== $attr['topMarginTablet'] ? $attr['topMarginTablet'] : $attr['topMarginDesktop'];
$bottom_margin_tablet = '' !== $attr['bottomMarginTablet'] ? $attr['bottomMarginTablet'] : $attr['bottomMarginDesktop'];

// Mobile.
$left_padding_mobile   = '' !== $attr['leftPaddingMobile'] ? $attr['leftPaddingMobile'] : $left_padding_tablet;
$right_padding_mobile  = '' !== $attr['rightPaddingMobile'] ? $attr['rightPaddingMobile'] : $right_padding_tablet;
$top_padding_mobile    = '' !== $attr['topPaddingMobile'] ? $attr['topPaddingMobile'] : $top_padding_tablet;
$bottom_padding_mobile = '' !== $attr['bottomPaddingMobile'] ? $attr['bottomPaddingMobile'] : $bottom_padding_tablet;

$left_margin_mobile   = '' !== $attr['leftMarginMobile'] ? $attr['leftMarginMobile'] : $left_margin_tablet;
$right_margin_mobile  = '' !== $attr['rightMarginMobile'] ? $attr['rightMarginMobile'] : $right_margin_tablet;
$top_margin_mobile    = '' !== $attr['topMarginMobile'] ? $attr['topMarginMobile'] : $top_margin_tablet;
$bottom_margin_mobile = '' !== $attr['bottomMarginMobile'] ? $attr['bottomMarginMobile'] : $bottom_margin_tablet;

$arrow_size_tablet = '' !== $attr['arrowSizeTablet'] ? $attr['arrowSizeTablet'] : $attr['arrowSize'];
$arrow_size_mobile = '' !== $attr['arrowSizeMobile'] ? $attr['arrowSizeMobile'] : $arrow_size_tablet;

$arrow_distance_tablet = '' !== $attr['arrowDistanceTablet'] ? $attr['arrowDistanceTablet'] : $attr['arrowDistance'];
$arrow_distance_mobile = '' !== $attr['arrowDistanceMobile'] ? $attr['arrowDistanceMobile'] : $arrow_distance_tablet;

$arrow_padding_tablet = '' !== $attr['arrowPaddingTablet'] ? $attr['arrowPaddingTablet'] : $attr['arrowPadding'];
$arrow_padding_mobile = '' !== $attr['arrowPaddingMobile'] ? $attr['arrowPaddingMobile'] : $arrow_padding_tablet;

$container_css = array_merge(
	array(
		'box-shadow'     => $box_shadow_css,
		'padding-top'    => UAGB_Helper::get_css_value( $attr['topPaddingDesktop'], $attr['paddingType'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['bottomPaddingDesktop'], $attr['paddingType'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['leftPaddingDesktop'], $attr['paddingType'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['rightPaddingDesktop'], $attr['paddingType'] ),
		'margin-top'     => UAGB_Helper::get_css_value( $attr['topMarginDesktop'], $attr['marginType'] ),
		'margin-bottom'  => UAGB_Helper::get_css_value( $attr['bottomMarginDesktop'], $attr['marginType'] ),
		'margin-left'    => UAGB_Helper::get_css_value( $attr['leftMarginDesktop'], $attr['marginType'] ),
		'margin-right'   => UAGB_Helper::get_css_value( $attr['rightMarginDesktop'], $attr['marginType'] ),
	),
	$border
);
$container_css = array_merge( $container_css, $container_bg_css_desktop );

$arrow_style = array(
	'color'            => esc_attr( $attr['arrowColor'] ),
	'background-color' => esc_attr( $attr['arrowBgColor'] ),
	'width'            => UAGB_Helper::get_css_value( ( $attr['arrowPadding'] * 2 ) + $attr['arrowSize'], 'px' ),
	'height'           => UAGB_Helper::get_css_value( ( $attr['arrowPadding'] * 2 ) + $attr['arrowSize'], 'px' ),
	'line-height'      => UAGB_Helper::get_css_value( ( $attr['arrowPadding'] * 2 ) + $attr['arrowSize'], 'px' ),
);

$arrow_style = array_merge( $arrow_border, $arrow_style );

$selectors = array(
	'.uagb-block-' . $id                                  => $container_css, // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	'.uagb-block-' . $id . '.uagb-slider-container:hover' => array(
		'border-color' => $attr['sliderBorderHColor'],
	),
	'.uagb-block-' . $id . '.uagb-slider-container'       => array(
		'border-color' => $border['border-color'] ? $border['border-color'] : '#4B4F58',
	),
	'.uagb-block-' . $id . ' .swiper-button-next:after'   => array(
		'font-size' => UAGB_Helper::get_css_value( $attr['arrowSize'], 'px' ),
	),
	'.uagb-block-' . $id . ' .swiper-button-prev:after'   => array(
		'font-size' => UAGB_Helper::get_css_value( $attr['arrowSize'], 'px' ),
	),
	'.uagb-block-' . $id . ' .swiper-pagination-bullet'   => array(
		'background-color' => $attr['arrowColor'],
	),
	'.uagb-block-' . $id . ' .swiper-button-prev'         => array(
		'left' => UAGB_Helper::get_css_value( $attr['arrowDistance'], 'px' ),
	),
	'.uagb-block-' . $id . '.uagb-slider-container .swiper-button-prev' => $arrow_style,
	'.uagb-block-' . $id . '.uagb-slider-container .swiper-button-next' => $arrow_style,
	'.uagb-block-' . $id . '.uagb-slider-container .swiper-button-next:hover' => array(
		'border-color' => $attr['slider-arrowBorderHColor'],
	),
	'.uagb-block-' . $id . '.uagb-slider-container .swiper-button-prev:hover' => array(
		'border-color' => $attr['slider-arrowBorderHColor'],
	),
	'.uagb-block-' . $id . ' .swiper-button-next'         => array(
		'right' => UAGB_Helper::get_css_value( $attr['arrowDistance'], 'px' ),
	),
	'.uagb-block-' . $id . ' .swiper-wrapper'             => array(
		'align-items' => $attr['verticalAlign'],
		'min-height'  => UAGB_Helper::get_css_value( $attr['minHeight'], 'px' ),
	),
	'.uagb-block-' . $id . ' .swiper-pagination'          => array(
		'bottom' => UAGB_Helper::get_css_value( $attr['dotsMarginTop'], 'px' ),
	),
);

// If hover blur or hover color are set, show the hover shadow.
if ( $attr['useSeparateBoxShadows'] ) {

	$selectors[ '.uagb-block-' . $id . ':hover' ]['box-shadow'] = $box_shadow_hover_css;

}

$bg_obj_tablet           = array(
	'backgroundType'           => $attr['backgroundType'],
	'backgroundImage'          => $attr['backgroundImageTablet'],
	'backgroundColor'          => $attr['backgroundColor'],
	'gradientValue'            => $attr['gradientValue'],
	'gradientColor1'           => $attr['gradientColor1'],
	'gradientColor2'           => $attr['gradientColor2'],
	'gradientType'             => $attr['gradientType'],
	'gradientLocation1'        => $attr['gradientLocation1'],
	'gradientLocation2'        => $attr['gradientLocation2'],
	'gradientAngle'            => $attr['gradientAngle'],
	'selectGradient'           => $attr['selectGradient'],
	'backgroundRepeat'         => $attr['backgroundRepeatTablet'],
	'backgroundPosition'       => $attr['backgroundPositionTablet'],
	'backgroundSize'           => $attr['backgroundSizeTablet'],
	'backgroundAttachment'     => $attr['backgroundAttachmentTablet'],
	'backgroundImageColor'     => $attr['backgroundImageColor'],
	'overlayType'              => $attr['overlayType'],
	'backgroundCustomSize'     => $attr['backgroundCustomSizeTablet'],
	'backgroundCustomSizeType' => $attr['backgroundCustomSizeType'],
	'customPosition'           => $attr['customPosition'],
	'xPosition'                => $attr['xPositionTablet'],
	'xPositionType'            => $attr['xPositionTypeTablet'],
	'yPosition'                => $attr['yPositionTablet'],
	'yPositionType'            => $attr['yPositionTypeTablet'],
);
$container_bg_css_tablet = UAGB_Block_Helper::uag_get_background_obj( $bg_obj_tablet );
$container_tablet_css    = array_merge(
	array(
		'padding-top'    => UAGB_Helper::get_css_value( $top_padding_tablet, $attr['paddingTypeTablet'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $bottom_padding_tablet, $attr['paddingTypeTablet'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $left_padding_tablet, $attr['paddingTypeTablet'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $right_padding_tablet, $attr['paddingTypeTablet'] ),
		'margin-top'     => UAGB_Helper::get_css_value( $top_margin_tablet, $attr['marginTypeTablet'] ),
		'margin-bottom'  => UAGB_Helper::get_css_value( $bottom_margin_tablet, $attr['marginTypeTablet'] ),
		'margin-left'    => UAGB_Helper::get_css_value( $left_margin_tablet, $attr['marginTypeTablet'] ),
		'margin-right'   => UAGB_Helper::get_css_value( $right_margin_tablet, $attr['marginTypeTablet'] ),
	),
	$border_tablet
);
$container_tablet_css    = array_merge( $container_tablet_css, $container_bg_css_tablet );

$arrow_style_tablet = array(
	'width'       => UAGB_Helper::get_css_value( ( $arrow_padding_tablet * 2 ) + $arrow_size_tablet, 'px' ),
	'height'      => UAGB_Helper::get_css_value( ( $arrow_padding_tablet * 2 ) + $arrow_size_tablet, 'px' ),
	'line-height' => UAGB_Helper::get_css_value( ( $arrow_padding_tablet * 2 ) + $arrow_size_tablet, 'px' ),
);

$arrow_style_tablet = array_merge( $arrow_border_tablet, $arrow_style_tablet );

$t_selectors = array(
	'.uagb-block-' . $id                                => $container_tablet_css, // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	'.uagb-block-' . $id . ' .swiper-button-prev:after' => array(
		'font-size' => UAGB_Helper::get_css_value( $arrow_size_tablet, 'px' ),
	),
	'.uagb-block-' . $id . ' .swiper-button-next:after' => array(
		'font-size' => UAGB_Helper::get_css_value( $arrow_size_tablet, 'px' ),
	),
	'.uagb-block-' . $id . ' .swiper-button-prev'       => array(
		'left' => UAGB_Helper::get_css_value( $arrow_distance_tablet, 'px' ),
	),
	'.uagb-block-' . $id . ' .swiper-button-next'       => array(
		'right' => UAGB_Helper::get_css_value( $arrow_distance_tablet, 'px' ),
	),
	'.uagb-block-' . $id . ' .swiper-pagination'        => array(
		'margin-top' => UAGB_Helper::get_css_value( $attr['dotsMarginTopTablet'], 'px' ),
	),
	'.uagb-block-' . $id . '.uagb-slider-container .swiper-button-prev' => $arrow_style_tablet,
	'.uagb-block-' . $id . '.uagb-slider-container .swiper-button-next' => $arrow_style_tablet,
	'.uagb-block-' . $id . ' .swiper-wrapper'           => array(
		'min-height' => UAGB_Helper::get_css_value( $attr['minHeightTablet'], 'px' ),
	),
);

$bg_obj_mobile           = array(
	'backgroundType'           => $attr['backgroundType'],
	'backgroundImage'          => $attr['backgroundImageMobile'],
	'backgroundColor'          => $attr['backgroundColor'],
	'gradientValue'            => $attr['gradientValue'],
	'gradientColor1'           => $attr['gradientColor1'],
	'gradientColor2'           => $attr['gradientColor2'],
	'gradientType'             => $attr['gradientType'],
	'gradientLocation1'        => $attr['gradientLocation1'],
	'gradientLocation2'        => $attr['gradientLocation2'],
	'gradientAngle'            => $attr['gradientAngle'],
	'selectGradient'           => $attr['selectGradient'],
	'backgroundRepeat'         => $attr['backgroundRepeatMobile'],
	'backgroundPosition'       => $attr['backgroundPositionMobile'],
	'backgroundSize'           => $attr['backgroundSizeMobile'],
	'backgroundAttachment'     => $attr['backgroundAttachmentMobile'],
	'backgroundImageColor'     => $attr['backgroundImageColor'],
	'overlayType'              => $attr['overlayType'],
	'backgroundCustomSize'     => $attr['backgroundCustomSizeMobile'],
	'backgroundCustomSizeType' => $attr['backgroundCustomSizeType'],
	'customPosition'           => $attr['customPosition'],
	'xPosition'                => $attr['xPositionMobile'],
	'xPositionType'            => $attr['xPositionTypeMobile'],
	'yPosition'                => $attr['yPositionMobile'],
	'yPositionType'            => $attr['yPositionTypeMobile'],
);
$container_bg_css_mobile = UAGB_Block_Helper::uag_get_background_obj( $bg_obj_mobile );
$container_mobile_css    = array_merge(
	array(
		'padding-top'    => UAGB_Helper::get_css_value( $top_padding_mobile, $attr['paddingTypeMobile'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $bottom_padding_mobile, $attr['paddingTypeMobile'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $left_padding_mobile, $attr['paddingTypeMobile'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $right_padding_mobile, $attr['paddingTypeMobile'] ),
		'margin-top'     => UAGB_Helper::get_css_value( $top_margin_mobile, $attr['marginTypeMobile'] ),
		'margin-bottom'  => UAGB_Helper::get_css_value( $bottom_margin_mobile, $attr['marginTypeMobile'] ),
		'margin-left'    => UAGB_Helper::get_css_value( $left_margin_mobile, $attr['marginTypeMobile'] ),
		'margin-right'   => UAGB_Helper::get_css_value( $right_margin_mobile, $attr['marginTypeMobile'] ),
	),
	$border_mobile
);
$container_mobile_css    = array_merge( $container_mobile_css, $container_bg_css_mobile );

$arrow_style_mobile = array(
	'width'       => UAGB_Helper::get_css_value( ( $arrow_padding_mobile * 2 ) + $arrow_size_mobile, 'px' ),
	'height'      => UAGB_Helper::get_css_value( ( $arrow_padding_mobile * 2 ) + $arrow_size_mobile, 'px' ),
	'line-height' => UAGB_Helper::get_css_value( ( $arrow_padding_mobile * 2 ) + $arrow_size_mobile, 'px' ),
);

$arrow_style_mobile = array_merge( $arrow_border_mobile, $arrow_style_mobile );

$m_selectors = array(
	'.uagb-block-' . $id                                => $container_mobile_css, // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	'.uagb-block-' . $id . ' .swiper-button-prev:after' => array(
		'font-size' => UAGB_Helper::get_css_value( $arrow_size_mobile, 'px' ),
	),
	'.uagb-block-' . $id . ' .swiper-button-next:after' => array(
		'font-size' => UAGB_Helper::get_css_value( $arrow_size_mobile, 'px' ),
	),
	'.uagb-block-' . $id . ' .swiper-button-prev'       => array(
		'left' => UAGB_Helper::get_css_value( $arrow_distance_mobile, 'px' ),
	),
	'.uagb-block-' . $id . ' .swiper-button-next'       => array(
		'right' => UAGB_Helper::get_css_value( $arrow_distance_mobile, 'px' ),
	),
	'.uagb-block-' . $id . ' .swiper-pagination'        => array(
		'margin-top' => UAGB_Helper::get_css_value( $attr['dotsMarginTopMobile'], 'px' ),
	),
	'.uagb-block-' . $id . '.uagb-slider-container .swiper-button-prev' => $arrow_style_mobile,
	'.uagb-block-' . $id . '.uagb-slider-container .swiper-button-next' => $arrow_style_mobile,
	'.uagb-block-' . $id . ' .swiper-wrapper'           => array(
		'min-height' => UAGB_Helper::get_css_value( $attr['minHeightMobile'], 'px' ),
	),
);

$z_index        = isset( $attr['zIndex'] ) ? $attr['zIndex'] : '';
$z_index_tablet = isset( $attr['zIndexTablet'] ) ? $attr['zIndexTablet'] : '';
$z_index_mobile = isset( $attr['zIndexMobile'] ) ? $attr['zIndexMobile'] : '';

$selectors[ '.uagb-block-' . $id . '.uag-blocks-common-selector' ] = array(
	'--z-index-desktop' => $z_index,
	'--z-index-tablet'  => $z_index_tablet,
	'--z-index-mobile'  => $z_index_mobile,
);

$combined_selectors = UAGB_Helper::get_combined_selectors(
	'slider', 
	array(
		'desktop' => $selectors,
		'tablet'  => $t_selectors,
		'mobile'  => $m_selectors,
	),
	$attr
);

return UAGB_Helper::generate_all_css( $combined_selectors, '.uagb-slider-container' );
