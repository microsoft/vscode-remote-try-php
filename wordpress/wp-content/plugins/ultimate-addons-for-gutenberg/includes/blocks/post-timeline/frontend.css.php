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
UAGB_Block_JS::blocks_post_timeline_gfont( $attr );

$horizontal_space        = $attr['horizontalSpace'];
$horizontal_space_tablet = '' !== $attr['horizontalSpaceTablet'] ? $attr['horizontalSpaceTablet'] : $horizontal_space;
$horizontal_space_mobile = '' !== $attr['horizontalSpaceMobile'] ? $attr['horizontalSpaceMobile'] : $horizontal_space_tablet;

$t_selectors = array();

$left_margin  = isset( $attr['leftMargin'] ) ? $attr['leftMargin'] : $attr['horizontalSpace'];
$right_margin = isset( $attr['rightMargin'] ) ? $attr['rightMargin'] : $attr['horizontalSpace'];

$selectors = array(
	' .uagb-timeline__heading'                            => array(
		'text-align' => $attr['align'],
	),
	' .uagb-timeline__link_parent'                        => array(
		'text-align' => $attr['align'],
	),
	' .uagb-timeline__image a'                            => array(
		'text-align' => $attr['align'],
	),
	' a.uagb-timeline__image'                             => array(
		'text-align' => $attr['align'],
	),
	' .uagb-timeline__author-link'                        => array(
		'color'      => $attr['authorColor'],
		'text-align' => $attr['align'],
	),
	' .uagb-timeline__link'                               => array(
		'text-align'    => $attr['align'],
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['ctaBottomSpacing'], 'px' ),
	),
	' .dashicons-admin-users'                             => array(
		'color'       => $attr['authorColor'],
		'font-size'   => UAGB_Helper::get_css_value( $attr['authorFontSize'], $attr['authorFontSizeType'] ),
		'font-weight' => $attr['authorFontWeight'],
		'line-height' => UAGB_Helper::get_css_value( $attr['authorLineHeight'], $attr['authorLineHeightType'] ),
	),
	' .uagb-timeline__heading a'                          => array(
		'text-align' => $attr['align'],
		'color'      => $attr['headingColor'],
	),
	' .uagb-timeline__heading-text'                       => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['headSpace'], 'px' ),
		'margin-top'    => UAGB_Helper::get_css_value( $attr['headTopSpacing'], 'px' ),
	),
	'.uagb_timeline__cta-enable .uagb-timeline-desc-content' => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['contentSpace'], 'px' ),
		'margin-top'    => UAGB_Helper::get_css_value( $attr['authorSpace'], 'px' ),
	),
	' .uagb-timeline__author-link + .uagb-timeline__link_parent' => array(
		'margin-top' => UAGB_Helper::get_css_value( $attr['authorSpace'], 'px' ),
	),
	'.uagb-timeline__center-block .uagb-timeline__marker' => array(
		'margin-left'  => UAGB_Helper::get_css_value( $horizontal_space, $attr['horizontalSpaceUnit'] ),
		'margin-right' => UAGB_Helper::get_css_value( $horizontal_space, $attr['horizontalSpaceUnit'] ),
	),
	' .uagb-timeline__field:not(:last-child)'             => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['verticalSpace'], $attr['verticalSpaceUnit'] ),
	),
);

$desktop_selectors = UAGB_Block_Helper::get_timeline_selectors( $attr );
$selectors         = array_merge( $selectors, $desktop_selectors );

$t_selectors = array(
	' .dashicons-admin-users'                              => array(
		'font-size'   => UAGB_Helper::get_css_value( $attr['authorFontSizeTablet'], $attr['authorFontSizeType'] ),
		'line-height' => UAGB_Helper::get_css_value( $attr['authorLineHeightTablet'], $attr['authorLineHeightType'] ),
	),
	' .uagb-timeline__link'                                => array(
		'text-align'    => $attr['alignTablet'],
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['ctaBottomSpacingTablet'], 'px' ),
	),
	' .uagb-timeline__heading-text'                        => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['headSpaceTablet'], 'px' ),
		'margin-top'    => UAGB_Helper::get_css_value( $attr['headTopSpacingTablet'], 'px' ),
	),
	'.uagb_timeline__cta-enable .uagb-timeline-desc-content' => array(
		'margin-top' => UAGB_Helper::get_css_value( $attr['authorSpaceTablet'], 'px' ),
	),
	' .uagb-timeline__author-link + .uagb-timeline__link_parent' => array(
		'margin-top' => UAGB_Helper::get_css_value( $attr['authorSpaceTablet'], 'px' ),
	),
	'.uagb-timeline__center-block .uagb-timeline__marker'  => array(
		'margin-left'  => UAGB_Helper::get_css_value( $horizontal_space_tablet, $attr['horizontalSpaceUnitTablet'] ),
		'margin-right' => UAGB_Helper::get_css_value( $horizontal_space_tablet, $attr['horizontalSpaceUnitTablet'] ),
	),
	' .uagb-timeline__day-new .uagb-timeline__inner-date-new' => array(
		'text-align' => $attr['alignTablet'],
	),
	' .uagb-timeline__right .uagb-timeline__day-new'       => array(
		'text-align' => $attr['alignTablet'],
	),
	' .uagb-timeline__day-new.uagb-timeline__events-inner-new' => array(
		'text-align' => $attr['alignTablet'],
	),
	' .uagb-timeline__day-new .uagb-timeline__link_parent' => array(
		'text-align' => $attr['alignTablet'],
	),
	' .uagb-timeline__day-new .uagb-timeline__image a'     => array(
		'text-align' => $attr['alignTablet'],
	),
	' .uagb-timeline__day-new a.uagb-timeline__image'      => array(
		'text-align' => $attr['alignTablet'],
	),
	' .uagb-timeline__day-new .uagb-timeline__author-link' => array(
		'text-align' => $attr['alignTablet'],
	),
	' .uagb-timeline__day-new .uagb-timeline__heading a'   => array(
		'text-align' => $attr['alignTablet'],
	),
	' .uagb-timeline__day-new .uagb-timeline__heading'     => array(
		'text-align' => $attr['alignTablet'],
	),
	' .uagb-timeline-desc-content'                         => array(
		'text-align' => $attr['alignTablet'],
	),
	' .uagb-timeline__field:not(:last-child)'              => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['verticalSpaceTablet'], $attr['verticalSpaceUnitTablet'] ),
	),
);

$tablet_selectors = UAGB_Block_Helper::get_timeline_tablet_selectors( $attr );
$t_selectors      = array_merge( $t_selectors, $tablet_selectors );

// Mobile responsive CSS.
$m_selectors = array(
	' .dashicons-admin-users'                              => array(
		'font-size'   => UAGB_Helper::get_css_value( $attr['authorFontSizeMobile'], $attr['authorFontSizeType'] ),
		'line-height' => UAGB_Helper::get_css_value( $attr['authorLineHeightMobile'], $attr['authorLineHeightType'] ),
	),
	' .uagb-timeline__link'                                => array(
		'text-align'    => $attr['alignMobile'],
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['ctaBottomSpacingMobile'], 'px' ),
	),
	' .uagb-timeline__heading-text'                        => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['headSpaceMobile'], 'px' ),
		'margin-top'    => UAGB_Helper::get_css_value( $attr['headTopSpacingMobile'], 'px' ),
	),
	'.uagb_timeline__cta-enable .uagb-timeline-desc-content' => array(
		'margin-top' => UAGB_Helper::get_css_value( $attr['authorSpaceMobile'], 'px' ),
	),
	' .uagb-timeline__author-link + .uagb-timeline__link_parent' => array(
		'margin-top' => UAGB_Helper::get_css_value( $attr['authorSpaceMobile'], 'px' ),
	),
	'.uagb-timeline__center-block .uagb-timeline__marker'  => array(
		'margin-left'  => UAGB_Helper::get_css_value( $horizontal_space_mobile, $attr['horizontalSpaceUnitMobile'] ),
		'margin-right' => UAGB_Helper::get_css_value( $horizontal_space_mobile, $attr['horizontalSpaceUnitTablet'] ),
	),
	' .uagb-timeline__day-new .uagb-timeline__inner-date-new' => array(
		'text-align' => $attr['alignMobile'],
	),
	' .uagb-timeline__right .uagb-timeline__day-new'       => array(
		'text-align' => $attr['alignMobile'],
	),
	' .uagb-timeline__day-new.uagb-timeline__events-inner-new' => array(
		'text-align' => $attr['alignMobile'],
	),
	' .uagb-timeline__day-new .uagb-timeline__link_parent' => array(
		'text-align' => $attr['alignMobile'],
	),
	' .uagb-timeline__day-new .uagb-timeline__image a'     => array(
		'text-align' => $attr['alignMobile'],
	),
	' .uagb-timeline__day-new a.uagb-timeline__image'      => array(
		'text-align' => $attr['alignMobile'],
	),
	' .uagb-timeline__day-new .uagb-timeline__author-link' => array(
		'text-align' => $attr['alignMobile'],
	),
	' .uagb-timeline__day-new .uagb-timeline__heading a'   => array(
		'text-align' => $attr['alignMobile'],
	),
	' .uagb-timeline__day-new .uagb-timeline__heading'     => array(
		'text-align' => $attr['alignMobile'],
	),
	' .uagb-timeline-desc-content'                         => array(
		'text-align' => $attr['alignMobile'],
	),
	' .uagb-timeline__field:not(:last-child)'              => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['verticalSpaceMobile'], $attr['verticalSpaceUnitMobile'] ),
	),
);

if ( ! $attr['inheritFromTheme'] ) { 
	$selectors = array_merge(
		$selectors,
		array(
			' .uagb-timeline__link' => array(
				'color'            => $attr['ctaColor'],
				'background-color' => $attr['ctaBackground'],
				'text-align'       => $attr['align'],
				'margin-bottom'    => UAGB_Helper::get_css_value( $attr['ctaBottomSpacing'], 'px' ),
			),      
		)
	);
}

$mobile_selectors = UAGB_Block_Helper::get_timeline_mobile_selectors( $attr );
$m_selectors      = array_merge( $m_selectors, $mobile_selectors );

$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);
// .uagb-timeline__date-hide.uagb-timeline__inner-date-new
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'date', ' .uagb-timeline__date-hide.uagb-timeline__inner-date-new', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'date', ' .uagb-timeline__date-hide.uagb-timeline__date-inner', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'date', ' .uagb-timeline__date-new', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'subHead', ' .uagb-timeline-desc-content', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'author', ' .uagb-timeline__author-link', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'head', ' .uagb-timeline__heading a', $combined_selectors );

if ( ! $attr['inheritFromTheme'] ) {
	$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'cta', ' .uagb-timeline__link', $combined_selectors );
}

return UAGB_Helper::generate_all_css( $combined_selectors, '.uagb-block-' . $id . '.uagb-timeline__outer-wrap' );
