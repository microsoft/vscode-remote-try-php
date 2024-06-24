<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 * @var mixed[] $attr
 * @var int $id
 * @package uagb
 */

// Adds Fonts.
UAGB_Block_JS::blocks_post_gfont( $attr );

$selectors = UAGB_Block_Helper::get_post_selectors( $attr );

$m_selectors = UAGB_Block_Helper::get_post_mobile_selectors( $attr );

$t_selectors = UAGB_Block_Helper::get_post_tablet_selectors( $attr );

$paginationpaddingTop    = isset( $attr['paginationButtonPaddingTop'] ) ? $attr['paginationButtonPaddingTop'] : $attr['vpaginationButtonPaddingDesktop'];
$paginationpaddingBottom = isset( $attr['paginationButtonPaddingBottom'] ) ? $attr['paginationButtonPaddingBottom'] : $attr['vpaginationButtonPaddingDesktop'];
$paginationpaddingLeft   = isset( $attr['paginationButtonPaddingLeft'] ) ? $attr['paginationButtonPaddingLeft'] : $attr['hpaginationButtonPaddingDesktop'];
$paginationpaddingRight  = isset( $attr['paginationButtonPaddingRight'] ) ? $attr['paginationButtonPaddingRight'] : $attr['hpaginationButtonPaddingDesktop'];

$paginationButtonPaddingTopTablet    = isset( $attr['paginationButtonPaddingTopTablet'] ) ? $attr['paginationButtonPaddingTopTablet'] : $attr['vpaginationButtonPaddingTablet'];
$paginationButtonPaddingBottomTablet = isset( $attr['paginationButtonPaddingBottomTablet'] ) ? $attr['paginationButtonPaddingBottomTablet'] : $attr['vpaginationButtonPaddingTablet'];
$paginationButtonPaddingLeftTablet   = isset( $attr['paginationButtonPaddingLeftTablet'] ) ? $attr['paginationButtonPaddingLeftTablet'] : $attr['hpaginationButtonPaddingTablet'];
$paginationButtonPaddingRightTablet  = isset( $attr['paginationButtonPaddingRightTablet'] ) ? $attr['paginationButtonPaddingRightTablet'] : $attr['hpaginationButtonPaddingTablet'];

$paginationButtonPaddingTopMobile    = isset( $attr['paginationButtonPaddingTopMobile'] ) ? $attr['paginationButtonPaddingTopMobile'] : $attr['vpaginationButtonPaddingMobile'];
$paginationButtonPaddingBottomMobile = isset( $attr['paginationButtonPaddingBottomMobile'] ) ? $attr['paginationButtonPaddingBottomMobile'] : $attr['vpaginationButtonPaddingMobile'];
$paginationButtonPaddingLeftMobile   = isset( $attr['paginationButtonPaddingLeftMobile'] ) ? $attr['paginationButtonPaddingLeftMobile'] : $attr['hpaginationButtonPaddingMobile'];
$paginationButtonPaddingRightMobile  = isset( $attr['paginationButtonPaddingRightMobile'] ) ? $attr['paginationButtonPaddingRightMobile'] : $attr['hpaginationButtonPaddingMobile'];

$pagination_masonry_border_css        = UAGB_Block_Helper::uag_generate_border_css( $attr, 'paginationMasonry' );
$pagination_masonry_border_css_tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'paginationMasonry', 'tablet' );
$pagination_masonry_border_css_mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'paginationMasonry', 'mobile' );

if ( 'infinite' === $attr['paginationType'] ) {

	$selectors[' .uagb-post__load-more-wrap'] = array(
		'text-align' => $attr['paginationAlign'],
	);

	$selectors[' .uagb-post__load-more-wrap .uagb-post-pagination-button']       = array_merge(
		array(

			'color'            => $attr['paginationTextColor'],
			'background-color' => $attr['paginationMasonryBgColor'],
			'font-size'        => UAGB_Helper::get_css_value( $attr['paginationFontSize'], 'px' ),
			'padding-top'      => UAGB_Helper::get_css_value(
				$paginationpaddingTop,
				$attr['paginationButtonPaddingType']
			),
			'padding-bottom'   => UAGB_Helper::get_css_value(
				$paginationpaddingBottom,
				$attr['paginationButtonPaddingType']
			),
			'padding-right'    => UAGB_Helper::get_css_value(
				$paginationpaddingRight,
				$attr['paginationButtonPaddingType']
			),
			'padding-left'     => UAGB_Helper::get_css_value(
				$paginationpaddingLeft,
				$attr['paginationButtonPaddingType']
			),
		),
		$pagination_masonry_border_css
	);
	$selectors[' .uagb-post__load-more-wrap .uagb-post-pagination-button:hover'] = array(
		'color'            => $attr['paginationTextHoverColor'],
		'background-color' => $attr['paginationBgHoverColor'],
		'border-color'     => $attr['paginationMasonryBorderHColor'],
	);
	$m_selectors[' .uagb-post__load-more-wrap .uagb-post-pagination-button']     = array(
		'padding-top'    => UAGB_Helper::get_css_value(
			$paginationButtonPaddingTopMobile,
			$attr['mobilepaginationButtonPaddingType']
		),
		'padding-right'  => UAGB_Helper::get_css_value(
			$paginationButtonPaddingRightMobile,
			$attr['mobilepaginationButtonPaddingType']
		),
		'padding-bottom' => UAGB_Helper::get_css_value(
			$paginationButtonPaddingBottomMobile,
			$attr['mobilepaginationButtonPaddingType']
		),
		'padding-left'   => UAGB_Helper::get_css_value(
			$paginationButtonPaddingLeftMobile,
			$attr['mobilepaginationButtonPaddingType']
		),
	);
	$t_selectors[' .uagb-post__load-more-wrap .uagb-post-pagination-button']     = array(
		'padding-top'    => UAGB_Helper::get_css_value(
			$paginationButtonPaddingTopTablet,
			$attr['tabletpaginationButtonPaddingType']
		),
		'padding-bottom' => UAGB_Helper::get_css_value(
			$paginationButtonPaddingBottomTablet,
			$attr['tabletpaginationButtonPaddingType']
		),
		'padding-right'  => UAGB_Helper::get_css_value(
			$paginationButtonPaddingRightTablet,
			$attr['tabletpaginationButtonPaddingType']
		),
		'padding-left'   => UAGB_Helper::get_css_value(
			$paginationButtonPaddingLeftTablet,
			$attr['tabletpaginationButtonPaddingType']
		),
	);
	$t_selectors[' .uagb-post__load-more-wrap .uagb-post-pagination-button']     = $pagination_masonry_border_css_tablet;
	$m_selectors[' .uagb-post__load-more-wrap .uagb-post-pagination-button']     = $pagination_masonry_border_css_mobile;

	$selectors['.uagb-post-grid .uagb-post-inf-loader div'] = array(
		'width'            => UAGB_Helper::get_css_value( $attr['loaderSize'], 'px' ),
		'height'           => UAGB_Helper::get_css_value( $attr['loaderSize'], 'px' ),
		'background-color' => $attr['loaderColor'],
	);
}

$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);


$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'title', ' .uagb-post__text.uagb-post__title', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'title', ' .uagb-post__text.uagb-post__title a', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'meta', ' .uagb-post__text.uagb-post-grid-byline > span', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'meta', ' .uagb-post__text.uagb-post-grid-byline time', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'meta', ' .uagb-post__text.uagb-post-grid-byline .uagb-post__author', $combined_selectors );

$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'meta', ' .uagb-post__text.uagb-post-grid-byline .uagb-post__author a', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'meta', ' span.uagb-post__taxonomy', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'meta', ' .uagb-post__inner-wrap .uagb-post__taxonomy.highlighted', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'excerpt', ' .uagb-post__text.uagb-post__excerpt', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'cta', ' .uagb-post__text.uagb-post__cta', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'cta', ' .uagb-post__text.uagb-post__cta a', $combined_selectors );


return UAGB_Helper::generate_all_css( $combined_selectors, '.uagb-block-' . $id );
