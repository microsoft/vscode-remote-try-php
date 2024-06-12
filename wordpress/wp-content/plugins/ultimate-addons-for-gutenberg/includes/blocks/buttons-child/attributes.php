<?php
/**
 * Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$disable_v_h_padding = apply_filters( 'uagb_disable_v_h_padding', false );

$border_attribute = UAGB_Block_Helper::uag_generate_border_attribute( 'btn' );

$enable_legacy_blocks = UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_legacy_blocks', ( 'yes' === get_option( 'uagb-old-user-less-than-2' ) ) ? 'yes' : 'no' );

$v_padding_default = false === $disable_v_h_padding && ( 'yes' === get_option( 'uagb-old-user-less-than-2' ) || 'yes' === $enable_legacy_blocks ) ? 10 : '';

$h_padding_default = false === $disable_v_h_padding && ( 'yes' === get_option( 'uagb-old-user-less-than-2' ) || 'yes' === $enable_legacy_blocks ) ? 14 : '';

$inherit_from_theme = 'enabled' === UAGB_Admin_Helper::get_admin_settings_option( 'uag_btn_inherit_from_theme', 'disabled' );

return array_merge(
	array(
		'inheritFromTheme'       => $inherit_from_theme,
		'buttonType'             => 'primary',
		'block_id'               => '',
		'label'                  => '#Click Here',
		'link'                   => '',
		'opensInNewTab'          => false,
		'target'                 => '',
		'size'                   => '',
		// If the paddings aren't set, the button child will fallback to the following vPadding and hPadding.
		'vPadding'               => $v_padding_default,
		'hPadding'               => $h_padding_default,
		'topTabletPadding'       => '',
		'rightTabletPadding'     => '',
		'bottomTabletPadding'    => '',
		'leftTabletPadding'      => '',
		'topMobilePadding'       => '',
		'rightMobilePadding'     => '',
		'bottomMobilePadding'    => '',
		'leftMobilePadding'      => '',
		'paddingUnit'            => 'px',
		'mobilePaddingUnit'      => 'px',
		'tabletPaddingUnit'      => 'px',
		'paddingLink'            => '',
		'color'                  => '',
		'background'             => '',
		'hColor'                 => '',
		'hBackground'            => '',
		'sizeType'               => 'px',
		'sizeTypeTablet'         => 'px',
		'sizeTypeMobile'         => 'px',
		'sizeMobile'             => '',
		'sizeTablet'             => '',
		'lineHeight'             => '',
		'lineHeightType'         => 'em',
		'lineHeightMobile'       => '',
		'lineHeightTablet'       => '',
		'icon'                   => '',
		'iconPosition'           => 'after',
		'iconSpace'              => 8,
		'iconSpaceTablet'        => '',
		'iconSpaceMobile'        => '',
		'iconSize'               => 15,
		'iconSizeTablet'         => '',
		'iconSizeMobile'         => '',
		'LoadGoogleFonts'        => '',
		'noFollow'               => false,
		'fontFamily'             => '',
		'fontWeight'             => '',
		'fontStyle'              => '',
		'transform'              => '',
		'decoration'             => '',
		'backgroundType'         => 'color',
		'hoverbackgroundType'    => 'color',
		'topMargin'              => '',
		'rightMargin'            => '',
		'bottomMargin'           => '',
		'leftMargin'             => '',
		'topMarginTablet'        => '',
		'rightMarginTablet'      => '',
		'bottomMarginTablet'     => '',
		'leftMarginTablet'       => '',
		'topMarginMobile'        => '',
		'rightMarginMobile'      => '',
		'bottomMarginMobile'     => '',
		'leftMarginMobile'       => '',
		'marginType'             => 'px',
		'marginLink'             => '',
		'boxShadowColor'         => '#00000026',
		'boxShadowHOffset'       => 0,
		'boxShadowVOffset'       => 0,
		'boxShadowBlur'          => '',
		'boxShadowSpread'        => '',
		'boxShadowPosition'      => 'outset',
		'useSeparateBoxShadows'  => true,
		'boxShadowColorHover'    => '',
		'boxShadowHOffsetHover'  => 0,
		'boxShadowVOffsetHover'  => 0,
		'boxShadowBlurHover'     => '',
		'boxShadowSpreadHover'   => '',
		'boxShadowPositionHover' => 'outset',
		'iconColor'              => '',
		'iconHColor'             => '',
		'buttonSize'             => '',
		'removeText'             => false,
		'gradientValue'          => '',
		'gradientColor1'         => '#06558a',
		'gradientColor2'         => '#0063A1',
		'gradientType'           => 'linear',
		'gradientLocation1'      => 0,
		'gradientLocation2'      => 100,
		'gradientAngle'          => 0,
		'selectGradient'         => 'basic',
		'hovergradientValue'     => '',
		'hovergradientColor1'    => '#06558a',
		'hovergradientColor2'    => '#0063A1',
		'hovergradientType'      => 'linear',
		'hovergradientLocation1' => 0,
		'hovergradientLocation2' => 100,
		'hovergradientAngle'     => 0,
		'hoverselectGradient'    => 'basic',
		'backgroundOpacity'      => '',
		'backgroundHoverOpacity' => '',
		// letter spacing.
		'letterSpacing'          => '',
		'letterSpacingTablet'    => '',
		'letterSpacingMobile'    => '',
		'letterSpacingType'      => 'px',
		'borderWidth'            => '',
		'borderRadius'           => '',
		'borderStyle'            => 'solid',
		'borderColor'            => '#000',
		'borderHColor'           => '',
		// For Global Block Styles.
		'globalBlockStyleName'   => '',
		'globalBlockStyleId'     => '',
	),
	$border_attribute
);
