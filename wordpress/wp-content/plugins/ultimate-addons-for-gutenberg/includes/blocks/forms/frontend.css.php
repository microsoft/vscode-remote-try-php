<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

/**
 * Adding this comment to avoid PHPStan errors of undefined variable as these variables are defined elsewhere.
 *
 * @var mixed[] $attr
 */

// Adds Fonts.
UAGB_Block_JS::blocks_forms_gfont( $attr );

$selectors   = array();
$m_selectors = array();
$t_selectors = array();

$btnPaddingTop    = isset( $attr['paddingBtnTop'] ) ? $attr['paddingBtnTop'] : $attr['vPaddingSubmit'];
$btnPaddingBottom = isset( $attr['paddingBtnBottom'] ) ? $attr['paddingBtnBottom'] : $attr['vPaddingSubmit'];
$btnPaddingLeft   = isset( $attr['paddingBtnLeft'] ) ? $attr['paddingBtnLeft'] : $attr['hPaddingSubmit'];
$btnPaddingRight  = isset( $attr['paddingBtnRight'] ) ? $attr['paddingBtnRight'] : $attr['hPaddingSubmit'];

$paddingFieldTop    = isset( $attr['paddingFieldTop'] ) ? $attr['paddingFieldTop'] : $attr['vPaddingField'];
$paddingFieldBottom = isset( $attr['paddingFieldBottom'] ) ? $attr['paddingFieldBottom'] : $attr['vPaddingField'];
$paddingFieldLeft   = isset( $attr['paddingFieldLeft'] ) ? $attr['paddingFieldLeft'] : $attr['hPaddingField'];
$paddingFieldRight  = isset( $attr['paddingFieldRight'] ) ? $attr['paddingFieldRight'] : $attr['hPaddingField'];

$toggle_size_number_tablet = is_numeric( $attr['toggleSizeTablet'] ) ? $attr['toggleSizeTablet'] : $attr['toggleSize'];
$toggle_size_number_mobile = is_numeric( $attr['toggleSizeMobile'] ) ? $attr['toggleSizeMobile'] : $toggle_size_number_tablet;

$toggle_width_size_number_tablet = is_numeric( $attr['toggleWidthSizeTablet'] ) ? $attr['toggleWidthSizeTablet'] : $attr['toggleWidthSize'];
$toggle_width_size_number_mobile = is_numeric( $attr['toggleWidthSizeMobile'] ) ? $attr['toggleWidthSizeMobile'] : $toggle_width_size_number_tablet;

$input_overall_border        = UAGB_Block_Helper::uag_generate_border_css( $attr, 'field' );
$input_overall_border        = UAGB_Block_Helper::uag_generate_deprecated_border_css(
	$input_overall_border,
	( isset( $attr['inputborderWidth'] ) ? $attr['inputborderWidth'] : '' ),
	( isset( $attr['inputborderRadius'] ) ? $attr['inputborderRadius'] : '' ),
	( isset( $attr['inputborderColor'] ) ? $attr['inputborderColor'] : '' ),
	( isset( $attr['inputborderStyle'] ) ? $attr['inputborderStyle'] : '' )
);
$input_overall_border_tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'field', 'tablet' );
$input_overall_border_mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'field', 'mobile' );
$input_underline_border      = ( isset( $attr['fieldBorderBottomWidth'] ) ? UAGB_Helper::get_css_value( $attr['fieldBorderBottomWidth'], 'px' ) : '' );

$success_message_border        = UAGB_Block_Helper::uag_generate_border_css( $attr, 'successMsg' );
$success_message_border        = UAGB_Block_Helper::uag_generate_deprecated_border_css(
	$success_message_border,
	( isset( $attr['successMessageBorderWidth'] ) ? $attr['successMessageBorderWidth'] : '' ),
	( isset( $attr['successMessageBorderRadius'] ) ? $attr['successMessageBorderRadius'] : '' ),
	( isset( $attr['successMessageBorderColor'] ) ? $attr['successMessageBorderColor'] : '' ),
	( isset( $attr['successMessageBorderStyle'] ) ? $attr['successMessageBorderStyle'] : '' )
);
$success_message_border_tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'successMsg', 'tablet' );
$success_message_border_mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'successMsg', 'mobile' );

$failed_message_border        = UAGB_Block_Helper::uag_generate_border_css( $attr, 'errorMsg' );
$failed_message_border        = UAGB_Block_Helper::uag_generate_deprecated_border_css(
	$failed_message_border,
	( isset( $attr['failedMessageBorderWidth'] ) ? $attr['failedMessageBorderWidth'] : '' ),
	( isset( $attr['failedMessageBorderRadius'] ) ? $attr['failedMessageBorderRadius'] : '' ),
	( isset( $attr['failedMessageBorderColor'] ) ? $attr['failedMessageBorderColor'] : '' ),
	( isset( $attr['failedMessageBorderStyle'] ) ? $attr['failedMessageBorderStyle'] : '' )
);
$failed_message_border_tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'errorMsg', 'tablet' );
$failed_message_border_mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'errorMsg', 'mobile' );

$toggle_border        = UAGB_Block_Helper::uag_generate_border_css( $attr, 'checkBoxToggle' );
$toggle_border        = UAGB_Block_Helper::uag_generate_deprecated_border_css(
	$toggle_border,
	( isset( $attr['inputborderWidth'] ) ? $attr['inputborderWidth'] : '' ),
	( isset( $attr['inputborderRadius'] ) ? $attr['inputborderRadius'] : '' ),
	( isset( $attr['inputborderColor'] ) ? $attr['inputborderColor'] : '' ),
	( isset( $attr['inputborderStyle'] ) ? $attr['inputborderStyle'] : '' )
);
$toggle_border_tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'checkBoxToggle', 'tablet' );
$toggle_border_mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'checkBoxToggle', 'mobile' );

// Individual Toggle Border Width Fallback for Math Calculations.
$toggle_border_top_tablet_fallback    = isset( $toggle_border_tablet['border-top-width'] ) ? ( ! empty( $toggle_border_tablet['border-top-width'] ) ? $toggle_border_tablet['border-top-width'] : $toggle_border['border-top-width'] ) : $toggle_border['border-top-width'];
$toggle_border_left_tablet_fallback   = isset( $toggle_border_tablet['border-left-width'] ) ? ( ! empty( $toggle_border_tablet['border-left-width'] ) ? $toggle_border_tablet['border-left-width'] : $toggle_border['border-left-width'] ) : $toggle_border['border-left-width'];
$toggle_border_right_tablet_fallback  = isset( $toggle_border_tablet['border-right-width'] ) ? ( ! empty( $toggle_border_tablet['border-right-width'] ) ? $toggle_border_tablet['border-right-width'] : $toggle_border['border-right-width'] ) : $toggle_border['border-right-width'];
$toggle_border_bottom_tablet_fallback = isset( $toggle_border_tablet['border-bottom-width'] ) ? ( ! empty( $toggle_border_tablet['border-bottom-width'] ) ? $toggle_border_tablet['border-bottom-width'] : $toggle_border['border-bottom-width'] ) : $toggle_border['border-bottom-width'];
$toggle_border_top_mobile_fallback    = isset( $toggle_border_mobile['border-top-width'] ) ? ( ! empty( $toggle_border_mobile['border-top-width'] ) ? $toggle_border_mobile['border-top-width'] : $toggle_border_top_tablet_fallback ) : $toggle_border_top_tablet_fallback;
$toggle_border_left_mobile_fallback   = isset( $toggle_border_mobile['border-left-width'] ) ? ( ! empty( $toggle_border_mobile['border-left-width'] ) ? $toggle_border_mobile['border-left-width'] : $toggle_border_left_tablet_fallback ) : $toggle_border_left_tablet_fallback;
$toggle_border_right_mobile_fallback  = isset( $toggle_border_mobile['border-right-width'] ) ? ( ! empty( $toggle_border_mobile['border-right-width'] ) ? $toggle_border_mobile['border-right-width'] : $toggle_border_right_tablet_fallback ) : $toggle_border_right_tablet_fallback;
$toggle_border_bottom_mobile_fallback = isset( $toggle_border_mobile['border-bottom-width'] ) ? ( ! empty( $toggle_border_mobile['border-bottom-width'] ) ? $toggle_border_mobile['border-bottom-width'] : $toggle_border_bottom_tablet_fallback ) : $toggle_border_bottom_tablet_fallback;

// Individual Toggle Border Radius Fallback for Inner Dot.
$toggle_border_radius_tl_tablet_fallback = isset( $toggle_border_tablet['border-top-left-radius'] ) ? ( ! empty( $toggle_border_tablet['border-top-left-radius'] ) ? $toggle_border_tablet['border-top-left-radius'] : $toggle_border['border-top-left-radius'] ) : $toggle_border['border-top-left-radius'];
$toggle_border_radius_tr_tablet_fallback = isset( $toggle_border_tablet['border-top-right-radius'] ) ? ( ! empty( $toggle_border_tablet['border-top-right-radius'] ) ? $toggle_border_tablet['border-top-right-radius'] : $toggle_border['border-top-right-radius'] ) : $toggle_border['border-top-right-radius'];
$toggle_border_radius_bl_tablet_fallback = isset( $toggle_border_tablet['border-bottom-left-radius'] ) ? ( ! empty( $toggle_border_tablet['border-bottom-left-radius'] ) ? $toggle_border_tablet['border-bottom-left-radius'] : $toggle_border['border-bottom-left-radius'] ) : $toggle_border['border-bottom-left-radius'];
$toggle_border_radius_br_tablet_fallback = isset( $toggle_border_tablet['border-bottom-right-radius'] ) ? ( ! empty( $toggle_border_tablet['border-bottom-right-radius'] ) ? $toggle_border_tablet['border-bottom-right-radius'] : $toggle_border['border-bottom-right-radius'] ) : $toggle_border['border-bottom-right-radius'];
$toggle_border_radius_tl_mobile_fallback = isset( $toggle_border_mobile['border-top-left-radius'] ) ? ( ! empty( $toggle_border_mobile['border-top-left-radius'] ) ? $toggle_border_mobile['border-top-left-radius'] : $toggle_border_radius_tl_tablet_fallback ) : $toggle_border_radius_tl_tablet_fallback;
$toggle_border_radius_tr_mobile_fallback = isset( $toggle_border_mobile['border-top-right-radius'] ) ? ( ! empty( $toggle_border_mobile['border-top-right-radius'] ) ? $toggle_border_mobile['border-top-right-radius'] : $toggle_border_radius_tr_tablet_fallback ) : $toggle_border_radius_tr_tablet_fallback;
$toggle_border_radius_bl_mobile_fallback = isset( $toggle_border_mobile['border-bottom-left-radius'] ) ? ( ! empty( $toggle_border_mobile['border-bottom-left-radius'] ) ? $toggle_border_mobile['border-bottom-left-radius'] : $toggle_border_radius_bl_tablet_fallback ) : $toggle_border_radius_bl_tablet_fallback;
$toggle_border_radius_br_mobile_fallback = isset( $toggle_border_mobile['border-bottom-right-radius'] ) ? ( ! empty( $toggle_border_mobile['border-bottom-right-radius'] ) ? $toggle_border_mobile['border-bottom-right-radius'] : $toggle_border_radius_br_tablet_fallback ) : $toggle_border_radius_br_tablet_fallback;

$btn_border        = UAGB_Block_Helper::uag_generate_border_css( $attr, 'btn' );
$btn_border        = UAGB_Block_Helper::uag_generate_deprecated_border_css(
	$btn_border,
	( isset( $attr['submitborderWidth'] ) ? $attr['submitborderWidth'] : '' ),
	( isset( $attr['submitborderRadius'] ) ? $attr['submitborderRadius'] : '' ),
	( isset( $attr['submitborderColor'] ) ? $attr['submitborderColor'] : '' ),
	( isset( $attr['submitborderStyle'] ) ? $attr['submitborderStyle'] : '' )
);
$btn_border_Tablet = UAGB_Block_Helper::uag_generate_border_css( $attr, 'btn', 'tablet' );
$btn_border_Mobile = UAGB_Block_Helper::uag_generate_border_css( $attr, 'btn', 'mobile' );

// fallback for forms select field.
$forms_padding_right_mobile_fallback = (int) $attr['paddingFieldRightMobile'] + 30;
$forms_padding_right_tablet_fallback = (int) $attr['paddingFieldRightTablet'] + 30;

$selectors = array(
	'.uagb-forms__outer-wrap'                              => array(
		'padding-top'    => UAGB_Helper::get_css_value( $attr['formPaddingTop'], $attr['formPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['formPaddingRight'], $attr['formPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['formPaddingBottom'], $attr['formPaddingUnit'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['formPaddingLeft'], $attr['formPaddingUnit'] ),
	),
	' .uagb-forms-main-form textarea'                      => array(
		'text-align' => $attr['overallAlignment'],
	),
	' .uagb-forms-input'                                   => array(
		'text-align' => $attr['overallAlignment'],
	),
	' .uagb-forms-input-label'                             => array(
		'display'    => $attr['displayLabels'] ? 'block' : 'none',
		'text-align' => null === $attr['labelAlignment'] ? $attr['overallAlignment'] : $attr['labelAlignment'],
	),
	' .uagb-forms-main-form .uagb-forms-field-set'         => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['fieldGap'], $attr['fieldGapType'] ),
	),
	' .uagb-forms-main-form .uagb-forms-input-label'       => array(
		'color'         => $attr['labelColor'],
		'font-size'     => UAGB_Helper::get_css_value( $attr['labelFontSize'], $attr['labelFontSizeType'] ),
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['labelGap'], $attr['labelGapUnit'] ),
	),
	' .uagb-forms-success-message'                         => array_merge(
		array(
			'background-color' => $attr['successMessageBGColor'],
			'color'            => $attr['successMessageTextColor'],
		),
		$success_message_border
	),
	' .uagb-forms-success-message:hover'                   => array(
		'border-color' => $attr['successMsgBorderHColor'],
	),
	' .uagb-forms-failed-message'                          => array_merge(
		array(
			'background-color' => $attr['failedMessageBGColor'],
			'color'            => $attr['failedMessageTextColor'],
		),
		$failed_message_border
	),
	' .uagb-forms-failed-message:hover'                    => array(
		'border-color' => $attr['errorMsgBorderHColor'],
	),
	' .uagb-forms-main-form .uagb-forms-input:focus'       => array(
		'outline'          => ' none !important',
		'border-color'     => ! empty( $attr['fieldBorderHColor'] ) ? $attr['fieldBorderHColor'] : $attr['inputborderHoverColor'],
		'background-color' => $attr['bgActiveColor'] . ' !important',
	),
	' .uagb-forms-main-form .uagb-forms-input:focus::placeholder' => array(
		'color' => $attr['inputplaceholderActiveColor'] . ' !important',
	),
	// Hover Colors.
	' .uagb-forms-field-set:hover .uagb-forms-input-label' => array(
		'color' => $attr['labelHoverColor'],
	),
	' .uagb-forms-field-set:hover .uagb-forms-input'       => array(
		'background-color' => $attr['bgHoverColor'],
		'border-color'     => ! empty( $attr['btnBorderHColor'] ) ? $attr['btnBorderHColor'] : $attr['submitborderHoverColor'],
	),
	' .uagb-forms-field-set:hover .uagb-forms-input::placeholder' => array(
		'color' => $attr['inputplaceholderHoverColor'],
	),
	' .uagb-slider.round'                                  => array(
		// Important is added to override the usual border radius we set with a completely round one.
		'border-radius' => UAGB_Helper::get_css_value( 20 + $attr['toggleWidthSize'], 'px' ) . ' !important',
	),
	// Drop icon position css.
	// select control color.
	' .uagb-form-phone-country'                            => array(
		'background'          => 'url(data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgd2lkdGg9JzE4cHgnIGhlaWdodD0nMThweCcgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCA1MTIgNTEyIj48cGF0aCBkPSJtMzk2LjYgMTYwIDE5LjQgMjAuN0wyNTYgMzUyIDk2IDE4MC43bDE5LjMtMjAuN0wyNTYgMzEwLjV6IiBmaWxsPSIjMWQyMzI3IiBjbGFzcz0iZmlsbC0wMDAwMDAiPjwvcGF0aD48L3N2Zz4=) no-repeat',
		'-moz-appearance'     => 'none !important',
		'-webkit-appearance'  => ' none !important',
		'background-position' => ' top 50% right ' . UAGB_Helper::get_css_value( $attr['paddingFieldRight'], $attr['paddingFieldUnit'] ),
		'appearance'          => 'none !important',
		'color'               => $attr['inputplaceholderColor'],
	),

	' .uagb-forms-field-set:hover .uagb-form-phone-country' => array(
		'color' => $attr['inputplaceholderHoverColor'],
	),
);

if ( 'full' !== $attr['buttonAlign'] ) {
	$selectors[' .uagb-forms-main-form .uagb-forms-main-submit-button-wrap'] = array(
		'text-align' => $attr['buttonAlign'],
	);
} else {
	$selectors[' .uagb-forms-main-form .uagb-forms-main-submit-button-wrap'] = array(
		'display' => 'grid',
	);
}

$t_selectors = array(
	'.uagb-forms__outer-wrap'                        => array(
		'padding-top'    => UAGB_Helper::get_css_value( $attr['formPaddingTopTab'], $attr['formPaddingUnitTab'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['formPaddingRightTab'], $attr['formPaddingUnitTab'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['formPaddingBottomTab'], $attr['formPaddingUnitTab'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['formPaddingLeftTab'], $attr['formPaddingUnitTab'] ),
	),
	' .uagb-forms-main-form .uagb-forms-field-set'   => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['fieldGapTablet'], $attr['fieldGapType'] ),
	),
	' .uagb-forms-main-form .uagb-forms-input-label' => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['labelGapTablet'], $attr['labelGapUnit'] ),
	),
	' .uagb-slider.round'                            => array(
		// Important is added to override the usual border radius we set with a completely round one.
		'border-radius' => UAGB_Helper::get_css_value( 20 + $toggle_width_size_number_tablet, 'px' ) . ' !important',
	),
	' .uagb-forms-success-message'                   => $success_message_border_tablet,
	' .uagb-forms-failed-message'                    => $failed_message_border_tablet,
	// Drop icon position css.
	' .uagb-form-phone-country'                      => array(
		'background-position' => 'top 50% right ' . UAGB_Helper::get_css_value( $attr['paddingFieldRightTablet'] ? $attr['paddingFieldRightTablet'] : 12, $attr['paddingFieldUnitTablet'] ),
		'padding-right'       => UAGB_Helper::get_css_value( $forms_padding_right_tablet_fallback, $attr['paddingFieldUnitTablet'] ) . ' !important',
	),
	' .uagb-forms-main-form textarea'                => array(
		'text-align' => $attr['overallAlignmentTablet'],
	),
	' .uagb-forms-input'                             => array(
		'text-align' => $attr['overallAlignmentTablet'],
	),
	' .uagb-forms-input-label'                       => array(
		'text-align' => $attr['labelAlignmentTablet'],
	),
);

if ( 'full' !== $attr['buttonAlignTablet'] ) {
	$t_selectors[' .uagb-forms-main-form .uagb-forms-main-submit-button-wrap'] = array(
		'text-align' => $attr['buttonAlignTablet'],
	);
} else {
	$t_selectors[' .uagb-forms-main-form .uagb-forms-main-submit-button-wrap'] = array(
		'display' => 'grid',
	);
}

$m_selectors = array(
	'.uagb-forms__outer-wrap'                        => array(
		'padding-top'    => UAGB_Helper::get_css_value( $attr['formPaddingTopMob'], $attr['formPaddingUnitMob'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['formPaddingRightMob'], $attr['formPaddingUnitMob'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['formPaddingBottomMob'], $attr['formPaddingUnitMob'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $attr['formPaddingLeftMob'], $attr['formPaddingUnitMob'] ),
	),
	' .uagb-forms-main-form .uagb-forms-field-set'   => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['fieldGapMobile'], $attr['fieldGapType'] ),
	),
	' .uagb-forms-main-form .uagb-forms-input-label' => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['labelGapMobile'], $attr['labelGapUnit'] ),
	),
	' .uagb-slider.round'                            => array(
		// Important is added to override the usual border radius we set with a completely round one.
		'border-radius' => UAGB_Helper::get_css_value( 20 + $toggle_width_size_number_mobile, 'px' ) . ' !important',
	),
	' .uagb-forms-success-message'                   => $success_message_border_mobile,
	' .uagb-forms-failed-message'                    => $failed_message_border_mobile,

	// Drop icon position css.
	' .uagb-form-phone-country'                      => array(
		'background-position' => 'top 50% right ' . UAGB_Helper::get_css_value( $attr['paddingFieldRightMobile'] ? $attr['paddingFieldRightMobile'] : 6, $attr['paddingFieldUnitmobile'] ),
		'padding-right'       => UAGB_Helper::get_css_value( $forms_padding_right_mobile_fallback, $attr['paddingFieldUnitmobile'] ) . ' !important',
	),
	' .uagb-forms-main-form textarea'                => array(
		'text-align' => $attr['overallAlignmentMobile'],
	),
	' .uagb-forms-input'                             => array(
		'text-align' => $attr['overallAlignmentMobile'],
	),
	' .uagb-forms-input-label'                       => array(
		'text-align' => $attr['labelAlignmentMobile'],
	),
);
if ( 'full' !== $attr['buttonAlignMobile'] ) {
	$m_selectors[' .uagb-forms-main-form .uagb-forms-main-submit-button-wrap'] = array(
		'text-align' => $attr['buttonAlignMobile'],
	);
} else {
	$m_selectors[' .uagb-forms-main-form .uagb-forms-main-submit-button-wrap'] = array(
		'display' => 'grid',
	);
}
// Checkbox Field css.
$selectors[' .uagb-forms-checkbox-wrap input[type=checkbox] + label:before'] = array(
	'background-color' => $attr['toggleColor'],
	'width'            => UAGB_Helper::get_css_value( $attr['toggleSize'], $attr['toggleSizeType'] ),
	'height'           => UAGB_Helper::get_css_value( $attr['toggleSize'], $attr['toggleSizeType'] ),
);
$selectors[' .uagb-forms-checkbox-wrap > label']                             = array(
	'color' => $attr['inputColor'],
);

// Radio Button Field css.
$selectors[' .uagb-forms-radio-wrap input[type=radio] + label:before'] = array(
	'background-color' => $attr['toggleColor'],
	'width'            => UAGB_Helper::get_css_value( $attr['toggleSize'], $attr['toggleSizeType'] ),
	'height'           => UAGB_Helper::get_css_value( $attr['toggleSize'], $attr['toggleSizeType'] ),
);
$selectors[' .uagb-forms-radio-wrap > label']                          = array(
	'color' => $attr['inputColor'],
);

// Toggle Field css.
$selectors[' .uagb-slider']                                     = array(
	'background-color' => $attr['toggleColor'],
);
$selectors[' .uagb-forms-main-form .uagb-switch']               = array(
	'height' => 'calc(' . $toggle_border['border-top-width'] . ' + ' . $toggle_border['border-bottom-width'] . ' + ' . UAGB_Helper::get_css_value(
		(int) ( 20 + $attr['toggleWidthSize'] + ( ( 20 + $attr['toggleWidthSize'] ) / 3 ) ),
		'px'
	) . ')',
	'width'  => 'calc(' . $toggle_border['border-left-width'] . ' + ' . $toggle_border['border-right-width'] . ' + ' . UAGB_Helper::get_css_value(
		(int) ( ( ( 20 + $attr['toggleWidthSize'] ) * 2.5 ) + ( ( 20 + $attr['toggleWidthSize'] ) / 3 ) ),
		'px'
	) . ')',
);
$selectors[' .uagb-forms-main-form .uagb-slider:before']        = array(
	'height'           => UAGB_Helper::get_css_value( 20 + $attr['toggleWidthSize'], 'px' ),
	'width'            => UAGB_Helper::get_css_value( 20 + $attr['toggleWidthSize'], 'px' ),
	'top'              => UAGB_Helper::get_css_value( (int) ( ( 20 + $attr['toggleWidthSize'] ) / 6 ), 'px' ),
	'bottom'           => UAGB_Helper::get_css_value( (int) ( ( 20 + $attr['toggleWidthSize'] ) / 6 ), 'px' ),
	'left'             => UAGB_Helper::get_css_value( (int) ( ( 20 + $attr['toggleWidthSize'] ) / 6 ), 'px' ),
	'background-color' => $attr['toggleDotColor'],
	'border-radius'    => $toggle_border['border-top-left-radius'] . ' ' . $toggle_border['border-top-right-radius'] . ' ' . $toggle_border['border-bottom-right-radius'] . ' ' . $toggle_border['border-bottom-left-radius'],
);
$selectors[' .uagb-switch input:checked + .uagb-slider']        = array(
	'background-color' => $attr['toggleActiveColor'],
	'border-color'     => ! empty( $attr['checkBoxToggleBorderHColor'] ) ? $attr['checkBoxToggleBorderHColor'] : $attr['inputborderHoverColor'],
);
$selectors[' .uagb-switch input:checked + .uagb-slider:before'] = array(
	'transform'        => 'translateX(' . UAGB_Helper::get_css_value(
		(int) ( ( ( ( 20 + $attr['toggleWidthSize'] ) * 2.5 ) - ( 20 + $attr['toggleWidthSize'] ) ) ),
		'px'
	) . ')',
	'background-color' => $attr['toggleDotActiveColor'],
);
$selectors[' .uagb-switch input:focus + .uagb-slider']          = array(
	'box-shadow' => '0 0 1px' . $attr['toggleActiveColor'],
);

// Accept Field css.
$selectors[' .uagb-forms-accept-wrap input[type=checkbox] + label:before'] = array(
	'background-color' => $attr['toggleColor'],
	'width'            => UAGB_Helper::get_css_value( $attr['toggleSize'], $attr['toggleSizeType'] ),
	'height'           => UAGB_Helper::get_css_value( $attr['toggleSize'], $attr['toggleSizeType'] ),
);
$selectors[' .uagb-forms-accept-wrap > label']                             = array(
	'color' => $attr['inputColor'],
);

if ( 'boxed' === $attr['formStyle'] ) {
	$selectors[' .uagb-forms-main-form  .uagb-forms-checkbox-wrap input[type=checkbox] + label:before'] = $toggle_border;
	$selectors[' .uagb-forms-main-form .uagb-forms-checkbox-wrap > input']                              = array(
		'color' => $attr['inputColor'],
	);
	$selectors[' .uagb-forms-main-form  .uagb-forms-radio-wrap input[type=radio] + label:before']       = $toggle_border;
	$selectors[' .uagb-forms-main-form .uagb-forms-radio-wrap > input']                                 = array(
		'color' => $attr['inputColor'],
	);
	$selectors[' .uagb-forms-main-form .uagb-slider'] = $toggle_border;
	$selectors[' .uagb-forms-main-form  .uagb-forms-accept-wrap input[type=checkbox] + label:before'] = $toggle_border;
	$selectors[' .uagb-forms-main-form .uagb-forms-accept-wrap > input']                              = array(
		'color' => $attr['inputColor'],
	);
	$selectors[' .uagb-forms-main-form .uagb-forms-input']                         = array_merge(
		array(
			'background-color' => $attr['bgColor'],
			'color'            => $attr['inputColor'],
		),
		$input_overall_border
	);
	$selectors[' .uagb-forms-main-form .uagb-forms-input.uagb-form-phone-country'] = array(
		'padding-top'    => UAGB_Helper::get_css_value( ( $paddingFieldTop - 1 ), $attr['paddingFieldUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( ( $paddingFieldBottom - 1 ), $attr['paddingFieldUnit'] ),
		'padding-left'   => UAGB_Helper::get_css_value( $paddingFieldLeft, $attr['paddingFieldUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $paddingFieldRight, $attr['paddingFieldUnit'] ),
	);

	$selectors[' .uagb-forms-input:hover']        = array(
		'border-color' => ! empty( $attr['fieldBorderHColor'] ) ? $attr['fieldBorderHColor'] : $attr['inputborderHoverColor'],
	);
	$selectors[' .uagb-forms-input::placeholder'] = array(
		'color' => $attr['inputplaceholderColor'],
	);

	$t_selectors[' .uagb-forms-main-form  .uagb-forms-checkbox-wrap input[type=checkbox] + label:before'] = $toggle_border_tablet;
	$t_selectors[' .uagb-forms-main-form  .uagb-forms-radio-wrap input[type=radio] + label:before']       = $toggle_border_tablet;
	$t_selectors[' .uagb-forms-main-form .uagb-slider'] = $toggle_border_tablet;
	$t_selectors[' .uagb-forms-main-form  .uagb-forms-accept-wrap input[type=checkbox] + label:before'] = $toggle_border_tablet;
	$t_selectors[' .uagb-forms-main-form .uagb-forms-input'] = $input_overall_border_tablet;

	$m_selectors[' .uagb-forms-main-form  .uagb-forms-checkbox-wrap input[type=checkbox] + label:before'] = $toggle_border_mobile;
	$m_selectors[' .uagb-forms-main-form  .uagb-forms-radio-wrap input[type=radio] + label:before']       = $toggle_border_mobile;
	$m_selectors[' .uagb-forms-main-form .uagb-slider'] = $toggle_border_mobile;
	$m_selectors[' .uagb-forms-main-form  .uagb-forms-accept-wrap input[type=checkbox] + label:before'] = $toggle_border_mobile;
	$m_selectors[' .uagb-forms-main-form .uagb-forms-input'] = $input_overall_border_mobile;
}

$selectors[' .uagb-forms-main-form  .uagb-forms-input']   = array(
	'padding-top'    => UAGB_Helper::get_css_value( $paddingFieldTop, $attr['paddingFieldUnit'] ),
	'padding-bottom' => UAGB_Helper::get_css_value( $paddingFieldBottom, $attr['paddingFieldUnit'] ),
	'padding-left'   => UAGB_Helper::get_css_value( $paddingFieldLeft, $attr['paddingFieldUnit'] ),
	'padding-right'  => UAGB_Helper::get_css_value( $paddingFieldRight, $attr['paddingFieldUnit'] ),
);
$t_selectors[' .uagb-forms-main-form  .uagb-forms-input'] = array(
	'padding-top'    => UAGB_Helper::get_css_value( $attr['paddingFieldTopTablet'], $attr['paddingFieldUnitTablet'] ),
	'padding-bottom' => UAGB_Helper::get_css_value( $attr['paddingFieldBottomTablet'], $attr['paddingFieldUnitTablet'] ),
	'padding-left'   => UAGB_Helper::get_css_value( $attr['paddingFieldLeftTablet'], $attr['paddingFieldUnitTablet'] ),
	'padding-right'  => UAGB_Helper::get_css_value( $attr['paddingFieldRightTablet'], $attr['paddingFieldUnitTablet'] ),

);
$t_selectors[' .uagb-forms-main-form  .uagb-forms-input.uagb-form-phone-country'] = array(
	'padding-top'    => UAGB_Helper::get_css_value( $attr['paddingFieldTopTablet'], $attr['paddingFieldUnitTablet'] ),
	'padding-bottom' => UAGB_Helper::get_css_value( $attr['paddingFieldBottomTablet'], $attr['paddingFieldUnitTablet'] ),
	'padding-left'   => UAGB_Helper::get_css_value( $attr['paddingFieldLeftTablet'], $attr['paddingFieldUnitTablet'] ),
	'padding-right'  => UAGB_Helper::get_css_value( $attr['paddingFieldRightTablet'], $attr['paddingFieldUnitTablet'] ),

);
$t_selectors[' .uagb-switch input:checked + .uagb-slider:before']                 = array(
	'transform' => 'translateX(' . UAGB_Helper::get_css_value(
		(int) ( ( ( ( 20 + $toggle_width_size_number_tablet ) * 2.5 ) - ( 20 + $toggle_width_size_number_tablet ) ) ),
		'px'
	) . ')',
);
$m_selectors[' .uagb-forms-main-form  .uagb-forms-input.uagb-form-phone-country'] = array(
	'padding-top'    => UAGB_Helper::get_css_value( $attr['paddingFieldTopMobile'], $attr['paddingFieldUnitmobile'] ),
	'padding-bottom' => UAGB_Helper::get_css_value( $attr['paddingFieldBottomMobile'], $attr['paddingFieldUnitmobile'] ),
	'padding-left'   => UAGB_Helper::get_css_value( $attr['paddingFieldLeftMobile'], $attr['paddingFieldUnitmobile'] ),
	'padding-right'  => UAGB_Helper::get_css_value( $attr['paddingFieldRightMobile'], $attr['paddingFieldUnitmobile'] ),

);
$m_selectors[' .uagb-forms-main-form  .uagb-forms-input']         = array(
	'padding-top'    => UAGB_Helper::get_css_value( $attr['paddingFieldTopMobile'], $attr['paddingFieldUnitmobile'] ),
	'padding-bottom' => UAGB_Helper::get_css_value( $attr['paddingFieldBottomMobile'], $attr['paddingFieldUnitmobile'] ),
	'padding-left'   => UAGB_Helper::get_css_value( $attr['paddingFieldLeftMobile'], $attr['paddingFieldUnitmobile'] ),
	'padding-right'  => UAGB_Helper::get_css_value( $attr['paddingFieldRightMobile'], $attr['paddingFieldUnitmobile'] ),
);
$m_selectors[' .uagb-switch input:checked + .uagb-slider:before'] = array(
	'transform' => 'translateX(' . UAGB_Helper::get_css_value(
		(int) ( ( ( ( 20 + $toggle_width_size_number_mobile ) * 2.5 ) - ( 20 + $toggle_width_size_number_mobile ) ) ),
		'px'
	) . ')',
);
if ( 'underlined' === $attr['formStyle'] ) {
	$selectors[' .uagb-forms-main-form  .uagb-forms-accept-wrap input[type=checkbox] + label:before']   = array(
		'border-bottom' => UAGB_Helper::get_css_value( $attr['checkBoxToggleBorderBottomWidth'], 'px' ) . ' ' . $attr['checkBoxToggleBorderStyle'] . ' ' . $attr['checkBoxToggleBorderColor'],
	);
	$selectors[' .uagb-forms-main-form  .uagb-forms-checkbox-wrap input[type=checkbox] + label:before'] = array(
		'border-bottom' => UAGB_Helper::get_css_value( $attr['checkBoxToggleBorderBottomWidth'], 'px' ) . ' ' . $attr['checkBoxToggleBorderStyle'] . ' ' . $attr['checkBoxToggleBorderColor'],
	);
	$selectors[' .uagb-forms-main-form .uagb-slider'] = array(
		'border-bottom' => UAGB_Helper::get_css_value( $attr['checkBoxToggleBorderBottomWidth'], 'px' ) . ' ' . $attr['checkBoxToggleBorderStyle'] . ' ' . $attr['checkBoxToggleBorderColor'],
	);
	$selectors[' .uagb-forms-main-form  .uagb-forms-radio-wrap input[type=radio] + label:before'] = array(
		'border-bottom' => UAGB_Helper::get_css_value( $attr['checkBoxToggleBorderBottomWidth'], 'px' ) . ' ' . $attr['checkBoxToggleBorderStyle'] . ' ' . $attr['checkBoxToggleBorderColor'],
	);
	$selectors[' .uagb-forms-main-form  .uagb-forms-input']                                       = array_merge(
		array(
			'border-top'     => 0,
			'border-left'    => 0,
			'border-right'   => 0,
			'outline'        => 0,
			'border-radius'  => 0,
			'background'     => 'transparent',
			'border-bottom'  => UAGB_Helper::get_css_value( $attr['fieldBorderBottomWidth'], 'px' ) . ' ' . $attr['fieldBorderStyle'] . ' ' . $attr['fieldBorderColor'],
			'color'          => $attr['inputColor'],
			'padding-top'    => UAGB_Helper::get_css_value( $paddingFieldTop, $attr['paddingFieldUnit'] ),
			'padding-bottom' => UAGB_Helper::get_css_value( $paddingFieldBottom, $attr['paddingFieldUnit'] ),
			'padding-left'   => UAGB_Helper::get_css_value( $paddingFieldLeft, $attr['paddingFieldUnit'] ),
			'padding-right'  => UAGB_Helper::get_css_value( $paddingFieldRight, $attr['paddingFieldUnit'] ),
		),
		$input_overall_border
	);
	$selectors['.uagb-forms__outer-wrap .uagb-forms-main-form  .uagb-forms-input']                = array(
		'border-top-width'    => 0,
		'border-right-width'  => 0,
		'border-left-width'   => 0,
		'border-bottom-width' => $input_underline_border,
	);
	$selectors[' .uagb-forms-input:hover']        = array(
		'border-color' => ! empty( $attr['fieldBorderHColor'] ) ? $attr['fieldBorderHColor'] : $attr['inputborderHoverColor'],
	);
	$selectors[' .uagb-forms-input::placeholder'] = array(
		'color' => $attr['inputplaceholderColor'],
	);
}

// Element Active CSS.
$selectors[' .uagb-forms-checkbox-wrap input[type=checkbox]:checked + label:before'] = array(
	'color'            => $attr['toggleDotActiveColor'],
	'background-color' => $attr['toggleActiveColor'],
	'border-color'     => $attr['checkBoxToggleBorderHColor'],
	'font-size'        => 'calc(' . $attr['toggleSize'] . $attr['toggleSizeType'] . ' / 1.2)',
);
$selectors[' .uagb-forms-radio-wrap input[type=radio]:checked + label:before']       = array(
	'background-color' => $attr['toggleDotActiveColor'],
	'border-color'     => $attr['checkBoxToggleBorderHColor'],
	'box-shadow'       => 'inset 0 0 0 4px ' . $attr['toggleActiveColor'],
	'font-size'        => 'calc(' . $attr['toggleSize'] . 'px / 1.2)',
);
$selectors[' .uagb-forms-accept-wrap input[type=checkbox]:checked + label:before']   = array(
	'color'            => $attr['toggleDotActiveColor'],
	'background-color' => $attr['toggleActiveColor'],
	'border-color'     => $attr['checkBoxToggleBorderHColor'],
	'font-size'        => 'calc(' . $attr['toggleSize'] . $attr['toggleSizeType'] . ' / 1.2)',
);

// Checkbox Field css.
$t_selectors[' .uagb-forms-checkbox-wrap input[type=checkbox]:checked + label:before'] = array(
	'font-size' => 'calc(' . $toggle_size_number_tablet . $attr['toggleSizeType'] . ' / 1.2)',
);
$t_selectors[' .uagb-forms-checkbox-wrap input[type=checkbox] + label:before']         = array(
	'width'  => UAGB_Helper::get_css_value( $attr['toggleSizeTablet'], $attr['toggleSizeType'] ),
	'height' => UAGB_Helper::get_css_value( $attr['toggleSizeTablet'], $attr['toggleSizeType'] ),
);
// Radio Button Field css.
$t_selectors[' .uagb-forms-radio-wrap input[type=radio]:checked + label:before'] = array(
	'font-size' => 'calc(' . $toggle_size_number_tablet . 'px / 1.2)',
);
$t_selectors[' .uagb-forms-radio-wrap input[type=radio] + label:before']         = array(
	'width'  => UAGB_Helper::get_css_value( $attr['toggleSizeTablet'], $attr['toggleSizeType'] ),
	'height' => UAGB_Helper::get_css_value( $attr['toggleSizeTablet'], $attr['toggleSizeType'] ),
);
// Accept Field css.
$t_selectors[' .uagb-forms-accept-wrap input[type=checkbox]:checked + label:before'] = array(
	'font-size' => 'calc(' . $toggle_size_number_tablet . $attr['toggleSizeType'] . ' / 1.2)',
);
$t_selectors[' .uagb-forms-accept-wrap input[type=checkbox] + label:before']         = array(
	'width'  => UAGB_Helper::get_css_value( $attr['toggleSizeTablet'], $attr['toggleSizeType'] ),
	'height' => UAGB_Helper::get_css_value( $attr['toggleSizeTablet'], $attr['toggleSizeType'] ),
);
$t_selectors[' .uagb-forms-main-form .uagb-switch']                                  = array(
	'height' => 'calc(' . $toggle_border_top_tablet_fallback . ' + ' . $toggle_border_bottom_tablet_fallback . ' + ' . UAGB_Helper::get_css_value(
		(int) ( 20 + $toggle_width_size_number_tablet + ( ( 20 + $toggle_width_size_number_tablet ) / 3 ) ),
		'px'
	) . ')',
	'width'  => 'calc(' . $toggle_border_left_tablet_fallback . ' + ' . $toggle_border_right_tablet_fallback . ' + ' . UAGB_Helper::get_css_value(
		(int) ( ( ( 20 + $toggle_width_size_number_tablet ) * 2.5 ) + ( ( 20 + $toggle_width_size_number_tablet ) / 3 ) ),
		'px'
	) . ')',
);
$t_selectors[' .uagb-forms-main-form .uagb-slider:before']                           = array(
	'height'        => 'calc(20px + ' . $toggle_width_size_number_tablet . 'px)',
	'width'         => 'calc(20px + ' . $toggle_width_size_number_tablet . 'px)',
	'top'           => UAGB_Helper::get_css_value( (int) ( ( 20 + $toggle_width_size_number_tablet ) / 6 ), 'px' ),
	'bottom'        => UAGB_Helper::get_css_value( (int) ( ( 20 + $toggle_width_size_number_tablet ) / 6 ), 'px' ),
	'left'          => UAGB_Helper::get_css_value( (int) ( ( 20 + $toggle_width_size_number_tablet ) / 6 ), 'px' ),
	'border-radius' => $toggle_border_radius_tl_tablet_fallback . ' ' . $toggle_border_radius_tr_tablet_fallback . ' ' . $toggle_border_radius_br_tablet_fallback . ' ' . $toggle_border_radius_bl_tablet_fallback,
);

// Checkbox Field css.
$m_selectors[' .uagb-forms-checkbox-wrap input[type=checkbox]:checked + label:before'] = array(
	'font-size' => 'calc(' . $toggle_size_number_mobile . $attr['toggleSizeType'] . ' / 1.2)',
);
$m_selectors[' .uagb-forms-checkbox-wrap input[type=checkbox] + label:before']         = array(
	'width'  => UAGB_Helper::get_css_value( $attr['toggleSizeMobile'], $attr['toggleSizeType'] ),
	'height' => UAGB_Helper::get_css_value( $attr['toggleSizeMobile'], $attr['toggleSizeType'] ),
);
// Radio Button Field css.
$m_selectors[' .uagb-forms-radio-wrap input[type=radio]:checked + label:before'] = array(
	'font-size' => 'calc(' . $toggle_size_number_mobile . 'px / 1.2)',
);
$m_selectors[' .uagb-forms-radio-wrap input[type=radio] + label:before']         = array(
	'width'  => UAGB_Helper::get_css_value( $attr['toggleSizeMobile'], $attr['toggleSizeType'] ),
	'height' => UAGB_Helper::get_css_value( $attr['toggleSizeMobile'], $attr['toggleSizeType'] ),
);
// Accept Field css.
$m_selectors[' .uagb-forms-accept-wrap input[type=checkbox]:checked + label:before'] = array(
	'font-size' => 'calc(' . $toggle_size_number_mobile . $attr['toggleSizeType'] . ' / 1.2)',
);
$m_selectors[' .uagb-forms-accept-wrap input[type=checkbox] + label:before']         = array(
	'width'  => UAGB_Helper::get_css_value( $attr['toggleSizeMobile'], $attr['toggleSizeType'] ),
	'height' => UAGB_Helper::get_css_value( $attr['toggleSizeMobile'], $attr['toggleSizeType'] ),
);
$m_selectors[' .uagb-forms-main-form .uagb-switch']                                  = array(
	'height' => 'calc(' . $toggle_border_top_mobile_fallback . ' + ' . $toggle_border_bottom_mobile_fallback . ' + ' . UAGB_Helper::get_css_value(
		(int) ( 20 + $toggle_width_size_number_mobile + ( ( 20 + $toggle_width_size_number_mobile ) / 3 ) ),
		'px'
	) . ')',
	'width'  => 'calc(' . $toggle_border_left_mobile_fallback . ' + ' . $toggle_border_right_mobile_fallback . ' + ' . UAGB_Helper::get_css_value(
		(int) ( ( ( 20 + $toggle_width_size_number_mobile ) * 2.5 ) + ( ( 20 + $toggle_width_size_number_mobile ) / 3 ) ),
		'px'
	) . ')',
);
$m_selectors[' .uagb-forms-main-form .uagb-slider:before']                           = array(
	'height'        => 'calc(20px + ' . $toggle_width_size_number_mobile . 'px)',
	'width'         => 'calc(20px + ' . $toggle_width_size_number_mobile . 'px)',
	'top'           => UAGB_Helper::get_css_value( (int) ( ( 20 + $toggle_width_size_number_mobile ) / 6 ), 'px' ),
	'bottom'        => UAGB_Helper::get_css_value( (int) ( ( 20 + $toggle_width_size_number_mobile ) / 6 ), 'px' ),
	'left'          => UAGB_Helper::get_css_value( (int) ( ( 20 + $toggle_width_size_number_mobile ) / 6 ), 'px' ),
	'border-radius' => $toggle_border_radius_tl_mobile_fallback . ' ' . $toggle_border_radius_tr_mobile_fallback . ' ' . $toggle_border_radius_br_mobile_fallback . ' ' . $toggle_border_radius_bl_mobile_fallback,
);

if ( ! $attr['inheritFromTheme'] ) {
	$selectors[' .uagb-forms-main-form .uagb-forms-main-submit-button-wrap.wp-block-button:not(.is-style-outline) .uagb-forms-main-submit-button.wp-block-button__link '] = array_merge(
		array(
			'font-size'      => UAGB_Helper::get_css_value( $attr['submitTextFontSize'], $attr['submitTextFontSizeType'] ),
			'color'          => $attr['submitColor'],
			'padding-top'    => UAGB_Helper::get_css_value( $btnPaddingTop, $attr['paddingBtnUnit'] ),
			'padding-bottom' => UAGB_Helper::get_css_value( $btnPaddingBottom, $attr['paddingBtnUnit'] ),
			'padding-left'   => UAGB_Helper::get_css_value( $btnPaddingLeft, $attr['paddingBtnUnit'] ),
			'padding-right'  => UAGB_Helper::get_css_value( $btnPaddingRight, $attr['paddingBtnUnit'] ),
		),
		$btn_border
	);


	$selectors[' .uagb-forms-main-form .uagb-forms-main-submit-button '] = array_merge(
		array(
			'font-size'      => UAGB_Helper::get_css_value( $attr['submitTextFontSize'], $attr['submitTextFontSizeType'] ),
			'color'          => $attr['submitColor'],
			'padding-top'    => UAGB_Helper::get_css_value( $btnPaddingTop, $attr['paddingBtnUnit'] ),
			'padding-bottom' => UAGB_Helper::get_css_value( $btnPaddingBottom, $attr['paddingBtnUnit'] ),
			'padding-left'   => UAGB_Helper::get_css_value( $btnPaddingLeft, $attr['paddingBtnUnit'] ),
			'padding-right'  => UAGB_Helper::get_css_value( $btnPaddingRight, $attr['paddingBtnUnit'] ),
		),
		$btn_border
	);

	$selectors[' .uagb-forms-main-form .uagb-forms-main-submit-button-wrap.wp-block-button:not(.is-style-outline) .uagb-forms-main-submit-button.wp-block-button__link:hover '] = array(
		'color'        => $attr['submitColorHover'],
		'border-color' => ! empty( $attr['btnBorderHColor'] ) ? $attr['btnBorderHColor'] : $attr['submitborderHoverColor'],
	);

	$selectors[' .uagb-forms-main-form .uagb-forms-main-submit-button:hover'] = array(
		'color'        => $attr['submitColorHover'],
		'border-color' => ! empty( $attr['btnBorderHColor'] ) ? $attr['btnBorderHColor'] : $attr['submitborderHoverColor'],
	);

	$selectors[' .uagb-forms-main-form .uagb-forms-main-submit-button-wrap.wp-block-button:not(.is-style-outline) .uagb-forms-main-submit-button.wp-block-button__link:focus '] = array(
		'color'            => $attr['submitColorHover'],
		'background-color' => ( 'color' === $attr['submitBgHoverType'] ) ? $attr['submitBgColorHover'] : 'transparent',
		'border-color'     => ! empty( $attr['btnBorderHColor'] ) ? $attr['btnBorderHColor'] : $attr['submitborderHoverColor'],
	);

	$selectors[' .uagb-forms-main-form .uagb-forms-main-submit-button:focus'] = array(
		'color'            => $attr['submitColorHover'],
		'background-color' => ( 'color' === $attr['submitBgHoverType'] ) ? $attr['submitBgColorHover'] : 'transparent',
		'border-color'     => ! empty( $attr['btnBorderHColor'] ) ? $attr['btnBorderHColor'] : $attr['submitborderHoverColor'],
	);

	$selectors['.uagb-forms__full-btn .uagb-forms-main-submit-button-wrap .uagb-forms-main-submit-button']                  = array(
		'width'   => '100%',
		'padding' => '10px 15px',
	);
	$selectors['.uagb-forms__small-btn .uagb-forms-main-submit-button-wrap .uagb-forms-main-submit-button']['padding']      = '5px 10px';
	$selectors['.uagb-forms__medium-btn .uagb-forms-main-submit-button-wrap .uagb-forms-main-submit-button']['padding']     = '12px 24px';
	$selectors['.uagb-forms__large-btn .uagb-forms-main-submit-button-wrap .uagb-forms-main-submit-button']['padding']      = '20px 30px';
	$selectors['.uagb-forms__extralarge-btn .uagb-forms-main-submit-button-wrap .uagb-forms-main-submit-button']['padding'] = '30px 65px';

	if ( 'transparent' === $attr['submitBgType'] ) {

		$selectors['  .uagb-forms-main-form .wp-block-button:not(.is-style-outline) .uagb-forms-main-submit-button.wp-block-button__link']['background'] = 'transparent';
	
	} elseif ( 'color' === $attr['submitBgType'] ) {
	
		$selectors[' .uagb-forms-main-form .wp-block-button:not(.is-style-outline) .uagb-forms-main-submit-button.wp-block-button__link']['background'] = $attr['submitBgColor'];
	
	} elseif ( 'gradient' === $attr['submitBgType'] ) {
		$bg_obj = array(
			'backgroundType'    => 'gradient',
			'gradientValue'     => $attr['gradientValue'],
			'gradientColor1'    => $attr['gradientColor1'],
			'gradientColor2'    => $attr['gradientColor2'],
			'gradientType'      => $attr['gradientType'],
			'gradientLocation1' => $attr['gradientLocation1'],
			'gradientLocation2' => $attr['gradientLocation2'],
			'gradientAngle'     => $attr['gradientAngle'],
			'selectGradient'    => $attr['selectGradient'],
		);
	
		$btn_bg_css = UAGB_Block_Helper::uag_get_background_obj( $bg_obj );
		$selectors['  .uagb-forms-main-form .wp-block-button:not(.is-style-outline) .uagb-forms-main-submit-button.wp-block-button__link'] = $btn_bg_css;
	}
	
	// Hover.
	if ( 'transparent' === $attr['submitBgHoverType'] ) {
	
		$selectors['  .uagb-forms-main-form .wp-block-button:not(.is-style-outline) .uagb-forms-main-submit-button.wp-block-button__link:hover'] = array(
			'background' => 'transparent',
		);
	
	} elseif ( 'color' === $attr['submitBgHoverType'] ) {
	
		$selectors['  .uagb-forms-main-form .wp-block-button:not(.is-style-outline) .uagb-forms-main-submit-button.wp-block-button__link:hover'] = array(
			'background' => $attr['submitBgColorHover'],
		);
	
	} elseif ( 'gradient' === $attr['submitBgHoverType'] ) {
		$bg_hover_obj = array(
			'backgroundType'    => 'gradient',
			'gradientValue'     => $attr['gradientHValue'],
			'gradientColor1'    => $attr['gradientColor1'],
			'gradientColor2'    => $attr['gradientColor2'],
			'gradientType'      => $attr['gradientType'],
			'gradientLocation1' => $attr['gradientLocation1'],
			'gradientLocation2' => $attr['gradientLocation2'],
			'gradientAngle'     => $attr['gradientAngle'],
			'selectGradient'    => $attr['selectHGradient'],
		);
	
		$btn_hover_bg_css = UAGB_Block_Helper::uag_get_background_obj( $bg_hover_obj );
		$selectors['  .uagb-forms-main-form .wp-block-button:not(.is-style-outline) .uagb-forms-main-submit-button.wp-block-button__link:hover'] = $btn_hover_bg_css;
	}

	$t_selectors[' .uagb-forms-main-form .uagb-forms-main-submit-button'] = array_merge(
		array(
			'padding-top'    => UAGB_Helper::get_css_value( $attr['paddingBtnTopTablet'], $attr['tabletPaddingBtnUnit'] ),
			'padding-bottom' => UAGB_Helper::get_css_value( $attr['paddingBtnBottomTablet'], $attr['tabletPaddingBtnUnit'] ),
			'padding-left'   => UAGB_Helper::get_css_value( $attr['paddingBtnLeftTablet'], $attr['tabletPaddingBtnUnit'] ),
			'padding-right'  => UAGB_Helper::get_css_value( $attr['paddingBtnRightTablet'], $attr['tabletPaddingBtnUnit'] ),
		),
		$btn_border_Tablet
	);

	$t_selectors[' .uagb-forms-main-form .uagb-forms-main-submit-button-wrap.wp-block-button:not(.is-style-outline) .uagb-forms-main-submit-button.wp-block-button__link '] = $btn_border_Tablet;


	$m_selectors[' .uagb-forms-main-form .uagb-forms-main-submit-button'] = array_merge(
		array(
			'padding-top'    => UAGB_Helper::get_css_value( $attr['paddingBtnTopMobile'], $attr['mobilePaddingBtnUnit'] ),
			'padding-bottom' => UAGB_Helper::get_css_value( $attr['paddingBtnBottomMobile'], $attr['mobilePaddingBtnUnit'] ),
			'padding-left'   => UAGB_Helper::get_css_value( $attr['paddingBtnLeftMobile'], $attr['mobilePaddingBtnUnit'] ),
			'padding-right'  => UAGB_Helper::get_css_value( $attr['paddingBtnRightMobile'], $attr['mobilePaddingBtnUnit'] ),
		),
		$btn_border_Mobile
	);

	$m_selectors[' .uagb-forms-main-form .uagb-forms-main-submit-button-wrap.wp-block-button:not(.is-style-outline) .uagb-forms-main-submit-button.wp-block-button__link '] = $btn_border_Mobile;
};

$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);

if ( ! $attr['inheritFromTheme'] ) {
	$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'submitText', ' .uagb-forms-main-form .uagb-forms-main-submit-button', $combined_selectors );
}

$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'label', ' .uagb-forms-main-form .uagb-forms-input-label', $combined_selectors );

$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'input', ' .uagb-forms-main-form  .uagb-forms-input::placeholder', $combined_selectors );

$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'input', ' .uagb-forms-main-form  .uagb-forms-input', $combined_selectors );

return UAGB_Helper::generate_all_css( $combined_selectors, '.uagb-block-' . $id );
