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

$paddingLeftMobile  = isset( $attr['paddingLeftMobile'] ) ? $attr['paddingLeftMobile'] : $attr['contentPaddingMobile'];
$paddingRightMobile = isset( $attr['paddingRightMobile'] ) ? $attr['paddingRightMobile'] : $attr['contentPaddingMobile'];
$paddingLeftTablet  = isset( $attr['paddingLeftTablet'] ) ? $attr['paddingLeftTablet'] : $attr['contentPadding'];
$paddingRightTablet = isset( $attr['paddingRightTablet'] ) ? $attr['paddingRightTablet'] : $attr['contentPadding'];
$paddingLeft        = isset( $attr['paddingLeft'] ) ? $attr['paddingLeft'] : $attr['contentPadding'];
$paddingRight       = isset( $attr['paddingRight'] ) ? $attr['paddingRight'] : $attr['contentPadding'];

$selectors = UAGB_Block_Helper::get_post_selectors( $attr );
// Pagination CSS.
$selectors[' .uagb-post-pagination-wrap'] = array(

	'margin-top'                             => UAGB_Helper::get_css_value( $attr['paginationSpacing'], $attr['paginationSpacingUnit'] ),
	'justify-content'                        => $attr['paginationAlignment'],
	'margin-' . $attr['paginationAlignment'] => '10px',
);

if ( 'filled' === $attr['paginationLayout'] ) {
	$selectors[' .uagb-post-pagination-wrap .page-numbers.current'] = array(

		'background-color' => $attr['paginationBgActiveColor'],
		'color'            => $attr['paginationActiveColor'],
	);
	$selectors[' .uagb-post-pagination-wrap a']                     = array(

		'background-color' => $attr['paginationBgColor'],
		'color'            => $attr['paginationColor'],
	);
} else {

	$selectors[' .uagb-post-pagination-wrap .page-numbers.current'] = array(

		'border-style'     => 'solid',
		'background-color' => 'transparent',
		'border-width'     => UAGB_Helper::get_css_value( $attr['paginationBorderSize'], 'px' ),
		'border-color'     => $attr['paginationBorderActiveColor'],
		'border-radius'    => UAGB_Helper::get_css_value( $attr['paginationBorderRadius'], 'px' ),
		'color'            => $attr['paginationActiveColor'],
	);

	$selectors[' .uagb-post-pagination-wrap a'] = array(

		'border-style'     => 'solid',
		'background-color' => 'transparent',
		'border-width'     => UAGB_Helper::get_css_value( $attr['paginationBorderSize'], 'px' ),
		'border-color'     => $attr['paginationBorderColor'],
		'border-radius'    => UAGB_Helper::get_css_value( $attr['paginationBorderRadius'], 'px' ),
		'color'            => $attr['paginationColor'],
	);

}

$m_selectors = UAGB_Block_Helper::get_post_mobile_selectors( $attr );
$t_selectors = UAGB_Block_Helper::get_post_tablet_selectors( $attr );

if ( 'top' === $attr['imgPosition'] ) {
	$selectors['.uagb-equal_height_inline-read-more-buttons .uagb-post__inner-wrap .uagb-post__text:last-child']   = array(
		'left'  => UAGB_Helper::get_css_value( $paddingLeft, $attr['contentPaddingUnit'] ),
		'right' => UAGB_Helper::get_css_value( $paddingRight, $attr['contentPaddingUnit'] ),
	);
	$m_selectors['.uagb-equal_height_inline-read-more-buttons .uagb-post__inner-wrap .uagb-post__text:last-child'] = array(
		'left'  => UAGB_Helper::get_css_value( $paddingLeftMobile, $attr['mobilePaddingUnit'] ),
		'right' => UAGB_Helper::get_css_value( $paddingRightMobile, $attr['mobilePaddingUnit'] ),
	);
	$m_selectors['.uagb-equal_height_inline-read-more-buttons .uagb-post__inner-wrap .uagb-post__text:last-child'] = array(
		'left'  => UAGB_Helper::get_css_value( $paddingLeftTablet, $attr['tabletPaddingUnit'] ),
		'right' => UAGB_Helper::get_css_value( $paddingRightTablet, $attr['tabletPaddingUnit'] ),
	);
} else {
	$selectors['.uagb-equal_height_inline-read-more-buttons .uagb-post__inner-wrap .uagb-post__text:nth-last-child(2)']   = array(
		'left'  => UAGB_Helper::get_css_value( $paddingLeft, $attr['contentPaddingUnit'] ),
		'right' => UAGB_Helper::get_css_value( $paddingRight, $attr['contentPaddingUnit'] ),
	);
	$m_selectors['.uagb-equal_height_inline-read-more-buttons .uagb-post__inner-wrap .uagb-post__text:nth-last-child(2)'] = array(
		'left'  => UAGB_Helper::get_css_value( $paddingLeftMobile, $attr['mobilePaddingUnit'] ),
		'right' => UAGB_Helper::get_css_value( $paddingRightMobile, $attr['mobilePaddingUnit'] ),
	);
	$m_selectors['.uagb-equal_height_inline-read-more-buttons .uagb-post__inner-wrap .uagb-post__text:nth-last-child(2)'] = array(
		'left'  => UAGB_Helper::get_css_value( $paddingLeftTablet, $attr['tabletPaddingUnit'] ),
		'right' => UAGB_Helper::get_css_value( $paddingRightTablet, $attr['tabletPaddingUnit'] ),
	);
}

if ( $attr['isLeftToRightLayout'] ) {
	$selectors['.wp-block-uagb-post-grid'] = array(
		'display'        => 'flex',
		'flex-direction' => 'column',
	);

	$selectors['.wp-block-uagb-post-grid article'] = array(
		'display'        => 'flex',
		'flex-direction' => $attr['wrapperAlign'],
		'width'          => '100%',
	);

	$selectors['.wp-block-uagb-post-grid .uagb-post__image'] = array(
		'flex'  => 'none',
		'width' => ( 'top' === $attr['imgPosition'] ) ? '35%' : '100%',
	);
	
}


$selectors['.wp-block-uagb-post-grid .uag-post-grid-wrapper'] = array(
	'padding-top'     => UAGB_Helper::get_css_value( $attr['wrapperTopPadding'], $attr['wrapperPaddingUnit'] ),
	'padding-right'   => UAGB_Helper::get_css_value( $attr['wrapperRightPadding'], $attr['wrapperPaddingUnit'] ),
	'padding-bottom'  => UAGB_Helper::get_css_value( $attr['wrapperBottomPadding'], $attr['wrapperPaddingUnit'] ),
	'padding-left'    => UAGB_Helper::get_css_value( $attr['wrapperLeftPadding'], $attr['wrapperPaddingUnit'] ),
	'width'           => '100%',
	'display'         => 'flex',
	'flex-direction'  => 'column',
	'justify-content' => $attr['wrapperAlignPosition'],
);

$t_selectors['.wp-block-uagb-post-grid .uag-post-grid-wrapper'] = array(
	'padding-top'    => UAGB_Helper::get_css_value( $attr['wrapperTopPaddingTablet'], $attr['wrapperPaddingUnitTablet'] ),
	'padding-right'  => UAGB_Helper::get_css_value( $attr['wrapperRightPaddingTablet'], $attr['wrapperPaddingUnitTablet'] ),
	'padding-bottom' => UAGB_Helper::get_css_value( $attr['wrapperBottomPaddingTablet'], $attr['wrapperPaddingUnitTablet'] ),
	'padding-left'   => UAGB_Helper::get_css_value( $attr['wrapperLeftPaddingTablet'], $attr['wrapperPaddingUnitTablet'] ),
);

$m_selectors['.wp-block-uagb-post-grid .uag-post-grid-wrapper'] = array(
	'padding-top'    => UAGB_Helper::get_css_value( $attr['wrapperTopPaddingMobile'], $attr['wrapperPaddingUnitMobile'] ),
	'padding-right'  => UAGB_Helper::get_css_value( $attr['wrapperRightPaddingMobile'], $attr['wrapperPaddingUnitMobile'] ),
	'padding-bottom' => UAGB_Helper::get_css_value( $attr['wrapperBottomPaddingMobile'], $attr['wrapperPaddingUnitMobile'] ),
	'padding-left'   => UAGB_Helper::get_css_value( $attr['wrapperLeftPaddingMobile'], $attr['wrapperPaddingUnitMobile'] ),
	'width'          => 'unset',
);

if ( $attr['isLeftToRightLayout'] ) {
	$m_selectors['.wp-block-uagb-post-grid .uagb-post__image'] = array(
		'width' => ( 'top' === $attr['imgPosition'] ) ? 'unset' : '100%',
	);
	$t_selectors['.wp-block-uagb-post-grid .uagb-post__image'] = array(
		'width' => ( 'top' === $attr['imgPosition'] ) ? '45%' : '100%',
	);
}

$m_selectors['.wp-block-uagb-post-grid']         = ( $attr['isLeftToRightLayout'] ? array(
	'display' => 'grid',
) : array() );
$m_selectors['.wp-block-uagb-post-grid article'] = ( $attr['isLeftToRightLayout'] ? array(
	'display' => 'inline-block',
) : array() );

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
if ( ! $attr['inheritFromThemeBtn'] ) {
	$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'cta', ' .uagb-post__text.uagb-post__cta', $combined_selectors );
	$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'cta', ' .uagb-post__text.uagb-post__cta a', $combined_selectors );
}

return UAGB_Helper::generate_all_css( $combined_selectors, '.uagb-block-' . $id );
