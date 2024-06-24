<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$selectors   = UAGB_Block_Helper::get_icon_list_child_selectors( $attr, $id, true )['desktop'];
$t_selectors = UAGB_Block_Helper::get_icon_list_child_selectors( $attr, $id, true )['tablet'];
$m_selectors = UAGB_Block_Helper::get_icon_list_child_selectors( $attr, $id, true )['mobile'];

$desktop = UAGB_Helper::generate_css( $selectors, '.uagb-block-' . $id );
$tablet  = UAGB_Helper::generate_css( $t_selectors, '.uagb-block-' . $id );
$mobile  = UAGB_Helper::generate_css( $m_selectors, '.uagb-block-' . $id );

$generated_css = array(
	'desktop' => $desktop,
	'tablet'  => $tablet,
	'mobile'  => $mobile,
);

return $generated_css;
