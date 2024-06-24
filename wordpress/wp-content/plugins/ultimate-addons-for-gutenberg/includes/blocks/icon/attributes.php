<?php
/**
 * Attributes File.
 *
 * @since 2.4.0
 *
 * @package uagb
 */

$icon_border_attribute = UAGB_Block_Helper::uag_generate_border_attribute( 'icon' );

return array_merge(
	$icon_border_attribute,
	array(
		// Size.
		'icon'                             => '',
		'iconSize'                         => 40,
		'iconSizeTablet'                   => '',
		'iconSizeMobile'                   => '',
		'iconSizeUnit'                     => 'px',
		// Alignment.
		'align'                            => 'center',
		'alignTablet'                      => '',
		'alignMobile'                      => '',
		// Rotation.
		'rotation'                         => 0,
		'rotationUnit'                     => 'deg',
		// StyleSettings.
		'iconView'                         => 'none',
		'iconBorderWidth'                  => 3,
		// Color.
		'iconColor'                        => '#333',
		'iconBorderColor'                  => '',
		'iconBackgroundColorType'          => 'classic',
		'iconBackgroundColor'              => '',
		'iconBackgroundGradientColor'      => 'linear-gradient(90deg, rgb(155, 81, 224) 0%, rgb(6, 147, 227) 100%)',
		'iconHoverColor'                   => '',
		'iconHoverBorderColor'             => '',
		'iconHoverBackgroundColorType'     => 'classic',
		'iconHoverBackgroundColor'         => '',
		'iconHoverBackgroundGradientColor' => 'linear-gradient(90deg, rgb(155, 81, 224) 0%, rgb(6, 147, 227) 100%)',
		// Padding.
		'iconTopPadding'                   => 5,
		'iconRightPadding'                 => 5,
		'iconBottomPadding'                => 5,
		'iconLeftPadding'                  => 5,
		'iconTopTabletPadding'             => '',
		'iconRightTabletPadding'           => '',
		'iconBottomTabletPadding'          => '',
		'iconLeftTabletPadding'            => '',
		'iconTopMobilePadding'             => '',
		'iconRightMobilePadding'           => '',
		'iconBottomMobilePadding'          => '',
		'iconLeftMobilePadding'            => '',
		'iconPaddingUnit'                  => 'px',
		'iconMobilePaddingUnit'            => 'px',
		'iconTabletPaddingUnit'            => 'px',
		// Margin.
		'iconTopMargin'                    => '',
		'iconRightMargin'                  => '',
		'iconBottomMargin'                 => '',
		'iconLeftMargin'                   => '',
		'iconTopTabletMargin'              => '',
		'iconRightTabletMargin'            => '',
		'iconBottomTabletMargin'           => '',
		'iconLeftTabletMargin'             => '',
		'iconTopMobileMargin'              => '',
		'iconRightMobileMargin'            => '',
		'iconBottomMobileMargin'           => '',
		'iconLeftMobileMargin'             => '',
		'iconMarginUnit'                   => 'px',
		'iconMobileMarginUnit'             => 'px',
		'iconTabletMarginUnit'             => 'px',
		// Shadow.
		'iconShadowColor'                  => '#00000070',
		'iconShadowHOffset'                => 0,
		'iconShadowVOffset'                => 0,
		'iconShadowBlur'                   => 0,
		// Box Shadow.
		'useSeparateBoxShadows'            => true,
		'iconBoxShadowColor'               => '#00000070',
		'iconBoxShadowHOffset'             => 0,
		'iconBoxShadowVOffset'             => 0,
		'iconBoxShadowBlur'                => '',
		'iconBoxShadowSpread'              => '',
		'iconBoxShadowPosition'            => '',
		'iconBoxShadowColorHover'          => '',
		'iconBoxShadowHOffsetHover'        => 0,
		'iconBoxShadowVOffsetHover'        => 0,
		'iconBoxShadowBlurHover'           => '',
		'iconBoxShadowSpreadHover'         => '',
		'iconBoxShadowPositionHover'       => '',
		// Border.
		'iconBorderStyle'                  => 'default',
		// For Global Block Styles.
		'globalBlockStyleName'             => '',
		'globalBlockStyleId'               => '',
		'iconAccessabilityMode'            => '',
		'iconAccessabilityDesc'            => '',
	)
);
