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

// For Global Block Styles.
$base_selector = ! empty( $is_gbs ) && ! empty( $gbs_class ) ? $gbs_class : '.uagb-block-' . $id;

$inner_content_custom_width_tablet_fallback = is_numeric( $attr['innerContentCustomWidthTablet'] ) ? $attr['innerContentCustomWidthTablet'] : $attr['innerContentCustomWidthDesktop'];
$inner_content_custom_width_mobile_fallback = is_numeric( $attr['innerContentCustomWidthMobile'] ) ? $attr['innerContentCustomWidthMobile'] : $inner_content_custom_width_tablet_fallback;

$box_shadow_position_css = $attr['boxShadowPosition'];

if ( 'outset' === $attr['boxShadowPosition'] ) {
	$box_shadow_position_css = '';
}

$box_shadow_position_css_hover = $attr['boxShadowPositionHover'];

if ( 'outset' === $attr['boxShadowPositionHover'] ) {
	$box_shadow_position_css_hover = '';
}

$border        = UAGB_Block_Helper::uag_generate_border_css( $attr, 'container' );
$border_tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'container', 'tablet' );
$border_mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'container', 'mobile' );

// If there's no border-color, set it to inherit.
if ( empty( $border['border-color'] ) ) {
	$border['border-color'] = 'inherit';
}

$container_bg_css_desktop = UAGB_Block_Helper::get_background_css_by_device( $attr );
$container_bg_css_tablet  = UAGB_Block_Helper::get_background_css_by_device( $attr, 'Tablet' );
$container_bg_css_mobile  = UAGB_Block_Helper::get_background_css_by_device( $attr, 'Mobile' );

$container_bg_overlay_css        = array();
$container_bg_overlay_css_mobile = array();
$container_bg_overlay_css_tablet = array();

if ( $attr['overlayType'] ) {
	$container_bg_overlay_css        = UAGB_Block_Helper::get_background_css_by_device( $attr, 'Desktop', 'yes' );
	$container_bg_overlay_css_tablet = UAGB_Block_Helper::get_background_css_by_device( $attr, 'Tablet', 'yes' );
	$container_bg_overlay_css_mobile = UAGB_Block_Helper::get_background_css_by_device( $attr, 'Mobile', 'yes' );
}

$video_bg_css = UAGB_Block_Helper::get_background_css_by_device( $attr, 'Desktop', 'no' );

// Tablet.
$left_padding_tablet   = '' !== $attr['leftPaddingTablet'] ? $attr['leftPaddingTablet'] : $attr['leftPaddingDesktop'];
$right_padding_tablet  = '' !== $attr['rightPaddingTablet'] ? $attr['rightPaddingTablet'] : $attr['rightPaddingDesktop'];
$top_padding_tablet    = '' !== $attr['topPaddingTablet'] ? $attr['topPaddingTablet'] : $attr['topPaddingDesktop'];
$bottom_padding_tablet = '' !== $attr['bottomPaddingTablet'] ? $attr['bottomPaddingTablet'] : $attr['bottomPaddingDesktop'];

$left_margin_tablet   = '' !== $attr['leftMarginTablet'] ? $attr['leftMarginTablet'] : $attr['leftMarginDesktop'];
$right_margin_tablet  = '' !== $attr['rightMarginTablet'] ? $attr['rightMarginTablet'] : $attr['rightMarginDesktop'];
$top_margin_tablet    = '' !== $attr['topMarginTablet'] ? $attr['topMarginTablet'] : $attr['topMarginDesktop'];
$bottom_margin_tablet = '' !== $attr['bottomMarginTablet'] ? $attr['bottomMarginTablet'] : $attr['bottomMarginDesktop'];

$column_gap_tablet = ! empty( $attr['columnGapTablet'] ) ? $attr['columnGapTablet'] : $attr['columnGapDesktop'];

// Mobile.
$left_padding_mobile   = '' !== $attr['leftPaddingMobile'] ? $attr['leftPaddingMobile'] : $left_padding_tablet;
$right_padding_mobile  = '' !== $attr['rightPaddingMobile'] ? $attr['rightPaddingMobile'] : $right_padding_tablet;
$top_padding_mobile    = '' !== $attr['topPaddingMobile'] ? $attr['topPaddingMobile'] : $top_padding_tablet;
$bottom_padding_mobile = '' !== $attr['bottomPaddingMobile'] ? $attr['bottomPaddingMobile'] : $bottom_padding_tablet;

$left_margin_mobile   = '' !== $attr['leftMarginMobile'] ? $attr['leftMarginMobile'] : $left_margin_tablet;
$right_margin_mobile  = '' !== $attr['rightMarginMobile'] ? $attr['rightMarginMobile'] : $right_margin_tablet;
$top_margin_mobile    = '' !== $attr['topMarginMobile'] ? $attr['topMarginMobile'] : $top_margin_tablet;
$bottom_margin_mobile = '' !== $attr['bottomMarginMobile'] ? $attr['bottomMarginMobile'] : $bottom_margin_tablet;

$column_gap_mobile = ! empty( $attr['columnGapMobile'] ) ? $attr['columnGapMobile'] : $column_gap_tablet;

$is_layout_grid        = 'grid' === $attr['layout'];
$has_inner_blocks_wrap = 'alignwide' === $attr['innerContentWidth'] && 'alignfull' === $attr['contentWidth'];

$should_merge_inner_container_css = ( $attr['isBlockRootParent'] && ! $has_inner_blocks_wrap ) || ! $attr['isBlockRootParent'] || 'alignwide' !== $attr['innerContentWidth'];

$container_css       = array_merge(
	array(
		'min-height'     => UAGB_Helper::get_css_value( $attr['minHeightDesktop'], $attr['minHeightType'] ),
		'box-shadow'     =>
				UAGB_Helper::get_css_value( $attr['boxShadowHOffset'], 'px' ) .
				' ' .
				UAGB_Helper::get_css_value( $attr['boxShadowVOffset'], 'px' ) .
				' ' .
				UAGB_Helper::get_css_value( $attr['boxShadowBlur'], 'px' ) .
				' ' .
				UAGB_Helper::get_css_value( $attr['boxShadowSpread'], 'px' ) .
				' ' .
				$attr['boxShadowColor'] .
				' ' .
				$box_shadow_position_css,
		'padding-top'    => UAGB_Helper::get_css_value( $attr['topPaddingDesktop'], $attr['paddingType'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['bottomPaddingDesktop'], $attr['paddingType'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['leftPaddingDesktop'], $attr['paddingType'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['rightPaddingDesktop'], $attr['paddingType'] ),
		'margin-top'     => UAGB_Helper::get_css_value( $attr['topMarginDesktop'], $attr['marginType'] ) . ' !important',
		'margin-bottom'  => UAGB_Helper::get_css_value( $attr['bottomMarginDesktop'], $attr['marginType'] ) . ' !important',
		'margin-left'    => UAGB_Helper::get_css_value( $attr['leftMarginDesktop'], $attr['marginType'] ),
		'margin-right'   => UAGB_Helper::get_css_value( $attr['rightMarginDesktop'], $attr['marginType'] ),
		'overflow'       => $attr['overflow'],
	),
	$border
);
$container_css       = array_merge( $container_css, $container_bg_css_desktop );
$inner_container_css = array(
	'flex-direction'  => $attr['directionDesktop'],
	'align-items'     => $attr['alignItemsDesktop'],
	'justify-content' => $attr['justifyContentDesktop'],
	'flex-wrap'       => $attr['wrapDesktop'],
	'align-content'   => $attr['alignContentDesktop'],
);

// Keeping $inner_container_css empty array because it will be used when layout is grid.
if ( $is_layout_grid ) {
	$inner_container_css = array();
}

if ( $should_merge_inner_container_css ) {
	$container_css = array_merge( $container_css, $inner_container_css );
}

// Handle backward opacity for video.
// If this was saved in the updated version, backgroundVideoOpacity will be 0, and this will be skipped.
if ( 'video' === $attr['backgroundType'] && ! empty( $attr['backgroundVideoOpacity'] ) ) {
	$attr['overlayOpacity'] = $attr['backgroundVideoOpacity'];
}

$background_video_opacity_value = ( isset( $attr['overlayOpacity'] ) && 'none' !== $attr['overlayType'] && ( ( 'color' === $attr['overlayType'] && ! empty( $attr['backgroundVideoColor'] ) ) || ( 'gradient' === $attr['overlayType'] && ! empty( $attr['gradientValue'] ) ) ) ) ? 1 - $attr['overlayOpacity'] : 1;
$bg_video_image_fallback        = ! empty( $attr['backgroundVideoFallbackImage']['url'] ) ? $attr['backgroundVideoFallbackImage']['url'] : '';

$selectors = array(
	$base_selector . '.wp-block-uagb-container'           => array(
		'color' => $attr['textColor'],
	),
	$base_selector . '.wp-block-uagb-container *'         => array(
		'color' => $attr['textColor'],
	),
	$base_selector . ' a'                                 => array(
		'color' => $attr['linkColor'],
	),
	$base_selector . ' a:hover'                           => array(
		'color' => $attr['linkHoverColor'],
	),
	$base_selector . ' .uagb-container__shape-top svg'    => array(
		'height' => UAGB_Helper::get_css_value( $attr['topHeight'], 'px' ),
	),
	$base_selector . ' .uagb-container__shape.uagb-container__shape-top .uagb-container__shape-fill' => array(
		'fill' => UAGB_Helper::hex2rgba( $attr['topColor'], ( isset( $attr['topDividerOpacity'] ) && '' !== $attr['topDividerOpacity'] ) ? $attr['topDividerOpacity'] : 100 ),
	),
	$base_selector . ' .uagb-container__shape-bottom svg' => array(
		'height' => UAGB_Helper::get_css_value( $attr['bottomHeight'], 'px' ),
	),
	$base_selector . ' .uagb-container__shape.uagb-container__shape-bottom .uagb-container__shape-fill' => array(
		'fill' => UAGB_Helper::hex2rgba( $attr['bottomColor'], ( isset( $attr['bottomDividerOpacity'] ) && '' !== $attr['bottomDividerOpacity'] ) ? $attr['bottomDividerOpacity'] : 100 ),
	),
	$base_selector . ' .uagb-container__video-wrap video' => array(
		'opacity' => $background_video_opacity_value,
	),
);

if ( $bg_video_image_fallback ) {
	$selectors[ $base_selector . ' .uagb-container__video-wrap video' ]['background']      = 'url(' . $bg_video_image_fallback . ') 50% 50%;';
	$selectors[ $base_selector . ' .uagb-container__video-wrap video' ]['background-size'] = 'cover';
}

if ( '' !== $attr['topWidth'] ) {
	$selectors[ $base_selector . ' .uagb-container__shape-top svg' ]['width'] = 'calc( ' . $attr['topWidth'] . '% + 1.3px )';
}

if ( '' !== $attr['bottomWidth'] ) {
	$selectors[ $base_selector . ' .uagb-container__shape-bottom svg' ]['width'] = 'calc( ' . $attr['bottomWidth'] . '% + 1.3px )';
}

$container_tablet_css = array_merge(
	array(
		'min-height'     => UAGB_Helper::get_css_value( $attr['minHeightTablet'], $attr['minHeightTypeTablet'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $top_padding_tablet, $attr['paddingTypeTablet'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $bottom_padding_tablet, $attr['paddingTypeTablet'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $left_padding_tablet, $attr['paddingTypeTablet'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $right_padding_tablet, $attr['paddingTypeTablet'] ),
		'margin-top'     => UAGB_Helper::get_css_value( $top_margin_tablet, $attr['marginTypeTablet'] ) . ' !important',
		'margin-bottom'  => UAGB_Helper::get_css_value( $bottom_margin_tablet, $attr['marginTypeTablet'] ) . ' !important',
		'margin-left'    => UAGB_Helper::get_css_value( $left_margin_tablet, $attr['marginTypeTablet'] ),
		'margin-right'   => UAGB_Helper::get_css_value( $right_margin_tablet, $attr['marginTypeTablet'] ),
	),
	$border_tablet
);

if ( ! empty( $container_bg_css_tablet ) ) {
	$container_tablet_css = array_merge( $container_tablet_css, $container_bg_css_tablet );
}

$inner_container_tablet_css = array(
	'flex-direction'  => $attr['directionTablet'],
	'align-items'     => $attr['alignItemsTablet'],
	'justify-content' => $attr['justifyContentTablet'],
	'flex-wrap'       => $attr['wrapTablet'],
	'align-content'   => $attr['alignContentTablet'],
);

// Keeping $inner_container_tablet_css empty array because it will be used when layout is grid.
if ( $is_layout_grid ) {
	$inner_container_tablet_css = array();
}

if ( $should_merge_inner_container_css && ! $is_layout_grid ) {
	$container_tablet_css = array_merge( $container_tablet_css, $inner_container_tablet_css );
}

$t_selectors = array(
	$base_selector . ' .uagb-container__shape-bottom svg' => array(
		'height' => UAGB_Helper::get_css_value( $attr['bottomHeightTablet'], 'px' ),
	),
	$base_selector . ' .uagb-container__shape-top svg'    => array(
		'height' => UAGB_Helper::get_css_value( $attr['topHeightTablet'], 'px' ),
	),
);

$container_mobile_css = array_merge(
	array(
		'min-height'     => UAGB_Helper::get_css_value( $attr['minHeightMobile'], $attr['minHeightTypeMobile'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $top_padding_mobile, $attr['paddingTypeMobile'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $bottom_padding_mobile, $attr['paddingTypeMobile'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $left_padding_mobile, $attr['paddingTypeMobile'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $right_padding_mobile, $attr['paddingTypeMobile'] ),
		'margin-top'     => UAGB_Helper::get_css_value( $top_margin_mobile, $attr['marginTypeMobile'] ) . ' !important',
		'margin-bottom'  => UAGB_Helper::get_css_value( $bottom_margin_mobile, $attr['marginTypeMobile'] ) . ' !important',
		'margin-left'    => UAGB_Helper::get_css_value( $left_margin_mobile, $attr['marginTypeMobile'] ),
		'margin-right'   => UAGB_Helper::get_css_value( $right_margin_mobile, $attr['marginTypeMobile'] ),
		'row-gap'        => UAGB_Helper::get_css_value( $attr['rowGapMobile'], $attr['rowGapTypeMobile'] ),
		'column-gap'     => UAGB_Helper::get_css_value( $attr['columnGapMobile'], $attr['columnGapTypeMobile'] ),
	),
	$border_mobile
);

if ( ! empty( $container_bg_css_mobile ) ) {
	$container_mobile_css = array_merge( $container_mobile_css, $container_bg_css_mobile );
}

$inner_container_mobile_css = array(
	'flex-direction'  => $attr['directionMobile'],
	'align-items'     => $attr['alignItemsMobile'],
	'justify-content' => $attr['justifyContentMobile'],
	'flex-wrap'       => $attr['wrapMobile'],
	'align-content'   => $attr['alignContentMobile'],
);

// Keeping $inner_container_mobile_css empty array because it will be used when layout is grid.
if ( $is_layout_grid ) {
	$inner_container_mobile_css = array();
}

if ( $should_merge_inner_container_css && ! $is_layout_grid ) {
	$container_mobile_css = array_merge( $container_mobile_css, $inner_container_mobile_css );
}

$m_selectors = array(
	$base_selector . ' .uagb-container__shape-bottom svg' => array(
		'height' => UAGB_Helper::get_css_value( $attr['bottomHeightMobile'], 'px' ),
	),
	$base_selector . ' .uagb-container__shape-top svg'    => array(
		'height' => UAGB_Helper::get_css_value( $attr['topHeightMobile'], 'px' ),
	),
);

if ( ! $is_layout_grid ) {
	// Add row and column gap if layout is not grid.
	$container_css['row-gap']          = UAGB_Helper::get_css_value( $attr['rowGapDesktop'], $attr['rowGapType'] );
	$container_css['column-gap']       = UAGB_Helper::get_css_value( $attr['columnGapDesktop'], $attr['columnGapType'] );
	$inner_container_css['row-gap']    = UAGB_Helper::get_css_value( $attr['rowGapDesktop'], $attr['rowGapType'] );
	$inner_container_css['column-gap'] = UAGB_Helper::get_css_value( $attr['columnGapDesktop'], $attr['columnGapType'] );

	// for tablet devices.
	$container_tablet_css['row-gap']          = UAGB_Helper::get_css_value( $attr['rowGapTablet'], $attr['rowGapTypeTablet'] );
	$container_tablet_css['column-gap']       = UAGB_Helper::get_css_value( $attr['columnGapTablet'], $attr['columnGapTypeTablet'] );
	$inner_container_tablet_css['row-gap']    = UAGB_Helper::get_css_value( $attr['rowGapTablet'], $attr['rowGapTypeTablet'] );
	$inner_container_tablet_css['column-gap'] = UAGB_Helper::get_css_value( $attr['columnGapTablet'], $attr['columnGapTypeTablet'] );

	// for mobile devices.
	$container_mobile_css['row-gap']          = UAGB_Helper::get_css_value( $attr['rowGapMobile'], $attr['rowGapTypeMobile'] );
	$container_mobile_css['column-gap']       = UAGB_Helper::get_css_value( $attr['columnGapMobile'], $attr['columnGapTypeMobile'] );
	$inner_container_mobile_css['row-gap']    = UAGB_Helper::get_css_value( $attr['rowGapMobile'], $attr['rowGapTypeMobile'] );
	$inner_container_mobile_css['column-gap'] = UAGB_Helper::get_css_value( $attr['columnGapMobile'], $attr['columnGapTypeMobile'] );
}

	// Add max-width and width if layout is not grid.
	$selectors[ '.uagb-is-root-container .uagb-block-' . $id ] = array( // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		'max-width' => UAGB_Helper::get_css_value( $attr['widthDesktop'], $attr['widthType'] ),
		'width'     => '100%',
	);

	$t_selectors[ '.uagb-is-root-container ' . $base_selector ] = array( // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		'max-width' => UAGB_Helper::get_css_value( $attr['widthTablet'], $attr['widthTypeTablet'] ),
		'width'     => '100%',
	);

	$m_selectors[ '.uagb-is-root-container ' . $base_selector ] = array( // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		'max-width' => UAGB_Helper::get_css_value( $attr['widthMobile'], $attr['widthTypeMobile'] ),
		'width'     => '100%',
	);

	if ( $has_inner_blocks_wrap ) {
		$selectors[ '.uagb-is-root-container.alignfull' . $base_selector . ' > .uagb-container-inner-blocks-wrap' ] = array_merge(
			array( // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			'--inner-content-custom-width' => 'min( 100%, ' . $attr['innerContentCustomWidthDesktop'] . $attr['innerContentCustomWidthType'] . ')',
			'max-width'                    => 'var(--inner-content-custom-width)',
			'width'                        => '100%',
			),
			$inner_container_css
		);

		$t_selectors[ '.uagb-is-root-container.alignfull' . $base_selector . ' > .uagb-container-inner-blocks-wrap' ] = array_merge(
			array( // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			'--inner-content-custom-width' => 'min( 100%, ' . $inner_content_custom_width_tablet_fallback . $attr['innerContentCustomWidthTypeTablet'] . ')',
			'max-width'                    => 'var(--inner-content-custom-width)',
			'width'                        => '100%',
			),
			$inner_container_tablet_css
		);

		$m_selectors[ '.uagb-is-root-container.alignfull' . $base_selector . ' > .uagb-container-inner-blocks-wrap' ] = array_merge(
			array( // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			'--inner-content-custom-width' => 'min( 100%, ' . $inner_content_custom_width_mobile_fallback . $attr['innerContentCustomWidthTypeMobile'] . ')',
			'max-width'                    => 'var(--inner-content-custom-width)',
			'width'                        => '100%',
			),
			$inner_container_mobile_css
		);
	}

	if ( $is_layout_grid ) {
		$grid_base_selector      = $base_selector . '.uagb-layout-grid';
		$container_base_selector = $has_inner_blocks_wrap && $attr['isBlockRootParent'] ? $grid_base_selector . ' > .uagb-container-inner-blocks-wrap' : $grid_base_selector;
		$grid_css                = array();
		
		$grid_css['row-gap']    = UAGB_Helper::get_css_value( $attr['rowGapDesktop'], $attr['rowGapType'] );
		$grid_css['column-gap'] = UAGB_Helper::get_css_value( $attr['columnGapDesktop'], $attr['columnGapType'] );

		// Grid css for desktop.
		$selectors[ $container_base_selector ] = array_merge( $grid_css, UAGB_Block_Helper::grid_css_object( $attr, 'Desktop' ) );

		// Grid css for tablet.
		$grid_css_tablet = array();

		$grid_css_tablet['row-gap']    = UAGB_Helper::get_css_value( $attr['rowGapTablet'], $attr['rowGapTypeTablet'] );
		$grid_css_tablet['column-gap'] = UAGB_Helper::get_css_value( $attr['columnGapTablet'], $attr['columnGapTypeTablet'] );
		
		$t_selectors[ $container_base_selector ] = array_merge( $grid_css_tablet, UAGB_Block_Helper::grid_css_object( $attr, 'Tablet' ) );

		// Grid css for mobile.
		$grid_css_mobile = array();

		$grid_css_mobile['row-gap']    = UAGB_Helper::get_css_value( $attr['rowGapMobile'], $attr['rowGapTypeMobile'] );
		$grid_css_mobile['column-gap'] = UAGB_Helper::get_css_value( $attr['columnGapMobile'], $attr['columnGapTypeMobile'] );

		$m_selectors[ $container_base_selector ] = array_merge( $grid_css_mobile, UAGB_Block_Helper::grid_css_object( $attr, 'Mobile' ) );
	}

	if ( 'video' === $attr['backgroundType'] ) {
		$selectors[ $base_selector . ' .uagb-container__video-wrap' ]   = array_merge( $video_bg_css, $border );
		$t_selectors[ $base_selector . ' .uagb-container__video-wrap' ] = $border_tablet;
		$m_selectors[ $base_selector . ' .uagb-container__video-wrap' ] = $border_mobile;

		$selectorClass = '.wp-block-uagb-container' . $base_selector;

		$selectors[ $base_selector . ' > div:not(.uagb-container__video-wrap):not(.uagb-container__shape)' ] = array(
			'position' => 'relative',
		);
		$selectors[ $selectorClass ]   = $inner_container_css;
		$t_selectors[ $selectorClass ] = $inner_container_tablet_css;
		$m_selectors[ $selectorClass ] = $inner_container_mobile_css;

		$selectors[ $base_selector ]   = array(
			'min-height'     => UAGB_Helper::get_css_value( $attr['minHeightDesktop'], $attr['minHeightType'] ),
			'box-shadow'     =>
					UAGB_Helper::get_css_value( $attr['boxShadowHOffset'], 'px' ) .
					' ' .
					UAGB_Helper::get_css_value( $attr['boxShadowVOffset'], 'px' ) .
					' ' .
					UAGB_Helper::get_css_value( $attr['boxShadowBlur'], 'px' ) .
					' ' .
					UAGB_Helper::get_css_value( $attr['boxShadowSpread'], 'px' ) .
					' ' .
					$attr['boxShadowColor'] .
					' ' .
					$box_shadow_position_css,
			'padding-top'    => UAGB_Helper::get_css_value( $attr['topPaddingDesktop'], $attr['paddingType'] ),
			'padding-bottom' => UAGB_Helper::get_css_value( $attr['bottomPaddingDesktop'], $attr['paddingType'] ),
			'padding-left'   => UAGB_Helper::get_css_value( $attr['leftPaddingDesktop'], $attr['paddingType'] ),
			'padding-right'  => UAGB_Helper::get_css_value( $attr['rightPaddingDesktop'], $attr['paddingType'] ),
			'margin-top'     => UAGB_Helper::get_css_value( $attr['topMarginDesktop'], $attr['marginType'] ) . ' !important',
			'margin-bottom'  => UAGB_Helper::get_css_value( $attr['bottomMarginDesktop'], $attr['marginType'] ) . ' !important',
			'margin-left'    => UAGB_Helper::get_css_value( $attr['leftMarginDesktop'], $attr['marginType'] ),
			'margin-right'   => UAGB_Helper::get_css_value( $attr['rightMarginDesktop'], $attr['marginType'] ),
			'row-gap'        => UAGB_Helper::get_css_value( $attr['rowGapDesktop'], $attr['rowGapType'] ),
			'column-gap'     => UAGB_Helper::get_css_value( $attr['columnGapDesktop'], $attr['columnGapType'] ),
			'overflow'       => $attr['overflow'],
		);
		$t_selectors[ $base_selector ] = array(
			'min-height'     => UAGB_Helper::get_css_value( $attr['minHeightTablet'], $attr['minHeightTypeTablet'] ),
			'padding-top'    => UAGB_Helper::get_css_value( $top_padding_tablet, $attr['paddingTypeTablet'] ),
			'padding-bottom' => UAGB_Helper::get_css_value( $bottom_padding_tablet, $attr['paddingTypeTablet'] ),
			'padding-left'   => UAGB_Helper::get_css_value( $left_padding_tablet, $attr['paddingTypeTablet'] ),
			'padding-right'  => UAGB_Helper::get_css_value( $right_padding_tablet, $attr['paddingTypeTablet'] ),
			'margin-top'     => UAGB_Helper::get_css_value( $top_margin_tablet, $attr['marginTypeTablet'] ) . ' !important',
			'margin-bottom'  => UAGB_Helper::get_css_value( $bottom_margin_tablet, $attr['marginTypeTablet'] ) . ' !important',
			'margin-left'    => UAGB_Helper::get_css_value( $left_margin_tablet, $attr['marginTypeTablet'] ),
			'margin-right'   => UAGB_Helper::get_css_value( $right_margin_tablet, $attr['marginTypeTablet'] ),
			'row-gap'        => UAGB_Helper::get_css_value( $attr['rowGapTablet'], $attr['rowGapTypeTablet'] ),
			'column-gap'     => UAGB_Helper::get_css_value( $attr['columnGapTablet'], $attr['columnGapTypeTablet'] ),
		);
		$m_selectors[ $base_selector ] = array(
			'min-height'     => UAGB_Helper::get_css_value( $attr['minHeightMobile'], $attr['minHeightTypeMobile'] ),
			'padding-top'    => UAGB_Helper::get_css_value( $top_padding_mobile, $attr['paddingTypeMobile'] ),
			'padding-bottom' => UAGB_Helper::get_css_value( $bottom_padding_mobile, $attr['paddingTypeMobile'] ),
			'padding-left'   => UAGB_Helper::get_css_value( $left_padding_mobile, $attr['paddingTypeMobile'] ),
			'padding-right'  => UAGB_Helper::get_css_value( $right_padding_mobile, $attr['paddingTypeMobile'] ),
			'margin-top'     => UAGB_Helper::get_css_value( $top_margin_mobile, $attr['marginTypeMobile'] ) . ' !important',
			'margin-bottom'  => UAGB_Helper::get_css_value( $bottom_margin_mobile, $attr['marginTypeMobile'] ) . ' !important',
			'margin-left'    => UAGB_Helper::get_css_value( $left_margin_mobile, $attr['marginTypeMobile'] ),
			'margin-right'   => UAGB_Helper::get_css_value( $right_margin_mobile, $attr['marginTypeMobile'] ),
			'row-gap'        => UAGB_Helper::get_css_value( $attr['rowGapMobile'], $attr['rowGapTypeMobile'] ),
			'column-gap'     => UAGB_Helper::get_css_value( $attr['columnGapMobile'], $attr['columnGapTypeMobile'] ),
		);
		$selectors[ '.wp-block-uagb-container' . $base_selector . ':hover .uagb-container__video-wrap' ] = array(
			'border-color' => $attr['containerBorderHColor'],
		);
		// If hover blur or hover color are set, show the hover shadow.
		if ( ( ( '' !== $attr['boxShadowBlurHover'] ) && ( null !== $attr['boxShadowBlurHover'] ) ) || '' !== $attr['boxShadowColorHover'] ) {

			$selectors[ $base_selector . ':hover ' ]['box-shadow'] = UAGB_Helper::get_css_value( $attr['boxShadowHOffsetHover'], 'px' ) .
																	' ' .
																	UAGB_Helper::get_css_value( $attr['boxShadowVOffsetHover'], 'px' ) .
																	' ' .
																	UAGB_Helper::get_css_value( $attr['boxShadowBlurHover'], 'px' ) .
																	' ' .
																	UAGB_Helper::get_css_value( $attr['boxShadowSpreadHover'], 'px' ) .
																	' ' .
																	$attr['boxShadowColorHover'] .
																	' ' .
																	$box_shadow_position_css_hover;

		}
	} else {
		$selectors[ $base_selector ]   = $container_css; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$t_selectors[ $base_selector ] = $container_tablet_css; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$m_selectors[ $base_selector ] = $container_mobile_css; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$selectors[ '.wp-block-uagb-container' . $base_selector . ':hover' ] = array(
			'border-color' => $attr['containerBorderHColor'],
		);
		// If hover blur or hover color are set, show the hover shadow.
		if ( ( ( '' !== $attr['boxShadowBlurHover'] ) && ( null !== $attr['boxShadowBlurHover'] ) ) || '' !== $attr['boxShadowColorHover'] ) {

			$selectors[ $base_selector . ':hover' ]['box-shadow'] = UAGB_Helper::get_css_value( $attr['boxShadowHOffsetHover'], 'px' ) .
																	' ' .
																	UAGB_Helper::get_css_value( $attr['boxShadowVOffsetHover'], 'px' ) .
																	' ' .
																	UAGB_Helper::get_css_value( $attr['boxShadowBlurHover'], 'px' ) .
																	' ' .
																	UAGB_Helper::get_css_value( $attr['boxShadowSpreadHover'], 'px' ) .
																	' ' .
																	$attr['boxShadowColorHover'] .
																	' ' .
																	$box_shadow_position_css_hover;

		}
	}

	if ( 'default' === $attr['contentWidth'] ) {
		$selectors[ $base_selector ]['max-width']    = UAGB_Helper::get_css_value( $attr['widthDesktop'], $attr['widthType'] ) . ' !important';
		$selectors[ $base_selector ]['margin-left']  = ( '' !== $attr['leftMarginDesktop'] ? UAGB_Helper::get_css_value( $attr['leftMarginDesktop'], $attr['marginType'] ) . ' !important' : '' );
		$selectors[ $base_selector ]['margin-right'] = ( '' !== $attr['rightMarginDesktop'] ? UAGB_Helper::get_css_value( $attr['rightMarginDesktop'], $attr['marginType'] ) . ' !important' : '' );

		$t_selectors[ $base_selector ]['max-width']    = UAGB_Helper::get_css_value( $attr['widthTablet'], $attr['widthTypeTablet'] ) . ' !important';
		$t_selectors[ $base_selector ]['margin-left']  = ( '' !== $attr['leftMarginTablet'] ? UAGB_Helper::get_css_value( $left_margin_tablet, $attr['marginTypeTablet'] ) . ' !important' : '' );
		$t_selectors[ $base_selector ]['margin-right'] = ( '' !== $attr['rightMarginTablet'] ? UAGB_Helper::get_css_value( $right_margin_tablet, $attr['marginTypeTablet'] ) . ' !important' : '' );

		$m_selectors[ $base_selector ]['max-width']    = UAGB_Helper::get_css_value( $attr['widthMobile'], $attr['widthTypeMobile'] ) . ' !important';
		$m_selectors[ $base_selector ]['margin-left']  = ( '' !== $attr['leftMarginMobile'] ? UAGB_Helper::get_css_value( $left_margin_mobile, $attr['marginTypeMobile'] ) . ' !important' : '' );
		$m_selectors[ $base_selector ]['margin-right'] = ( '' !== $attr['rightMarginMobile'] ? UAGB_Helper::get_css_value( $right_margin_mobile, $attr['marginTypeMobile'] ) . ' !important' : '' );
	}

	// Add the overlay CSS if needed.
	if (
	! empty( $attr['overlayType'] )
	&& 'none' !== $attr['overlayType']
	&& ! empty( $container_bg_overlay_css )
	&& is_array( $container_bg_overlay_css )
	) {
		$desktop_border_width = array(
			'top'    => is_numeric( $attr['containerBorderTopWidth'] ) ? $attr['containerBorderTopWidth'] : 0,
			'right'  => is_numeric( $attr['containerBorderRightWidth'] ) ? $attr['containerBorderRightWidth'] : 0,
			'bottom' => is_numeric( $attr['containerBorderBottomWidth'] ) ? $attr['containerBorderBottomWidth'] : 0,
			'left'   => is_numeric( $attr['containerBorderLeftWidth'] ) ? $attr['containerBorderLeftWidth'] : 0,
		);
		$tablet_border_width  = array(
			'top'    => is_numeric( $attr['containerBorderTopWidthTablet'] ) ? $attr['containerBorderTopWidthTablet'] : $desktop_border_width['top'],
			'right'  => is_numeric( $attr['containerBorderRightWidthTablet'] ) ? $attr['containerBorderRightWidthTablet'] : $desktop_border_width['right'],
			'bottom' => is_numeric( $attr['containerBorderBottomWidthTablet'] ) ? $attr['containerBorderBottomWidthTablet'] : $desktop_border_width['bottom'],
			'left'   => is_numeric( $attr['containerBorderLeftWidthTablet'] ) ? $attr['containerBorderLeftWidthTablet'] : $desktop_border_width['left'],
		);
		$mobile_border_width  = array(
			'top'    => is_numeric( $attr['containerBorderTopWidthMobile'] ) ? $attr['containerBorderTopWidthMobile'] : $tablet_border_width['top'],
			'right'  => is_numeric( $attr['containerBorderRightWidthMobile'] ) ? $attr['containerBorderRightWidthMobile'] : $tablet_border_width['right'],
			'bottom' => is_numeric( $attr['containerBorderBottomWidthMobile'] ) ? $attr['containerBorderBottomWidthMobile'] : $tablet_border_width['bottom'],
			'left'   => is_numeric( $attr['containerBorderLeftWidthMobile'] ) ? $attr['containerBorderLeftWidthMobile'] : $tablet_border_width['left'],
		);

		$selectors   = array_merge(
			$selectors,
			array(
				$base_selector . '::before'       => array_merge(
					array(
						'content'        => '""',
						'position'       => 'absolute',
						'pointer-events' => 'none',
						'top'            => '-' . UAGB_Helper::get_css_value( $desktop_border_width['top'], 'px' ),
						'left'           => '-' . UAGB_Helper::get_css_value( $desktop_border_width['left'], 'px' ),
						'width'          => 'calc(100% + ' . UAGB_Helper::get_css_value( $desktop_border_width['left'], 'px' ) . ' + ' . UAGB_Helper::get_css_value( $desktop_border_width['right'], 'px' ) . ')',
						'height'         => 'calc(100% + ' . UAGB_Helper::get_css_value( $desktop_border_width['top'], 'px' ) . ' + ' . UAGB_Helper::get_css_value( $desktop_border_width['bottom'], 'px' ) . ')',
					),
					$border,
					$container_bg_overlay_css
				),
				$base_selector . ':hover::before' => array(
					'border-color' => $attr['containerBorderHColor'],
				),
			)
		);
		$t_selectors = array_merge(
			$t_selectors,
			array(
				$base_selector . '::before' => array_merge(
					array(
						'top'    => '-' . UAGB_Helper::get_css_value( $tablet_border_width['top'], 'px' ),
						'left'   => '-' . UAGB_Helper::get_css_value( $tablet_border_width['left'], 'px' ),
						'width'  => 'calc(100% + ' . UAGB_Helper::get_css_value( $tablet_border_width['left'], 'px' ) . ' + ' . UAGB_Helper::get_css_value( $tablet_border_width['right'], 'px' ) . ')',
						'height' => 'calc(100% + ' . UAGB_Helper::get_css_value( $tablet_border_width['top'], 'px' ) . ' + ' . UAGB_Helper::get_css_value( $tablet_border_width['bottom'], 'px' ) . ')',
					),
					$border_tablet
				),
			)
		);
		$m_selectors = array_merge(
			$m_selectors,
			array(
				$base_selector . '::before' => array_merge(
					array(
						'top'    => '-' . UAGB_Helper::get_css_value( $mobile_border_width['top'], 'px' ),
						'left'   => '-' . UAGB_Helper::get_css_value( $mobile_border_width['left'], 'px' ),
						'width'  => 'calc(100% + ' . UAGB_Helper::get_css_value( $mobile_border_width['left'], 'px' ) . ' + ' . UAGB_Helper::get_css_value( $mobile_border_width['right'], 'px' ) . ')',
						'height' => 'calc(100% + ' . UAGB_Helper::get_css_value( $mobile_border_width['top'], 'px' ) . ' + ' . UAGB_Helper::get_css_value( $mobile_border_width['bottom'], 'px' ) . ')',
					),
					$border_mobile
				),
			)
		);
		if ( 'image' === $attr['overlayType'] ) {
			$t_selectors[ $base_selector . '::before' ] = array_merge(
				$t_selectors[ $base_selector . '::before' ],
				$container_bg_overlay_css_tablet
			);
			$m_selectors[ $base_selector . '::before' ] = array_merge(
				$m_selectors[ $base_selector . '::before' ],
				$container_bg_overlay_css_mobile
			);
		};
	}//end if;

	$z_index        = isset( $attr['zIndex'] ) ? $attr['zIndex'] : '';
	$z_index_tablet = isset( $attr['zIndexTablet'] ) ? $attr['zIndexTablet'] : '';
	$z_index_mobile = isset( $attr['zIndexMobile'] ) ? $attr['zIndexMobile'] : '';

	$selectors[ $base_selector . '.uag-blocks-common-selector' ] = array(
		'--z-index-desktop' => $z_index,
		'--z-index-tablet'  => $z_index_tablet,
		'--z-index-mobile'  => $z_index_mobile,
	);

	$flex_directions = array( 'row-reverse', 'row' );
	$auto_width      = array( 'width' => 'auto' );
	$set_width       = array( 'width' => '100%' );

	$base_width_selector = $base_selector . '.wp-block-uagb-container > *:not( .wp-block-uagb-container ):not( .wp-block-uagb-column ):not( .wp-block-uagb-container ):not( .wp-block-uagb-section ):not( .uagb-container__shape ):not( .uagb-container__video-wrap ):not( .wp-block-uagb-image ):not( .wp-block-spectra-pro-register ):not( .wp-block-spectra-pro-login ):not( .uagb-slider-container ):not( .spectra-container-link-overlay ):not(.spectra-image-gallery__control-lightbox):not(.wp-block-uagb-lottie):not(.uagb-container-inner-blocks-wrap)';

	$base_width_selector_2 = $base_selector . '.wp-block-uagb-container > .uagb-container-inner-blocks-wrap > *:not( .wp-block-uagb-container ):not( .wp-block-uagb-column ):not( .wp-block-uagb-container ):not( .wp-block-uagb-section ):not( .uagb-container__shape ):not( .uagb-container__video-wrap ):not( .wp-block-spectra-pro-register ):not( .wp-block-spectra-pro-login ):not( .uagb-slider-container ):not(.spectra-image-gallery__control-lightbox)';

	// Add auto width to the inner blocks in desktop.
	if ( ! empty( $attr['directionDesktop'] ) ) {
		if ( in_array( $attr['directionDesktop'], $flex_directions, true ) && 'auto' === $attr['childrenWidthDesktop'] ) {
			$selectors[ $base_width_selector ]   = $auto_width;
			$selectors[ $base_width_selector_2 ] = $auto_width;
		}
	}

	// Add auto width to the inner blocks in tablet.
	if ( ! empty( $attr['directionTablet'] ) ) {
		if ( in_array( $attr['directionTablet'], $flex_directions, true ) && 'auto' === $attr['childrenWidthTablet'] ) {
			$t_selectors[ $base_width_selector ]   = $auto_width;
			$t_selectors[ $base_width_selector_2 ] = $auto_width;
		} else {
			$t_selectors[ $base_width_selector ]   = $set_width;
			$t_selectors[ $base_width_selector_2 ] = $set_width;
		}
	}

	// Add auto width to the inner blocks in mobile.
	if ( ! empty( $attr['directionMobile'] ) ) {
		if ( in_array( $attr['directionMobile'], $flex_directions, true ) && 'auto' === $attr['childrenWidthMobile'] ) {
			$m_selectors[ $base_width_selector ]   = $auto_width;
			$m_selectors[ $base_width_selector_2 ] = $auto_width;
		} else {
			$m_selectors[ $base_width_selector ]   = $set_width;
			$m_selectors[ $base_width_selector_2 ] = $set_width;
		}
	}

	if ( ! empty( $attr['isGridCssInParent'] ) ) {
		$gridChildrenCSS       = array();
		$gridChildrenCSSTab    = array(
			// Add default css for the Tablet.
			'grid-column' => 'span 1',
			'grid-row'    => 'span 1',
		);
		$gridChildrenCSSMobile = array(
			// Add default css for the Mobile.
			'grid-column' => 'span 1',
			'grid-row'    => 'span 1',
		);
	
		if ( ! empty( $attr['gridSettingType'] ) && 'advance' === $attr['gridSettingType'] ) {
			// For desktop.
			if ( ! empty( $attr['gridColumnStart'] ) && ! empty( $attr['gridColumnEnd'] ) ) {
				$gridChildrenCSS['grid-column'] = $attr['gridColumnStart'] . ' / ' . $attr['gridColumnEnd'];
			}

			if ( ! empty( $attr['gridRowStart'] ) && ! empty( $attr['gridRowEnd'] ) ) {
				$gridChildrenCSS['grid-row'] = $attr['gridRowStart'] . ' / ' . $attr['gridRowEnd'];
			}

			// For tablet.
			if ( ! empty( $attr['gridColumnStartTablet'] ) && ! empty( $attr['gridColumnEndTablet'] ) ) {
				$gridChildrenCSSTab['grid-column'] = $attr['gridColumnStartTablet'] . ' / ' . $attr['gridColumnEndTablet'];
			}

			if ( ! empty( $attr['gridRowStartTablet'] ) && ! empty( $attr['gridRowEndTablet'] ) ) {
				$gridChildrenCSSTab['grid-row'] = $attr['gridRowStartTablet'] . ' / ' . $attr['gridRowEndTablet'];
			}

			// For mobile.
			if ( ! empty( $attr['gridColumnStartMobile'] ) && ! empty( $attr['gridColumnEndMobile'] ) ) {
				$gridChildrenCSSMobile['grid-column'] = $attr['gridColumnStartMobile'] . ' / ' . $attr['gridColumnEndMobile'];
			}

			if ( ! empty( $attr['gridRowStartMobile'] ) && ! empty( $attr['gridRowEndMobile'] ) ) {
				$gridChildrenCSSMobile['grid-row'] = $attr['gridRowStartMobile'] . ' / ' . $attr['gridRowEndMobile'];
			}   
		} else {
			// For desktop.
			if ( ! empty( $attr['gridColumnSpan'] ) ) {
				$gridChildrenCSS['grid-column'] = 'span ' . $attr['gridColumnSpan'];
			}

			if ( ! empty( $attr['gridRowSpan'] ) ) {
				$gridChildrenCSS['grid-row'] = 'span ' . $attr['gridRowSpan'];
			}

			// For tablet.
			if ( ! empty( $attr['gridColumnSpanTablet'] ) ) {
				$gridChildrenCSSTab['grid-column'] = 'span ' . $attr['gridColumnSpanTablet'];
			}

			if ( ! empty( $attr['gridRowSpanTablet'] ) ) {
				$gridChildrenCSSTab['grid-row'] = 'span ' . $attr['gridRowSpanTablet'];
			}

			// For mobile.
			if ( ! empty( $attr['gridColumnSpanMobile'] ) ) {
				$gridChildrenCSSMobile['grid-column'] = 'span ' . $attr['gridColumnSpanMobile'];
			}

			if ( ! empty( $attr['gridRowSpanMobile'] ) ) {
				$gridChildrenCSSMobile['grid-row'] = 'span ' . $attr['gridRowSpanMobile'];
			}
		}

		// For desktop.
		if ( ! empty( $attr['gridAlignItems'] ) ) {
			$gridChildrenCSS['align-self'] = $attr['gridAlignItems'];
		}

		if ( ! empty( $attr['gridJustifyItems'] ) ) {
			$gridChildrenCSS['justify-self'] = $attr['gridJustifyItems'];
		}

		// For tablet.
		if ( ! empty( $attr['gridAlignItemsTablet'] ) ) {
			$gridChildrenCSSTab['align-self'] = $attr['gridAlignItemsTablet'];
		}

		if ( ! empty( $attr['gridJustifyItemsTablet'] ) ) {
			$gridChildrenCSSTab['justify-self'] = $attr['gridJustifyItemsTablet'];
		}

		// For mobile.
		if ( ! empty( $attr['gridAlignItemsMobile'] ) ) {
			$gridChildrenCSSMobile['align-self'] = $attr['gridAlignItemsMobile'];
		}

		if ( ! empty( $attr['gridJustifyItemsMobile'] ) ) {
			$gridChildrenCSSMobile['justify-self'] = $attr['gridJustifyItemsMobile'];
		}

		$selectors[ $base_selector ]   = array_merge( $selectors[ $base_selector ], $gridChildrenCSS );
		$t_selectors[ $base_selector ] = array_merge( $t_selectors[ $base_selector ], $gridChildrenCSSTab );
		$m_selectors[ $base_selector ] = array_merge( $m_selectors[ $base_selector ], $gridChildrenCSSMobile );
	}

	$combined_selectors = array(
		'desktop' => $selectors,
		'tablet'  => $t_selectors,
		'mobile'  => $m_selectors,
	);

	return UAGB_Helper::generate_all_css( $combined_selectors, '.wp-block-uagb-container' );
