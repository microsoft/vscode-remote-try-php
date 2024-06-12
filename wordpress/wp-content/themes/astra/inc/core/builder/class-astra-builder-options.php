<?php
/**
 * Astra Builder Options default values.
 *
 * @package astra-builder
 */

// No direct access, please.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'astra_theme_defaults', 'astra_hf_builder_customizer_defaults' );

/**
 * Return defaults for Builder Options.
 *
 * @param array $defaults exsiting options array.
 * @return array
 */
function astra_hf_builder_customizer_defaults( $defaults ) {

	$astra_options                              = Astra_Theme_Options::get_astra_options();
	$astra_update_footer_row_vertical_alignment = Astra_Dynamic_CSS::astra_4_5_0_compatibility();
	$blog_update                                = Astra_Dynamic_CSS::astra_4_6_0_compatibility();

	/**
	 * Update Astra default color and typography values. To not update directly on existing users site, added backwards.
	 *
	 * @since 4.0.0
	 */
	$apply_new_default_color_typo_values = Astra_Dynamic_CSS::astra_check_default_color_typo();

	/**
	 * Header Builder - Desktop Defaults.
	 */
	$defaults['header-desktop-items'] = array(
		'popup'   => array( 'popup_content' => array( 'mobile-menu' ) ),
		'above'   =>
			array(
				'above_left'         => array(),
				'above_left_center'  => array(),
				'above_center'       => array(),
				'above_right_center' => array(),
				'above_right'        => array(),
			),
		'primary' =>
			array(
				'primary_left'         => array( 'logo' ),
				'primary_left_center'  => array(),
				'primary_center'       => array(),
				'primary_right_center' => array(),
				'primary_right'        => array( 'menu-1' ),
			),
		'below'   =>
			array(
				'below_left'         => array(),
				'below_left_center'  => array(),
				'below_center'       => array(),
				'below_right_center' => array(),
				'below_right'        => array(),
			),
	);

	/**
	 * Header Builder - Mobile Defaults.
	 */
	$defaults['header-mobile-items'] = array(
		'popup'   => array( 'popup_content' => array( 'mobile-menu' ) ),
		'above'   =>
			array(
				'above_left'   => array(),
				'above_center' => array(),
				'above_right'  => array(),
			),
		'primary' =>
			array(
				'primary_left'   => array( 'logo' ),
				'primary_center' => array(),
				'primary_right'  => array( 'mobile-trigger' ),
			),
		'below'   =>
			array(
				'below_left'   => array(),
				'below_center' => array(),
				'below_right'  => array(),
			),
	);

	/**
	 * Primary Header Defaults.
	 */
	$defaults['hb-header-main-layout-width'] = 'content';
	$defaults['hb-header-height']            = array(
		'desktop' => ( false === astra_check_is_structural_setup() ) ? 70 : 80,
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults['hb-stack']                    = array(
		'desktop' => 'stack',
		'tablet'  => 'stack',
		'mobile'  => 'stack',
	);

	$defaults['hb-header-main-sep']          = 1;
	$defaults['hb-header-main-sep-color']    = '#eaeaea';
	$defaults['hb-header-main-menu-align']   = 'inline';
	$defaults['hb-header-bg-obj-responsive'] = array(
		'desktop' => array(
			'background-color'      => '#ffffff',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'tablet'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'mobile'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
	);

	$defaults['hb-header-spacing'] = array(
		'desktop'      => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'tablet'       => array(
			'top'    => '1.5',
			'right'  => '',
			'bottom' => '1.5',
			'left'   => '',
		),
		'mobile'       => array(
			'top'    => '1',
			'right'  => '',
			'bottom' => '1',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'em',
		'mobile-unit'  => 'em',
	);

	/**
	 * Above Header Defaults.
	 */
	$defaults['hba-header-layout']                  = 'above-header-layout-1';
	$defaults['hba-header-height']                  = array(
		'desktop' => 50,
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults['hba-stack']                          = array(
		'desktop' => 'stack',
		'tablet'  => 'stack',
		'mobile'  => 'stack',
	);
	$defaults['hba-header-separator']               = 1;
	$defaults['hba-header-bottom-border-color']     = '#eaeaea';
	$defaults['hba-header-bg-obj-responsive']       = array(
		'desktop' => array(
			'background-color'      => '#ffffff',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'tablet'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'mobile'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
	);
	$defaults['hba-header-text-color-responsive']   = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults['hba-header-link-color-responsive']   = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults['hba-header-link-h-color-responsive'] = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults['hba-header-spacing']                 = array(
		'desktop'      => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'tablet'       => array(
			'top'    => '0',
			'right'  => '',
			'bottom' => '0',
			'left'   => '',
		),
		'mobile'       => array(
			'top'    => '0.5',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'em',
	);

	/**
	 * Logo defaults.
	 */
	$defaults['ast-header-responsive-logo-width'] = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);

	/**
	 * Below Header Defaults.
	 */
	$defaults['hbb-header-layout'] = 'below-header-layout-1';
	$defaults['hbb-header-height'] = array(
		'desktop' => 60,
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults['hbb-stack']         = array(
		'desktop' => 'stack',
		'tablet'  => 'stack',
		'mobile'  => 'stack',
	);

	$defaults['hbb-header-separator']           = 1;
	$defaults['hbb-header-bottom-border-color'] = '#eaeaea';
	$defaults['hbb-header-bg-obj-responsive']   = array(
		'desktop' => array(
			'background-color'      => '#eeeeee',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'tablet'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'mobile'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
	);
	$defaults['hbb-header-spacing']             = array(
		'desktop'      => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'tablet'       => array(
			'top'    => '1',
			'right'  => '',
			'bottom' => '1',
			'left'   => '',
		),
		'mobile'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'em',
		'mobile-unit'  => 'px',
	);


	$margin_defaults = array(
		'section-footer-builder-layout-padding',
		'section-footer-builder-layout-margin',
		'section-above-header-builder-padding',
		'section-above-header-builder-margin',
		'section-below-header-builder-padding',
		'section-below-header-builder-margin',
		'section-header-mobile-trigger-margin',
		'section-primary-header-builder-padding',
		'section-primary-header-builder-margin',
		'title_tagline-margin',
		'section-header-search-margin',
		'header-account-margin',
		'header-mobile-menu-menu-spacing',
		'section-header-mobile-menu-margin',
		'section-above-footer-builder-padding',
		'section-above-footer-builder-margin',
		'section-below-footer-builder-margin',
		'section-footer-copyright-margin',
		'section-footer-menu-margin',
		'section-primary-footer-builder-padding',
		'section-primary-footer-builder-margin',
		'section-header-woo-cart-padding',
		'section-header-woo-cart-margin',
	);

	foreach ( $margin_defaults as $margin_default ) {
		$defaults[ $margin_default ] = Astra_Builder_Helper::$default_responsive_spacing;
	}

	for ( $index = 1; $index <= Astra_Builder_Helper::$component_limit; $index++ ) {

		$defaults = astra_prepare_button_defaults( $defaults, absint( $index ) );
		$defaults = astra_prepare_html_defaults( $defaults, absint( $index ) );
		$defaults = astra_prepare_social_icon_defaults( $defaults, absint( $index ) );
		$defaults = astra_prepare_widget_defaults( $defaults, absint( $index ) );
		$defaults = astra_prepare_menu_defaults( $defaults, absint( $index ) );
		$defaults = astra_prepare_divider_defaults( $defaults, absint( $index ) );
	}

	/**
	 * Header Types - Defaults
	 */
	$defaults['transparent-header-main-sep']       = ( false === astra_get_transparent_header_default_value() ) ? '' : 0;
	$defaults['transparent-header-main-sep-color'] = '';

	/**
	 * Header > Sticky Defaults.
	 */
	$defaults['sticky-header-on-devices'] = 'desktop';
	$defaults['sticky-header-style']      = 'none';

	/**
	 * Footer Builder - Desktop Defaults.
	 */
	$defaults['footer-desktop-items'] = array(
		'above'   =>
			array(
				'above_1' => array(),
				'above_2' => array(),
				'above_3' => array(),
				'above_4' => array(),
				'above_5' => array(),
			),
		'primary' =>
			array(
				'primary_1' => array(),
				'primary_2' => array(),
				'primary_3' => array(),
				'primary_4' => array(),
				'primary_5' => array(),
			),
		'below'   =>
			array(
				'below_1' => array( 'copyright' ),
				'below_2' => array(),
				'below_3' => array(),
				'below_4' => array(),
				'below_5' => array(),
			),
	);

	/**
	 * Above Footer Defaults.
	 */
	$defaults['hba-footer-height'] = 60;
	$defaults['hba-footer-column'] = '2';
	$defaults['hba-footer-layout'] = array(
		'desktop' => '2-equal',
		'tablet'  => '2-equal',
		'mobile'  => 'full',
	);

	/**
	 * Footer - Defaults
	 */
	$defaults['hba-footer-bg-obj-responsive'] = array(
		'desktop' => array(
			'background-color'      => '#eeeeee',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'tablet'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'mobile'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
	);
	$defaults['hbb-footer-bg-obj-responsive'] = array(
		'desktop' => array(
			'background-color'      => $apply_new_default_color_typo_values ? 'var(--ast-global-color-5)' : '#eeeeee',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'tablet'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'mobile'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
	);
	$defaults['hb-footer-bg-obj-responsive']  = array(
		'desktop' => array(
			'background-color'      => '#f9f9f9',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'tablet'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'mobile'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
	);
	$defaults['hbb-footer-top-border-color']  = $blog_update ? '#eaeaea' : 'var(--ast-global-color-6)';
	$defaults['hbb-footer-separator']         = 1;

	/**
	 * Header Margin defaults.
	 */
	$defaults['section-header-builder-layout-margin'] = array(
		'desktop'      => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'tablet'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'mobile'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);

	/**
	 * Below Footer Defaults.
	 */
	$defaults['hbb-footer-height'] = $astra_update_footer_row_vertical_alignment ? 60 : 80;
	$defaults['hbb-footer-column'] = '1';
	$defaults['hbb-footer-layout'] = array(
		'desktop' => 'full',
		'tablet'  => 'full',
		'mobile'  => 'full',
	);

	/**
	 * Primary Footer Default Height.
	 */
	$defaults['hb-primary-footer-height'] = '';

	$defaults['hba-footer-layout-width'] = 'content';
	$defaults['hb-footer-layout-width']  = 'content';
	$defaults['hbb-footer-layout-width'] = 'content';

	$defaults['hba-footer-vertical-alignment'] = 'flex-start';
	$defaults['hb-footer-vertical-alignment']  = 'flex-start';
	$defaults['hbb-footer-vertical-alignment'] = $astra_update_footer_row_vertical_alignment ? 'center' : 'flex-start';

	$defaults['footer-bg-obj-responsive'] = array(
		'desktop' => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'tablet'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'mobile'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
	);

	/**
	 * Primary Footer Defaults.
	 */
	$defaults['hb-footer-column']              = '3';
	$defaults['hb-footer-separator']           = 1;
	$defaults['hb-footer-bottom-border-color'] = '#e6e6e6';
	$defaults['hb-footer-layout']              = array(
		'desktop' => '3-equal',
		'tablet'  => '3-equal',
		'mobile'  => 'full',
	);

	$defaults['hb-footer-main-sep']       = 1;
	$defaults['hb-footer-main-sep-color'] = '#e6e6e6';

	/**
	 * Footer Copyright.
	 */
	$defaults['footer-copyright-editor']              = 'Copyright [copyright] [current_year] [site_title] | Powered by [theme_author]';
	$defaults['footer-copyright-color']               = $apply_new_default_color_typo_values ? 'var(--ast-global-color-3)' : '';
	$defaults['line-height-section-footer-copyright'] = 2;
	$defaults['footer-copyright-alignment']           = array(
		'desktop' => 'center',
		'tablet'  => 'center',
		'mobile'  => 'center',
	);
	$defaults['font-size-section-footer-copyright']   = array(
		'desktop'      => $apply_new_default_color_typo_values ? 16 : '',
		'tablet'       => '',
		'mobile'       => '',
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults['font-weight-section-footer-copyright'] = 'inherit';
	$defaults['font-family-section-footer-copyright'] = 'inherit';
	$defaults['font-extras-section-footer-copyright'] = array(
		'line-height'         => ! isset( $astra_options['font-extras-section-footer-copyright'] ) && isset( $astra_options['line-height-section-footer-copyright'] ) ? $astra_options['line-height-section-footer-copyright'] : '',
		'line-height-unit'    => 'em',
		'letter-spacing'      => '',
		'letter-spacing-unit' => 'px',
		'text-transform'      => ! isset( $astra_options['font-extras-section-footer-copyright'] ) && isset( $astra_options['text-transform-section-footer-copyright'] ) ? $astra_options['text-transform-section-footer-copyright'] : '',
		'text-decoration'     => '',
	);

	$defaults['footer-menu-alignment'] = array(
		'desktop' => 'center',
		'tablet'  => 'center',
		'mobile'  => 'center',
	);

	/**
	 * Footer Below Padding.
	 */
	$defaults['section-below-footer-builder-padding'] = array(
		'desktop'      => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'tablet'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'mobile'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);

	/**
	 * Search.
	 */
	$defaults['header-search-icon-space'] = array(
		'desktop' => 18,
		'tablet'  => 18,
		'mobile'  => 18,
	);

	$defaults['header-search-icon-color'] = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);

	/**
	 * Search Advanced.
	 */
	$defaults['header-search-width']    = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults['live-search']            = false;
	$defaults['live-search-post-types'] = array(
		'post' => 1,
		'page' => 1,
	);

	/**
	 * Transparent Header > Component Configs
	 */
	$defaults['transparent-header-social-icons-color']      = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults['transparent-header-social-icons-h-color']    = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults['transparent-header-social-icons-bg-color']   = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults['transparent-header-social-icons-bg-h-color'] = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);

	$defaults['transparent-header-html-text-color']   = '';
	$defaults['transparent-header-html-link-color']   = '';
	$defaults['transparent-header-html-link-h-color'] = '';

	$defaults['transparent-header-widget-title-color']   = '';
	$defaults['transparent-header-widget-content-color'] = '';
	$defaults['transparent-header-widget-link-color']    = '';
	$defaults['transparent-header-widget-link-h-color']  = '';

	$defaults['transparent-header-button-text-color']   = '';
	$defaults['transparent-header-button-text-h-color'] = '';
	$defaults['transparent-header-button-bg-color']     = '';
	$defaults['transparent-header-button-bg-h-color']   = '';

	/**
	 * Off-Canvas defaults.
	 */
	$defaults['off-canvas-layout']                  = 'side-panel';
	$defaults['off-canvas-slide']                   = 'right';
	$defaults['header-builder-menu-toggle-target']  = 'icon';
	$defaults['header-offcanvas-content-alignment'] = 'flex-start';
	$defaults['off-canvas-background']              = array(
		'background-color'      => '#ffffff',
		'background-image'      => '',
		'background-repeat'     => 'repeat',
		'background-position'   => 'center center',
		'background-size'       => 'auto',
		'background-attachment' => 'scroll',
		'overlay-type'          => '',
		'overlay-color'         => '',
		'overlay-opacity'       => '',
		'overlay-gradient'      => '',
	);
	$defaults['off-canvas-close-color']             = '#3a3a3a';
	$defaults['mobile-header-type']                 = 'dropdown';
	$defaults['off-canvas-inner-spacing']           = '';
	$defaults['footer-menu-layout']                 = array(
		'desktop' => 'horizontal',
		'tablet'  => 'vertical',
		'mobile'  => 'vertical',
	);

	$defaults['footer-menu-bg-obj-responsive'] = array(
		'desktop' => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'tablet'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'mobile'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
	);

	$defaults['footer-menu-color-responsive'] = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);

	$defaults['footer-menu-h-bg-color-responsive'] = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);

	$defaults['footer-menu-h-color-responsive'] = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);

	$defaults['footer-menu-a-bg-color-responsive'] = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);

	$defaults['footer-menu-a-color-responsive'] = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);

	$defaults['footer-menu-font-size']   = array(
		'desktop'      => '',
		'tablet'       => '',
		'mobile'       => '',
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults['footer-menu-font-weight'] = 'inherit';
	$defaults['footer-menu-font-family'] = 'inherit';
	$defaults['footer-menu-font-extras'] = array(
		'line-height'         => ! isset( $astra_options['footer-menu-font-extras'] ) && isset( $astra_options['footer-menu-line-height'] ) ? $astra_options['footer-menu-line-height'] : '',
		'line-height-unit'    => 'em',
		'letter-spacing'      => '',
		'letter-spacing-unit' => 'px',
		'text-transform'      => ! isset( $astra_options['footer-menu-font-extras'] ) && isset( $astra_options['footer-menu-text-transform'] ) ? $astra_options['footer-menu-text-transform'] : '',
		'text-decoration'     => '',
	);

	$defaults['footer-main-menu-spacing'] = array(
		'desktop'      => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'tablet'       => array(
			'top'    => '0',
			'right'  => '20',
			'bottom' => '0',
			'left'   => '20',
		),
		'mobile'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);

	// Mobile Trigger defaults.
	$defaults['header-trigger-icon']                  = 'menu';
	$defaults['mobile-header-toggle-icon-size']       = 20;
	$defaults['mobile-header-toggle-btn-style']       = 'minimal';
	$defaults['mobile-header-toggle-btn-border-size'] = array(
		'top'    => 1,
		'right'  => 1,
		'bottom' => 1,
		'left'   => 1,
	);
	$defaults['mobile-header-toggle-border-radius']   = 2;

	$mobile_header_toggle_border_radius_fields             = ! isset( $astra_options['mobile-header-toggle-border-radius-fields'] ) && isset( $astra_options['mobile-header-toggle-border-radius'] ) ? $astra_options['mobile-header-toggle-border-radius'] : '';
	$defaults['mobile-header-toggle-border-radius-fields'] = array(
		'desktop'      => array(
			'top'    => $mobile_header_toggle_border_radius_fields,
			'right'  => $mobile_header_toggle_border_radius_fields,
			'bottom' => $mobile_header_toggle_border_radius_fields,
			'left'   => $mobile_header_toggle_border_radius_fields,
		),
		'tablet'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'mobile'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);

	/**
	 * Mobile trigger - Label Typography.
	 */
	$defaults['mobile-header-label-font-family']    = 'inherit';
	$defaults['mobile-header-label-font-weight']    = 'inherit';
	$defaults['mobile-header-label-text-transform'] = '';
	$defaults['mobile-header-label-line-height']    = '';
	$defaults['mobile-header-label-font-size']      = '';

	/**
	 * Global Color Palette.
	 */
	$update_colors_for_starter_library = Astra_Dynamic_CSS::astra_4_4_0_compatibility();
	$update_color_for_forms_ui         = Astra_Dynamic_CSS::astra_4_6_0_compatibility();
	if ( $update_color_for_forms_ui ) {
		$color_palette_7 = '#D1D5DB';
	} else {
		$color_palette_7 = $update_colors_for_starter_library ? '#ADB6BE' : '#e2e8f0';
	}
	$defaults['global-color-palette'] = $apply_new_default_color_typo_values ? array(
		'palette' => array(
			'#046bd2',
			'#045cb4',
			'#1e293b',
			'#334155',
			$update_colors_for_starter_library ? '#F0F5FA' : '#f9fafb',
			'#FFFFFF',
			$color_palette_7,
			$update_colors_for_starter_library ? '#111111' : '#cbd5e1',
			$update_colors_for_starter_library ? '#111111' : '#94a3b8',
		),
	)
	:
	array(
		'palette' => array(
			'#0170B9',
			'#3a3a3a',
			'#3a3a3a',
			'#4B4F58',
			'#F5F5F5',
			'#FFFFFF',
			'#E5E5E5',
			'#424242',
			'#000000',
		),
	);

	// Default global SVG values.
	$defaults['header-logo-color'] = '';

	/**
	* Mobile Menu
	*/

	// Specify all the default values for Menu from here.
	$defaults['header-mobile-menu-bg-color']   = '';
	$defaults['header-mobile-menu-color']      = '';
	$defaults['header-mobile-menu-h-bg-color'] = '';
	$defaults['header-mobile-menu-h-color']    = '';
	$defaults['header-mobile-menu-a-bg-color'] = '';
	$defaults['header-mobile-menu-a-color']    = '';

	$defaults['header-mobile-menu-bg-obj-responsive'] = array(
		'desktop' => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'tablet'  => array(
			'background-color'      => $apply_new_default_color_typo_values ? 'var(--ast-global-color-5)' : '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'mobile'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
	);

	$defaults['header-mobile-menu-color-responsive'] = array(
		'desktop' => $apply_new_default_color_typo_values ? 'var(--ast-global-color-3)' : '',
		'tablet'  => $apply_new_default_color_typo_values ? 'var(--ast-global-color-3)' : '',
		'mobile'  => '',
	);

	$defaults['header-mobile-menu-h-color-responsive'] = array(
		'desktop' => $apply_new_default_color_typo_values ? 'var(--ast-global-color-1)' : '',
		'tablet'  => $apply_new_default_color_typo_values ? 'var(--ast-global-color-1)' : '',
		'mobile'  => '',
	);

	$defaults['header-mobile-menu-a-color-responsive'] = array(
		'desktop' => $apply_new_default_color_typo_values ? 'var(--ast-global-color-1)' : '',
		'tablet'  => $apply_new_default_color_typo_values ? 'var(--ast-global-color-1)' : '',
		'mobile'  => '',
	);

	$defaults['header-mobile-menu-h-bg-color-responsive'] = array(
		'desktop' => '',
		'tablet'  => $apply_new_default_color_typo_values ? 'var(--ast-global-color-4)' : '',
		'mobile'  => '',
	);

	$defaults['header-mobile-menu-a-bg-color-responsive'] = array(
		'desktop' => '',
		'tablet'  => $apply_new_default_color_typo_values ? 'var(--ast-global-color-4)' : '',
		'mobile'  => '',
	);

	$defaults['header-mobile-menu-submenu-container-animation'] = 'fade';

		/**
		 * Submenu
		*/
	$defaults['header-mobile-menu-submenu-item-border']  = false;
	$defaults['header-mobile-menu-submenu-item-b-size']  = '1';
	$defaults['header-mobile-menu-submenu-item-b-color'] = '#eaeaea';
	$defaults['header-mobile-menu-submenu-border']       = array(
		'top'    => 2,
		'bottom' => 0,
		'left'   => 0,
		'right'  => 0,
	);


		/**
		 * Menu - Typography.
		*/
	$defaults['header-mobile-menu-font-size'] = array(
		'desktop'      => '',
		'tablet'       => '',
		'mobile'       => '',
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);

	$defaults['font-extras-header-mobile-menu'] = array(
		'line-height'         => ! isset( $astra_options['font-extras-header-mobile-menu'] ) && isset( $astra_options['header-mobile-menu-line-height'] ) ? $astra_options['header-mobile-menu-line-height'] : '',
		'line-height-unit'    => 'em',
		'letter-spacing'      => '',
		'letter-spacing-unit' => 'px',
		'text-transform'      => ! isset( $astra_options['font-extras-header-mobile-menu'] ) && isset( $astra_options['header-mobile-menu-text-transform'] ) ? $astra_options['header-mobile-menu-text-transform'] : '',
		'text-decoration'     => '',
	);

	/**
	 * Woo-Cart.
	 */
	$defaults['woo-header-cart-click-action']              = 'default';
	$defaults['woo-slide-in-cart-width']                   = array(
		'desktop'      => 35,
		'tablet'       => '',
		'mobile'       => '',
		'desktop-unit' => '%',
		'tablet-unit'  => '%',
		'mobile-unit'  => '%',
	);
	$defaults['woo-header-cart-icon-total-label-position'] = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults['header-woo-cart-icon-size']                 = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults['woo-header-cart-icon']                      = 'default';
	$defaults['woo-header-cart-icon-style']                = 'outline';
	$defaults['woo-desktop-cart-flyout-direction']         = 'right';
	$defaults['header-woo-cart-icon-color']                = '';
	$defaults['transparent-header-woo-cart-icon-color']    = '';
	$defaults['header-woo-cart-icon-hover-color']          = '';
	$defaults['woo-header-cart-border-width']              = 2;
	$woo_header_cart_border_radius                         = ! isset( $astra_options['woo-header-cart-icon-radius-fields'] ) && isset( $astra_options['woo-header-cart-icon-radius'] ) ? $astra_options['woo-header-cart-icon-radius'] : '';
	$defaults['woo-header-cart-icon-radius-fields']        = array(
		'desktop'      => array(
			'top'    => $woo_header_cart_border_radius,
			'right'  => $woo_header_cart_border_radius,
			'bottom' => $woo_header_cart_border_radius,
			'left'   => $woo_header_cart_border_radius,
		),
		'tablet'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'mobile'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults['woo-header-cart-badge-display']             = true;
	// Woo Cart - Dynamic label default value.
	$defaults['woo-header-cart-label-display'] = '';

	// Cart tray > General Color styles.
	$defaults['header-woo-cart-text-color']             = '';
	$defaults['header-woo-cart-link-color']             = '';
	$defaults['header-woo-cart-background-color']       = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults['header-woo-cart-background-hover-color'] = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults['header-woo-cart-separator-color']        = '';
	$defaults['header-woo-cart-link-hover-color']       = '';

	// Cart tray > Cart Button styles.
	$defaults['header-woo-cart-btn-text-color']       = '';
	$defaults['header-woo-cart-btn-background-color'] = '';
	$defaults['header-woo-cart-btn-text-hover-color'] = '';
	$defaults['header-woo-cart-btn-bg-hover-color']   = '';

	// Cart tray > Checkout Button styles.
	$defaults['header-woo-checkout-btn-text-color']       = '';
	$defaults['header-woo-checkout-btn-background-color'] = '';
	$defaults['header-woo-checkout-btn-text-hover-color'] = '';
	$defaults['header-woo-checkout-btn-bg-hover-color']   = '';

	/**
	 * EDD-Cart.
	*/
	$defaults['edd-header-cart-icon-style']             = 'outline';
	$defaults['edd-header-cart-icon-color']             = '';
	$defaults['edd-header-cart-icon-radius']            = 3;
	$defaults['transparent-header-edd-cart-icon-color'] = '';
	$defaults['edd-header-cart-total-display']          = true;
	$defaults['edd-header-cart-title-display']          = true;

	// Cart tray > General Color styles.
	$defaults['header-edd-cart-text-color']       = '';
	$defaults['header-edd-cart-link-color']       = '';
	$defaults['header-edd-cart-background-color'] = '';
	$defaults['header-edd-cart-separator-color']  = '';

	// Cart tray > Checkout Button styles.
	$defaults['header-edd-checkout-btn-text-color']       = '';
	$defaults['header-edd-checkout-btn-background-color'] = '';
	$defaults['header-edd-checkout-btn-text-hover-color'] = '';
	$defaults['header-edd-checkout-btn-bg-hover-color']   = '';

	/**
	 * Account element.
	*/
	$defaults['header-account-type']                                  = 'default';
	$defaults['header-account-login-style']                           = 'icon';
	$defaults['header-account-login-style-extend-text-profile-type']  = 'default';
	$defaults['header-account-action-type']                           = 'link';
	$defaults['header-account-link-type']                             = 'default';
	$defaults['header-account-logout-style']                          = 'icon';
	$defaults['header-account-logout-style-extend-text-profile-type'] = 'default';
	$defaults['header-account-logged-out-text']                       = __( 'Log In', 'astra' );
	$defaults['header-account-logged-in-text']                        = __( 'My Account', 'astra' );
	$defaults['header-account-logout-action']                         = 'link';
	$defaults['header-account-image-width']                           = array(
		'desktop' => '40',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults['header-account-icon-size']                             = array(
		'desktop' => 18,
		'tablet'  => 18,
		'mobile'  => 18,
	);

	$defaults['header-account-icon-color'] = '';

	$defaults['header-account-login-link'] = array(
		'url'      => '',
		'new_tab'  => false,
		'link_rel' => '',
	);

	$defaults['header-account-logout-link'] = array(
		'url'      => esc_url( wp_login_url() ),
		'new_tab'  => false,
		'link_rel' => '',
	);

	$defaults['font-size-section-header-account'] = array(
		'desktop'      => '',
		'tablet'       => '',
		'mobile'       => '',
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);

	$defaults['header-account-type-text-color'] = '';
	$defaults['header-account-woo-menu']        = false;

	$defaults['cloned-component-track'] = Astra_Builder_Helper::$component_count_array;

	return $defaults;
}

/**
 * Prepare Divider Defaults.
 *
 * @param array   $defaults defaults.
 * @param integer $index index.
 */
function astra_prepare_divider_defaults( $defaults, $index ) {

	$defaults[ 'section-hb-divider-' . $index . '-margin' ] = Astra_Builder_Helper::$default_responsive_spacing;
	$defaults[ 'section-fb-divider-' . $index . '-margin' ] = Astra_Builder_Helper::$default_responsive_spacing;

	return $defaults;
}

/**
 * Prepare Button Defaults.
 *
 * @param array   $defaults defaults.
 * @param integer $index index.
 */
function astra_prepare_button_defaults( $defaults, $index ) {

	/**
	 * Update Astra default color and typography values. To not update directly on existing users site, added backwards.
	 *
	 * @since 4.0.0
	 */
	$apply_new_default_color_typo_values = Astra_Dynamic_CSS::astra_check_default_color_typo();

	$astra_options = Astra_Theme_Options::get_astra_options();

	$_prefix = 'button' . $index;

	$defaults[ 'header-' . $_prefix . '-text' ]                 = __( 'Button', 'astra' );
	$defaults[ 'header-' . $_prefix . '-link-option' ]          = array(
		'url'      => apply_filters( 'astra_site_url', 'https://www.wpastra.com' ),
		'new_tab'  => false,
		'link_rel' => '',
	);
	$defaults[ 'header-' . $_prefix . '-font-family' ]          = 'inherit';
	$defaults[ 'header-' . $_prefix . '-font-weight' ]          = 'inherit';
	$defaults[ 'header-' . $_prefix . '-font-extras' ]          = array(
		'line-height'         => ! isset( $astra_options[ 'header-' . $_prefix . '-font-extras' ] ) && isset( $astra_options[ 'header-' . $_prefix . '-line-height' ] ) ? $astra_options[ 'header-' . $_prefix . '-line-height' ] : '',
		'line-height-unit'    => 'em',
		'letter-spacing'      => '',
		'letter-spacing-unit' => 'px',
		'text-transform'      => ! isset( $astra_options[ 'header-' . $_prefix . '-font-extras' ] ) && isset( $astra_options[ 'header-' . $_prefix . '-text-transform' ] ) ? $astra_options[ 'header-' . $_prefix . '-text-transform' ] : '',
		'text-decoration'     => '',
	);
	$defaults[ 'header-' . $_prefix . '-font-size' ]            = array(
		'desktop'      => '',
		'tablet'       => '',
		'mobile'       => '',
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'header-' . $_prefix . '-text-color' ]           = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'header-' . $_prefix . '-back-color' ]           = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'header-' . $_prefix . '-text-h-color' ]         = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'header-' . $_prefix . '-back-h-color' ]         = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'header-' . $_prefix . '-padding' ]              = array(
		'desktop'      => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'tablet'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'mobile'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'header-' . $_prefix . '-border-size' ]          = array(
		'top'    => '',
		'right'  => '',
		'bottom' => '',
		'left'   => '',
	);
	$defaults[ 'header-' . $_prefix . '-border-color' ]         = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$header_button_border_radius_fields                         = ! isset( $astra_options[ 'header-' . $_prefix . '-border-radius-fields' ] ) && isset( $astra_options[ 'header-' . $_prefix . '-border-radius' ] ) ? $astra_options[ 'header-' . $_prefix . '-border-radius' ] : '';
	$defaults[ 'header-' . $_prefix . '-border-radius-fields' ] = array(
		'desktop'      => array(
			'top'    => $header_button_border_radius_fields,
			'right'  => $header_button_border_radius_fields,
			'bottom' => $header_button_border_radius_fields,
			'left'   => $header_button_border_radius_fields,
		),
		'tablet'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'mobile'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);

	$defaults[ 'header-' . $_prefix . '-border-radius' ] = '';

	$astra_4_6_4_compatibility = Astra_Dynamic_CSS::astra_4_6_4_compatibility();
	$legacy_hb_button_padding  = $apply_new_default_color_typo_values ? Astra_Builder_Helper::$default_button_responsive_spacing : Astra_Builder_Helper::$default_responsive_spacing;

	/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	$builder_button_top_padding = isset( $astra_options[ 'section-hb-button-' . $index . '-padding' ]['desktop']['top'] ) ? $astra_options[ 'section-hb-button-' . $index . '-padding' ]['desktop']['top'] : $legacy_hb_button_padding['desktop']['top'];
	/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	$builder_button_right_padding = isset( $astra_options[ 'section-hb-button-' . $index . '-padding' ]['desktop']['right'] ) ? $astra_options[ 'section-hb-button-' . $index . '-padding' ]['desktop']['right'] : $legacy_hb_button_padding['desktop']['right'];
	/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	$builder_button_bottom_padding = isset( $astra_options[ 'section-hb-button-' . $index . '-padding' ]['desktop']['bottom'] ) ? $astra_options[ 'section-hb-button-' . $index . '-padding' ]['desktop']['bottom'] : $legacy_hb_button_padding['desktop']['bottom'];
	/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	$builder_button_left_padding = isset( $astra_options[ 'section-hb-button-' . $index . '-padding' ]['desktop']['left'] ) ? $astra_options[ 'section-hb-button-' . $index . '-padding' ]['desktop']['left'] : $legacy_hb_button_padding['desktop']['left'];
	/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

	$defaults[ 'section-hb-button-' . $index . '-padding' ]         = array(
		'desktop'      => array(
			'top'    => $astra_4_6_4_compatibility ? 15 : $builder_button_top_padding,
			'right'  => $astra_4_6_4_compatibility ? 30 : $builder_button_right_padding,
			'bottom' => $astra_4_6_4_compatibility ? 15 : $builder_button_bottom_padding,
			'left'   => $astra_4_6_4_compatibility ? 30 : $builder_button_left_padding,
		),
		'tablet'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'mobile'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'header-button' . $index . '-border-radius-fields' ] = array(
		'desktop'      => array(
			'top'    => $astra_4_6_4_compatibility ? 40 : '',
			'right'  => $astra_4_6_4_compatibility ? 40 : '',
			'bottom' => $astra_4_6_4_compatibility ? 40 : '',
			'left'   => $astra_4_6_4_compatibility ? 40 : '',
		),
		'tablet'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'mobile'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'section-hb-button-' . $index . '-margin' ]          = Astra_Builder_Helper::$default_responsive_spacing;
	$defaults[ 'sticky-header-button' . $index . '-padding' ]       = Astra_Builder_Helper::$default_responsive_spacing;


	$_prefix = 'button' . $index;

	$defaults[ 'footer-' . $_prefix . '-text' ]                 = __( 'Button', 'astra' );
	$defaults[ 'footer-' . $_prefix . '-link-option' ]          = array(
		'url'      => apply_filters( 'astra_site_url', 'https://www.wpastra.com' ),
		'new_tab'  => false,
		'link_rel' => '',
	);
	$defaults[ 'footer-' . $_prefix . '-font-family' ]          = 'inherit';
	$defaults[ 'footer-' . $_prefix . '-font-weight' ]          = 'inherit';
	$defaults[ 'footer-' . $_prefix . '-text-transform' ]       = '';
	$defaults[ 'footer-' . $_prefix . '-line-height' ]          = '';
	$defaults[ 'footer-' . $_prefix . '-font-size' ]            = array(
		'desktop'      => '',
		'tablet'       => '',
		'mobile'       => '',
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'footer-' . $_prefix . '-text-color' ]           = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'footer-' . $_prefix . '-back-color' ]           = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'footer-' . $_prefix . '-text-h-color' ]         = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'footer-' . $_prefix . '-back-h-color' ]         = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'footer-' . $_prefix . '-padding' ]              = array(
		'desktop'      => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'tablet'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'mobile'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'footer-' . $_prefix . '-border-size' ]          = array(
		'top'    => '',
		'right'  => '',
		'bottom' => '',
		'left'   => '',
	);
	$defaults[ 'footer-' . $_prefix . '-border-color' ]         = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$footer_button_border_radius_fields                         = ! isset( $astra_options[ 'footer-' . $_prefix . '-border-radius-fields' ] ) && isset( $astra_options[ 'footer-' . $_prefix . '-border-radius' ] ) ? $astra_options[ 'footer-' . $_prefix . '-border-radius' ] : '';
	$defaults[ 'footer-' . $_prefix . '-border-radius-fields' ] = array(
		'desktop'      => array(
			'top'    => $footer_button_border_radius_fields,
			'right'  => $footer_button_border_radius_fields,
			'bottom' => $footer_button_border_radius_fields,
			'left'   => $footer_button_border_radius_fields,
		),
		'tablet'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'mobile'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'footer-button-' . $index . '-alignment' ]       = array(
		'desktop' => 'center',
		'tablet'  => 'center',
		'mobile'  => 'center',
	);

	$legacy_fb_button_padding = Astra_Builder_Helper::$default_responsive_spacing;
	/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	$builder_button_top_padding = isset( $astra_options[ 'section-fb-button-' . $index . '-padding' ]['desktop']['top'] ) ? $astra_options[ 'section-fb-button-' . $index . '-padding' ]['desktop']['top'] : $legacy_fb_button_padding['desktop']['top'];
	/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	$builder_button_right_padding = isset( $astra_options[ 'section-fb-button-' . $index . '-padding' ]['desktop']['right'] ) ? $astra_options[ 'section-fb-button-' . $index . '-padding' ]['desktop']['right'] : $legacy_fb_button_padding['desktop']['right'];
	/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	$builder_button_bottom_padding = isset( $astra_options[ 'section-fb-button-' . $index . '-padding' ]['desktop']['bottom'] ) ? $astra_options[ 'section-fb-button-' . $index . '-padding' ]['desktop']['bottom'] : $legacy_fb_button_padding['desktop']['bottom'];
	/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	$builder_button_left_padding = isset( $astra_options[ 'section-fb-button-' . $index . '-padding' ]['desktop']['left'] ) ? $astra_options[ 'section-fb-button-' . $index . '-padding' ]['desktop']['left'] : $legacy_fb_button_padding['desktop']['left'];
	/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

	$defaults[ 'section-fb-button-' . $index . '-padding' ]         = array(
		'desktop'      => array(
			'top'    => $astra_4_6_4_compatibility ? 15 : $builder_button_top_padding,
			'right'  => $astra_4_6_4_compatibility ? 30 : $builder_button_right_padding,
			'bottom' => $astra_4_6_4_compatibility ? 15 : $builder_button_bottom_padding,
			'left'   => $astra_4_6_4_compatibility ? 30 : $builder_button_left_padding,
		),
		'tablet'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'mobile'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'section-fb-button-' . $index . '-margin' ]          = Astra_Builder_Helper::$default_responsive_spacing;
	$defaults[ 'footer-button' . $index . '-border-radius-fields' ] = array(
		'desktop'      => array(
			'top'    => $astra_4_6_4_compatibility ? 40 : '',
			'right'  => $astra_4_6_4_compatibility ? 40 : '',
			'bottom' => $astra_4_6_4_compatibility ? 40 : '',
			'left'   => $astra_4_6_4_compatibility ? 40 : '',
		),
		'tablet'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'mobile'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);

	return $defaults;
}

/**
 * Prepare HTML Defaults.
 *
 * @param array   $defaults defaults.
 * @param integer $index index.
 */
function astra_prepare_html_defaults( $defaults, $index ) {

	$astra_options = Astra_Theme_Options::get_astra_options();

	$_section = 'section-hb-html-' . $index;

	$defaults[ 'header-html-' . $index ]                  = __( 'Insert HTML text here.', 'astra' );
	$defaults[ 'header-html-' . $index . 'color' ]        = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'header-html-' . $index . 'link-color' ]   = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'header-html-' . $index . 'link-h-color' ] = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'font-size-' . $_section ]                 = array(
		'desktop'      => 15,
		'tablet'       => '',
		'mobile'       => '',
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'font-weight-' . $_section ]               = 'inherit';
	$defaults[ 'font-family-' . $_section ]               = 'inherit';
	$defaults[ 'font-extras-' . $_section ]               = array(
		'line-height'         => ! isset( $astra_options[ 'font-extras-' . $_section ] ) && isset( $astra_options[ 'line-height-' . $_section ] ) ? $astra_options[ 'line-height-' . $_section ] : '',
		'line-height-unit'    => 'em',
		'letter-spacing'      => '',
		'letter-spacing-unit' => 'px',
		'text-transform'      => ! isset( $astra_options[ 'font-extras-' . $_section ] ) && isset( $astra_options[ 'text-transform-' . $_section ] ) ? $astra_options[ 'text-transform-' . $_section ] : '',
		'text-decoration'     => '',
	);

	$defaults[ 'section-hb-html-' . $index . '-margin' ] = Astra_Builder_Helper::$default_responsive_spacing;

	$_section = 'section-fb-html-' . $index;

	$defaults[ 'footer-html-' . $index ]                  = __( 'Insert HTML text here.', 'astra' );
	$defaults[ 'footer-html-' . $index . 'color' ]        = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'footer-html-' . $index . 'link-color' ]   = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'footer-html-' . $index . 'link-h-color' ] = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'font-size-' . $_section ]                 = array(
		'desktop'      => 15,
		'tablet'       => '',
		'mobile'       => '',
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'footer-html-' . $index . '-alignment' ]   = array(
		'desktop' => 'center',
		'tablet'  => 'center',
		'mobile'  => 'center',
	);
	$defaults[ 'font-size-' . $_section ]                 = array(
		'desktop'      => '',
		'tablet'       => '',
		'mobile'       => '',
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'font-weight-' . $_section ]               = 'inherit';
	$defaults[ 'font-family-' . $_section ]               = 'inherit';
	$defaults[ 'font-extras-' . $_section ]               = array(
		'line-height'         => ! isset( $astra_options[ 'font-extras-' . $_section ] ) && isset( $astra_options[ 'line-height-' . $_section ] ) ? $astra_options[ 'line-height-' . $_section ] : '',
		'line-height-unit'    => 'em',
		'letter-spacing'      => '',
		'letter-spacing-unit' => 'px',
		'text-transform'      => ! isset( $astra_options[ 'font-extras-' . $_section ] ) && isset( $astra_options[ 'text-transform-' . $_section ] ) ? $astra_options[ 'text-transform-' . $_section ] : '',
		'text-decoration'     => '',
	);

	$defaults[ 'section-fb-html-' . $index . '-margin' ] = Astra_Builder_Helper::$default_responsive_spacing;

	return $defaults;
}

/**
 * Prepare Social Icon Defaults.
 *
 * @param array   $defaults defaults.
 * @param integer $index index.
 */
function astra_prepare_social_icon_defaults( $defaults, $index ) {

	$astra_options = Astra_Theme_Options::get_astra_options();

	$defaults[ 'header-social-' . $index . '-space' ]              = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'header-social-' . $index . '-bg-space' ]           = '';
	$defaults[ 'header-social-' . $index . '-size' ]               = array(
		'desktop' => 18,
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'header-social-' . $index . '-radius-fields' ]      = array(
		'desktop'      => array(
			'top'    => ! isset( $astra_options[ 'header-social-' . $index . '-radius' ] ) ? '' : $astra_options[ 'header-social-' . $index . '-radius' ],
			'right'  => ! isset( $astra_options[ 'header-social-' . $index . '-radius' ] ) ? '' : $astra_options[ 'header-social-' . $index . '-radius' ],
			'bottom' => ! isset( $astra_options[ 'header-social-' . $index . '-radius' ] ) ? '' : $astra_options[ 'header-social-' . $index . '-radius' ],
			'left'   => ! isset( $astra_options[ 'header-social-' . $index . '-radius' ] ) ? '' : $astra_options[ 'header-social-' . $index . '-radius' ],
		),
		'tablet'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'mobile'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'header-social-' . $index . '-color' ]              = '';
	$defaults[ 'header-social-' . $index . '-h-color' ]            = '';
	$defaults[ 'header-social-' . $index . '-bg-color' ]           = '';
	$defaults[ 'header-social-' . $index . '-bg-h-color' ]         = '';
	$defaults[ 'header-social-' . $index . '-label-toggle' ]       = false;
	$defaults[ 'header-social-' . $index . '-color-type' ]         = 'custom';
	$defaults[ 'header-social-' . $index . '-brand-hover-toggle' ] = false;
	$defaults[ 'font-size-section-hb-social-icons-' . $index ]     = array(
		'desktop'      => '',
		'tablet'       => '',
		'mobile'       => '',
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'header-social-icons-' . $index ]                   = array(
		'items' =>
			array(
				array(
					'id'         => 'facebook',
					'enabled'    => true,
					'source'     => 'icon',
					'url'        => '',
					'color'      => '#557dbc',
					'background' => 'transparent',
					'icon'       => 'facebook',
					'label'      => 'Facebook',
				),
				array(
					'id'         => 'twitter',
					'enabled'    => true,
					'source'     => 'icon',
					'url'        => '',
					'color'      => '#7acdee',
					'background' => 'transparent',
					'icon'       => 'twitter',
					'label'      => 'Twitter',
				),
				array(
					'id'         => 'instagram',
					'enabled'    => true,
					'source'     => 'icon',
					'url'        => '',
					'color'      => '#8a3ab9',
					'background' => 'transparent',
					'icon'       => 'instagram',
					'label'      => 'Instagram',
				),
			),
	);

	$defaults[ 'section-hb-social-icons-' . $index . '-margin' ] = Astra_Builder_Helper::$default_responsive_spacing;


	$defaults[ 'footer-social-' . $index . '-space' ]              = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'footer-social-' . $index . '-bg-space' ]           = '';
	$defaults[ 'footer-social-' . $index . '-size' ]               = array(
		'desktop' => 18,
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'footer-social-' . $index . '-radius' ]             = '';
	$defaults[ 'footer-social-' . $index . '-color' ]              = '';
	$defaults[ 'footer-social-' . $index . '-h-color' ]            = '';
	$defaults[ 'footer-social-' . $index . '-bg-color' ]           = '';
	$defaults[ 'footer-social-' . $index . '-bg-h-color' ]         = '';
	$defaults[ 'footer-social-' . $index . '-label-toggle' ]       = false;
	$defaults[ 'footer-social-' . $index . '-color-type' ]         = 'custom';
	$defaults[ 'footer-social-' . $index . '-brand-color' ]        = '';
	$defaults[ 'footer-social-' . $index . '-brand-label-color' ]  = '';
	$defaults[ 'footer-social-' . $index . '-brand-hover-toggle' ] = false;
	$defaults[ 'font-size-section-fb-social-icons-' . $index ]     = array(
		'desktop'      => '',
		'tablet'       => '',
		'mobile'       => '',
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'footer-social-icons-' . $index ]                   = array(
		'items' =>
			array(
				array(
					'id'         => 'facebook',
					'enabled'    => true,
					'source'     => 'icon',
					'url'        => '',
					'color'      => '#557dbc',
					'background' => 'transparent',
					'icon'       => 'facebook',
					'label'      => 'Facebook',
				),
				array(
					'id'         => 'twitter',
					'enabled'    => true,
					'source'     => 'icon',
					'url'        => '',
					'color'      => '#7acdee',
					'background' => 'transparent',
					'icon'       => 'twitter',
					'label'      => 'Twitter',
				),
				array(
					'id'         => 'instagram',
					'enabled'    => true,
					'source'     => 'icon',
					'url'        => '',
					'color'      => '#8a3ab9',
					'background' => 'transparent',
					'icon'       => 'instagram',
					'label'      => 'Instagram',
				),
			),
	);
	$defaults[ 'footer-social-' . $index . '-alignment' ]          = array(
		'desktop' => 'center',
		'tablet'  => 'center',
		'mobile'  => 'center',
	);

	$defaults[ 'section-fb-social-icons-' . $index . '-margin' ] = Astra_Builder_Helper::$default_responsive_spacing;

	return $defaults;
}

/**
 * Prepare Widget Defaults.
 *
 * @param array   $defaults defaults.
 * @param integer $index index.
 */
function astra_prepare_widget_defaults( $defaults, $index ) {

	$astra_options                       = Astra_Theme_Options::get_astra_options();
	$apply_new_default_color_typo_values = Astra_Dynamic_CSS::astra_check_default_color_typo();

	// Widget Header defaults.

	// Colors.
	$defaults[ 'header-widget-' . $index . '-title-color' ]  = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'header-widget-' . $index . '-color' ]        = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'header-widget-' . $index . '-link-color' ]   = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'header-widget-' . $index . '-link-h-color' ] = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);

	// Title Typography.
	$defaults[ 'header-widget-' . $index . '-font-family' ] = 'inherit';
	$defaults[ 'header-widget-' . $index . '-font-weight' ] = 'inherit';
	$defaults[ 'header-widget-' . $index . '-font-size' ]   = array(
		'desktop'      => '',
		'tablet'       => '',
		'mobile'       => '',
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'header-widget-' . $index . '-font-extras' ] = array(
		'line-height'         => ! isset( $astra_options[ 'header-widget-' . $index . '-font-extras' ] ) && isset( $astra_options[ 'header-widget-' . $index . '-line-height' ] ) ? $astra_options[ 'header-widget-' . $index . '-line-height' ] : '',
		'line-height-unit'    => 'em',
		'letter-spacing'      => ! isset( $astra_options[ 'header-widget-' . $index . '-font-extras' ] ) && isset( $astra_options[ 'header-widget-' . $index . '-letter-spacing' ] ) ? $astra_options[ 'header-widget-' . $index . '-letter-spacing' ] : '',
		'letter-spacing-unit' => 'px',
		'text-transform'      => ! isset( $astra_options[ 'header-widget-' . $index . '-font-extras' ] ) && isset( $astra_options[ 'header-widget-' . $index . '-text-transform' ] ) ? $astra_options[ 'header-widget-' . $index . '-text-transform' ] : '',
		'text-decoration'     => '',
	);


	// Content Typography.
	$defaults[ 'header-widget-' . $index . '-content-font-family' ] = 'inherit';
	$defaults[ 'header-widget-' . $index . '-content-font-weight' ] = 'inherit';
	$defaults[ 'header-widget-' . $index . '-content-font-size' ]   = array(
		'desktop'      => '',
		'tablet'       => '',
		'mobile'       => '',
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'header-widget-' . $index . '-content-font-extras' ] = array(
		'line-height'         => ! isset( $astra_options[ 'header-widget-' . $index . '-content-font-extras' ] ) && isset( $astra_options[ 'header-widget-' . $index . '-content-line-height' ] ) ? $astra_options[ 'header-widget-' . $index . '-content-line-height' ] : '',
		'line-height-unit'    => 'em',
		'letter-spacing'      => ! isset( $astra_options[ 'header-widget-' . $index . '-content-font-extras' ] ) && isset( $astra_options[ 'header-widget-' . $index . '-content-letter-spacing' ] ) ? $astra_options[ 'header-widget-' . $index . '-content-letter-spacing' ] : '',
		'letter-spacing-unit' => 'px',
		'text-transform'      => ! isset( $astra_options[ 'header-widget-' . $index . '-content-font-extras' ] ) && isset( $astra_options[ 'header-widget-' . $index . '-content-transform' ] ) ? $astra_options[ 'header-widget-' . $index . '-content-transform' ] : '',
		'text-decoration'     => '',
	);

	$defaults[ 'sidebar-widgets-header-widget-' . $index . '-margin' ] = Astra_Builder_Helper::$default_responsive_spacing;

	// Widget Footer defaults.

	// Colors.
	$defaults[ 'footer-widget-' . $index . '-title-color' ]  = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'footer-widget-' . $index . '-color' ]        = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'footer-widget-' . $index . '-link-color' ]   = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);
	$defaults[ 'footer-widget-' . $index . '-link-h-color' ] = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);

	// Title Typography.
	$defaults[ 'footer-widget-' . $index . '-font-family' ]    = 'inherit';
	$defaults[ 'footer-widget-' . $index . '-font-weight' ]    = 'inherit';
	$defaults[ 'footer-widget-' . $index . '-text-transform' ] = '';
	$defaults[ 'footer-widget-' . $index . '-line-height' ]    = '';
	$defaults[ 'footer-widget-' . $index . '-font-size' ]      = array(
		'desktop'      => '',
		'tablet'       => '',
		'mobile'       => '',
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);

	// Content Typography.
	$defaults[ 'footer-widget-' . $index . '-content-font-family' ] = 'inherit';
	$defaults[ 'footer-widget-' . $index . '-content-font-weight' ] = 'inherit';
	$defaults[ 'footer-widget-' . $index . '-content-font-extras' ] = array(
		'line-height'         => ! isset( $astra_options[ 'footer-widget-' . $index . '-content-font-extras' ] ) && isset( $astra_options[ 'footer-widget-' . $index . '-content-line-height' ] ) ? $astra_options[ 'footer-widget-' . $index . '-content-line-height' ] : '',
		'line-height-unit'    => 'em',
		'letter-spacing'      => ! isset( $astra_options[ 'footer-widget-' . $index . '-content-font-extras' ] ) && isset( $astra_options[ 'footer-widget-' . $index . '-content-letter-spacing' ] ) ? $astra_options[ 'footer-widget-' . $index . '-content-letter-spacing' ] : '',
		'letter-spacing-unit' => 'px',
		'text-transform'      => ! isset( $astra_options[ 'footer-widget-' . $index . '-content-font-extras' ] ) && isset( $astra_options[ 'footer-widget-' . $index . '-content-transform' ] ) ? $astra_options[ 'footer-widget-' . $index . '-content-transform' ] : '',
		'text-decoration'     => '',
	);
	$defaults[ 'footer-widget-' . $index . '-content-font-size' ]   = array(
		'desktop'      => '',
		'tablet'       => '',
		'mobile'       => '',
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);

	$defaults[ 'footer-widget-alignment-' . $index ] = array(
		'desktop' => 'left',
		'tablet'  => $apply_new_default_color_typo_values ? '' : 'center',
		'mobile'  => $apply_new_default_color_typo_values ? '' : 'center',
	);

	$defaults[ 'sidebar-widgets-footer-widget-' . $index . '-margin' ] = Astra_Builder_Helper::$default_responsive_spacing;


	return $defaults;
}

/**
 * Prepare menu Defaults.
 *
 * @param array   $defaults defaults.
 * @param integer $index index.
 */
function astra_prepare_menu_defaults( $defaults, $index ) {

	/**
	 * Update Astra default color and typography values. To not update directly on existing users site, added backwards.
	 *
	 * @since 4.0.0
	 */
	$apply_new_default_color_typo_values = Astra_Dynamic_CSS::astra_check_default_color_typo();
	$astra_options                       = Astra_Theme_Options::get_astra_options();

	$_prefix = 'menu' . $index;

	// Specify all the default values for Menu from here.
	$defaults[ 'header-' . $_prefix . '-bg-color' ]   = '';
	$defaults[ 'header-' . $_prefix . '-color' ]      = '';
	$defaults[ 'header-' . $_prefix . '-h-bg-color' ] = '';
	$defaults[ 'header-' . $_prefix . '-h-color' ]    = '';
	$defaults[ 'header-' . $_prefix . '-a-bg-color' ] = '';
	$defaults[ 'header-' . $_prefix . '-a-color' ]    = '';

	$defaults[ 'header-' . $_prefix . '-bg-obj-responsive' ] = array(
		'desktop' => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'tablet'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
		'mobile'  => array(
			'background-color'      => '',
			'background-image'      => '',
			'background-repeat'     => 'repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
			'overlay-type'          => '',
			'overlay-color'         => '',
			'overlay-opacity'       => '',
			'overlay-gradient'      => '',
		),
	);

	$defaults[ 'header-' . $_prefix . '-color-responsive' ] = array(
		'desktop' => $apply_new_default_color_typo_values ? 'var(--ast-global-color-3)' : '',
		'tablet'  => '',
		'mobile'  => '',
	);

	$defaults[ 'header-' . $_prefix . '-h-bg-color-responsive' ] = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);

	$defaults[ 'header-' . $_prefix . '-h-color-responsive' ] = array(
		'desktop' => $apply_new_default_color_typo_values ? 'var(--ast-global-color-1)' : '',
		'tablet'  => '',
		'mobile'  => '',
	);

	$defaults[ 'header-' . $_prefix . '-a-bg-color-responsive' ] = array(
		'desktop' => '',
		'tablet'  => '',
		'mobile'  => '',
	);

	$defaults[ 'header-' . $_prefix . '-a-color-responsive' ] = array(
		'desktop' => $apply_new_default_color_typo_values ? 'var(--ast-global-color-1)' : '',
		'tablet'  => '',
		'mobile'  => '',
	);

	$defaults[ 'header-' . $_prefix . '-menu-hover-animation' ]        = '';
	$defaults[ 'header-' . $_prefix . '-submenu-container-animation' ] = '';

	$defaults[ 'section-hb-menu-' . $index . '-margin' ]  = Astra_Builder_Helper::$default_responsive_spacing;
	$defaults[ 'header-menu' . $index . '-menu-spacing' ] = Astra_Builder_Helper::$default_responsive_spacing;

	/**
	 * Submenu
	 */
	$defaults[ 'header-' . $_prefix . '-submenu-item-border' ]          = false;
	$defaults[ 'header-' . $_prefix . '-submenu-item-b-size' ]          = '1';
	$defaults[ 'header-' . $_prefix . '-submenu-item-b-color' ]         = '#eaeaea';
	$header_button_submenu_border_radius_fields                         = ! isset( $astra_options[ 'header-' . $_prefix . '-submenu-border-radius-fields' ] ) && isset( $astra_options[ 'header-' . $_prefix . '-submenu-border-radius' ] ) ? $astra_options[ 'header-' . $_prefix . '-submenu-border-radius' ] : '';
	$defaults[ 'header-' . $_prefix . '-submenu-border-radius-fields' ] = array(
		'desktop'      => array(
			'top'    => $header_button_submenu_border_radius_fields,
			'right'  => $header_button_submenu_border_radius_fields,
			'bottom' => $header_button_submenu_border_radius_fields,
			'left'   => $header_button_submenu_border_radius_fields,
		),
		'tablet'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'mobile'       => array(
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		),
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'header-' . $_prefix . '-submenu-top-offset' ]           = '';
	$defaults[ 'header-' . $_prefix . '-submenu-width' ]                = '';
	$defaults[ 'header-' . $_prefix . '-submenu-border' ]               = array(
		'top'    => 2,
		'bottom' => 0,
		'left'   => 0,
		'right'  => 0,
	);

	/**
	 * Menu Stack on Mobile.
	 */
	$defaults[ 'header-' . $_prefix . '-menu-stack-on-mobile' ] = true;

	/**
	 * Menu - Typography.
	 */
	$defaults[ 'header-' . $_prefix . '-font-size' ]   = array(
		'desktop'      => '',
		'tablet'       => '',
		'mobile'       => '',
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	);
	$defaults[ 'header-' . $_prefix . '-font-weight' ] = 'inherit';
	$defaults[ 'header-' . $_prefix . '-font-family' ] = 'inherit';
	$defaults[ 'header-' . $_prefix . '-font-extras' ] = array(
		'line-height'         => ! isset( $astra_options[ 'header-' . $_prefix . '-font-extras' ] ) && isset( $astra_options[ 'header-' . $_prefix . '-line-height' ] ) ? $astra_options[ 'header-' . $_prefix . '-line-height' ] : '',
		'line-height-unit'    => 'em',
		'letter-spacing'      => '',
		'letter-spacing-unit' => 'px',
		'text-transform'      => ! isset( $astra_options[ 'header-' . $_prefix . '-font-extras' ] ) && isset( $astra_options[ 'header-' . $_prefix . '-text-transform' ] ) ? $astra_options[ 'header-' . $_prefix . '-text-transform' ] : '',
		'text-decoration'     => '',
	);

	/**
	 * Header Types - Defaults
	 */
	$defaults['transparent-header-main-sep']       = '';
	$defaults['transparent-header-main-sep-color'] = '';

	return $defaults;
}
