<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$t_selectors = array();
$m_selectors = array();
$selectors   = array();

$selectors = array(
	' .uagb-google-map__iframe' => array(
		'height' => UAGB_Helper::get_css_value( $attr['height'], 'px' ),
	),
);

$m_selectors = array(
	' .uagb-google-map__iframe' => array(
		'height' => UAGB_Helper::get_css_value( $attr['heightMobile'], 'px' ),
	),
);

$t_selectors = array(
	' .uagb-google-map__iframe' => array(
		'height' => UAGB_Helper::get_css_value( $attr['heightTablet'], 'px' ),
	),
);

$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);

return UAGB_Helper::generate_all_css( $combined_selectors, ' .uagb-block-' . $id );
