<?php
/**
 * Slider child front end style
 *
 * @since 2.0.0
 * @var mixed[] $attr
 * @var int $id
 * @package uagb
 */

$block_name = 'slider';

$bg_obj_desktop        = array(
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
$slider_bg_css_desktop = UAGB_Block_Helper::uag_get_background_obj( $bg_obj_desktop );

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

$slider_css = array_merge(
	array(
		'padding-top'    => UAGB_Helper::get_css_value( $attr['topPaddingDesktop'], $attr['paddingType'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['bottomPaddingDesktop'], $attr['paddingType'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['leftPaddingDesktop'], $attr['paddingType'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['rightPaddingDesktop'], $attr['paddingType'] ),
		'margin-top'     => UAGB_Helper::get_css_value( $attr['topMarginDesktop'], $attr['marginType'] ),
		'margin-bottom'  => UAGB_Helper::get_css_value( $attr['bottomMarginDesktop'], $attr['marginType'] ),
		'margin-left'    => UAGB_Helper::get_css_value( $attr['leftMarginDesktop'], $attr['marginType'] ),
		'margin-right'   => UAGB_Helper::get_css_value( $attr['rightMarginDesktop'], $attr['marginType'] ),
	)
);
$slider_css = array_merge( $slider_css, $slider_bg_css_desktop );

$selectors = array(
	' .swiper-content' => $slider_css, // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
);

$bg_obj_tablet        = array(
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
$slider_bg_css_tablet = UAGB_Block_Helper::uag_get_background_obj( $bg_obj_tablet );
$slider_tablet_css    = array_merge(
	array(
		'padding-top'    => UAGB_Helper::get_css_value( $top_padding_tablet, $attr['paddingTypeTablet'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $bottom_padding_tablet, $attr['paddingTypeTablet'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $left_padding_tablet, $attr['paddingTypeTablet'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $right_padding_tablet, $attr['paddingTypeTablet'] ),
		'margin-top'     => UAGB_Helper::get_css_value( $top_margin_tablet, $attr['marginTypeTablet'] ),
		'margin-bottom'  => UAGB_Helper::get_css_value( $bottom_margin_tablet, $attr['marginTypeTablet'] ),
		'margin-left'    => UAGB_Helper::get_css_value( $left_margin_tablet, $attr['marginTypeTablet'] ),
		'margin-right'   => UAGB_Helper::get_css_value( $right_margin_tablet, $attr['marginTypeTablet'] ),
	)
);
$slider_tablet_css    = array_merge( $slider_tablet_css, $slider_bg_css_tablet );

$t_selectors = array(
	' .swiper-content' => $slider_tablet_css, // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
);

$bg_obj_mobile        = array(
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
$slider_bg_css_mobile = UAGB_Block_Helper::uag_get_background_obj( $bg_obj_mobile );
$slider_mobile_css    = array_merge(
	array(
		'padding-top'    => UAGB_Helper::get_css_value( $top_padding_mobile, $attr['paddingTypeMobile'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $bottom_padding_mobile, $attr['paddingTypeMobile'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $left_padding_mobile, $attr['paddingTypeMobile'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $right_padding_mobile, $attr['paddingTypeMobile'] ),
		'margin-top'     => UAGB_Helper::get_css_value( $top_margin_mobile, $attr['marginTypeMobile'] ),
		'margin-bottom'  => UAGB_Helper::get_css_value( $bottom_margin_mobile, $attr['marginTypeMobile'] ),
		'margin-left'    => UAGB_Helper::get_css_value( $left_margin_mobile, $attr['marginTypeMobile'] ),
		'margin-right'   => UAGB_Helper::get_css_value( $right_margin_mobile, $attr['marginTypeMobile'] ),
	)
);
$slider_mobile_css    = array_merge( $slider_mobile_css, $slider_bg_css_mobile );
$m_selectors          = array(
	' .swiper-content' => $slider_mobile_css, // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
);

$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);

return UAGB_Helper::generate_all_css( $combined_selectors, '.uagb-block-' . $id );
