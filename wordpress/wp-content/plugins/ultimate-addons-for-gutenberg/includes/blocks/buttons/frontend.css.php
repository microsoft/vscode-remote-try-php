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
 */

// Adds Fonts.
UAGB_Block_JS::blocks_buttons_gfont( $attr );

$m_selectors = array();
$t_selectors = array();
$selectors   = array();

$button_desktop_padding = array();
$button_tablet_padding  = array();
$button_mobile_padding  = array();

if ( ! $attr['inheritGap'] ) {
	if ( 'desktop' === $attr['stack'] ) {
		$selectors[' .uagb-buttons__wrap ']   = array(
			'flex-direction' => 'column',
			'gap'            => UAGB_Helper::get_css_value( $attr['gap'], 'px' ),
		);
		$t_selectors[' .uagb-buttons__wrap '] = array(
			'gap' => UAGB_Helper::get_css_value( $attr['gapTablet'], 'px' ),
		);
		$m_selectors[' .uagb-buttons__wrap '] = array(
			'gap' => UAGB_Helper::get_css_value( $attr['gapMobile'], 'px' ),
		);

	} elseif ( 'tablet' === $attr['stack'] ) {

		$selectors['.wp-block-uagb-buttons.uagb-buttons__outer-wrap  .uagb-buttons__wrap '] = array(
			'gap' => UAGB_Helper::get_css_value( $attr['gap'], 'px' ),
		);
		$t_selectors[' .uagb-buttons__wrap'] = array(
			'flex-direction' => 'column',
			'gap'            => UAGB_Helper::get_css_value( $attr['gapTablet'], 'px' ),
		);
		$m_selectors[' .uagb-buttons__wrap'] = array(
			'flex-direction' => 'column',
			'gap'            => UAGB_Helper::get_css_value( $attr['gapMobile'], 'px' ),
		);

	} elseif ( 'mobile' === $attr['stack'] ) {

		$selectors['.wp-block-uagb-buttons.uagb-buttons__outer-wrap .uagb-buttons__wrap ']  = array(
			'flex-direction' => 'row',
			'gap'            => UAGB_Helper::get_css_value( $attr['gap'], 'px' ),
		);
		$t_selectors['.wp-block-uagb-buttons.uagb-buttons__outer-wrap .uagb-buttons__wrap'] = array(
			'gap' => UAGB_Helper::get_css_value( $attr['gapTablet'], 'px' ),
		);
		$m_selectors['.wp-block-uagb-buttons.uagb-buttons__outer-wrap .uagb-buttons__wrap'] = array(
			'flex-direction' => 'column',
			'gap'            => UAGB_Helper::get_css_value( $attr['gapMobile'], 'px' ),
		);

	} elseif ( 'none' === $attr['stack'] ) {
		$selectors['.wp-block-uagb-buttons.uagb-buttons__outer-wrap .uagb-buttons__wrap ']  = array(
			'gap' => UAGB_Helper::get_css_value( $attr['gap'], 'px' ),
		);
		$t_selectors['.wp-block-uagb-buttons.uagb-buttons__outer-wrap .uagb-buttons__wrap'] = array(
			'gap' => UAGB_Helper::get_css_value( $attr['gapTablet'], 'px' ),
		);
		$m_selectors['.wp-block-uagb-buttons.uagb-buttons__outer-wrap .uagb-buttons__wrap'] = array(
			'gap' => UAGB_Helper::get_css_value( $attr['gapMobile'], 'px' ),
		);
	}
}

if ( $attr['inheritGap'] ) {
	if ( 'desktop' === $attr['stack'] ) {
		$selectors[' .uagb-buttons__wrap '] = array(
			'flex-direction' => 'column',
		);
	} elseif ( 'tablet' === $attr['stack'] ) {

		$t_selectors[' .uagb-buttons__wrap'] = array(
			'flex-direction' => 'column',
		);
		$m_selectors[' .uagb-buttons__wrap'] = array(
			'flex-direction' => 'column',
		);

	} elseif ( 'mobile' === $attr['stack'] ) {

		$selectors['.wp-block-uagb-buttons.uagb-buttons__outer-wrap .uagb-buttons__wrap ']  = array(
			'flex-direction' => 'row',
		);
		$m_selectors['.wp-block-uagb-buttons.uagb-buttons__outer-wrap .uagb-buttons__wrap'] = array(
			'flex-direction' => 'column',
		);
	}
}

if ( $attr['flexWrap'] ) {
	$selectors[' .uagb-buttons__wrap ']['flex-wrap'] = 'wrap';
}

$vAlign = '';
switch ( $attr['verticalAlignment'] ) {
	case 'top':
		$vAlign = 'flex-start';
		break;
	case 'bottom':
		$vAlign = 'flex-end';
		break;
	default:
		$vAlign = 'center';
		break;
}
if ( 'full' !== $attr['align'] ) {
	$selectors['.uagb-buttons__outer-wrap .uagb-buttons__wrap '] = array(
		'justify-content' => $attr['align'],
		'align-items'     => $vAlign,
	);
} else {
	$selectors['.uagb-buttons__outer-wrap .uagb-buttons__wrap']                   = array(
		'width'       => '100%',
		'align-items' => $vAlign,
	);
	$selectors['.uagb-buttons__outer-wrap .uagb-buttons__wrap .wp-block-button '] = array(
		'width' => '100%',
	);
}

if ( 'full' !== $attr['alignTablet'] ) {
	$t_selectors['.uagb-buttons__outer-wrap .uagb-buttons__wrap ']                 = array(
		'justify-content' => $attr['alignTablet'],
		'align-items'     => $vAlign,
	);
	$t_selectors['.uagb-buttons__outer-wrap .uagb-buttons__wrap .wp-block-button'] = array(
		'width' => 'auto',
	);
} else {
	$t_selectors['.uagb-buttons__outer-wrap .uagb-buttons__wrap']                   = array(
		'width' => '100%',
	);
	$t_selectors['.uagb-buttons__outer-wrap .uagb-buttons__wrap .wp-block-button '] = array(
		'width' => '100%',
	);
}

if ( 'full' !== $attr['alignMobile'] ) {
	$m_selectors['.uagb-buttons__outer-wrap .uagb-buttons__wrap ']                 = array(
		'justify-content' => $attr['alignMobile'],
		'align-items'     => $vAlign,
	);
	$m_selectors['.uagb-buttons__outer-wrap .uagb-buttons__wrap .wp-block-button'] = array(
		'width' => 'auto',
	);
} else {
	$m_selectors['.uagb-buttons__outer-wrap .uagb-buttons__wrap']                   = array(
		'width' => '100%',
	);
	$m_selectors['.uagb-buttons__outer-wrap .uagb-buttons__wrap .wp-block-button '] = array(
		'width' => '100%',
	);
}

if ( $attr['childMigrate'] ) {

	$button_desktop_style = array( // For Backword user.
		'font-family'     => $attr['fontFamily'],
		'text-transform'  => $attr['fontTransform'],
		'text-decoration' => $attr['fontDecoration'],
		'font-style'      => $attr['fontStyle'],
		'font-weight'     => $attr['fontWeight'],
		'font-size'       => UAGB_Helper::get_css_value( $attr['fontSize'], $attr['fontSizeType'] ),
		'line-height'     => UAGB_Helper::get_css_value( $attr['lineHeight'], $attr['lineHeightType'] ),
		'letter-spacing'  => UAGB_Helper::get_css_value( $attr['fontLetterSpacing'], $attr['fontLetterSpacingType'] ),
	);

	if ( 'default' === $attr['buttonSize'] ) {
		$button_desktop_padding = array(
			'padding-top'    => UAGB_Helper::get_css_value( $attr['topPadding'], $attr['paddingUnit'] ),
			'padding-bottom' => UAGB_Helper::get_css_value( $attr['bottomPadding'], $attr['paddingUnit'] ),
			'padding-left'   => UAGB_Helper::get_css_value( $attr['leftPadding'], $attr['paddingUnit'] ),
			'padding-right'  => UAGB_Helper::get_css_value( $attr['rightPadding'], $attr['paddingUnit'] ),
		);
		$button_tablet_padding  = array(
			'padding-top'    => UAGB_Helper::get_css_value( $attr['topTabletPadding'], $attr['tabletPaddingUnit'] ),
			'padding-bottom' => UAGB_Helper::get_css_value( $attr['bottomTabletPadding'], $attr['tabletPaddingUnit'] ),
			'padding-left'   => UAGB_Helper::get_css_value( $attr['leftTabletPadding'], $attr['tabletPaddingUnit'] ),
			'padding-right'  => UAGB_Helper::get_css_value( $attr['rightTabletPadding'], $attr['tabletPaddingUnit'] ),
		);
		$button_mobile_padding  = array(
			'padding-top'    => UAGB_Helper::get_css_value( $attr['topMobilePadding'], $attr['mobilePaddingUnit'] ),
			'padding-bottom' => UAGB_Helper::get_css_value( $attr['bottomMobilePadding'], $attr['mobilePaddingUnit'] ),
			'padding-left'   => UAGB_Helper::get_css_value( $attr['leftMobilePadding'], $attr['mobilePaddingUnit'] ),
			'padding-right'  => UAGB_Helper::get_css_value( $attr['rightMobilePadding'], $attr['mobilePaddingUnit'] ),
		);
	}

	$button_tablet_style = array(
		'font-size'      => UAGB_Helper::get_css_value( $attr['fontSizeTablet'], $attr['fontSizeTypeTablet'] ),
		'line-height'    => UAGB_Helper::get_css_value( $attr['lineHeightTablet'], $attr['lineHeightType'] ),
		'letter-spacing' => UAGB_Helper::get_css_value( $attr['fontLetterSpacingTablet'], $attr['fontLetterSpacingType'] ),
	);

	$button_mobile_style = array(
		'font-size'      => UAGB_Helper::get_css_value( $attr['fontSizeMobile'], $attr['fontSizeTypeMobile'] ),
		'line-height'    => UAGB_Helper::get_css_value( $attr['lineHeightMobile'], $attr['lineHeightType'] ),
		'letter-spacing' => UAGB_Helper::get_css_value( $attr['fontLetterSpacingMobile'], $attr['fontLetterSpacingType'] ),
	);

	$button_desktop_style = $button_desktop_padding ? array_merge( $button_desktop_style, $button_desktop_padding ) : $button_desktop_style;
	$button_tablet_style  = $button_tablet_padding ? array_merge( $button_tablet_style, $button_tablet_padding ) : $button_tablet_style;
	$button_mobile_style  = $button_mobile_padding ? array_merge( $button_mobile_style, $button_mobile_padding ) : $button_mobile_style;

	$selectors[' .uagb-buttons-repeater:not(.wp-block-button__link)']                 = $button_desktop_style; // For Backword user.
	$selectors[' .uagb-button__wrapper .uagb-buttons-repeater.wp-block-button__link'] = $button_desktop_style; // For New User.
	$selectors[' .uagb-button__wrapper .uagb-buttons-repeater.ast-outline-button']    = $button_desktop_style; // For Secondary color from Astra Customizer.
	$t_selectors[' .uagb-buttons-repeater:not(.wp-block-button__link)']               = $button_tablet_style; // For Backword user.
	$t_selectors[' .uagb-buttons-repeater.wp-block-button__link']                     = $button_tablet_style; // For New User.
	$m_selectors[' .uagb-buttons-repeater:not(.wp-block-button__link)']               = $button_mobile_style; // For Backword user.
	$m_selectors[' .uagb-buttons-repeater.wp-block-button__link']                     = $button_mobile_style; // For New User.

	$selectors[' .uagb-button__wrapper'] = array(
		'margin-top'    => UAGB_Helper::get_css_value( $attr['topMargin'], $attr['marginType'] ),
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['bottomMargin'], $attr['marginType'] ),
		'margin-left'   => UAGB_Helper::get_css_value( $attr['leftMargin'], $attr['marginType'] ),
		'margin-right'  => UAGB_Helper::get_css_value( $attr['rightMargin'], $attr['marginType'] ),
	);

	$t_selectors[' .uagb-button__wrapper'] = array(
		'margin-top'    => UAGB_Helper::get_css_value( $attr['topMarginTablet'], $attr['marginType'] ),
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['bottomMarginTablet'], $attr['marginType'] ),
		'margin-left'   => UAGB_Helper::get_css_value( $attr['leftMarginTablet'], $attr['marginType'] ),
		'margin-right'  => UAGB_Helper::get_css_value( $attr['rightMarginTablet'], $attr['marginType'] ),
	);

	$m_selectors[' .uagb-button__wrapper'] = array(
		'margin-top'    => UAGB_Helper::get_css_value( $attr['topMarginMobile'], $attr['marginType'] ),
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['bottomMarginMobile'], $attr['marginType'] ),
		'margin-left'   => UAGB_Helper::get_css_value( $attr['leftMarginMobile'], $attr['marginType'] ),
		'margin-right'  => UAGB_Helper::get_css_value( $attr['rightMarginMobile'], $attr['marginType'] ),
	);
}

if ( ! $attr['childMigrate'] ) {

	$defaults = UAGB_DIR . 'includes/blocks/buttons-child/attributes.php';

	if ( file_exists( $defaults ) ) {
		$default_attr = include $defaults;
	}

	$default_attr = ( ! empty( $default_attr ) && is_array( $default_attr ) ) ? $default_attr : array();

	foreach ( $attr['buttons'] as $key => $button ) {

		if ( $attr['btn_count'] <= $key ) {
			break;
		}

		$button = array_merge( $default_attr, $button );

		$wrapper = ( ! $attr['childMigrate'] ) ? ' .uagb-buttons-repeater-' . $key . '.uagb-button__wrapper' : ' .uagb-buttons-repeater';

		$selectors[ $wrapper ] = array(
			'font-family'     => $attr['fontFamily'],
			'text-transform'  => $attr['fontTransform'],
			'text-decoration' => $attr['fontDecoration'],
			'font-style'      => $attr['fontStyle'],
			'font-weight'     => $attr['fontWeight'],
		);

		$child_selectors = UAGB_Block_Helper::get_buttons_child_selectors( $button, $key, $attr['childMigrate'] );
		$selectors       = array_merge( $selectors, $child_selectors['selectors'] );
		$t_selectors     = array_merge( $t_selectors, $child_selectors['t_selectors'] );
		$m_selectors     = array_merge( $m_selectors, $child_selectors['m_selectors'] );
	}
}

$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);

$base_selector = ( $attr['classMigrate'] ) ? '.uagb-block-' : '#uagb-buttons-';

return UAGB_Helper::generate_all_css( 
	$combined_selectors,
	$base_selector . $id,
	isset( $gbs_class ) ? $gbs_class : ''
);
