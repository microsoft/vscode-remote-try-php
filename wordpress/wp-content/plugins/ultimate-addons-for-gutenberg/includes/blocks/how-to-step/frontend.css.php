<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

// Adds Fonts.
UAGB_Block_JS::blocks_how_to_step_gfont( $attr );

$t_selectors = array();
$m_selectors = array();
$selectors   = array(
	' .uagb-step-link-text'          => array(
		'color' => $attr['urlColor'],
	),
	' .uagb-how-to-step-name'        => array(
		'color' => $attr['titleColor'],
	),
	' .uagb-how-to-step-description' => array(
		'color' => $attr['descriptionColor'],
	),
);

$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);

$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'url', ' .uagb-step-link-text', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'title', ' .uagb-how-to-step-name', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'description', ' .uagb-how-to-step-description', $combined_selectors );

return UAGB_Helper::generate_all_css( $combined_selectors, ' .uagb-block-' . $id );
