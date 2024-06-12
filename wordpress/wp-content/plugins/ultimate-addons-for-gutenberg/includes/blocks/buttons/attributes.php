<?php
/**
 * Attributes File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$default_buttons_child = array();

for ( $i = 1; $i <= 2; $i++ ) {
	$default_buttons_child[] = array(
		'size'             => '',
		'vPadding'         => '',
		'hPadding'         => '',
		'borderWidth'      => '',
		'borderRadius'     => '',
		'borderStyle'      => 'none',
		'borderColor'      => '',
		'borderHColor'     => '',
		'color'            => '',
		'background'       => '',
		'hColor'           => '',
		'hBackground'      => '',
		'sizeType'         => 'px',
		'sizeMobile'       => '',
		'sizeTablet'       => '',
		'lineHeightType'   => 'em',
		'lineHeight'       => '',
		'lineHeightMobile' => '',
		'lineHeightTablet' => '',
	);
}

return array(
	'classMigrate'            => false,
	'childMigrate'            => false,
	'block_id'                => '',
	'align'                   => 'center',
	'btn_count'               => '1',
	'buttons'                 => $default_buttons_child,
	'gap'                     => 10,
	'gapTablet'               => '',
	'gapMobile'               => '',
	'inheritGap'              => false,
	'flexWrap'                => false,
	'stack'                   => 'none',
	'fontFamily'              => '',
	'fontWeight'              => '',
	'loadGoogleFonts'         => false,
	'fontStyle'               => '',
	'fontTransform'           => '',
	'fontDecoration'          => '',
	'alignTablet'             => 'center',
	'alignMobile'             => 'center',
	'fontSizeType'            => 'px',
	'fontSizeTypeTablet'      => 'px',
	'fontSizeTypeMobile'      => 'px',
	'fontSize'                => '',
	'fontSizeMobile'          => '',
	'fontSizeTablet'          => '',
	'lineHeightType'          => 'px',
	'lineHeight'              => '',
	'lineHeightMobile'        => '',
	'lineHeightTablet'        => '',
	'buttonSize'              => 'default',
	'buttonSizeTablet'        => 'default',
	'buttonSizeMobile'        => 'default',

	'topPadding'              => '',
	'rightPadding'            => '',
	'bottomPadding'           => '',
	'leftPadding'             => '',

	'topTabletPadding'        => '',
	'rightTabletPadding'      => '',
	'bottomTabletPadding'     => '',
	'leftTabletPadding'       => '',

	'topMobilePadding'        => '',
	'rightMobilePadding'      => '',
	'bottomMobilePadding'     => '',
	'leftMobilePadding'       => '',

	'paddingUnit'             => 'px',
	'mobilePaddingUnit'       => 'px',
	'tabletPaddingUnit'       => 'px',

	'topMargin'               => '',
	'rightMargin'             => '',
	'bottomMargin'            => '',
	'leftMargin'              => '',

	'topMarginTablet'         => '',
	'rightMarginTablet'       => '',
	'bottomMarginTablet'      => '',
	'leftMarginTablet'        => '',

	'topMarginMobile'         => '',
	'rightMarginMobile'       => '',
	'bottomMarginMobile'      => '',
	'leftMarginMobile'        => '',

	'marginType'              => 'px',
	'marginLink'              => '',

	'verticalAlignment'       => '',

	// letter spacing.
	'fontLetterSpacing'       => '',
	'fontLetterSpacingTablet' => '',
	'fontLetterSpacingMobile' => '',
	'fontLetterSpacingType'   => 'px',
	// For Global Block Styles.
	'globalBlockStyleName'    => '',
	'globalBlockStyleId'      => '',
);
