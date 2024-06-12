<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

// Adds Fonts.
UAGB_Block_JS::blocks_buttons_gfont( $attr );

$all_selectors = UAGB_Block_Helper::get_buttons_child_selectors( $attr, $id, true );

$combined_selectors = array(
	'desktop' => $all_selectors['selectors'],
	'tablet'  => $all_selectors['t_selectors'],
	'mobile'  => $all_selectors['m_selectors'],
);
if ( ! $attr['inheritFromTheme'] ) {
	$combined_selectors = UAGB_Helper::get_typography_css( $attr, '', ' .uagb-button__link', $combined_selectors );
}

return UAGB_Helper::generate_all_css(
	$combined_selectors,
	'.wp-block-uagb-buttons .uagb-block-' . $id,
	isset( $gbs_class ) ? $gbs_class : ''
);
