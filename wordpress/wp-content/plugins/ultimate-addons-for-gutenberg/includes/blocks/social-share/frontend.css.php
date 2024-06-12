<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 * @var mixed[] $attr
 * @var int $id
 *
 * @package uagb
 */

$gap_tablet_fallback = is_numeric( $attr['gapTablet'] ) ? $attr['gapTablet'] : $attr['gap'];
$gap_mobile_fallback = is_numeric( $attr['gapMobile'] ) ? $attr['gapMobile'] : $gap_tablet_fallback;

$alignment   = ( 'left' === $attr['align'] ) ? 'flex-start' : ( ( 'right' === $attr['align'] ) ? 'flex-end' : 'center' );
$t_alignment = ( 'left' === $attr['alignTablet'] ) ? 'flex-start' : ( ( 'right' === $attr['alignTablet'] ) ? 'flex-end' : 'center' );
$m_alignment = ( 'left' === $attr['alignMobile'] ) ? 'flex-start' : ( ( 'right' === $attr['alignMobile'] ) ? 'flex-end' : 'center' );

$m_selectors = array();
$t_selectors = array();

$image_size   = UAGB_Helper::get_css_value( $attr['size'], $attr['sizeType'] );
$m_image_size = UAGB_Helper::get_css_value( $attr['sizeMobile'], $attr['sizeType'] );
$t_image_size = UAGB_Helper::get_css_value( $attr['sizeTablet'], $attr['sizeType'] );

$selectors = array(
	' .uagb-ss-repeater span.uagb-ss__link'           => array(
		'color' => $attr['iconColor'],
	),
	' .uagb-ss-repeater a.uagb-ss__link'              => array( // Backward user case.
		'color' => $attr['iconColor'],
	),
	' .uagb-ss-repeater span.uagb-ss__link svg'       => array(
		'fill' => $attr['iconColor'],
	),
	' .uagb-ss-repeater a.uagb-ss__link svg'          => array( // Backward user case.
		'fill' => $attr['iconColor'],
	),
	' .uagb-ss-repeater:hover span.uagb-ss__link'     => array(
		'color' => $attr['iconHoverColor'],
	),
	' .uagb-ss-repeater:hover a.uagb-ss__link'        => array( // Backward user case.
		'color' => $attr['iconHoverColor'],
	),
	' .uagb-ss-repeater:hover span.uagb-ss__link svg' => array(
		'fill' => $attr['iconHoverColor'],
	),
	' .uagb-ss-repeater:hover a.uagb-ss__link svg'    => array( // Backward user case.
		'fill' => $attr['iconHoverColor'],
	),
	' .uagb-ss-repeater.uagb-ss__wrapper'             => array(
		'background' => $attr['iconBgColor'],
	),
	' .uagb-ss-repeater.uagb-ss__wrapper:hover'       => array(
		'background' => $attr['iconBgHoverColor'],
	),
);

$selectors['.uagb-social-share__outer-wrap .block-editor-inner-blocks']   = array(
	'text-align' => UAGB_Helper::get_css_value( $attr['align'] ),
);
$t_selectors['.uagb-social-share__outer-wrap .block-editor-inner-blocks'] = array(
	'text-align' => UAGB_Helper::get_css_value( $attr['alignTablet'] ),
);
$m_selectors['.uagb-social-share__outer-wrap .block-editor-inner-blocks'] = array(
	'text-align' => UAGB_Helper::get_css_value( $attr['alignMobile'] ),
);

$selectors['.uagb-social-share__layout-vertical .uagb-ss__wrapper']     = array(
	'margin-left'   => 0,
	'margin-right'  => 0,
	'margin-top'    => UAGB_Helper::get_css_value( ( $attr['gap'] / 2 ), 'px' ),
	'margin-bottom' => UAGB_Helper::get_css_value( ( $attr['gap'] / 2 ), 'px' ),
);
$selectors['.uagb-social-share__layout-vertical .uagb-ss__link']        = array(
	'padding' => UAGB_Helper::get_css_value( $attr['bgSize'], 'px' ),
);
$m_selectors['.uagb-social-share__layout-vertical .uagb-ss__wrapper']   = array(
	'margin-left'   => 0,
	'margin-right'  => 0,
	'margin-top'    => UAGB_Helper::get_css_value( ( $gap_mobile_fallback / 2 ), 'px' ),
	'margin-bottom' => UAGB_Helper::get_css_value( ( $gap_mobile_fallback / 2 ), 'px' ),
);
$t_selectors['.uagb-social-share__layout-vertical .uagb-ss__wrapper']   = array(
	'margin-left'   => 0,
	'margin-right'  => 0,
	'margin-top'    => UAGB_Helper::get_css_value( ( $gap_tablet_fallback / 2 ), 'px' ),
	'margin-bottom' => UAGB_Helper::get_css_value( ( $gap_tablet_fallback / 2 ), 'px' ),
);
$selectors['.uagb-social-share__layout-horizontal .uagb-ss__link']      = array(
	'padding' => UAGB_Helper::get_css_value( $attr['bgSize'], 'px' ),
);
$selectors['.uagb-social-share__layout-horizontal .uagb-ss__wrapper']   = array(
	'margin-left'  => UAGB_Helper::get_css_value( ( $attr['gap'] / 2 ), 'px' ),
	'margin-right' => UAGB_Helper::get_css_value( ( $attr['gap'] / 2 ), 'px' ),
);
$m_selectors['.uagb-social-share__layout-horizontal .uagb-ss__wrapper'] = array(
	'margin-left'  => UAGB_Helper::get_css_value( ( $gap_mobile_fallback / 2 ), 'px' ),
	'margin-right' => UAGB_Helper::get_css_value( ( $gap_mobile_fallback / 2 ), 'px' ),
);
$t_selectors['.uagb-social-share__layout-horizontal .uagb-ss__wrapper'] = array(
	'margin-left'  => UAGB_Helper::get_css_value( ( $gap_tablet_fallback / 2 ), 'px' ),
	'margin-right' => UAGB_Helper::get_css_value( ( $gap_tablet_fallback / 2 ), 'px' ),
);

$selectors[' .wp-block-uagb-social-share-child ']   = array(
	'border-radius' => UAGB_Helper::get_css_value( $attr['borderRadius'], 'px' ),
);
$m_selectors[' .wp-block-uagb-social-share-child '] = array(
	'border-radius' => UAGB_Helper::get_css_value( $attr['borderRadiusMobile'], 'px' ),
);
$t_selectors[' .wp-block-uagb-social-share-child '] = array(
	'border-radius' => UAGB_Helper::get_css_value( $attr['borderRadiusTablet'], 'px' ),
);

$selectors[' .uagb-ss__source-wrap'] = array(
	'width' => $image_size,
);

$selectors[' .uagb-ss__source-wrap svg'] = array(
	'width'  => $image_size,
	'height' => $image_size,
);

$selectors[' .uagb-ss__source-image'] = array(
	'width' => $image_size,
);

$selectors[' .uagb-ss__source-icon'] = array(
	'width'     => $image_size,
	'height'    => $image_size,
	'font-size' => $image_size,
);

$t_selectors[' .uagb-ss__source-wrap'] = array(
	'width'       => $t_image_size,
	'height'      => $t_image_size,
	'line-height' => $t_image_size,
);

$t_selectors[' .uagb-ss__source-wrap svg'] = array(
	'width'  => $t_image_size,
	'height' => $t_image_size,
);

$t_selectors[' .uagb-ss__source-image'] = array(
	'width' => $t_image_size,
);

$t_selectors[' .uagb-ss__source-icon'] = array(
	'width'       => $t_image_size,
	'height'      => $t_image_size,
	'font-size'   => $t_image_size,
	'line-height' => $t_image_size,
);

$m_selectors[' .uagb-ss__source-wrap'] = array(
	'width'       => $m_image_size,
	'height'      => $m_image_size,
	'line-height' => $m_image_size,
);

$m_selectors[' .uagb-ss__source-wrap svg'] = array(
	'width'  => $m_image_size,
	'height' => $m_image_size,
);

$m_selectors[' .uagb-ss__source-image'] = array(
	'width' => $m_image_size,
);

$m_selectors[' .uagb-ss__source-icon'] = array(
	'width'       => $m_image_size,
	'height'      => $m_image_size,
	'font-size'   => $m_image_size,
	'line-height' => $m_image_size,
);


$selectors['.uagb-social-share__outer-wrap'] = array(
	'justify-content'   => $alignment,
	'-webkit-box-pack'  => $alignment,
	'-ms-flex-pack'     => $alignment,
	'-webkit-box-align' => $alignment,
	'-ms-flex-align'    => $alignment,
	'align-items'       => $alignment,
);

$t_selectors['.uagb-social-share__outer-wrap'] = array(
	'justify-content'   => $t_alignment,
	'-webkit-box-pack'  => $t_alignment,
	'-ms-flex-pack'     => $t_alignment,
	'-webkit-box-align' => $t_alignment,
	'-ms-flex-align'    => $t_alignment,
	'align-items'       => $t_alignment,
);

$m_selectors['.uagb-social-share__outer-wrap'] = array(
	'justify-content'   => $m_alignment,
	'-webkit-box-pack'  => $m_alignment,
	'-ms-flex-pack'     => $m_alignment,
	'-webkit-box-align' => $m_alignment,
	'-ms-flex-align'    => $m_alignment,
	'align-items'       => $m_alignment,
);

if ( ! $attr['childMigrate'] ) {

	$defaults = UAGB_DIR . 'includes/blocks/social-share-child/attributes.php';

	if ( file_exists( $defaults ) ) {
		$default_attr = include $defaults;
	}

	$default_attr = ( ! empty( $default_attr ) && is_array( $default_attr ) ) ? $default_attr : array();

	foreach ( $attr['socials'] as $key => $socials ) {

		$socials                        = array_merge( $default_attr, (array) $socials );
		$socials['icon_color']          = ( isset( $socials['icon_color'] ) ) ? $socials['icon_color'] : '';
		$socials['icon_hover_color']    = ( isset( $socials['icon_hover_color'] ) ) ? $socials['icon_hover_color'] : '';
		$socials['icon_bg_color']       = ( isset( $socials['icon_bg_color'] ) ) ? $socials['icon_bg_color'] : '';
		$socials['icon_bg_hover_color'] = ( isset( $socials['icon_bg_hover_color'] ) ) ? $socials['icon_bg_hover_color'] : '';

		if ( $attr['social_count'] <= $key ) {
			break;
		}

		$child_selectors = UAGB_Block_Helper::get_social_share_child_selectors( $socials, $key, $attr['childMigrate'] );
		$selectors       = array_merge( $selectors, (array) $child_selectors );
	}
}

if ( 'horizontal' === $attr['social_layout'] ) {

	if ( 'desktop' === $attr['stack'] ) {

		$selectors[' .uagb-ss__wrapper']   = array(
			'margin-left'   => 0,
			'margin-right'  => 0,
			'margin-bottom' => UAGB_Helper::get_css_value( $attr['gap'], 'px' ),
		);
		$t_selectors[' .uagb-ss__wrapper'] = array(
			'margin-left'   => 0,
			'margin-right'  => 0,
			'margin-bottom' => UAGB_Helper::get_css_value( $attr['gapTablet'], 'px' ),
		);
		$m_selectors[' .uagb-ss__wrapper'] = array(
			'margin-left'   => 0,
			'margin-right'  => 0,
			'margin-bottom' => UAGB_Helper::get_css_value( $attr['gapMobile'], 'px' ),
		);

		$selectors['.uagb-social-share__outer-wrap'] = array(
			'flex-direction'    => 'column',
			'justify-content'   => $alignment,
			'-webkit-box-pack'  => $alignment,
			'-ms-flex-pack'     => $alignment,
			'-webkit-box-align' => $alignment,
			'-ms-flex-align'    => $alignment,
			'align-items'       => $alignment,
		);

		$t_selectors['.uagb-social-share__outer-wrap'] = array(
			'flex-direction'    => 'column',
			'justify-content'   => $t_alignment,
			'-webkit-box-pack'  => $t_alignment,
			'-ms-flex-pack'     => $t_alignment,
			'-webkit-box-align' => $t_alignment,
			'-ms-flex-align'    => $t_alignment,
			'align-items'       => $t_alignment,
		);

		$m_selectors['.uagb-social-share__outer-wrap'] = array(
			'flex-direction'    => 'column',
			'justify-content'   => $m_alignment,
			'-webkit-box-pack'  => $m_alignment,
			'-ms-flex-pack'     => $m_alignment,
			'-webkit-box-align' => $m_alignment,
			'-ms-flex-align'    => $m_alignment,
			'align-items'       => $m_alignment,
		);

	} elseif ( 'tablet' === $attr['stack'] ) {

		$t_selectors[' .uagb-ss__wrapper'] = array(
			'margin-left'   => 0,
			'margin-right'  => 0,
			'margin-bottom' => UAGB_Helper::get_css_value( $attr['gapTablet'], 'px' ),
		);

		$t_selectors['.uagb-social-share__outer-wrap'] = array(
			'flex-direction'    => 'column',
			'justify-content'   => $t_alignment,
			'-webkit-box-pack'  => $t_alignment,
			'-ms-flex-pack'     => $t_alignment,
			'-webkit-box-align' => $t_alignment,
			'-ms-flex-align'    => $t_alignment,
			'align-items'       => $t_alignment,
		);

	} elseif ( 'mobile' === $attr['stack'] ) {

		$m_selectors[' .uagb-ss__wrapper'] = array(
			'margin-left'   => 0,
			'margin-right'  => 0,
			'margin-bottom' => UAGB_Helper::get_css_value( $attr['gapMobile'], 'px' ),
		);

		$m_selectors['.uagb-social-share__outer-wrap'] = array(
			'flex-direction'    => 'column',
			'justify-content'   => $m_alignment,
			'-webkit-box-pack'  => $m_alignment,
			'-ms-flex-pack'     => $m_alignment,
			'-webkit-box-align' => $m_alignment,
			'-ms-flex-align'    => $m_alignment,
			'align-items'       => $m_alignment,
		);
	}
}

$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);

$base_selector = ( $attr['classMigrate'] ) ? '.uagb-block-' : '#uagb-social-share-';

return UAGB_Helper::generate_all_css( $combined_selectors, $base_selector . $id );
