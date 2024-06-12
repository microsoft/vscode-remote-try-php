<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * 1. Functions which can be used for doing some operations on the values.
 * 2. Third party plugins compatibility functions.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0.0
 */

/**
 * Function to get Body Font Family
 */
if ( ! function_exists( 'astra_body_font_family' ) ) {

	/**
	 * Function to get Body Font Family
	 *
	 * @since 1.0.0
	 * @return string
	 */
	function astra_body_font_family() {

		$font_family = astra_get_option( 'body-font-family' );

		// Body Font Family.
		if ( 'inherit' == $font_family ) {
			$font_family = '-apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen-Sans, Ubuntu, Cantarell, Helvetica Neue, sans-serif';
		}

		return apply_filters( 'astra_body_font_family', $font_family );
	}
}

/**
 * Function to Add Header Breakpoint Style
 */
if ( ! function_exists( 'astra_header_breakpoint_style' ) ) {

	/**
	 * Function to Add Header Breakpoint Style
	 *
	 * @param  string $dynamic_css          Astra Dynamic CSS.
	 * @param  string $dynamic_css_filtered Astra Dynamic CSS Filters.
	 * @since 1.5.2 Remove ob_start, ob_get_clean and .main-header-bar-wrap::before{content} for our .ast-header-break-point class
	 * @since 1.0.0
	 */
	function astra_header_breakpoint_style( $dynamic_css, $dynamic_css_filtered = '' ) {

		$astra_header_width = astra_get_option( 'header-main-layout-width' );

		if ( 'content' === $astra_header_width ) {
			return $dynamic_css;
		}

		// Header Break Point.
		$header_break_point = astra_header_break_point();

		/* Width for Header */
		$genral_global_responsive = array(
			'#masthead .ast-container, .ast-header-breadcrumb .ast-container' => array(
				'max-width'     => '100%',
				'padding-left'  => '35px',
				'padding-right' => '35px',
			),
		);
		$padding_below_breakpoint = array(
			'#masthead .ast-container, .ast-header-breadcrumb .ast-container' => array(
				'padding-left'  => '20px',
				'padding-right' => '20px',
			),
		);

		/* Parse CSS from array()*/
		$dynamic_css .= astra_parse_css( $genral_global_responsive );
		$dynamic_css .= astra_parse_css( $padding_below_breakpoint, '', $header_break_point );

		// trim white space for faster page loading.
		$dynamic_css .= Astra_Enqueue_Scripts::trim_css( $dynamic_css );

		return $dynamic_css;
	}
}

add_filter( 'astra_dynamic_theme_css', 'astra_header_breakpoint_style' );

/**
 * Function to filter comment form arguments
 */
if ( ! function_exists( 'astra_404_page_layout' ) ) {

	/**
	 * Function filter comment form arguments
	 *
	 * @since 1.0.0
	 * @param array $layout     Comment form arguments.
	 * @return array
	 */
	function astra_404_page_layout( $layout ) {

		if ( is_404() ) {
			$layout = 'no-sidebar';
		}

		return apply_filters( 'astra_404_page_layout', $layout );
	}
}

add_filter( 'astra_page_layout', 'astra_404_page_layout', 10, 1 );

/**
 * Return current content layout.
 */
if ( ! function_exists( 'astra_get_content_layout' ) ) {

	/**
	 * Return current content layout.
	 *
	 * @since 1.0.0
	 * @return mixed content layout.
	 */
	function astra_get_content_layout() {

		if ( is_singular() ) {

			// Old meta layout migration to new layout.
			$old_meta_content_layout = astra_get_option_meta( 'site-content-layout', '', true );

			if ( $old_meta_content_layout ) {
				$content_layout = astra_toggle_layout( 'ast-site-content-layout', 'meta', false, $old_meta_content_layout );
			} else {
				$content_layout = astra_get_option_meta( 'ast-site-content-layout', '', true );

				// If post meta value is present, apply new layout option.
				if ( $content_layout ) {
					$content_layout = astra_toggle_layout( 'ast-site-content-layout', 'meta', false );
				}
			}

			if ( empty( $content_layout ) ) {

				$post_type      = strval( get_post_type() );
				$content_layout = astra_toggle_layout( 'single-' . $post_type . '-ast-content-layout', 'single', false );

				if ( 'default' == $content_layout || empty( $content_layout ) ) {

					// Get the GLOBAL content layout value.
					// NOTE: Here not used `true` in the below function call.
					$content_layout = astra_toggle_layout( 'ast-site-content-layout', 'global', false );
				}
			}
		} else {

			$content_layout = '';
			$post_type      = strval( get_post_type() );
			$content_layout = astra_toggle_layout( 'archive-' . $post_type . '-ast-content-layout', 'archive', false );

			if ( is_search() ) {
				$search_content_layout = astra_get_option( 'ast-search-content-layout', 'default' );
				$content_layout_key    = 'default' !== $search_content_layout ? 'ast-search-content-layout' : 'archive-post-ast-content-layout';
				$content_layout        = astra_toggle_layout( $content_layout_key, 'archive', false );
			}

			if ( 'default' == $content_layout || empty( $content_layout ) ) {

				// Get the GLOBAL content layout value.
				// NOTE: Here not used `true` in the below function call.
				$content_layout = astra_toggle_layout( 'ast-site-content-layout', 'global', false );
			}
		}

		return apply_filters( 'astra_get_content_layout', $content_layout );
	}
}

/**
 * Return current content layout as per new layout selection.
 *
 * @since 4.2.0
 * @param mixed $new_content_option The new content layout option.
 * @param mixed $level The level of the layout.
 * @param mixed $post_id The id to fetch post meta.
 * @param mixed $old_meta The old content layout option for migration cases.
 * @return mixed content layout.
 */
function astra_toggle_layout( $new_content_option, $level, $post_id = false, $old_meta = false ) {

	// Dynamic layout option for meta case.
	$dynamic_layout_option = 'meta' === $level ? astra_get_option_meta( $new_content_option, '', true ) : astra_get_option( $new_content_option, 'post' === strval( get_post_type() ) ? 'narrow-width-container' : 'default' );
	$current_layout        = '';

	// Get meta value by ID if specified.
	if ( $post_id ) {
		$dynamic_layout_option = get_post_meta( $post_id, $new_content_option, true );
	}

	// Meta layout migrations.
	$meta_key              = astra_get_option_meta( 'astra-migrate-meta-layouts', '', true );
	$migrated_user         = ( ! Astra_Dynamic_CSS::astra_fullwidth_sidebar_support() );
	$third_party_meta_page = astra_third_party_archive_meta( 'site-content-layout' );
	if ( 'meta' === $level && $migrated_user ) {
		if ( false !== $third_party_meta_page ) {
			$old_meta = $third_party_meta_page;
			$meta_key = astra_third_party_archive_meta( 'astra-migrate-meta-layouts' );
		}
		if ( $old_meta && 'set' !== $meta_key ) {
			$dynamic_layout_option = astra_migrate_meta_layout( $old_meta );
		}
	}

	switch ( $dynamic_layout_option ) {
		case 'normal-width-container':
			$current_layout = 'plain-container';
			break;
		case 'narrow-width-container':
			$current_layout = 'narrow-container';

			// Exclude narrow layout for third party cases.
			if ( astra_with_third_party() && ( ! $migrated_user ) ) {
				$current_layout = 'plain-container';
			}
			break;
		case 'full-width-container':
			$current_layout = 'page-builder';
			break;
		default:
			if ( 'meta' === $level && ! $migrated_user && 'set' !== $meta_key && $old_meta ) {
				$current_layout = $old_meta;
			} else {
				$current_layout = $dynamic_layout_option;
			}
			break;
	}
	return $current_layout;
}

/**
 * Migrate old meta layout to new layout.
 *
 * @since 4.2.0
 * @param mixed $meta_layout Meta Layout.
 * @return mixed new layout.
 */
function astra_migrate_meta_layout( $meta_layout ) {
	$new_layout = '';
	switch ( $meta_layout ) {
		case 'boxed-container':
			$new_layout = 'normal-width-container';
			break;
		case 'content-boxed-container':
			$new_layout = 'normal-width-container';
			break;
		case 'plain-container':
			$new_layout = 'normal-width-container';
			break;
		case 'page-builder':
			$new_layout = 'full-width-container';
			break;
		case 'narrow-container':
			$new_layout = 'narrow-width-container';
			break;
		default:
			$new_layout = 'default';
			break;
	}
	return $new_layout;
}

/**
 * Function to check if it is Internet Explorer
 */
if ( ! function_exists( 'astra_check_is_ie' ) ) :

	/**
	 * Function to check if it is Internet Explorer.
	 *
	 * @return true | false boolean
	 */
	function astra_check_is_ie() {

		$is_ie = false;

		if ( ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$ua = htmlentities( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), ENT_QUOTES, 'UTF-8' );  // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___SERVER__HTTP_USER_AGENT__ -- Need to check if its ie.
			if ( strpos( $ua, 'Trident/7.0' ) !== false ) {
				$is_ie = true;
			}
		}

		return apply_filters( 'astra_check_is_ie', $is_ie );
	}

endif;

/**
 * Replace header logo.
 */
if ( ! function_exists( 'astra_replace_header_logo' ) ) :

	/**
	 * Replace header logo.
	 *
	 * @param array  $image Size.
	 * @param int    $attachment_id Image id.
	 * @param sting  $size Size name.
	 * @param string $icon Icon.
	 *
	 * @return array Size of image
	 */
	function astra_replace_header_logo( $image, $attachment_id, $size, $icon ) {

		$custom_logo_id = get_theme_mod( 'custom_logo' );

		if ( ! is_customize_preview() && $custom_logo_id == $attachment_id && 'full' == $size ) {

			$data = wp_get_attachment_image_src( $attachment_id, 'ast-logo-size' );

			if ( false != $data ) {
				$image = $data;
			}
		}

		return apply_filters( 'astra_replace_header_logo', $image );
	}

endif;

if ( ! function_exists( 'astra_strposa' ) ) :

	/**
	 * Strpos over an array.
	 *
	 * @since  1.2.4
	 * @param  String  $haystack The string to search in.
	 * @param  Array   $needles  Array of needles to be passed to strpos().
	 * @param  integer $offset   If specified, search will start this number of characters counted from the beginning of the string. If the offset is negative, the search will start this number of characters counted from the end of the string.
	 *
	 * @return bool            True if haystack if part of any of the $needles.
	 */
	function astra_strposa( $haystack, $needles, $offset = 0 ) {

		if ( ! is_array( $needles ) ) {
			$needles = array( $needles );
		}

		foreach ( $needles as $query ) {

			if ( strpos( $haystack, $query, $offset ) !== false ) {
				// stop on first true result.
				return true;
			}
		}

		return false;
	}

endif;

if ( ! function_exists( 'astra_get_prop' ) ) :

	/**
	 * Get a specific property of an array without needing to check if that property exists.
	 *
	 * Provide a default value if you want to return a specific value if the property is not set.
	 *
	 * @since  1.2.7
	 * @access public
	 * @author Gravity Forms - Easiest Tool to Create Advanced Forms for Your WordPress-Powered Website.
	 * @link  https://www.gravityforms.com/
	 *
	 * @param array  $array   Array from which the property's value should be retrieved.
	 * @param string $prop    Name of the property to be retrieved.
	 * @param string $default Optional. Value that should be returned if the property is not set or empty. Defaults to null.
	 *
	 * @return string|mixed The value
	 */
	function astra_get_prop( $array, $prop, $default = null ) {

		if ( ! is_array( $array ) && ! ( is_object( $array ) && $array instanceof ArrayAccess ) ) {
			return $default;
		}

		if ( ( isset( $array[ $prop ] ) && false === $array[ $prop ] ) ) {
			return false;
		}

		if ( isset( $array[ $prop ] ) ) {
			$value = $array[ $prop ];
		} else {
			$value = '';
		}

		return empty( $value ) && null !== $default ? $default : $value;
	}

endif;

/**
 * Build list of attributes into a string and apply contextual filter on string.
 *
 * The contextual filter is of the form `astra_attr_{context}_output`.
 *
 * @since 1.6.2
 * @credits - Genesis Theme By StudioPress.
 *
 * @param string $context    The context, to build filter name.
 * @param array  $attributes Optional. Extra attributes to merge with defaults.
 * @param array  $args       Optional. Custom data to pass to filter.
 * @return string String of HTML attributes and values.
 */
function astra_attr( $context, $attributes = array(), $args = array() ) {
	return Astra_Attr::get_instance()->astra_attr( $context, $attributes, $args );
}

/**
 * Get the theme author details
 *
 * @since  3.1.0
 * @return array            Return theme author URL and name.
 */
function astra_get_theme_author_details() {

	$theme_author = apply_filters(
		'astra_theme_author',
		array(
			'theme_name'       => esc_html__( 'Astra WordPress Theme', 'astra' ),
			'theme_author_url' => 'https://wpastra.com/',
		)
	);

	return $theme_author;
}

/**
 * Add dropdown icon if menu item has children.
 *
 * @since 3.3.0
 *
 * @param string   $title The menu item title.
 * @param WP_Post  $item All of our menu item data.
 * @param stdClass $args All of our menu item args.
 * @param int      $depth Depth of menu item.
 * @return string The menu item.
 */
function astra_dropdown_icon_to_menu_link( $title, $item, $args, $depth ) {
	$role = 'application';
	$icon = '';

	/**
	 * These menus are not overriden by the 'Astra_Custom_Nav_Walker' class present in Addon - Nav Menu module.
	 *
	 * Hence skipping these menus from getting overriden by blank SVG Icons and adding the icons from theme.
	 *
	 * @since 3.3.0
	 */
	$astra_menu_locations = array(
		'ast-hf-menu-1',        // Builder - Primary menu.
		'ast-hf-menu-2',        // Builder - Secondary menu.
		'ast-hf-menu-3',
		'ast-hf-menu-4',
		'ast-hf-menu-5',
		'ast-hf-menu-6',
		'ast-hf-menu-7',
		'ast-hf-menu-8',
		'ast-hf-menu-9',
		'ast-hf-menu-10',           // Cloned builder menus.
		'ast-hf-mobile-menu',       // Builder - Mobile Menu.
		'ast-desktop-toggle-menu',  // Builder - Toggle for Desktop Menu.
		'ast-hf-account-menu',      // Builder - Login Account Menu.
		'primary-menu',             // Old header - Primary Menu.
		'above_header-menu',        // Old header - Above Menu.
		'below_header-menu',        // Old header - Below Menu.
	);

	$load_svg_menu_icons = false;

	if ( defined( 'ASTRA_EXT_VER' ) ) {
		// Check whether Astra Pro is active + Nav menu addon is deactivate + menu registered by Astra only.
		if ( ! Astra_Ext_Extension::is_active( 'nav-menu' ) && in_array( $args->menu_id, $astra_menu_locations ) ) {
			$load_svg_menu_icons = true;
		}
	} else {
		// Check menu registered by Astra only.
		if ( in_array( $args->menu_id, $astra_menu_locations ) ) {
			$load_svg_menu_icons = true;
		}
	}

	if ( $load_svg_menu_icons || ( defined( 'ASTRA_EXT_VER' ) && ! Astra_Ext_Extension::is_active( 'nav-menu' ) ) ) {
		// Assign icons to only those menu which are registered by Astra.
		$icon = Astra_Icons::get_icons( 'arrow' );
	}

	// Dropdown svg arrow for submenu for non Astra registered menu's
	$is_nav_menu_extension_inactive = defined( 'ASTRA_EXT_VER' ) && ! Astra_Ext_Extension::is_active( 'nav-menu' );

	$special_menu_ids = array(
		'ast-hf-menu-3',
		'ast-hf-menu-4',
		'ast-hf-menu-5',
		'ast-hf-menu-6',
		'ast-hf-menu-7',
		'ast-hf-menu-8',
		'ast-hf-menu-9',
		'ast-hf-menu-10',
		'ast-hf-menu-3-sticky',
		'ast-hf-menu-4-sticky',
		'ast-hf-menu-5-sticky',
		'ast-hf-menu-6-sticky',
		'ast-hf-menu-7-sticky',
		'ast-hf-menu-8-sticky',
		'ast-hf-menu-9-sticky',
		'ast-hf-menu-10-sticky',
		'ast-hf-account-menu',
	);

	$is_special_menu = in_array( $args->menu_id, $special_menu_ids );

	if ( $is_nav_menu_extension_inactive || $is_special_menu ) {
		$icon = Astra_Icons::get_icons( 'arrow' );
	}

	$astra_arrow_icon = '';
	// Render arrow icon for special menu appearance or on pro deactivation or nav menu extension deactivation.
	if ( $is_special_menu || $is_nav_menu_extension_inactive || ! defined( 'ASTRA_EXT_VER' ) ) {
		$custom_tabindex  = true === Astra_Builder_Helper::$is_header_footer_builder_active ? 'tabindex="0"' : '';
		$astra_arrow_icon = '<span role="' . esc_attr( $role ) . '" class="dropdown-menu-toggle ast-header-navigation-arrow" ' . $custom_tabindex . ' aria-expanded="false" aria-label="' . esc_attr__( 'Menu Toggle', 'astra' ) . '" >' . $icon . '</span>';
	}

	foreach ( $item->classes as $value ) {
		if ( 'menu-item-has-children' === $value ) {
			$title = $title . $astra_arrow_icon;
		}
	}
	if ( 0 < $depth ) {
		$title = $icon . $title;
	}
	return $title;
}

if ( Astra_Icons::is_svg_icons() ) {
	add_filter( 'nav_menu_item_title', 'astra_dropdown_icon_to_menu_link', 10, 4 );
}

/**
 * Is theme existing header footer configs enable.
 *
 * @since 3.0.0
 *
 * @return boolean true/false.
 */
function astra_existing_header_footer_configs() {

	return apply_filters( 'astra_existing_header_footer_configs', true );
}

/**
 * Get Spacing value
 *
 * @param  array  $value        Responsive spacing value with unit.
 * @param  string $operation    + | - | * | /.
 * @param  string $from         Perform operation from the value.
 * @param  string $from_unit    Perform operation from the value of unit.
 *
 * @since 3.0.0
 * @return mixed
 */
function astra_calculate_spacing( $value, $operation = '', $from = '', $from_unit = '' ) {

	$css = '';
	if ( ! empty( $value ) ) {
		$css = $value;
		if ( ! empty( $operation ) && ! empty( $from ) ) {
			if ( ! empty( $from_unit ) ) {
				$css = 'calc( ' . $value . ' ' . $operation . ' ' . $from . $from_unit . ' )';
			}
			if ( '*' === $operation || '/' === $operation ) {
				$css = 'calc( ' . $value . ' ' . $operation . ' ' . $from . ' )';
			}
		}
	}

	return $css;
}

/**
 * Generate HTML Open markup
 *
 * @param string $context unique markup key.
 * @param array  $args {
 *      Contains markup arguments.
 *     @type array  attrs    Initial attributes to apply to `open` markup.
 *     @type bool   echo    Flag indicating whether to echo or return the resultant string.
 * }
 * @since 3.3.0
 * @return mixed
 */
function astra_markup_open( $context, $args = array() ) {
	$defaults = array(
		'open'    => '',
		'attrs'   => array(),
		'echo'    => true,
		'content' => '',
	);

	$args = wp_parse_args( $args, $defaults );
	if ( $context ) {
		$args     = apply_filters( "astra_markup_{$context}_open", $args );
		$open_tag = $args['open'] ? sprintf( $args['open'], astra_attr( $context, $args['attrs'] ) ) : '';

		if ( $args['echo'] ) {
			echo $open_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $open_tag;
		}
	}
	return false;
}

/**
 * Generate HTML close markup
 *
 * @param string $context unique markup key.
 * @param array  $args {
 *      Contains markup arguments.
 *     @type string close   Closing HTML markup.
 *     @type array  attrs    Initial attributes to apply to `open` markup.
 *     @type bool   echo    Flag indicating whether to echo or return the resultant string.
 * }
 * @since 3.3.0
 * @return mixed
 */
function astra_markup_close( $context, $args = array() ) {
	$defaults = array(
		'close' => '',
		'attrs' => array(),
		'echo'  => true,
	);

	$args = wp_parse_args( $args, $defaults );
	if ( $context ) {
		$args      = apply_filters( "astra_markup_{$context}_close", $args );
		$close_tag = $args['close'];
		if ( $args['echo'] ) {
			echo $close_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $close_tag;
		}
	}
	return false;
}

/**
 * Provision to update display rules for visibility of Related Posts section in Astra.
 *
 * @since 3.4.0
 * @return bool
 */
function astra_target_rules_for_related_posts() {

	$allow_related_posts = false;
	$supported_post_type = apply_filters( 'astra_related_posts_supported_post_types', 'post' );

	if ( astra_get_option( 'enable-related-posts' ) && is_singular( $supported_post_type ) ) {
		$allow_related_posts = true;
	}

	return apply_filters( 'astra_showcase_related_posts', $allow_related_posts );
}

/**
 * Check if elementor plugin is active on the site.
 *
 * @since 3.7.0
 * @return bool
 */
function astra_is_elemetor_active() {
	return class_exists( '\Elementor\Plugin' );
}

/**
 * Check the Astra addon version.
 * For  major update and frequently we used version_compare, added a function for this for easy maintenance.
 *
 * @param string $version Astra addon version.
 * @param string $compare Compare symbols.
 * @since  3.9.2
 */
function astra_addon_check_version( $version, $compare ) {
	return defined( 'ASTRA_EXT_VER' ) && version_compare( ASTRA_EXT_VER, $version, $compare );
}

/**
 * Get a stylesheet URL for a webfont.
 *
 * @since 3.6.0
 *
 * @param string $url    The URL of the remote webfont.
 * @param string $format The font-format. If you need to support IE, change this to "woff".
 *
 * @return string Returns the CSS.
 */
function astra_get_webfont_url( $url, $format = 'woff2' ) {

	// Check if already Google font URL present or not. Basically avoiding 'Astra_WebFont_Loader' class rendering.
	/** @psalm-suppress InvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	$astra_font_url = astra_get_option( 'astra_font_url', false );
	if ( $astra_font_url ) {
		return json_decode( $astra_font_url );
	}

	// Now create font URL if its not present.
	$font = astra_webfont_loader_instance( $url );
	$font->set_font_format( $format );
	return $font->get_url();
}

/**
 * Get the file preloads.
 *
 * @param string $url    The URL of the remote webfont.
 * @param string $format The font-format. If you need to support IE, change this to "woff".
 */
function astra_load_preload_local_fonts( $url, $format = 'woff2' ) {

	// Check if cached font files data preset present or not. Basically avoiding 'Astra_WebFont_Loader' class rendering.
	$astra_local_font_files = get_site_option( 'astra_local_font_files', false );

	if ( is_array( $astra_local_font_files ) && ! empty( $astra_local_font_files ) ) {
		$font_format = apply_filters( 'astra_local_google_fonts_format', $format );
		foreach ( $astra_local_font_files as $key => $local_font ) {
			if ( $local_font ) {
				echo '<link rel="preload" href="' . esc_url( $local_font ) . '" as="font" type="font/' . esc_attr( $font_format ) . '" crossorigin>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Preparing HTML link tag.
			}
		}
		return;
	}

	// Now preload font data after processing it, as we didn't get stored data.
	$font = astra_webfont_loader_instance( $url );
	$font->set_font_format( $format );
	$font->preload_local_fonts();
}

/**
 * Set flag to manage backward compatibility for v3.5.0 earlier users for the transparent header border bottom default value changed.
 *
 * @since 3.6.0
 */
function astra_get_transparent_header_default_value() {
	$astra_settings                                      = get_option( ASTRA_THEME_SETTINGS, array() );
	$astra_settings['transparent-header-default-border'] = isset( $astra_settings['transparent-header-default-border'] ) ? $astra_settings['transparent-header-default-border'] : true;

	return apply_filters( 'astra_transparent_header_default_border', $astra_settings['transparent-header-default-border'] );
}

/**
 * Check compatibility for content background and typography options.
 *
 * @since 3.7.0
 */
function astra_has_gcp_typo_preset_compatibility() {
	if ( defined( 'ASTRA_EXT_VER' ) && version_compare( ASTRA_EXT_VER, '3.6.0', '<' ) ) {
		return false;
	}
	return true;
}

/**
 * Check whether user is existing or new to apply the updated default values for button padding & support GB button paddings with global button padding options.
 *
 * @since 3.6.3
 * @return string
 */
function astra_button_default_padding_updated() {
	$astra_settings  = get_option( ASTRA_THEME_SETTINGS, array() );
	$padding_updated = isset( $astra_settings['btn-default-padding-updated'] ) ? $astra_settings['btn-default-padding-updated'] : true;
	return apply_filters( 'astra_update_button_padding_defaults', $padding_updated );
}

/**
 * Check whether user is existing or new to apply the updated default values for secondary button padding.
 *
 * @since 4.5.2
 * @return string
 */
function astra_scndry_btn_default_padding() {
	$astra_settings  = get_option( ASTRA_THEME_SETTINGS, array() );
	$padding_updated = isset( $astra_settings['scndry-btn-default-padding'] ) ? $astra_settings['scndry-btn-default-padding'] : true;
	return apply_filters( 'astra_update_secondary_button_padding_defaults', $padding_updated );
}

/**
 * Check is WordPress version is greater than or equal to beta 5.8 version.
 *
 * @since 3.6.5
 * @return boolean
 */
function astra_has_widgets_block_editor() {
	if ( ( defined( 'GUTENBERG_VERSION' ) && version_compare( GUTENBERG_VERSION, '10.6.2', '>' ) )
	|| version_compare( get_bloginfo( 'version' ), '5.8-alpha', '>=' ) ) {
		return true;
	}
	return false;
}

/**
 * Check whether user is exising or new to override the default margin space added to Elementor-TOC widget.
 *
 * @since 3.6.7
 * @return boolean
 */
function astra_can_remove_elementor_toc_margin_space() {
	$astra_settings                                    = get_option( ASTRA_THEME_SETTINGS );
	$astra_settings['remove-elementor-toc-margin-css'] = isset( $astra_settings['remove-elementor-toc-margin-css'] ) ? false : true;
	return apply_filters( 'astra_remove_elementor_toc_margin', $astra_settings['remove-elementor-toc-margin-css'] );
}

/**
 * Check whether user is exising or new to override the hr tag styling for elementor
 *
 * @since 4.3.0
 * @return boolean
 */
function astra_can_add_styling_for_hr() {
	$astra_settings                       = get_option( ASTRA_THEME_SETTINGS );
	$astra_settings['add-styling-for-hr'] = isset( $astra_settings['add-styling-for-hr'] ) ? false : true;
	return apply_filters( 'astra_highlight_elementor_hr_tag', $astra_settings['add-styling-for-hr'] );
}
/**
 * This will check if user is new and apply global color format. This is to manage backward compatibility for colors.
 *
 * @since 3.7.0
 * @return boolean false if it is an existing user, true for new user.
 */
function astra_has_global_color_format_support() {
	$astra_settings                                = get_option( ASTRA_THEME_SETTINGS );
	$astra_settings['support-global-color-format'] = isset( $astra_settings['support-global-color-format'] ) ? false : true;
	return apply_filters( 'astra_apply_global_color_format_support', $astra_settings['support-global-color-format'] );
}

/**
 * Get Global Color Palettes
 *
 * @return array color palettes array.
 * @since 3.7.0
 */
function astra_get_palette_colors() {
	return get_option( 'astra-color-palettes', apply_filters( 'astra_global_color_palette', Astra_Global_Palette::get_default_color_palette() ) );
}

/**
 * Get typography presets data.
 *
 * @return array Typography Presets data array.
 * @since 3.7.0
 */
function astra_get_typography_presets() {
	return get_option( 'astra-typography-presets', '' );
}

/**
 * Clear Astra + Astra Pro assets cache.
 *
 * @since 3.6.9
 * @return void
 */
function astra_clear_theme_addon_asset_cache() {
	astra_clear_all_assets_cache();
}

add_action( 'astra_theme_update_after', 'astra_clear_theme_addon_asset_cache', 10 );

/**
 * Check if Theme Global Colors need to be disable in Elementor global color settings.
 *
 * @since 3.7.4
 * @return bool
 */
function astra_maybe_disable_global_color_in_elementor() {
	return apply_filters( 'astra_disable_global_colors_in_elementor', false );
}

/**
 * Check is Elementor Pro version is greater than or equal to beta 3.5 version.
 *
 * @since 3.7.5
 * @return boolean
 */
function astra_check_elementor_pro_3_5_version() {
	if ( defined( 'ELEMENTOR_PRO_VERSION' ) && version_compare( ELEMENTOR_PRO_VERSION, '3.5', '>=' ) ) {
		return true;
	}
	return false;
}

/**
 * Should Content BG settings apply to Fullwidth Contained/Stretched layout or not?
 *
 * Do not apply content background to fullwidth layouts in following cases -
 * 1. For backward compatibility.
 * 2. When site layout is Max-width.
 * 3. When site layout is Padded.
 *
 * @since 3.7.8
 * @return boolean
 */
function astra_apply_content_background_fullwidth_layouts() {
	$astra_site_layout              = astra_get_option( 'site-layout' );
	$astra_apply_content_background = astra_get_option( 'apply-content-background-fullwidth-layouts', true );

	return ( $astra_apply_content_background && 'ast-box-layout' !== $astra_site_layout && 'ast-padded-layout' !== $astra_site_layout );
}

/**
 * Search Component static CSS.
 *
 * @return string
 * @since 3.5.0
 */
function astra_search_static_css() {
	$search_css = '
	.main-header-bar .main-header-bar-navigation .ast-search-icon {
		display: block;
		z-index: 4;
		position: relative;
	}
	.ast-search-icon .ast-icon {
		z-index: 4;
	}
	.ast-search-icon {
		z-index: 4;
		position: relative;
		line-height: normal;
	}
	.main-header-bar .ast-search-menu-icon .search-form {
		background-color: #ffffff;
	}
	.ast-search-menu-icon.ast-dropdown-active.slide-search .search-form {
		visibility: visible;
		opacity: 1;
	}
	.ast-search-menu-icon .search-form {
		border: 1px solid #e7e7e7;
		line-height: normal;
		padding: 0 3em 0 0;
		border-radius: 2px;
		display: inline-block;
		-webkit-backface-visibility: hidden;
		backface-visibility: hidden;
		position: relative;
		color: inherit;
		background-color: #fff;
	}
	.ast-search-menu-icon .astra-search-icon {
		-js-display: flex;
		display: flex;
		line-height: normal;
	}
	.ast-search-menu-icon .astra-search-icon:focus {
		outline: none;
	}
	.ast-search-menu-icon .search-field {
		border: none;
		background-color: transparent;
		transition: all .3s;
		border-radius: inherit;
		color: inherit;
		font-size: inherit;
		width: 0;
		color: #757575;
	}
	.ast-search-menu-icon .search-submit {
		display: none;
		background: none;
		border: none;
		font-size: 1.3em;
		color: #757575;
	}
	.ast-search-menu-icon.ast-dropdown-active {
		visibility: visible;
		opacity: 1;
		position: relative;
	}
	.ast-search-menu-icon.ast-dropdown-active .search-field, .ast-dropdown-active.ast-search-menu-icon.slide-search input.search-field {
		width: 235px;
	}
	.ast-header-search .ast-search-menu-icon.slide-search .search-form, .ast-header-search .ast-search-menu-icon.ast-inline-search .search-form {
		-js-display: flex;
		display: flex;
		align-items: center;
	}';

	if ( is_rtl() ) {
		$search_css .= '
		.ast-search-menu-icon.ast-inline-search .search-field {
			width : 100%;
			padding : 0.60em;
			padding-left : 5.5em;
			transition: all 0.2s;
		}
		.site-header-section-left .ast-search-menu-icon.slide-search .search-form {
			padding-right: 2em;
			padding-left: unset;
			right: -1em;
			left: unset;
		}
		.site-header-section-left .ast-search-menu-icon.slide-search .search-form .search-field {
			margin-left: unset;
		}
		.ast-search-menu-icon.slide-search .search-form {
			-webkit-backface-visibility: visible;
			backface-visibility: visible;
			visibility: hidden;
			opacity: 0;
			transition: all .2s;
			position: absolute;
			z-index: 3;
			left: -1em;
			top: 50%;
			transform: translateY(-50%);
		}';
	} else {
		$search_css .= '
		.ast-search-menu-icon.ast-inline-search .search-field {
			width : 100%;
			padding : 0.60em;
			padding-right : 5.5em;
			transition: all 0.2s;
		}
		.site-header-section-left .ast-search-menu-icon.slide-search .search-form {
			padding-left: 2em;
			padding-right: unset;
			left: -1em;
			right: unset;
		}
		.site-header-section-left .ast-search-menu-icon.slide-search .search-form .search-field {
			margin-right: unset;
		}
		.ast-search-menu-icon.slide-search .search-form {
			-webkit-backface-visibility: visible;
			backface-visibility: visible;
			visibility: hidden;
			opacity: 0;
			transition: all .2s;
			position: absolute;
			z-index: 3;
			right: -1em;
			top: 50%;
			transform: translateY(-50%);
		}';
	}

	return Astra_Enqueue_Scripts::trim_css( $search_css );
}

/**
 * Showcase "Upgrade to Pro" notices for Astra & here is the filter work as central control to enable/disable those notices from customizer, meta settings, admin area, pro post types pages.
 *
 * @since 3.9.4
 * @return bool
 */
function astra_showcase_upgrade_notices() {
	return ( ! defined( 'ASTRA_EXT_VER' ) && astra_get_option( 'ast-disable-upgrade-notices', true ) ) ? true : false;
}

/**
 * Function which will return CSS for font-extras control.
 * It includes - line-height, letter-spacing, text-decoration, font-style.
 *
 * @param array  $config contains extra font settings.
 * @param string $setting basis on this setting will return.
 * @param mixed  $unit Unit.
 *
 * @since 4.0.0
 */
function astra_get_font_extras( $config, $setting, $unit = false ) {
	$css = isset( $config[ $setting ] ) ? $config[ $setting ] : '';

	if ( $unit && $css ) {
		$unit_val = isset( $config[ $unit ] ) ? $config[ $unit ] : '';
		$unit_val = 'line-height-unit' === $unit ? apply_filters( 'astra_font_line_height_unit', $unit_val ) : $unit_val;
		$css     .= $unit_val;
	}

	return $css;
}

/**
 * Function which will return CSS array for font specific props for further parsing CSS.
 * It includes - font-family, font-weight, font-size, line-height, text-transform, letter-spacing, text-decoration, color (optional).
 *
 * @param string $font_family Font family.
 * @param string $font_weight Font weight.
 * @param array  $font_size Font size.
 * @param string $font_extras contains all font controls.
 * @param string $color In most of cases color is also added, so included optional param here.

 * @return array  array of build CSS font settings.
 *
 * @since 4.0.0
 */
function astra_get_font_array_css( $font_family, $font_weight, $font_size, $font_extras, $color = '' ) {
	$font_extras_ast_option = astra_get_option(
		$font_extras,
		array(
			'line-height'         => '',
			'line-height-unit'    => 'em',
			'letter-spacing'      => '',
			'letter-spacing-unit' => 'px',
			'text-transform'      => '',
			'text-decoration'     => '',
		)
	);
	return array(
		'color'           => esc_attr( $color ),
		'font-family'     => astra_get_css_value( $font_family, 'font' ),
		'font-weight'     => astra_get_css_value( $font_weight, 'font' ),
		'font-size'       => ! empty( $font_size ) ? astra_responsive_font( $font_size, 'desktop' ) : '',
		'line-height'     => astra_get_font_extras( $font_extras_ast_option, 'line-height', 'line-height-unit' ),
		'text-transform'  => astra_get_font_extras( $font_extras_ast_option, 'text-transform' ),
		'letter-spacing'  => astra_get_font_extras( $font_extras_ast_option, 'letter-spacing', 'letter-spacing-unit' ),
		'text-decoration' => astra_get_font_extras( $font_extras_ast_option, 'text-decoration' ),
	);
}

/**
 * Return the array of site's available image size.
 *
 * @param boolean $add_custom Add custom image size.
 * @since 4.4.0
 * @return array
 */
function astra_get_site_image_sizes( $add_custom = false ) {
	$image_sizes = array(
		'thumbnail'    => esc_html__( 'Thumbnail', 'astra' ),
		'medium'       => esc_html__( 'Medium', 'astra' ),
		'medium_large' => esc_html__( 'Medium Large', 'astra' ),
		'large'        => esc_html__( 'Large', 'astra' ),
		'full'         => esc_html__( 'Full Size', 'astra' ),
	);

	// Gets the available intermediate image size names on site.
	$all_sizes = get_intermediate_image_sizes();  // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_intermediate_image_sizes_get_intermediate_image_sizes -- Required for image sizes to work.


	$refactored_sizes = array(
		'full' => esc_html__( 'Full Size', 'astra' ),
	);

	foreach ( $all_sizes as $size ) {
		if ( isset( $image_sizes[ $size ] ) ) {
			$refactored_sizes[ $size ] = $image_sizes[ $size ];
		} else {
			$refactored_sizes[ $size ] = $size;
		}
	}

	/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	if ( $add_custom && defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'blog-pro' ) ) {
		/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$refactored_sizes['custom'] = esc_html__( 'Custom', 'astra' );
	}

	return $refactored_sizes;
}

/**
 * Return the aspect-ratio for dynamic image.
 *
 * @param string $aspect_ratio_type Aspect ratio type.
 * @param string $predefined_scale Predefined scale.
 * @param string $custom_scale_width Custom scale width.
 * @param string $custom_scale_height Custom scale height.
 *
 * @since 4.4.0
 * @return string
 */
function astra_get_dynamic_image_aspect_ratio( $aspect_ratio_type, $predefined_scale, $custom_scale_width, $custom_scale_height ) {
	switch ( $aspect_ratio_type ) {
		case 'predefined':
			return $predefined_scale;
		case 'custom':
			return absint( $custom_scale_width ) . '/' . absint( $custom_scale_height );
	}
	return '';
}

/**
 * Getting site active language & compatible with other plugins.
 *
 * @since 4.4.0
 * @return string
 */
function astra_get_current_language_slug() {
	$lang = '';
	if ( function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language();
	}
	return apply_filters( 'astra_addon_site_current_language', $lang );
}

/**
 * Function which will return the supported post types from core.
 *
 * Further processing includes:
 * 1. Dynamic customizer
 * 2. Live Search
 *
 * @since 4.4.0
 * @return array
 */
function astra_get_queried_post_types() {
	$queried_post_types = array_keys(
		get_post_types(
			apply_filters(
				'astra_dynamic_get_post_types_query_args',
				array(
					'public'   => true,
					'_builtin' => false,
				)
			)
		)
	);

	$queried_post_types   = array_diff(
		$queried_post_types,
		array(
			'astra-advanced-hook',
			'astra_adv_header',
			'elementor_library',
			'brizy_template',

			'course',
			'lesson',
			'llms_membership',

			'tutor_quiz',
			'tutor_assignments',

			'testimonial',
			'frm_display',
			'mec_esb',
			'mec-events',

			'sfwd-assignment',
			'sfwd-essays',
			'sfwd-transactions',
			'sfwd-certificates',
			'sfwd-quiz',
			'e-landing-page',
		)
	);
	$queried_post_types[] = 'post';
	$queried_post_types[] = 'page';

	return $queried_post_types;
}

/**
 * Get Global Color Palette's presets
 *
 * @return array color presets array.
 * @since 4.5.0
 */
function astra_get_palette_presets() {
	return array(
		'preset_1'  => array(
			'#0067FF',
			'#005EE9',
			'#0F172A',
			'#364151',
			'#E7F6FF',
			'#FFFFFF',
			'#D1DAE5',
			'#070614',
			'#222222',
		),
		'preset_2'  => array(
			'#6528F7',
			'#5511F8',
			'#0F172A',
			'#454F5E',
			'#F2F0FE',
			'#FFFFFF',
			'#D8D8F5',
			'#0D0614',
			'#222222',
		),
		'preset_3'  => array(
			'#DD183B',
			'#CC1939',
			'#0F172A',
			'#3A3A3A',
			'#FFEDE6',
			'#FFFFFF',
			'#FFD1BF',
			'#140609',
			'#222222',
		),
		'preset_4'  => array(
			'#54B435',
			'#379237',
			'#0F172A',
			'#2F3B40',
			'#EDFBE2',
			'#FFFFFF',
			'#D5EAD8',
			'#0C1406',
			'#222222',
		),
		'preset_5'  => array(
			'#DCA54A',
			'#D09A40',
			'#0F172A',
			'#4A4A4A',
			'#FAF5E5',
			'#FFFFFF',
			'#F0E6C5',
			'#141004',
			'#222222',
		),
		'preset_6'  => array(
			'#FB5FAB',
			'#EA559D',
			'#0F172A',
			'#454F5E',
			'#FCEEF5',
			'#FFFFFF',
			'#FAD8E9',
			'#140610',
			'#222222',
		),
		'preset_7'  => array(
			'#1B9C85',
			'#178E79',
			'#0F172A',
			'#454F5E',
			'#EDF6EE',
			'#FFFFFF',
			'#D4F3D7',
			'#06140C',
			'#222222',
		),
		'preset_8'  => array(
			'#FD9800',
			'#E98C00',
			'#0F172A',
			'#454F5E',
			'#FEF9E1',
			'#FFFFFF',
			'#F9F0C8',
			'#141006',
			'#222222',
		),
		'preset_9'  => array(
			'#FF6210',
			'#F15808',
			'#1C0D0A',
			'#353535',
			'#FEF1E4',
			'#FFFFFF',
			'#E5D7D1',
			'#140B06',
			'#222222',
		),
		'preset_10' => array(
			'#737880',
			'#65696F',
			'#151616',
			'#393C40',
			'#F6F6F6',
			'#FFFFFF',
			'#F1F0F0',
			'#232529',
			'#222222',
		),
	);
}

/**
 * Get Astra blog layout design.
 * Search / Blog.
 *
 * @return string $blog_layout.
 * @since 4.6.0
 */
function astra_get_blog_layout() {
	return ( is_search() && '' !== astra_get_option( 'ast-search-results-style' ) ) ? astra_get_option( 'ast-search-results-style' ) : astra_get_option( 'blog-layout' );
}

/**
 * Get Astra blog posts per page count.
 * Search / Blog.
 *
 * @return int $blog_layout.
 * @since 4.6.0
 */
function astra_get_blog_posts_per_page() {
	return ( is_search() && astra_get_option( 'ast-search-results-per-page' ) ) ? astra_get_option( 'ast-search-results-per-page' ) : astra_get_option( 'blog-post-per-page' );
}

/**
 * Get the remote WP-Astra docs data.
 *
 * @since 4.6.0
 */
function astra_remote_docs_data() {
	$astra_docs_instance = astra_docs_loader_instance( 'https://wpastra.com/wp-json/powerful-docs/v1/get-docs', 'astra-docs' );
	return json_decode( $astra_docs_instance->get_remote_data() );
}

/**
 * Post types for live search.
 *
 * @since 4.4.0
 */
function astra_customizer_live_search_posttypes() {
	$supported_post_types = array();
	if ( is_customize_preview() ) {
		$supported_post_types = astra_get_queried_post_types();
	}
	return apply_filters( 'astra_live_search_posttypes', $supported_post_types );
}

/**
 * Get formatted live search post types.
 *
 * @since 4.4.0
 * @return array
 */
function astra_customizer_search_post_types_choices() {
	$all_post_types    = astra_customizer_live_search_posttypes();
	$post_type_choices = array();
	foreach ( $all_post_types as $post_type ) {
		$post_type_object = get_post_type_object( $post_type );
		/** @psalm-suppress PossiblyNullPropertyFetch */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$post_type_choices[ $post_type ] = ! empty( $post_type_object->labels->name ) ? $post_type_object->labels->name : $post_type;
		/** @psalm-suppress PossiblyNullPropertyFetch */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	}
	return $post_type_choices;
}

/**
 * Adding backward compatibility fag to manage EDD featured image by default state.
 *
 * @since 4.7.0
 * @return bool
 */
function astra_enable_edd_featured_image_defaults() {
	$astra_settings = get_option( ASTRA_THEME_SETTINGS, array() );
	return apply_filters( 'astra_enable_edd_featured_image_defaults', isset( $astra_settings['can-update-edd-featured-image-default'] ) ? false : true );
}
