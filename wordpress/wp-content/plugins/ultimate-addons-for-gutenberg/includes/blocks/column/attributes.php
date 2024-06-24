<?php
/**
 * Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$border_attribute = UAGB_Block_Helper::uag_generate_border_attribute( 'column' );

return array_merge(
	array(
		'classMigrate'             => false,
		'block_id'                 => '',
		'topPadding'               => '',
		'bottomPadding'            => '',
		'leftPadding'              => '',
		'rightPadding'             => '',
		'topMargin'                => '',
		'bottomMargin'             => '',
		'leftMargin'               => '',
		'rightMargin'              => '',
		'topPaddingTablet'         => '',
		'bottomPaddingTablet'      => '',
		'leftPaddingTablet'        => '',
		'rightPaddingTablet'       => '',
		'topPaddingMobile'         => '',
		'bottomPaddingMobile'      => '',
		'leftPaddingMobile'        => '',
		'rightPaddingMobile'       => '',
		'topMarginMobile'          => '',
		'bottomMarginMobile'       => '',
		'leftMarginMobile'         => '',
		'rightMarginMobile'        => '',
		'topMarginTablet'          => '',
		'bottomMarginTablet'       => '',
		'leftMarginTablet'         => '',
		'rightMarginTablet'        => '',
		'colWidth'                 => '',
		'colWidthTablet'           => '',
		'colWidthMobile'           => '',
		'backgroundType'           => 'none',
		'backgroundImage'          => '',
		'backgroundPosition'       => 'center-center',
		'backgroundSize'           => 'cover',
		'backgroundRepeat'         => 'no-repeat',
		'backgroundAttachment'     => 'scroll',
		'backgroundColor'          => '',
		'gradientColor1'           => '',
		'gradientColor2'           => '',
		'gradientType'             => 'linear',
		'gradientLocation1'        => 0,
		'gradientLocation2'        => 100,
		'gradientAngle'            => 0,
		'backgroundImageColor'     => '',
		'align'                    => 'center',
		'alignMobile'              => '',
		'alignTablet'              => '',
		'mobileMarginType'         => 'px',
		'tabletMarginType'         => 'px',
		'desktopMarginType'        => 'px',
		'mobilePaddingType'        => 'px',
		'tabletPaddingType'        => 'px',
		'desktopPaddingType'       => 'px',
		'overlayType'              => 'color',
		'gradientOverlayColor1'    => '',
		'gradientOverlayColor2'    => '',
		'gradientOverlayType'      => 'linear',
		'gradientOverlayLocation1' => '0',
		'gradientOverlayLocation2' => '100',
		'gradientOverlayAngle'     => '0',
		'gradientValue'            => '',
		'selectGradient'           => 'basic',
	),
	$border_attribute
);
