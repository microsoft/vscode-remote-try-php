<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

// Adds Fonts.
// We have used the same buttons gfont function because the inputs to these functions are same.
// If need be please add a new function for Info Box and go ahead.
UAGB_Block_JS::blocks_buttons_gfont( $attr );

$font_size_fallback = is_numeric( $attr['fontSize'] ) ? $attr['fontSize'] : 16;

// Responsive Fallback Values that Need to be Numeric for Math.
$size_tablet_fallback      = is_numeric( $attr['sizeTablet'] ) ? $attr['sizeTablet'] : $attr['size'];
$size_mobile_fallback      = is_numeric( $attr['sizeMobile'] ) ? $attr['sizeMobile'] : $size_tablet_fallback;
$bg_size_tablet_fallback   = is_numeric( $attr['bgSizeTablet'] ) ? $attr['bgSizeTablet'] : $attr['bgSize'];
$bg_size_mobile_fallback   = is_numeric( $attr['bgSizeMobile'] ) ? $attr['bgSizeMobile'] : $bg_size_tablet_fallback;
$tborder_fallback          = is_numeric( $attr['borderTablet'] ) ? $attr['borderTablet'] : $attr['border'];
$mborder_fallback          = is_numeric( $attr['borderMobile'] ) ? $attr['borderMobile'] : $tborder_fallback;
$font_size_tablet_fallback = is_numeric( $attr['fontSizeTablet'] ) ? $attr['fontSizeTablet'] : $font_size_fallback;
$font_size_mobile_fallback = is_numeric( $attr['fontSizeMobile'] ) ? $attr['fontSizeMobile'] : $font_size_tablet_fallback;
$tgap_fallback             = is_numeric( $attr['gapTablet'] ) ? $attr['gapTablet'] : $attr['gap'];
$mgap_fallback             = is_numeric( $attr['gapMobile'] ) ? $attr['gapMobile'] : $tgap_fallback;

$alignment        = ( 'left' === $attr['align'] ) ? 'flex-start' : ( ( 'right' === $attr['align'] ) ? 'flex-end' : 'center' );
$alignment_tablet = ( 'left' === $attr['alignTablet'] ) ? 'flex-start' : ( ( 'right' === $attr['alignTablet'] ) ? 'flex-end' : ( ( 'center' === $attr['alignTablet'] ) ? 'center' : $alignment ) );
$alignment_mobile = ( 'left' === $attr['alignMobile'] ) ? 'flex-start' : ( ( 'right' === $attr['alignMobile'] ) ? 'flex-end' : ( ( 'center' === $attr['alignMobile'] ) ? 'center' : $alignment_tablet ) );


$icon_layout        = $attr['icon_layout'];
$icon_layout_tablet = ! empty( $attr['iconLayoutTablet'] ) ? $attr['iconLayoutTablet'] : $icon_layout;
$icon_layout_mobile = ! empty( $attr['iconLayoutMobile'] ) ? $attr['iconLayoutMobile'] : $icon_layout_tablet;

$m_selectors = array();
$t_selectors = array();

$icon_size   = UAGB_Helper::get_css_value( $attr['size'], $attr['sizeType'] );
$m_icon_size = UAGB_Helper::get_css_value( $size_mobile_fallback, $attr['sizeType'] );
$t_icon_size = UAGB_Helper::get_css_value( $size_tablet_fallback, $attr['sizeType'] );

// The Math ( 3 * Icon Size ) / 5 aligns perfectly with the current defaults ( Font Size: 16px, Line Height: 1.8em ).
$half_size        = 3 * $attr['size'] / 5;
$half_size_tablet = 3 * $size_tablet_fallback / 5;
$half_size_mobile = 3 * $size_mobile_fallback / 5;

$position        = 'top' === $attr['iconPosition'] ? 'flex-start' : 'center';
$tablet_position = '';
$mobile_position = '';

if ( 'top' === $attr['iconPositionTablet'] ) {
	$tablet_position = 'flex-start';
} elseif ( 'middle' === $attr['iconPositionTablet'] ) {
	$tablet_position = 'center';
} else {
	$tablet_position = $position;
}

if ( 'top' === $attr['iconPositionMobile'] ) {
	$mobile_position = 'flex-start';
} elseif ( 'middle' === $attr['iconPositionMobile'] ) {
	$mobile_position = 'center';
} else {
	$mobile_position = $tablet_position;
}

$selectors = array(
	// Desktop Icon Size CSS starts.
	' .uagb-icon-list__source-image' => array(
		'width' => $icon_size,
	),
	' .wp-block-uagb-icon-list-child .uagb-icon-list__source-wrap svg' => array(
		'width'     => $icon_size,
		'height'    => $icon_size,
		'font-size' => $icon_size,
		'color'     => $attr['iconColor'],
		'fill'      => $attr['iconColor'],
	),
	' .wp-block-uagb-icon-list-child .uagb-icon-list__source-wrap' => array_merge(
		array(
			'background'    => $attr['iconBgColor'],
			'border-color'  => $attr['iconBorderColor'],
			'padding'       => UAGB_Helper::get_css_value( $attr['bgSize'], $attr['bgSizeType'] ),
			'border-radius' => UAGB_Helper::get_css_value( $attr['borderRadius'], 'px' ),
			'border-style'  => ( $attr['border'] > 0 ) ? 'solid' : '',
			'border-width'  => UAGB_Helper::get_css_value( $attr['border'], $attr['borderType'] ),
			'align-self'    => $position,
		)
	),
	' .wp-block-uagb-icon-list-child .uagb-icon-list__label' => array(
		'font-size'       => UAGB_Helper::get_css_value( $attr['fontSize'], $attr['fontSizeType'] ),
		'font-family'     => $attr['fontFamily'],
		'text-transform'  => $attr['fontTransform'],
		'text-decoration' => $attr['fontDecoration'] . '!important',
		'font-style'      => $attr['fontStyle'],
		'font-weight'     => $attr['fontWeight'],
		'line-height'     => $attr['lineHeight'] . $attr['lineHeightType'],
	),
	' .uagb-icon-list__wrap'         => array(
		'display'           => 'flex',
		'flex-direction'    => 'row',
		'justify-content'   => $alignment,
		'-webkit-box-pack'  => $alignment,
		'-ms-flex-pack'     => $alignment,
		'-webkit-box-align' => 'center',
		'-ms-flex-align'    => 'center',
		'align-items'       => 'center',
		'margin-top'        => UAGB_Helper::get_css_value(
			$attr['blockTopMargin'],
			$attr['blockMarginUnit']
		),
		'margin-right'      => UAGB_Helper::get_css_value(
			$attr['blockRightMargin'],
			$attr['blockMarginUnit']
		),
		'margin-bottom'     => UAGB_Helper::get_css_value(
			$attr['blockBottomMargin'],
			$attr['blockMarginUnit']
		),
		'margin-left'       => UAGB_Helper::get_css_value(
			$attr['blockLeftMargin'],
			$attr['blockMarginUnit']
		),
		'padding-top'       => UAGB_Helper::get_css_value(
			$attr['blockTopPadding'],
			$attr['blockPaddingUnit']
		),
		'padding-right'     => UAGB_Helper::get_css_value(
			$attr['blockRightPadding'],
			$attr['blockPaddingUnit']
		),
		'padding-bottom'    => UAGB_Helper::get_css_value(
			$attr['blockBottomPadding'],
			$attr['blockPaddingUnit']
		),
		'padding-left'      => UAGB_Helper::get_css_value(
			$attr['blockLeftPadding'],
			$attr['blockPaddingUnit']
		),
	),
	' .wp-block-uagb-icon-list-child:hover .uagb-icon-list__source-wrap svg' => array(
		'color' => $attr['iconHoverColor'],
		'fill'  => $attr['iconHoverColor'],
	),
	' .wp-block-uagb-icon-list-child:hover .uagb-icon-list__label' => array(
		'color' => $attr['labelHoverColor'],
	),
	' .wp-block-uagb-icon-list-child:hover .uagb-icon-list__source-wrap' => array(
		'background'   => $attr['iconBgHoverColor'],
		'border-color' => $attr['iconBorderHoverColor'],
	),
	' .uagb-icon-list__label'        => array(
		'text-align' => $attr['align'],
	),
);


if ( $attr['childMigrate'] ) {
	$selectors[' .wp-block-uagb-icon-list-child'] = array(
		'font-family'     => $attr['fontFamily'],
		'text-transform'  => $attr['fontTransform'],
		'text-decoration' => $attr['fontDecoration'] . '!important',
		'font-style'      => $attr['fontStyle'],
		'font-weight'     => $attr['fontWeight'],
		'font-size'       => UAGB_Helper::get_css_value( $attr['fontSize'], $attr['fontSizeType'] ),
		'line-height'     => $attr['lineHeight'] . $attr['lineHeightType'],
	);
}


$t_selectors = array(
	' .uagb-icon-list__source-image' => array(
		'width' => $t_icon_size,
	),
	' .wp-block-uagb-icon-list-child .uagb-icon-list__source-wrap svg' => array(
		'width'     => $t_icon_size,
		'height'    => $t_icon_size,
		'font-size' => $t_icon_size,
	),
	' .wp-block-uagb-icon-list-child .uagb-icon-list__source-wrap ' => array_merge(
		array(
			'border-radius' => UAGB_Helper::get_css_value( $attr['borderRadiusTablet'], $attr['borderRadiusType'] ),
			'padding'       => UAGB_Helper::get_css_value( $bg_size_tablet_fallback, 'px' ),
			'border-style'  => ( $tborder_fallback > 0 ) ? 'solid' : '',
			'border-width'  => UAGB_Helper::get_css_value( $tborder_fallback, $attr['borderType'] ),
			'align-self'    => $tablet_position,
		)
	),
	' .wp-block-uagb-icon-list-child .uagb-icon-list__label' => array(
		'font-size'   => UAGB_Helper::get_css_value( $attr['fontSizeTablet'], $attr['fontSizeType'] ),
		'line-height' => UAGB_Helper::get_css_value( $attr['lineHeightTablet'], $attr['lineHeightType'] ),
	),
	' .uagb-icon-list__wrap'         => array(
		'display'           => 'flex',
		'flex-direction'    => 'row',
		'justify-content'   => $alignment_tablet,
		'-webkit-box-pack'  => $alignment_tablet,
		'-ms-flex-pack'     => $alignment_tablet,
		'-webkit-box-align' => 'center',
		'-ms-flex-align'    => 'center',
		'align-items'       => 'center',
		'margin-top'        => UAGB_Helper::get_css_value( $attr['blockTopMarginTablet'], $attr['blockMarginUnitTablet'] ),
		'margin-right'      => UAGB_Helper::get_css_value( $attr['blockRightMarginTablet'], $attr['blockMarginUnitTablet'] ),
		'margin-bottom'     => UAGB_Helper::get_css_value( $attr['blockBottomMarginTablet'], $attr['blockMarginUnitTablet'] ),
		'margin-left'       => UAGB_Helper::get_css_value( $attr['blockLeftMarginTablet'], $attr['blockMarginUnitTablet'] ),
		'padding-top'       => UAGB_Helper::get_css_value(
			$attr['blockTopPaddingTablet'],
			$attr['blockPaddingUnitTablet']
		),
		'padding-right'     => UAGB_Helper::get_css_value(
			$attr['blockRightPaddingTablet'],
			$attr['blockPaddingUnitTablet']
		),
		'padding-bottom'    => UAGB_Helper::get_css_value(
			$attr['blockBottomPaddingTablet'],
			$attr['blockPaddingUnitTablet']
		),
		'padding-left'      => UAGB_Helper::get_css_value(
			$attr['blockLeftPaddingTablet'],
			$attr['blockPaddingUnitTablet']
		),
	),
	' .uagb-icon-list__label'        => array(
		'text-align' => $attr['alignTablet'],
	),
);


$m_selectors = array(
	' .uagb-icon-list__source-image' => array(
		'width' => $m_icon_size,
	),
	' .uagb-icon-list__label'        => array(
		'text-align' => $attr['alignMobile'],
	),
	' .wp-block-uagb-icon-list-child .uagb-icon-list__source-wrap svg' => array(
		'width'     => $m_icon_size,
		'height'    => $m_icon_size,
		'font-size' => $m_icon_size,
	),
	' .wp-block-uagb-icon-list-child .uagb-icon-list__source-wrap' => array_merge(
		array(
			'border-radius' => UAGB_Helper::get_css_value( $attr['borderRadiusMobile'], $attr['borderRadiusType'] ),
			'padding'       => UAGB_Helper::get_css_value( $bg_size_mobile_fallback, 'px' ),
			'border-style'  => ( $mborder_fallback > 0 ) ? 'solid' : '',
			'border-width'  => UAGB_Helper::get_css_value( $mborder_fallback, $attr['borderType'] ),
			'align-self'    => $mobile_position,
		)
	),
	' .wp-block-uagb-icon-list-child .uagb-icon-list__label' => array(
		'font-size'   => UAGB_Helper::get_css_value( $attr['fontSizeMobile'], $attr['fontSizeType'] ),
		'line-height' => UAGB_Helper::get_css_value( $attr['lineHeightMobile'], $attr['lineHeightType'] ),
	),
	' .uagb-icon-list__wrap'         => array(
		'display'           => 'flex',
		'flex-direction'    => 'row',
		'justify-content'   => $alignment_mobile,
		'-webkit-box-pack'  => $alignment_mobile,
		'-ms-flex-pack'     => $alignment_mobile,
		'-webkit-box-align' => 'center',
		'-ms-flex-align'    => 'center',
		'align-items'       => 'center',
		'margin-top'        => UAGB_Helper::get_css_value( $attr['blockTopMarginMobile'], $attr['blockMarginUnitMobile'] ),
		'margin-right'      => UAGB_Helper::get_css_value( $attr['blockRightMarginMobile'], $attr['blockMarginUnitMobile'] ),
		'margin-bottom'     => UAGB_Helper::get_css_value( $attr['blockBottomMarginMobile'], $attr['blockMarginUnitMobile'] ),
		'margin-left'       => UAGB_Helper::get_css_value( $attr['blockLeftMarginMobile'], $attr['blockMarginUnitMobile'] ),
		'padding-top'       => UAGB_Helper::get_css_value(
			$attr['blockTopPaddingMobile'],
			$attr['blockPaddingUnitMobile']
		),
		'padding-right'     => UAGB_Helper::get_css_value(
			$attr['blockRightPaddingMobile'],
			$attr['blockPaddingUnitMobile']
		),
		'padding-bottom'    => UAGB_Helper::get_css_value(
			$attr['blockBottomPaddingMobile'],
			$attr['blockPaddingUnitMobile']
		),
		'padding-left'      => UAGB_Helper::get_css_value(
			$attr['blockLeftPaddingMobile'],
			$attr['blockPaddingUnitMobile']
		),
	),
);

$selectors[' .wp-block-uagb-icon-list-child .uagb-icon-list__label'] = array(
	'font-size'       => UAGB_Helper::get_css_value( $attr['fontSize'], $attr['fontSizeType'] ),
	'font-family'     => $attr['fontFamily'],
	'text-transform'  => $attr['fontTransform'],
	'text-decoration' => $attr['fontDecoration'] . '!important',
	'font-style'      => $attr['fontStyle'],
	'font-weight'     => $attr['fontWeight'],
	'line-height'     => $attr['lineHeight'] . $attr['lineHeightType'],
	'letter-spacing'  => UAGB_Helper::get_css_value( $attr['labelLetterSpacing'], $attr['labelLetterSpacingType'] ),
	'color'           => $attr['labelColor'],
);

$m_selectors[' .wp-block-uagb-icon-list-child .uagb-icon-list__label'] = array(
	'font-size'      => UAGB_Helper::get_css_value( $attr['fontSizeMobile'], $attr['fontSizeType'] ),
	'line-height'    => UAGB_Helper::get_css_value( $attr['lineHeightMobile'], $attr['lineHeightType'] ),
	'letter-spacing' => UAGB_Helper::get_css_value( $attr['labelLetterSpacingMobile'], $attr['labelLetterSpacingType'] ),
);

$t_selectors[' .wp-block-uagb-icon-list-child .uagb-icon-list__label'] = array(
	'font-size'      => UAGB_Helper::get_css_value( $attr['fontSizeTablet'], $attr['fontSizeType'] ),
	'line-height'    => UAGB_Helper::get_css_value( $attr['lineHeightTablet'], $attr['lineHeightType'] ),
	'letter-spacing' => UAGB_Helper::get_css_value( $attr['labelLetterSpacingTablet'], $attr['labelLetterSpacingType'] ),
);

if ( 'horizontal' === $icon_layout ) {
	$selectors['.wp-block-uagb-icon-list .wp-block-uagb-icon-list-child'] = array(
		'margin-left'  => UAGB_Helper::get_css_value( ( $attr['gap'] / 2 ), $attr['gapType'] ),
		'margin-right' => UAGB_Helper::get_css_value( ( $attr['gap'] / 2 ), $attr['gapType'] ),
		'display'      => 'inline-flex',
	);

	$selectors['.wp-block-uagb-icon-list .wp-block-uagb-icon-list-child:first-child'] = array(
		'margin-left' => 0,
	);
	$selectors['.wp-block-uagb-icon-list .wp-block-uagb-icon-list-child:last-child']  = array(
		'margin-right' => 0,
	);
} elseif ( 'vertical' === $icon_layout ) {
	$selectors[' .uagb-icon-list__wrap']['flex-direction']    = 'column';
	$selectors[' .uagb-icon-list__wrap']['align-items']       = $alignment;
	$selectors[' .uagb-icon-list__wrap']['-webkit-box-align'] = $alignment;
	$selectors[' .uagb-icon-list__wrap']['-ms-flex-align']    = $alignment;
	$selectors[' .uagb-icon-list__wrap']['justify-content']   = 'center';
	$selectors[' .uagb-icon-list__wrap']['-webkit-box-pack']  = 'center';
	$selectors[' .uagb-icon-list__wrap']['-ms-flex-pack']     = 'center';

	$selectors['.wp-block-uagb-icon-list .wp-block-uagb-icon-list-child'] = array(
		'margin-left'   => 0,
		'margin-right'  => 0,
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['gap'], $attr['gapType'] ),
	);
}

if ( 'horizontal' === $icon_layout_tablet ) {
	$t_selectors['.wp-block-uagb-icon-list .wp-block-uagb-icon-list-child']             = array(
		'margin-left'  => UAGB_Helper::get_css_value( ( $tgap_fallback / 2 ), $attr['gapType'] ),
		'margin-right' => UAGB_Helper::get_css_value( ( $tgap_fallback / 2 ), $attr['gapType'] ),
		'display'      => 'inline-flex',
	);
	$t_selectors['.wp-block-uagb-icon-list .wp-block-uagb-icon-list-child:first-child'] = array(
		'margin-left' => 0,
	);

} elseif ( 'vertical' === $icon_layout_tablet ) {
	$t_selectors[' .uagb-icon-list__wrap']['flex-direction']    = 'column';
	$t_selectors[' .uagb-icon-list__wrap']['align-items']       = $alignment_tablet;
	$t_selectors[' .uagb-icon-list__wrap']['-webkit-box-align'] = $alignment_tablet;
	$t_selectors[' .uagb-icon-list__wrap']['-ms-flex-align']    = $alignment_tablet;
	$t_selectors[' .uagb-icon-list__wrap']['justify-content']   = 'center';
	$t_selectors[' .uagb-icon-list__wrap']['-webkit-box-pack']  = 'center';
	$t_selectors[' .uagb-icon-list__wrap']['-ms-flex-pack']     = 'center';

	$t_selectors['.wp-block-uagb-icon-list .wp-block-uagb-icon-list-child'] = array(
		'margin-left'   => 0,
		'margin-right'  => 0,
		'margin-bottom' => UAGB_Helper::get_css_value( $tgap_fallback, $attr['gapType'] ),
	);
}


if ( 'horizontal' === $icon_layout_mobile ) {
	$m_selectors['.wp-block-uagb-icon-list .wp-block-uagb-icon-list-child']             = array(
		'margin-left'  => UAGB_Helper::get_css_value( ( $mgap_fallback / 2 ), $attr['gapType'] ),
		'margin-right' => UAGB_Helper::get_css_value( ( $mgap_fallback / 2 ), $attr['gapType'] ),
		'display'      => 'inline-flex',
	);
	$m_selectors['.wp-block-uagb-icon-list .wp-block-uagb-icon-list-child:first-child'] = array(
		'margin-left' => 0,
	);
} elseif ( 'vertical' === $icon_layout_mobile ) {
	$m_selectors[' .uagb-icon-list__wrap']['flex-direction']    = 'column';
	$m_selectors[' .uagb-icon-list__wrap']['align-items']       = $alignment_mobile;
	$m_selectors[' .uagb-icon-list__wrap']['-webkit-box-align'] = $alignment_mobile;
	$m_selectors[' .uagb-icon-list__wrap']['-ms-flex-align']    = $alignment_mobile;
	$m_selectors[' .uagb-icon-list__wrap']['justify-content']   = 'center';
	$m_selectors[' .uagb-icon-list__wrap']['-webkit-box-pack']  = 'center';
	$m_selectors[' .uagb-icon-list__wrap']['-ms-flex-pack']     = 'center';

	$m_selectors['.wp-block-uagb-icon-list .wp-block-uagb-icon-list-child'] = array(
		'margin-left'   => 0,
		'margin-right'  => 0,
		'margin-bottom' => UAGB_Helper::get_css_value( $mgap_fallback, $attr['gapType'] ),
	);
}

if ( ! $attr['childMigrate'] ) {

	$defaults = UAGB_DIR . 'includes/blocks/icon-list-child/attributes.php';

	if ( file_exists( $defaults ) ) {
		$default_attr = include $defaults;
	}

	$default_attr = ( ! empty( $default_attr ) && is_array( $default_attr ) ) ? $default_attr : array();

	foreach ( $attr['icons'] as $key => $icon ) {

		$wrapper = ( ! $attr['childMigrate'] ) ? ' .uagb-icon-list__repeater-' . $key . '.wp-block-uagb-icon-list-child' : ' .wp-block-uagb-icon-list-child';

		$selectors[ $wrapper ]                                     = array(
			'font-family'     => $attr['fontFamily'],
			'text-transform'  => $attr['fontTransform'],
			'text-decoration' => $attr['fontDecoration'] . '!important',
			'font-style'      => $attr['fontStyle'],
			'font-weight'     => $attr['fontWeight'],
			'font-size'       => UAGB_Helper::get_css_value( $attr['fontSize'], $attr['sizeType'] ),
			'line-height'     => $attr['lineHeight'] . $attr['lineHeightType'],
		);
		$m_selectors_child[ $wrapper . ' .uagb-icon-list__label' ] = array(
			'font-family'     => $attr['fontFamily'],
			'text-transform'  => $attr['fontTransform'],
			'text-decoration' => $attr['fontDecoration'] . '!important',
			'font-style'      => $attr['fontStyle'],
			'font-weight'     => $attr['fontWeight'],
			'font-size'       => UAGB_Helper::get_css_value( $attr['fontSizeMobile'], $attr['sizeType'] ),
			'line-height'     => $attr['lineHeightMobile'] . $attr['lineHeightType'],
		);
		$t_selectors_child[ $wrapper . ' .uagb-icon-list__label' ] = array(
			'font-family'     => $attr['fontFamily'],
			'text-transform'  => $attr['fontTransform'],
			'text-decoration' => $attr['fontDecoration'] . '!important',
			'font-style'      => $attr['fontStyle'],
			'font-weight'     => $attr['fontWeight'],
			'font-size'       => UAGB_Helper::get_css_value( $attr['fontSizeTablet'], $attr['sizeType'] ),
			'line-height'     => $attr['lineHeightTablet'] . $attr['lineHeightType'],
		);

		if ( $attr['icon_count'] <= $key ) {
			break;
		}

		$icon = array_merge( $default_attr, (array) $icon );

		$child_selectors = UAGB_Block_Helper::get_icon_list_child_selectors( $icon, $key, $attr['childMigrate'] );
		$selectors       = array_merge( $selectors, (array) $child_selectors );
		$t_selectors     = array_merge( $t_selectors, (array) $t_selectors_child );
		$m_selectors     = array_merge( $m_selectors, (array) $m_selectors_child );
	}
}

if ( 'right' === $attr['align'] && $attr['hideLabel'] ) {
	$selectors[' .uagb-icon-list__source-wrap']   = array(
		'margin-right' => '0px',
	);
	$m_selectors[' .uagb-icon-list__source-wrap'] = array(
		'margin-right' => '0px',
	);
	$t_selectors[' .uagb-icon-list__source-wrap'] = array(
		'margin-right' => '0px',
	);
} else {
	if ( 'before' === $attr['iconPlacement'] && ! $attr['hideLabel'] ) {
		$selectors[' .uagb-icon-list__source-wrap']   = array(
			'margin-right' => UAGB_Helper::get_css_value( $attr['inner_gap'], $attr['innerGapType'] ),
		);
		$m_selectors[' .uagb-icon-list__source-wrap'] = array(
			'margin-right' => UAGB_Helper::get_css_value( $attr['innerGapMobile'], $attr['innerGapType'] ),
		);
		$t_selectors[' .uagb-icon-list__source-wrap'] = array(
			'margin-right' => UAGB_Helper::get_css_value( $attr['innerGapTablet'], $attr['innerGapType'] ),
		);
	} elseif ( 'after' === $attr['iconPlacement'] && ! $attr['hideLabel'] ) {
		$selectors[' .uagb-icon-list__source-wrap']    = array(
			'margin-left' => UAGB_Helper::get_css_value( $attr['inner_gap'], $attr['innerGapType'] ),
		);
		$m_selectors[' .uagb-icon-list__source-wrap']  = array(
			'margin-left' => UAGB_Helper::get_css_value( $attr['innerGapMobile'], $attr['innerGapType'] ),
		);
		$t_selectors[' .uagb-icon-list__source-wrap']  = array(
			'margin-left' => UAGB_Helper::get_css_value( $attr['innerGapTablet'], $attr['innerGapType'] ),
		);
		$selectors[' .wp-block-uagb-icon-list-child '] = array(
			'flex-direction' => 'row-reverse',
		);
	}
	if ( 'center' === $attr['align'] ) {
		$selectors[' .wp-block-uagb-icon-list-child'] = array(
			'text-align' => 'center',
		);
	}
}


$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);

$base_selector = ( $attr['classMigrate'] ) ? '.wp-block-uagb-icon-list.uagb-block-' : '#uagb-icon-list-';

return UAGB_Helper::generate_all_css( $combined_selectors, $base_selector . $id );
