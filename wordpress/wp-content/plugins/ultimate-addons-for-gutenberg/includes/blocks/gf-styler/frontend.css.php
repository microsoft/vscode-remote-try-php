<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

// Adds Fonts.
UAGB_Block_JS::blocks_gf_styler_gfont( $attr );

$attr['msgVrPadding']   = ( '' === $attr['msgVrPadding'] ) ? '0' : $attr['msgVrPadding'];
$attr['msgHrPadding']   = ( '' === $attr['msgHrPadding'] ) ? '0' : $attr['msgHrPadding'];
$attr['textAreaHeight'] = ( 'auto' === $attr['msgHrPadding'] ) ?
$attr['textAreaHeight'] : $attr['textAreaHeight'] . 'px';
$button_top_padding     = isset( $attr['buttontopPadding'] ) ? $attr['buttontopPadding'] : $attr['buttonVrPadding'];
$button_bottom_padding  = isset( $attr['buttonbottomPadding'] ) ? $attr['buttonbottomPadding'] : $attr['buttonVrPadding'];
$button_left_padding    = isset( $attr['buttonleftPadding'] ) ? $attr['buttonleftPadding'] : $attr['buttonHrPadding'];
$button_right_padding   = isset( $attr['buttonrightPadding'] ) ? $attr['buttonrightPadding'] : $attr['buttonHrPadding'];

$msg_top_padding    = isset( $attr['msgtopPadding'] ) ? $attr['msgtopPadding'] : $attr['msgVrPadding'];
$msg_bottom_padding = isset( $attr['msgbottomPadding'] ) ? $attr['msgbottomPadding'] : $attr['msgVrPadding'];
$msg_left_padding   = isset( $attr['msgleftPadding'] ) ? $attr['msgleftPadding'] : $attr['msgHrPadding'];
$msg_right_padding  = isset( $attr['msgrightPadding'] ) ? $attr['msgrightPadding'] : $attr['msgHrPadding'];

$field_top_padding    = isset( $attr['fieldtopPadding'] ) ? $attr['fieldtopPadding'] : $attr['fieldVrPadding'];
$field_bottom_padding = isset( $attr['fieldbottomPadding'] ) ? $attr['fieldbottomPadding'] : $attr['fieldVrPadding'];
$field_left_padding   = isset( $attr['fieldleftPadding'] ) ? $attr['fieldleftPadding'] : $attr['fieldHrPadding'];
$field_right_padding  = isset( $attr['fieldrightPadding'] ) ? $attr['fieldrightPadding'] : $attr['fieldHrPadding'];

$selectors = array(
	' .gform_wrapper form'                                 => array(
		'text-align' => $attr['align'],
	),
	' .gform_wrapper .gfield_label'                        => array(
		'color' => $attr['fieldLabelColor'],
	),
	' .wp-block-uagb-gf-styler form:not(input)'            => array(
		'color' => $attr['fieldLabelColor'],
	),
	' .gform_heading'                                      => array(
		'text-align' => $attr['titleDescAlignment'],
	),
	' .gform_wrapper input:not([type=submit])'             => array(
		'background-color' => $attr['fieldBgColor'],
		'color'            => $attr['fieldInputColor'],
		'border-style'     => $attr['fieldBorderStyle'],
		'border-color'     => $attr['fieldBorderColor'],
		'border-width'     => UAGB_Helper::get_css_value(
			$attr['fieldBorderWidth'],
			$attr['fieldBorderWidthType']
		),
		'border-radius'    => UAGB_Helper::get_css_value(
			$attr['fieldBorderRadius'],
			$attr['fieldBorderRadiusType']
		),
		'padding-left'     => UAGB_Helper::get_css_value( $field_left_padding, $attr['fieldpaddingUnit'] ),
		'padding-right'    => UAGB_Helper::get_css_value( $field_right_padding, $attr['fieldpaddingUnit'] ),
		'padding-top'      => UAGB_Helper::get_css_value( $field_top_padding, $attr['fieldpaddingUnit'] ),
		'padding-bottom'   => UAGB_Helper::get_css_value( $field_bottom_padding, $attr['fieldpaddingUnit'] ),
		'margin-top'       => UAGB_Helper::get_css_value(
			$attr['fieldLabelSpacing'],
			'px'
		),
		'margin-bottom'    => UAGB_Helper::get_css_value(
			$attr['fieldSpacing'],
			'px'
		),
		'text-align'       => $attr['align'],
	),
	// Focus.
	' .gform_wrapper.gravity-theme input:not([type=submit]):focus' => array(
		'border-color' => $attr['fieldBorderFocusColor'],
	),
	' .gform_wrapper.gravity-theme select:focus'           => array(
		'border-color' => $attr['fieldBorderFocusColor'],
	),
	' .gform_wrapper.gravity-theme textarea:focus'         => array(
		'border-color' => $attr['fieldBorderFocusColor'],
	),
	' input[type=button]'                                  => array(
		'color'            => $attr['buttonTextColor'],
		'background-color' => $attr['buttonBgColor'],
		'border-color'     => $attr['buttonBorderColor'],
		'border-style'     => $attr['buttonBorderStyle'],
		'border-width'     => UAGB_Helper::get_css_value(
			$attr['buttonBorderWidth'],
			'px'
		),
		'border-radius'    => UAGB_Helper::get_css_value(
			$attr['buttonBorderRadius'],
			$attr['buttonBorderRadiusType']
		),
		'padding-left'     => UAGB_Helper::get_css_value( $button_left_padding, $attr['buttonpaddingUnit'] ),
		'padding-right'    => UAGB_Helper::get_css_value( $button_right_padding, $attr['buttonpaddingUnit'] ),
		'padding-top'      => UAGB_Helper::get_css_value( $button_top_padding, $attr['buttonpaddingUnit'] ),
		'padding-bottom'   => UAGB_Helper::get_css_value( $button_bottom_padding, $attr['buttonpaddingUnit'] ),
	),
	' input[type=button]:hover'                            => array(
		'color'            => $attr['buttonTextHoverColor'],
		'background-color' => $attr['buttonBgHoverColor'],
		'border-color'     => $attr['buttonBorderHoverColor'],
	),
	' input[type=button]:focus'                            => array(
		'color'            => $attr['buttonTextHoverColor'],
		'background-color' => $attr['buttonBgHoverColor'],
		'border-color'     => $attr['buttonBorderHoverColor'],
	),
	' .gform_wrapper select '                              => array(
		'background-color' => $attr['fieldBgColor'],
		'border-style'     => $attr['fieldBorderStyle'],
		'border-color'     => $attr['fieldBorderColor'],
		'border-width'     => UAGB_Helper::get_css_value(
			$attr['fieldBorderWidth'],
			$attr['fieldBorderWidthType']
		),
		'border-radius'    => UAGB_Helper::get_css_value(
			$attr['fieldBorderRadius'],
			$attr['fieldBorderRadiusType']
		),
		'margin-top'       => UAGB_Helper::get_css_value(
			$attr['fieldLabelSpacing'],
			'px'
		),
		'margin-bottom'    => UAGB_Helper::get_css_value(
			$attr['fieldSpacing'],
			'px'
		),
		'color'            => $attr['fieldInputColor'],
		'text-align'       => $attr['align'],
		'padding-left'     => UAGB_Helper::get_css_value( $field_left_padding, $attr['fieldpaddingUnit'] ),
		'padding-right'    => UAGB_Helper::get_css_value( $field_right_padding, $attr['fieldpaddingUnit'] ),
		'padding-top'      => UAGB_Helper::get_css_value( $field_top_padding, $attr['fieldpaddingUnit'] ),
		'padding-bottom'   => UAGB_Helper::get_css_value( $field_bottom_padding, $attr['fieldpaddingUnit'] ),
	),
	' .chosen-container-single span'                       => array(
		'background-color' => $attr['fieldBgColor'],
		'border-style'     => $attr['fieldBorderStyle'],
		'border-color'     => $attr['fieldBorderColor'],
		'border-width'     => UAGB_Helper::get_css_value(
			$attr['fieldBorderWidth'],
			$attr['fieldBorderWidthType']
		),
		'border-radius'    => UAGB_Helper::get_css_value(
			$attr['fieldBorderRadius'],
			$attr['fieldBorderRadiusType']
		),
		'margin-top'       => UAGB_Helper::get_css_value(
			$attr['fieldLabelSpacing'],
			'px'
		),
		'margin-bottom'    => UAGB_Helper::get_css_value(
			$attr['fieldSpacing'],
			'px'
		),
		'color'            => $attr['fieldInputColor'],
		'text-align'       => $attr['align'],
		'padding-left'     => UAGB_Helper::get_css_value( $field_left_padding, $attr['fieldpaddingUnit'] ),
		'padding-right'    => UAGB_Helper::get_css_value( $field_right_padding, $attr['fieldpaddingUnit'] ),
		'padding-top'      => UAGB_Helper::get_css_value( $field_top_padding, $attr['fieldpaddingUnit'] ),
		'padding-bottom'   => UAGB_Helper::get_css_value( $field_bottom_padding, $attr['fieldpaddingUnit'] ),
	),
	' .chosen-container-single.chosen-container-active .chosen-single span' => array(
		'margin-bottom' => 0,
	),
	' select.wpgf-form-control.wpgf-select:not([multiple="multiple"])' => array(
		'padding-left'   => UAGB_Helper::get_css_value( $field_left_padding, $attr['fieldpaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $field_right_padding, $attr['fieldpaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $field_top_padding, $attr['fieldpaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $field_bottom_padding, $attr['fieldpaddingUnit'] ),
	),
	' select.wpgf-select[multiple="multiple"] option'      => array(
		'padding-left'   => UAGB_Helper::get_css_value( $field_left_padding, $attr['fieldpaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $field_right_padding, $attr['fieldpaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $field_top_padding, $attr['fieldpaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $field_bottom_padding, $attr['fieldpaddingUnit'] ),
	),
	' .gform_wrapper .gfield textarea'                     => array(
		'background-color' => $attr['fieldBgColor'],
		'color'            => $attr['fieldInputColor'],
		'border-color'     => $attr['fieldBorderColor'],
		'border-width'     => UAGB_Helper::get_css_value(
			$attr['fieldBorderWidth'],
			$attr['fieldBorderWidthType']
		),
		'border-radius'    => UAGB_Helper::get_css_value(
			$attr['fieldBorderRadius'],
			$attr['fieldBorderRadiusType']
		),
		'border-style'     => $attr['fieldBorderStyle'],
		'padding-left'     => UAGB_Helper::get_css_value( $field_left_padding, $attr['fieldpaddingUnit'] ),
		'padding-right'    => UAGB_Helper::get_css_value( $field_right_padding, $attr['fieldpaddingUnit'] ),
		'padding-top'      => UAGB_Helper::get_css_value( $field_top_padding, $attr['fieldpaddingUnit'] ),
		'padding-bottom'   => UAGB_Helper::get_css_value( $field_bottom_padding, $attr['fieldpaddingUnit'] ),
		'margin-top'       => UAGB_Helper::get_css_value(
			$attr['fieldLabelSpacing'],
			'px'
		),
		'margin-bottom'    => UAGB_Helper::get_css_value(
			$attr['fieldSpacing'],
			'px'
		),
		'text-align'       => $attr['align'],
		'height'           => $attr['textAreaHeight'],
	),

	' .gform_wrapper.gravity-theme .gfield textarea.large:focus' => array(
		'border-color' => $attr['fieldBorderFocusColor'],
	),
	' textarea::placeholder'                               => array(
		'color'      => $attr['fieldInputColor'],
		'text-align' => $attr['align'],
	),
	' input::placeholder'                                  => array(
		'color'      => $attr['fieldInputColor'],
		'text-align' => $attr['align'],
	),
	' .gform_wrapper.gravity-theme .gfield_label'          => array(
		'color'           => $attr['fieldLabelColor'],
		'font-size'       => UAGB_Helper::get_css_value( $attr['labelFontSize'], $attr['labelFontSizeType'] ),
		'font-family'     => $attr['labelFontFamily'],
		'text-transform'  => $attr['labelTransform'],
		'text-decoration' => $attr['labelDecoration'] . '!important',
		'font-style'      => $attr['labelFontStyle'],
		'font-weight'     => $attr['labelFontWeight'],
		'line-height'     => UAGB_Helper::get_css_value( $attr['labelLineHeight'], $attr['labelLineHeightType'] ),
	),
	' form .gfield_radio label.gfield_label'               => array(
		'color'           => $attr['fieldLabelColor'],
		'font-size'       => UAGB_Helper::get_css_value( $attr['labelFontSize'], $attr['labelFontSizeType'] ),
		'font-family'     => $attr['labelFontFamily'],
		'text-transform'  => $attr['labelTransform'],
		'text-decoration' => $attr['labelDecoration'] . '!important',
		'font-style'      => $attr['labelFontStyle'],
		'font-weight'     => $attr['labelFontWeight'],
		'line-height'     => UAGB_Helper::get_css_value( $attr['labelLineHeight'], $attr['labelLineHeightType'] ),
	),
	' form .gfield_checkbox label.gfield_label'            => array(
		'color'           => $attr['fieldLabelColor'],
		'font-size'       => UAGB_Helper::get_css_value( $attr['labelFontSize'], $attr['labelFontSizeType'] ),
		'font-family'     => $attr['labelFontFamily'],
		'text-transform'  => $attr['labelTransform'],
		'text-decoration' => $attr['labelDecoration'] . '!important',
		'font-style'      => $attr['labelFontStyle'],
		'font-weight'     => $attr['labelFontWeight'],
		'line-height'     => UAGB_Helper::get_css_value( $attr['labelLineHeight'], $attr['labelLineHeightType'] ),
	),
	' .gform_wrapper.gravity-theme .gfield_checkbox '      => array(
		'margin-top' => UAGB_Helper::get_css_value(
			$attr['fieldLabelSpacing'],
			'px'
		),
	),
	' .gform_wrapper.gravity-theme .gfield_radio '         => array(
		'margin-top' => UAGB_Helper::get_css_value(
			$attr['fieldLabelSpacing'],
			'px'
		),
	),

	// Submit button.
	' input.gform_button'                                  => array(
		'color'            => $attr['buttonTextColor'],
		'background-color' => $attr['buttonBgColor'],
		'border-color'     => $attr['buttonBorderColor'],
		'border-style'     => $attr['buttonBorderStyle'],
		'border-width'     => UAGB_Helper::get_css_value(
			$attr['buttonBorderWidth'],
			'px'
		),
		'border-radius'    => UAGB_Helper::get_css_value(
			$attr['buttonBorderRadius'],
			$attr['buttonBorderRadiusType']
		),
		'padding-left'     => UAGB_Helper::get_css_value( $button_left_padding, $attr['buttonpaddingUnit'] ),
		'padding-right'    => UAGB_Helper::get_css_value( $button_right_padding, $attr['buttonpaddingUnit'] ),
		'padding-top'      => UAGB_Helper::get_css_value( $button_top_padding, $attr['buttonpaddingUnit'] ),
		'padding-bottom'   => UAGB_Helper::get_css_value( $button_bottom_padding, $attr['buttonpaddingUnit'] ),
	),

	' .gform_footer.top_label input[type="submit"]'        => array(
		'font-size' => UAGB_Helper::get_css_value( $attr['buttonFontSize'], $attr['buttonFontSizeType'] ),
	),

	' input.gform_button:hover'                            => array(
		'color'            => $attr['buttonTextHoverColor'],
		'background-color' => $attr['buttonBgHoverColor'],
		'border-color'     => $attr['buttonBorderHoverColor'],
	),
	' input.gform_button:focus'                            => array(
		'color'            => $attr['buttonTextHoverColor'],
		'background-color' => $attr['buttonBgHoverColor'],
		'border-color'     => $attr['buttonBorderHoverColor'],
	),

	// Check box Radio.
	' .gfield_checkbox input[type="checkbox"]:checked + label:before' => array(
		'background-color' => $attr['fieldBgColor'],
		'color'            => $attr['fieldInputColor'],
		'font-size'        => 'calc( ' . $attr['fieldVrPadding'] . 'px / 1.2 )',
		'border-color'     => $attr['fieldBorderFocusColor'],
	),
	' .gfield_checkbox input[type="checkbox"] + label:before' => array(
		'background-color' => $attr['fieldBgColor'],
		'color'            => $attr['fieldInputColor'],
		'height'           => UAGB_Helper::get_css_value( $attr['fieldVrPadding'], 'px' ),
		'width'            => UAGB_Helper::get_css_value( $attr['fieldVrPadding'], 'px' ),
		'border-style'     => $attr['fieldBorderStyle'],
		'border-color'     => $attr['fieldBorderColor'],
		'border-width'     => UAGB_Helper::get_css_value(
			$attr['fieldBorderWidth'],
			$attr['fieldBorderWidthType']
		),
		'border-radius'    => UAGB_Helper::get_css_value(
			$attr['fieldBorderRadius'],
			$attr['fieldBorderRadiusType']
		),
		'font-size'        => 'calc( ' . $attr['fieldVrPadding'] . 'px / 1.2 )',
	),
	' input[type="checkbox"]:checked + label:before'       => array(
		'background-color' => $attr['fieldBgColor'],
		'color'            => $attr['fieldInputColor'],
		'font-size'        => 'calc( ' . $attr['fieldVrPadding'] . 'px / 1.2 )',
		'border-color'     => $attr['fieldBorderFocusColor'],
	),
	' input[type="checkbox"] + label:before'               => array(
		'background-color' => $attr['fieldBgColor'],
		'color'            => $attr['fieldInputColor'],
		'height'           => UAGB_Helper::get_css_value( $attr['fieldVrPadding'], 'px' ),
		'width'            => UAGB_Helper::get_css_value( $attr['fieldVrPadding'], 'px' ),
		'font-size'        => 'calc( ' . $attr['fieldVrPadding'] . 'px / 1.2 )',
		'border-color'     => $attr['fieldBorderColor'],
		'border-style'     => $attr['fieldBorderStyle'],
		'border-width'     => UAGB_Helper::get_css_value(
			$attr['fieldBorderWidth'],
			$attr['fieldBorderWidthType']
		),
		'border-radius'    => UAGB_Helper::get_css_value(
			$attr['fieldBorderRadius'],
			$attr['fieldBorderRadiusType']
		),
	),
	' .wp-block-uagb-gf-styler .gform_wrapper .ginput_container_select select' => array(
		'background-color' => $attr['fieldBgColor'],
		'color'            => $attr['fieldInputColor'],
	),
	' .gfield_radio input[type="radio"] + label:before'    => array(
		'background-color' => $attr['fieldBgColor'],
		'color'            => $attr['fieldInputColor'],
		'height'           => UAGB_Helper::get_css_value( $attr['fieldVrPadding'], 'px' ),
		'width'            => UAGB_Helper::get_css_value( $attr['fieldVrPadding'], 'px' ),
		'border-style'     => $attr['fieldBorderStyle'],
		'border-color'     => $attr['fieldBorderColor'],
		'border-width'     => UAGB_Helper::get_css_value(
			$attr['fieldBorderWidth'],
			$attr['fieldBorderWidthType']
		),
	),
	' .gfield_radio input[type="radio"]:checked + label:before' => array(
		'border-color' => $attr['fieldBorderFocusColor'],
	),

	// Underline border.
	' .uagb-gf-styler__field-style-underline .gform_wrapper input:not([type=submit])' => array(
		'border-style'        => 'none',
		'border-bottom-color' => $attr['fieldBorderColor'],
		'border-bottom-style' => 'solid',
		'border-bottom-width' => UAGB_Helper::get_css_value(
			$attr['fieldBorderWidth'],
			$attr['fieldBorderWidthType']
		),
		'border-radius'       => UAGB_Helper::get_css_value(
			$attr['fieldBorderRadius'],
			$attr['fieldBorderRadiusType']
		),
	),
	' .uagb-gf-styler__field-style-underline .gform_wrapper .gfield textarea' => array(
		'background-color'    => $attr['fieldBgColor'],
		'color'               => $attr['fieldInputColor'],
		'border-style'        => 'none',
		'border-bottom-color' => $attr['fieldBorderColor'],
		'border-bottom-style' => 'solid',
		'border-bottom-width' => UAGB_Helper::get_css_value(
			$attr['fieldBorderWidth'],
			$attr['fieldBorderWidthType']
		),
		'border-radius'       => UAGB_Helper::get_css_value(
			$attr['fieldBorderRadius'],
			$attr['fieldBorderRadiusType']
		),
	),
	' .uagb-gf-styler__field-style-underline select'       => array(
		'border-style'        => 'none',
		'border-bottom-color' => $attr['fieldBorderColor'],
		'border-bottom-style' => 'solid',
		'border-bottom-width' => UAGB_Helper::get_css_value(
			$attr['fieldBorderWidth'],
			$attr['fieldBorderWidthType']
		),
		'border-radius'       => UAGB_Helper::get_css_value(
			$attr['fieldBorderRadius'],
			$attr['fieldBorderRadiusType']
		),
	),
	' .uagb-gf-styler__field-style-underline textarea'     => array(
		'border-style'        => 'none',
		'border-bottom-color' => $attr['fieldBorderColor'],
		'border-bottom-style' => 'solid',
		'border-bottom-width' => UAGB_Helper::get_css_value(
			$attr['fieldBorderWidth'],
			$attr['fieldBorderWidthType']
		),
		'border-radius'       => UAGB_Helper::get_css_value(
			$attr['fieldBorderRadius'],
			$attr['fieldBorderRadiusType']
		),
	),

	' .uagb-gf-styler__check-style-enabled .gfield_checkbox input[type="checkbox"] + label:before' => array(
		'border-style' => 'solid',
	),
	' .uagb-gf-styler__check-style-enabled input[type="radio"] + label:before' => array(
		'border-style' => 'solid',
	),
	' .uagb-gf-styler__field-style-box .gfield_checkbox input[type="checkbox"]:checked + label:before' => array(
		'border-style'  => 'solid',
		'border-width'  => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderWidth'],
			'px'
		),
		'border-radius' => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderRadius'],
			$attr['radioCheckBorderRadiusType']
		),
		'font-size'     => 'calc( ' . $attr['radioCheckSize'] . 'px / 1.2 )',
	),
	' .uagb-gf-styler__field-style-box input[type="checkbox"]:checked + label:before' => array(
		'border-style'  => 'solid',
		'border-width'  => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderWidth'],
			'px'
		),
		'border-radius' => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderRadius'],
			$attr['radioCheckBorderRadiusType']
		),
		'font-size'     => 'calc( ' . $attr['radioCheckSize'] . 'px / 1.2 )',
	),
	' .gfield_radio input[type="radio"]:checked + label:before' => array(
		'background-color' => $attr['fieldInputColor'],
	),

	// Override check box.
	' .uagb-gf-styler__check-style-enabled .gfield_checkbox input[type="checkbox"] + label:before' => array(
		'background-color' => $attr['radioCheckBgColor'],
		'color'            => $attr['radioCheckSelectColor'],
		'height'           => UAGB_Helper::get_css_value(
			$attr['radioCheckSize'],
			'px'
		),
		'width'            => UAGB_Helper::get_css_value(
			$attr['radioCheckSize'],
			'px'
		),
		'font-size'        => 'calc( ' . $attr['radioCheckSize'] . 'px / 1.2 )',
		'border-color'     => $attr['radioCheckBorderColor'],
		'border-style'     => 'solid',
		'border-width'     => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderWidth'],
			'px'
		),
		'border-radius'    => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderRadius'],
			$attr['radioCheckBorderRadiusType']
		),
	),
	' .uagb-gf-styler__check-style-enabled .gfield_checkbox input[type="checkbox"]:checked + label:before' => array(
		'border-color' => $attr['fieldBorderFocusColor'],
	),
	' .uagb-gf-styler__check-style-enabled input[type="checkbox"] + label:before' => array(
		'background-color' => $attr['radioCheckBgColor'],
		'color'            => $attr['radioCheckSelectColor'],
		'height'           => UAGB_Helper::get_css_value(
			$attr['radioCheckSize'],
			'px'
		),
		'width'            => UAGB_Helper::get_css_value(
			$attr['radioCheckSize'],
			'px'
		),
		'font-size'        => 'calc( ' . $attr['radioCheckSize'] . 'px / 1.2 )',
		'border-color'     => $attr['radioCheckBorderColor'],
		'border-width'     => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderWidth'],
			'px'
		),
		'border-radius'    => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderRadius'],
			$attr['radioCheckBorderRadiusType']
		),
	),
	' .uagb-gf-styler__check-style-enabled input[type="checkbox"]:checked + label:before' => array(
		'border-color' => $attr['fieldBorderFocusColor'],
	),

	' .uagb-gf-styler__check-style-enabled input[type="radio"] + label:before' => array(
		'background-color' => $attr['radioCheckBgColor'],
		'color'            => $attr['radioCheckSelectColor'],
		'height'           => UAGB_Helper::get_css_value(
			$attr['radioCheckSize'],
			'px'
		),
		'width'            => UAGB_Helper::get_css_value(
			$attr['radioCheckSize'],
			'px'
		),
		'font-size'        => 'calc( ' . $attr['radioCheckSize'] . 'px / 1.2 )',
		'border-color'     => $attr['radioCheckBorderColor'],
		'border-width'     => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderWidth'],
			'px'
		),
		'border-radius'    => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderRadius'],
			$attr['radioCheckBorderRadiusType']
		),
	),
	' .uagb-gf-styler__check-style-enabled .gfield_radio input[type="radio"]:checked + label:before' => array(
		'background-color' => $attr['radioCheckSelectColor'],
		'border-color'     => $attr['fieldBorderFocusColor'],
	),
	' .uagb-gf-styler__check-style-enabled form .gfield_radio label' => array(
		'color' => $attr['radioCheckLableColor'],
	),
	' .uagb-gf-styler__check-style-enabled form .gfield_checkbox label' => array(
		'color' => $attr['radioCheckLableColor'],
	),
	// Validation Errors.
	' .gform_wrapper .gfield_description.validation_message' => array(
		'color' => $attr['validationMsgColor'],
	),
	' .uagb-gf-styler__error-yes .gform_wrapper .gfield.gfield_error' => array(
		'background-color' => $attr['validationMsgBgColor'],
	),

	' .uagb-gf-styler__error-yes .gform_wrapper .gfield_error .ginput_container input' => array(
		'border-color' => $attr['highlightBorderColor'],
	),

	' .uagb-gf-styler__error-yes .gform_wrapper .gfield_error .ginput_container select' => array(
		'border-color' => $attr['highlightBorderColor'],
	),

	' .uagb-gf-styler__error-yes .gform_wrapper .gfield_error .ginput_container .chosen-single' => array(
		'border-color' => $attr['highlightBorderColor'],
	),

	' .uagb-gf-styler__error-yes .gform_wrapper .gfield_error .ginput_container textarea' => array(
		'border-color' => $attr['highlightBorderColor'],
	),

	' .uagb-gf-styler__error-yes .gform_wrapper li.gfield.gfield_error' => array(
		'border-color' => $attr['highlightBorderColor'],
	),

	' .uagb-gf-styler__error-yes .gform_wrapper li.gfield.gfield_error.gfield_contains_required.gfield_creditcard_warning' => array(
		'border-color' => $attr['highlightBorderColor'],
	),

	' .uagb-gf-styler__error-yes li.gfield_error .gfield_checkbox input[type="checkbox"] + label:before' => array(
		'border-color' => $attr['highlightBorderColor'],
	),

	' .uagb-gf-styler__error-yes li.gfield_error .ginput_container_consent input[type="checkbox"] + label:before' => array(
		'border-color' => $attr['highlightBorderColor'],
	),

	' .uagb-gf-styler__error-yes li.gfield_error .gfield_radio input[type="radio"] + label:before' => array(
		'border-color' => $attr['highlightBorderColor'],
	),

	' .uagb-gf-styler__error-yes .gform_wrapper li.gfield_error input[type="text"]' => array(
		'border' => $attr['fieldBorderWidth'] . $attr['fieldBorderWidthType'] . ' ' . $attr['fieldBorderStyle'] . ' ' . $attr['fieldBorderColor'] . '!important',
	),

	' .uael-gf-style-underline.uagb-gf-styler__error-yes .gform_wrapper li.gfield_error input[type="text"]' => array(
		'border-width' => $attr['fieldBorderWidth'] . $attr['fieldBorderWidthType'] . ' !important',
		'border-style' => 'solid !important',
		'border-color' => $attr['fieldBorderColor'] . '!important',
	),

	' .gform_wrapper.gravity-theme div.validation_message' => array(
		'color'            => $attr['errorMsgColor'],
		'background-color' => $attr['errorMsgBgColor'],
		'border-color'     => $attr['errorMsgBorderColor'],
		'border-style'     => 'solid',
		'border-width'     => UAGB_Helper::get_css_value(
			$attr['msgBorderSize'],
			'px'
		),
		'border-radius'    => UAGB_Helper::get_css_value(
			$attr['msgBorderRadius'],
			$attr['msgBorderRadiusType']
		),
		'padding-left'     => UAGB_Helper::get_css_value( $msg_left_padding, $attr['msgpaddingUnit'] ),
		'padding-right'    => UAGB_Helper::get_css_value( $msg_right_padding, $attr['msgpaddingUnit'] ),
		'padding-top'      => UAGB_Helper::get_css_value( $msg_top_padding, $attr['msgpaddingUnit'] ),
		'padding-bottom'   => UAGB_Helper::get_css_value( $msg_bottom_padding, $attr['msgpaddingUnit'] ),
	),

	' .gform_confirmation_message'                         => array(
		'color' => $attr['successMsgColor'],
	),
);

$t_selectors = array(
	' .uagb-gf-styler__field-style-box .gfield_checkbox input[type="checkbox"]:checked + label:before' => array(
		'border-width' => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderWidthTablet'],
			'px'
		),
		'font-size'    => 'calc( ' . $attr['radioCheckSizeTablet'] . 'px / 1.2 )',
	),
	' .uagb-gf-styler__field-style-box input[type="checkbox"]:checked + label:before' => array(
		'border-width' => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderWidthTablet'],
			'px'
		),
		'font-size'    => 'calc( ' . $attr['radioCheckSizeTablet'] . 'px / 1.2 )',
	),
	' .uagb-gf-styler__check-style-enabled .gfield_checkbox input[type="checkbox"] + label:before' => array(
		'height'       => UAGB_Helper::get_css_value(
			$attr['radioCheckSizeTablet'],
			'px'
		),
		'width'        => UAGB_Helper::get_css_value(
			$attr['radioCheckSizeTablet'],
			'px'
		),
		'font-size'    => 'calc( ' . $attr['radioCheckSizeTablet'] . 'px / 1.2 )',
		'border-width' => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderWidthTablet'],
			'px'
		),
	),
	' .uagb-gf-styler__check-style-enabled input[type="checkbox"] + label:before' => array(
		'height'       => UAGB_Helper::get_css_value(
			$attr['radioCheckSizeTablet'],
			'px'
		),
		'width'        => UAGB_Helper::get_css_value(
			$attr['radioCheckSizeTablet'],
			'px'
		),
		'font-size'    => 'calc( ' . $attr['radioCheckSizeTablet'] . 'px / 1.2 )',
		'border-width' => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderWidth'],
			'px'
		),
	),
	' .uagb-gf-styler__check-style-enabled input[type="radio"] + label:before' => array(
		'height'       => UAGB_Helper::get_css_value(
			$attr['radioCheckSizeTablet'],
			'px'
		),
		'width'        => UAGB_Helper::get_css_value(
			$attr['radioCheckSizeTablet'],
			'px'
		),
		'font-size'    => 'calc( ' . $attr['radioCheckSizeTablet'] . 'px / 1.2 )',
		'border-width' => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderWidthTablet'],
			'px'
		),
	),
	' form.wpgf-form:not(input)'                           => array(
		'color' => $attr['fieldLabelColor'],
	),
	' .gform_wrapper.gravity-theme input:not([type=submit])' => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['fieldleftTabletPadding'], $attr['fieldtabletPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['fieldrightTabletPadding'], $attr['fieldtabletPaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['fieldtopTabletPadding'], $attr['fieldtabletPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['fieldbottomTabletPadding'], $attr['fieldtabletPaddingUnit'] ),
		'margin-top'     => UAGB_Helper::get_css_value(
			$attr['fieldLabelSpacingTablet'],
			'px'
		),
		'margin-bottom'  => UAGB_Helper::get_css_value(
			$attr['fieldSpacingTablet'],
			'px'
		),
		'border-width'   => UAGB_Helper::get_css_value(
			$attr['fieldBorderWidthTablet'],
			$attr['fieldBorderWidthType']
		),
	),
	' .gform_wrapper.gravity-theme .gfield textarea.large' => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['fieldleftTabletPadding'], $attr['fieldtabletPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['fieldrightTabletPadding'], $attr['fieldtabletPaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['fieldtopTabletPadding'], $attr['fieldtabletPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['fieldbottomTabletPadding'], $attr['fieldtabletPaddingUnit'] ),
		'margin-top'     => UAGB_Helper::get_css_value(
			$attr['fieldLabelSpacingTablet'],
			'px'
		),
		'margin-bottom'  => UAGB_Helper::get_css_value(
			$attr['fieldSpacingTablet'],
			'px'
		),
		'height'         => 'auto' === $attr['textAreaHeightTablet'] ? $attr['textAreaHeightTablet'] : UAGB_Helper::get_css_value(
			$attr['textAreaHeightTablet'],
			'px'
		),
		'border-width'   => UAGB_Helper::get_css_value(
			$attr['fieldBorderWidthTablet'],
			$attr['fieldBorderWidthType']
		),
	),
	' input.gform_button'                                  => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['buttonleftTabletPadding'], $attr['buttontabletPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['buttonrightTabletPadding'], $attr['buttontabletPaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['buttontopTabletPadding'], $attr['buttontabletPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['buttonbottomTabletPadding'], $attr['buttontabletPaddingUnit'] ),
		'border-width'   => UAGB_Helper::get_css_value(
			$attr['buttonBorderWidthTablet'],
			'px'
		),
	),
	' .gform_wrapper.gravity-theme div.validation_message' => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['msgleftTabletPadding'], $attr['msgtabletPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['msgrightTabletPadding'], $attr['msgtabletPaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['msgtopTabletPadding'], $attr['msgtabletPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['msgbottomTabletPadding'], $attr['msgtabletPaddingUnit'] ),
	),
	' .gform_wrapper.gravity-theme select '                => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['fieldleftTabletPadding'], $attr['fieldtabletPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['fieldrightTabletPadding'], $attr['fieldtabletPaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['fieldtopTabletPadding'], $attr['fieldtabletPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['fieldbottomTabletPadding'], $attr['fieldtabletPaddingUnit'] ),
		'margin-top'     => UAGB_Helper::get_css_value(
			$attr['fieldLabelSpacingTablet'],
			'px'
		),
		'margin-bottom'  => UAGB_Helper::get_css_value(
			$attr['fieldSpacingTablet'],
			'px'
		),
		'border-width'   => UAGB_Helper::get_css_value(
			$attr['fieldBorderWidthTablet'],
			$attr['fieldBorderWidthType']
		),
	),
);

$m_selectors = array(
	' .uagb-gf-styler__field-style-box .gfield_checkbox input[type="checkbox"]:checked + label:before' => array(
		'border-width' => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderWidthMobile'],
			'px'
		),
		'font-size'    => 'calc( ' . $attr['radioCheckSizeMobile'] . 'px / 1.2 )',
	),
	' .uagb-gf-styler__field-style-box input[type="checkbox"]:checked + label:before' => array(
		'border-width' => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderWidthMobile'],
			'px'
		),
		'font-size'    => 'calc( ' . $attr['radioCheckSizeMobile'] . 'px / 1.2 )',
	),
	' .uagb-gf-styler__check-style-enabled .gfield_checkbox input[type="checkbox"] + label:before' => array(
		'height'       => UAGB_Helper::get_css_value(
			$attr['radioCheckSizeMobile'],
			'px'
		),
		'width'        => UAGB_Helper::get_css_value(
			$attr['radioCheckSizeMobile'],
			'px'
		),
		'font-size'    => 'calc( ' . $attr['radioCheckSizeMobile'] . 'px / 1.2 )',
		'border-width' => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderWidthMobile'],
			'px'
		),
	),
	' .uagb-gf-styler__check-style-enabled input[type="checkbox"] + label:before' => array(
		'height'       => UAGB_Helper::get_css_value(
			$attr['radioCheckSizeMobile'],
			'px'
		),
		'width'        => UAGB_Helper::get_css_value(
			$attr['radioCheckSizeMobile'],
			'px'
		),
		'font-size'    => 'calc( ' . $attr['radioCheckSizeMobile'] . 'px / 1.2 )',
		'border-width' => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderWidth'],
			'px'
		),
	),
	' .uagb-gf-styler__check-style-enabled input[type="radio"] + label:before' => array(
		'height'       => UAGB_Helper::get_css_value(
			$attr['radioCheckSizeMobile'],
			'px'
		),
		'width'        => UAGB_Helper::get_css_value(
			$attr['radioCheckSizeMobile'],
			'px'
		),
		'font-size'    => 'calc( ' . $attr['radioCheckSizeMobile'] . 'px / 1.2 )',
		'border-width' => UAGB_Helper::get_css_value(
			$attr['radioCheckBorderWidthMobile'],
			'px'
		),
	),
	' .gform_wrapper.gravity-theme input:not([type=submit])' => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['fieldleftMobilePadding'], $attr['fieldmobilePaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['fieldrightMobilePadding'], $attr['fieldmobilePaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['fieldtopMobilePadding'], $attr['fieldmobilePaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['fieldbottomMobilePadding'], $attr['fieldmobilePaddingUnit'] ),
		'margin-top'     => UAGB_Helper::get_css_value(
			$attr['fieldLabelSpacingMobile'],
			'px'
		),
		'margin-bottom'  => UAGB_Helper::get_css_value(
			$attr['fieldSpacingMobile'],
			'px'
		),
		'border-width'   => UAGB_Helper::get_css_value(
			$attr['fieldBorderWidthMobile'],
			$attr['fieldBorderWidthType']
		),
	),
	' .gform_wrapper.gravity-theme .gfield textarea.large' => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['fieldleftMobilePadding'], $attr['fieldmobilePaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['fieldrightMobilePadding'], $attr['fieldmobilePaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['fieldtopMobilePadding'], $attr['fieldmobilePaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['fieldbottomMobilePadding'], $attr['fieldmobilePaddingUnit'] ),
		'margin-top'     => UAGB_Helper::get_css_value(
			$attr['fieldLabelSpacingMobile'],
			'px'
		),
		'margin-bottom'  => UAGB_Helper::get_css_value(
			$attr['fieldSpacingMobile'],
			'px'
		),
		'height'         => 'auto' === $attr['textAreaHeightMobile'] ? $attr['textAreaHeightMobile'] : UAGB_Helper::get_css_value(
			$attr['textAreaHeightMobile'],
			'px'
		),
		'border-width'   => UAGB_Helper::get_css_value(
			$attr['fieldBorderWidthMobile'],
			$attr['fieldBorderWidthType']
		),
	),
	' input.gform_button'                                  => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['buttonleftMobilePadding'], $attr['buttonmobilePaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['buttonrightMobilePadding'], $attr['buttonmobilePaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['buttontopMobilePadding'], $attr['buttonmobilePaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['buttonbottomMobilePadding'], $attr['buttonmobilePaddingUnit'] ),
		'border-width'   => UAGB_Helper::get_css_value(
			$attr['buttonBorderWidthMobile'],
			'px'
		),
	),
	' .gform_wrapper.gravity-theme div.validation_message' => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['msgleftMobilePadding'], $attr['msgmobilePaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['msgrightMobilePadding'], $attr['msgmobilePaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['msgtopMobilePadding'], $attr['msgmobilePaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['msgbottomMobilePadding'], $attr['msgmobilePaddingUnit'] ),
	),
	' .gform_wrapper.gravity-theme select '                => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['fieldleftTabletPadding'], $attr['fieldtabletPaddingUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['fieldrightTabletPadding'], $attr['fieldtabletPaddingUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['fieldtopTabletPadding'], $attr['fieldtabletPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['fieldbottomTabletPadding'], $attr['fieldtabletPaddingUnit'] ),
		'margin-top'     => UAGB_Helper::get_css_value(
			$attr['fieldLabelSpacingTablet'],
			'px'
		),
		'margin-bottom'  => UAGB_Helper::get_css_value(
			$attr['fieldSpacingTablet'],
			'px'
		),
		'border-width'   => UAGB_Helper::get_css_value(
			$attr['fieldBorderWidthTablet'],
			$attr['fieldBorderWidthType']
		),
	),
);

$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);

$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'validationMsg', ' .gform_wrapper.gravity-theme .validation_message', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'validationMsg', ' span.wpgf-not-valid-tip', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'successMsg', ' .gform_confirmation_message', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'msg', ' .gform_wrapper div.validation_error', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'msg', ' .wpgf-response-output', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'button', ' input.gform_button, input.gform_previous_button, input.gform_next_button', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'label', ' form .gfield_checkbox label.gfield_label', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'label', ' form .gfield_radio label.gfield_label', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'label', ' .gform_wrapper.gravity-theme label.gfield_label', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'input', ' textarea', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'input', ' select', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'input', ' .gform_wrapper.gravity-theme input:not([type=submit])', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'radioCheck', ' .uagb-gf-styler__check-style-enabled form .gfield_checkbox label', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'input', ' .chosen-container-single span', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'radioCheck', ' .uagb-gf-styler__check-style-enabled form .gfield_radio label', $combined_selectors );

return UAGB_Helper::generate_all_css( $combined_selectors, '.uagb-block-' . $id );
