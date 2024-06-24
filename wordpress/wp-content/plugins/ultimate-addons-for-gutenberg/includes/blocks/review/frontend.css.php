<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 * @var mixed[] $attr
 * @package uagb
 */

// Adds Fonts.
UAGB_Block_JS::blocks_review_gfont( $attr );

$t_selectors = array();
$m_selectors = array();
$selectors   = array();

$top_padding    = isset( $attr['topPadding'] ) ? $attr['topPadding'] : $attr['contentVrPadding'];
$bottom_padding = isset( $attr['bottomPadding'] ) ? $attr['bottomPadding'] : $attr['contentVrPadding'];
$left_padding   = isset( $attr['leftPadding'] ) ? $attr['leftPadding'] : $attr['contentHrPadding'];
$right_padding  = isset( $attr['rightPadding'] ) ? $attr['rightPadding'] : $attr['contentHrPadding'];

$selectors = array(
	' .uagb_review_block .uagb-rating-title'  => array(
		'color' => $attr['titleColor'],
	),
	' .uagb_review_block .uagb-rating-desc'   => array(
		'color' => $attr['descColor'],
	),
	' .uagb_review_block .uagb-rating-author' => array(
		'color' => $attr['authorColor'],
	),
	' .uagb_review_entry'                     => array(
		'color' => $attr['contentColor'],
	),
	' .uagb_review_block'                     => array(
		'padding-left'   => UAGB_Helper::get_css_value( $left_padding, $attr['paddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $right_padding, $attr['paddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $top_padding, $attr['paddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $bottom_padding, $attr['paddingUnit'] ),
		'text-align'     => $attr['overallAlignment'],
	),
	' .uagb_review_summary'                   => array(
		'color' => $attr['summaryColor'],
	),
	' .uagb_review_entry .star, .uagb_review_average_stars .star' => array(
		'fill' => $attr['starColor'],
	),
	' .uagb_review_entry path, .uagb_review_average_stars path' => array(
		'stroke' => $attr['starOutlineColor'],
		'fill'   => $attr['starActiveColor'],
	),
);

$m_selectors = array(
	' .uagb_review_block' => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['paddingLeftMobile'], $attr['mobilePaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['paddingRightMobile'], $attr['mobilePaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['paddingTopMobile'], $attr['mobilePaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['paddingBottomMobile'], $attr['mobilePaddingUnit'] ),
	),
);

$t_selectors = array(
	' .uagb_review_block' => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['paddingLeftTablet'], $attr['tabletPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['paddingRightTablet'], $attr['tabletPaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['paddingTopTablet'], $attr['tabletPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['paddingBottomTablet'], $attr['tabletPaddingUnit'] ),
	),
);

$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);

$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'head', ' .uagb-rating-title, .uagb_review_entry', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'subHead', ' .uagb-rating-desc, .uagb-rating-author', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'content', ' .uagb_review_summary, .uagb_review_block .uagb_review_summary_title', $combined_selectors );

return UAGB_Helper::generate_all_css( $combined_selectors, ' .uagb-block-' . substr( $attr['block_id'], 0, 8 ) );
