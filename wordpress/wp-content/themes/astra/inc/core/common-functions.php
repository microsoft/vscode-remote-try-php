<?php
/**
 * Functions for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Foreground Color
 */
if ( ! function_exists( 'astra_get_foreground_color' ) ) {

	/**
	 * Foreground Color
	 *
	 * @param  string $hex Color code in HEX format.
	 * @return string      Return foreground color depend on input HEX color.
	 */
	function astra_get_foreground_color( $hex ) {

		$hex = apply_filters( 'astra_before_foreground_color_generation', $hex );

		// bail early if color's not set.
		if ( 'transparent' == $hex || 'false' == $hex || '#' == $hex || empty( $hex ) ) {
			return 'transparent';
		}

		// Get clean hex code.
		$hex = str_replace( '#', '', $hex );

		if ( 3 == strlen( $hex ) ) {
			$hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );
		}

		if ( strpos( $hex, 'rgba' ) !== false ) {

			$rgba = preg_replace( '/[^0-9,]/', '', $hex );
			$rgba = explode( ',', $rgba );

			$hex = sprintf( '#%02x%02x%02x', $rgba[0], $rgba[1], $rgba[2] );
		}

		// Return if non hex.
		if ( function_exists( 'ctype_xdigit' ) && is_callable( 'ctype_xdigit' ) ) {
			if ( ! ctype_xdigit( $hex ) ) {
				return $hex;
			}
		} else {
			if ( ! preg_match( '/^[a-f0-9]{2,}$/i', $hex ) ) {
				return $hex;
			}
		}

		// Get r, g & b codes from hex code.
		$r   = hexdec( substr( $hex, 0, 2 ) );
		$g   = hexdec( substr( $hex, 2, 2 ) );
		$b   = hexdec( substr( $hex, 4, 2 ) );
		$hex = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;

		return 128 <= $hex ? '#000000' : '#ffffff';
	}
}

/**
 * Generate CSS
 */
if ( ! function_exists( 'astra_css' ) ) {

	/**
	 * Generate CSS
	 *
	 * @param  mixed  $value         CSS value.
	 * @param  string $css_property CSS property.
	 * @param  string $selector     CSS selector.
	 * @param  string $unit         CSS property unit.
	 * @return void               Echo generated CSS.
	 */
	function astra_css( $value = '', $css_property = '', $selector = '', $unit = '' ) {

		if ( $selector ) {
			if ( $css_property && $value ) {

				if ( '' != $unit ) {
					$value .= $unit;
				}

				$css  = $selector;
				$css .= '{';
				$css .= '	' . $css_property . ': ' . $value . ';';
				$css .= '}';

				echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
	}
}

/**
 * Get Font Size value
 */
if ( ! function_exists( 'astra_responsive_font' ) ) {

	/**
	 * Get Font CSS value
	 *
	 * @param  array  $font    CSS value.
	 * @param  string $device  CSS device.
	 * @param  string $default Default value.
	 * @return mixed
	 */
	function astra_responsive_font( $font, $device = 'desktop', $default = '' ) {

		if ( isset( $font[ $device ] ) && isset( $font[ $device . '-unit' ] ) ) {
			if ( '' != $default ) {
				$font_size = astra_get_css_value( $font[ $device ], $font[ $device . '-unit' ], $default );
			} else {
				$font_size = astra_get_font_css_value( $font[ $device ], $font[ $device . '-unit' ] );
			}
		} elseif ( is_numeric( $font ) ) {
			$font_size = astra_get_css_value( $font );
		} else {
			$font_size = ( ! is_array( $font ) ) ? $font : '';
		}

		return $font_size;
	}
}

/**
 * Get Font Size value
 */
if ( ! function_exists( 'astra_get_font_css_value' ) ) {

	/**
	 * Get Font CSS value
	 *
	 * Syntax:
	 *
	 *  astra_get_font_css_value( VALUE, DEVICE, UNIT );
	 *
	 * E.g.
	 *
	 *  astra_get_css_value( VALUE, 'desktop', '%' );
	 *  astra_get_css_value( VALUE, 'tablet' );
	 *  astra_get_css_value( VALUE, 'mobile' );
	 *
	 * @param  mixed  $value        CSS value.
	 * @param  string $unit         CSS unit.
	 * @param  string $device       CSS device.
	 * @return mixed                CSS value depends on $unit & $device
	 */
	function astra_get_font_css_value( $value, $unit = 'px', $device = 'desktop' ) {

		// If value is empty then return blank.
		if ( '' == $value || ( 0 == $value && ! astra_zero_font_size_case() ) ) {
			return '';
		}

		$css_val = '';

		switch ( $unit ) {
			case 'em':
			case 'vw':
			case 'rem':
			case '%':
						$css_val = esc_attr( $value ) . $unit;
				break;

			case 'px':
				if ( is_numeric( $value ) || strpos( $value, 'px' ) ) {
					$value            = intval( $value );
					$fonts            = array();
					$body_font_size   = astra_get_option( 'font-size-body' );
					$fonts['desktop'] = ( isset( $body_font_size['desktop'] ) && '' != $body_font_size['desktop'] ) ? $body_font_size['desktop'] : 15;
					$fonts['tablet']  = ( isset( $body_font_size['tablet'] ) && '' != $body_font_size['tablet'] ) ? $body_font_size['tablet'] : $fonts['desktop'];
					$fonts['mobile']  = ( isset( $body_font_size['mobile'] ) && '' != $body_font_size['mobile'] ) ? $body_font_size['mobile'] : $fonts['tablet'];

					if ( $fonts[ $device ] ) {
						$css_val = esc_attr( $value ) . 'px;font-size:' . ( esc_attr( $value ) / esc_attr( $fonts[ $device ] ) ) . 'rem';
					}
				} else {
					$css_val = esc_attr( $value );
				}
		}

		return $css_val;
	}
}

/**
 * Get Font family
 */
if ( ! function_exists( 'astra_get_font_family' ) ) {

	/**
	 * Get Font family
	 *
	 * Syntax:
	 *
	 *  astra_get_font_family( VALUE, DEFAULT );
	 *
	 * E.g.
	 *  astra_get_font_family( VALUE, '' );
	 *
	 * @since  1.0.19
	 *
	 * @param  string $value       CSS value.
	 * @return mixed               CSS value depends on $unit
	 */
	function astra_get_font_family( $value = '' ) {
		$system_fonts = Astra_Font_Families::get_system_fonts();
		if ( isset( $system_fonts[ $value ] ) && isset( $system_fonts[ $value ]['fallback'] ) ) {
			$value .= ',' . $system_fonts[ $value ]['fallback'];
		}

		return $value;
	}
}


/**
 * Get CSS value
 */
if ( ! function_exists( 'astra_get_css_value' ) ) {

	/**
	 * Get CSS value
	 *
	 * Syntax:
	 *
	 *  astra_get_css_value( VALUE, UNIT );
	 *
	 * E.g.
	 *
	 *  astra_get_css_value( VALUE, 'url' );
	 *  astra_get_css_value( VALUE, 'px' );
	 *  astra_get_css_value( VALUE, 'em' );
	 *
	 * @param  string $value        CSS value.
	 * @param  string $unit         CSS unit.
	 * @param  string $default      CSS default font.
	 * @return mixed               CSS value depends on $unit
	 */
	function astra_get_css_value( $value = '', $unit = 'px', $default = '' ) {

		if ( '' == $value && '' == $default ) {
			return $value;
		}

		$css_val = '';

		switch ( $unit ) {

			case 'font':
				if ( 'inherit' != $value ) {
					$value   = astra_get_font_family( $value );
					$css_val = $value;
				} elseif ( '' != $default ) {
					$css_val = $default;
				} else {
					$css_val = '';
				}
				break;

			case 'px':
			case '%':
				if ( 'inherit' === strtolower( $value ) || 'inherit' === strtolower( $default ) ) {
					return $value;
				}

				$value   = ( '' != $value ) ? $value : $default;
				$css_val = esc_attr( $value ) . $unit;
				break;

			case 'url':
				$css_val = $unit . '(' . esc_url( $value ) . ')';
				break;

			default:
				$value = ( '' != $value ) ? $value : $default;
				if ( '' != $value ) {
					$css_val = esc_attr( $value ) . $unit;
				}
		}

		return $css_val;
	}
}

/**
 * Adjust the background obj.
 */
if ( ! function_exists( 'astra_get_background_obj' ) ) {

	/**
	 * Adjust Brightness
	 *
	 * @param  array $bg_obj   Color code in HEX.
	 *
	 * @return array         Color code in HEX.
	 */
	function astra_get_background_obj( $bg_obj ) {

		$gen_bg_css = array();

		$bg_img   = isset( $bg_obj['background-image'] ) ? $bg_obj['background-image'] : '';
		$bg_color = isset( $bg_obj['background-color'] ) ? $bg_obj['background-color'] : '';
		$bg_type  = isset( $bg_obj['background-type'] ) ? $bg_obj['background-type'] : '';

		if ( '' !== $bg_type ) {
			switch ( $bg_type ) {
				case 'color':
					if ( '' !== $bg_img && '' !== $bg_color ) {
						$gen_bg_css['background-image'] = 'linear-gradient(to right, ' . $bg_color . ', ' . $bg_color . '), url(' . $bg_img . ');';
					} elseif ( '' === $bg_img ) {
						$gen_bg_css['background-color'] = $bg_color . ';';
					}
					break;

				case 'image':
					$overlay_type    = isset( $bg_obj['overlay-type'] ) ? $bg_obj['overlay-type'] : 'none';
					$overlay_color   = isset( $bg_obj['overlay-color'] ) ? $bg_obj['overlay-color'] : '';
					$overlay_opacity = isset( $bg_obj['overlay-opacity'] ) ? $bg_obj['overlay-opacity'] : '';
					$overlay_grad    = isset( $bg_obj['overlay-gradient'] ) ? $bg_obj['overlay-gradient'] : '';
					if ( '' !== $bg_img ) {
						if ( 'none' !== $overlay_type ) {
							if ( 'classic' === $overlay_type && '' !== $overlay_color ) {
								$updated_overlay_color = $overlay_color;

								// Compatibility of overlay color opacity to HEX & VAR colors.
								if ( '' !== $overlay_opacity ) {
									$is_linked_with_gcp = 'var' === substr( $overlay_color, 0, 3 );

									if ( $is_linked_with_gcp ) {
										$astra_gcp_instance    = new Astra_Global_Palette();
										$updated_overlay_color = $astra_gcp_instance->get_color_by_palette_variable( $overlay_color );
									}

									if ( '#' === $updated_overlay_color[0] ) {
										$updated_overlay_color = astra_hex_to_rgba( $updated_overlay_color, $overlay_opacity );
									}
								}

								$gen_bg_css['background-image'] = 'linear-gradient(to right, ' . $updated_overlay_color . ', ' . $updated_overlay_color . '), url(' . $bg_img . ');';
							} elseif ( 'gradient' === $overlay_type && '' !== $overlay_grad ) {
								$gen_bg_css['background-image'] = $overlay_grad . ', url(' . $bg_img . ');';
							} else {
								$gen_bg_css['background-image'] = 'url(' . $bg_img . ');';
							}
						} else {
							$gen_bg_css['background-image'] = 'url(' . $bg_img . ');';
						}
					}
					break;

				case 'gradient':
					if ( isset( $bg_color ) ) {
						$gen_bg_css['background-image'] = $bg_color . ';';
					}
					break;

				default:
					break;
			}
		} elseif ( '' !== $bg_color ) {
			$gen_bg_css['background-color'] = $bg_color . ';';
		}

		if ( '' !== $bg_img ) {
			if ( isset( $bg_obj['background-repeat'] ) ) {
				$gen_bg_css['background-repeat'] = esc_attr( $bg_obj['background-repeat'] );
			}

			if ( isset( $bg_obj['background-position'] ) ) {
				$gen_bg_css['background-position'] = esc_attr( $bg_obj['background-position'] );
			}

			if ( isset( $bg_obj['background-size'] ) ) {
				$gen_bg_css['background-size'] = esc_attr( $bg_obj['background-size'] );
			}

			if ( isset( $bg_obj['background-attachment'] ) ) {
				$gen_bg_css['background-attachment'] = esc_attr( $bg_obj['background-attachment'] );
			}
		}

		return $gen_bg_css;
	}
}

/**
 * Parse CSS
 */
if ( ! function_exists( 'astra_parse_css' ) ) {

	/**
	 * Parse CSS
	 *
	 * @param  array $css_output Array of CSS.
	 * @param  mixed $min_media  Min Media breakpoint.
	 * @param  mixed $max_media  Max Media breakpoint.
	 * @return string             Generated CSS.
	 */
	function astra_parse_css( $css_output = array(), $min_media = '', $max_media = '' ) {

		$parse_css = '';
		if ( is_array( $css_output ) && count( $css_output ) > 0 ) {

			foreach ( $css_output as $selector => $properties ) {

				if ( null === $properties ) {
					break;
				}

				if ( ! count( $properties ) ) {
					continue;
				}

				$temp_parse_css   = $selector . '{';
				$properties_added = 0;

				foreach ( $properties as $property => $value ) {

					if ( '' == $value && 0 !== $value ) {
						continue;
					}

					$properties_added++;
					$temp_parse_css .= $property . ':' . $value . ';';
				}

				$temp_parse_css .= '}';

				if ( $properties_added > 0 ) {
					$parse_css .= $temp_parse_css;
				}
			}

			if ( '' != $parse_css && ( '' !== $min_media || '' !== $max_media ) ) {

				$media_css       = '@media ';
				$min_media_css   = '';
				$max_media_css   = '';
				$media_separator = '';

				if ( '' !== $min_media ) {
					$min_media_css = '(min-width:' . $min_media . 'px)';
				}
				if ( '' !== $max_media ) {
					$max_media_css = '(max-width:' . $max_media . 'px)';
				}
				if ( '' !== $min_media && '' !== $max_media ) {
					$media_separator = ' and ';
				}

				$media_css .= $min_media_css . $media_separator . $max_media_css . '{' . $parse_css . '}';

				return $media_css;
			}
		}

		return $parse_css;
	}
}

/**
 * Return Theme options.
 */
if ( ! function_exists( 'astra_get_option' ) ) {

	/**
	 * Return Theme options.
	 *
	 * @param  string $option       Option key.
	 * @param  mixed  $default      Option default value.
	 * @param  string $deprecated   Option default value.
	 * @return mixed               Return option value.
	 */
	function astra_get_option( $option, $default = '', $deprecated = '' ) {

		if ( '' != $deprecated ) {
			$default = $deprecated;
		}

		$theme_options = Astra_Theme_Options::get_options();

		/**
		 * Filter the options array for Astra Settings.
		 *
		 * @since  1.0.20
		 * @var Array
		 */
		$theme_options = apply_filters( 'astra_get_option_array', $theme_options, $option, $default );

		$value = ( isset( $theme_options[ $option ] ) && '' !== $theme_options[ $option ] ) ? $theme_options[ $option ] : $default;

		/**
		 * Dynamic filter astra_get_option_$option.
		 * $option is the name of the Astra Setting, Refer Astra_Theme_Options::defaults() for option names from the theme.
		 *
		 * @since  1.0.20
		 * @var Mixed.
		 */
		return apply_filters( "astra_get_option_{$option}", $value, $option, $default );
	}
}

if ( ! function_exists( 'astra_update_option' ) ) {

	/**
	 * Update Theme options.
	 *
	 * @param  string $option option key.
	 * @param  Mixed  $value  option value.
	 * @return void
	 */
	function astra_update_option( $option, $value ) {

		do_action( "astra_before_update_option_{$option}", $value, $option );

		// Get all customizer options.
		$theme_options = get_option( ASTRA_THEME_SETTINGS );

		// Update value in options array.
		if ( ! is_array( $theme_options ) ) {
			$theme_options = array();
		}
		$theme_options[ $option ] = $value;

		update_option( ASTRA_THEME_SETTINGS, $theme_options );

		do_action( "astra_after_update_option_{$option}", $value, $option );
	}
}

if ( ! function_exists( 'astra_delete_option' ) ) {

	/**
	 * Update Theme options.
	 *
	 * @param  string $option option key.
	 * @return void
	 */
	function astra_delete_option( $option ) {

		do_action( "astra_before_delete_option_{$option}", $option );

		// Get all customizer options.
		$theme_options = get_option( ASTRA_THEME_SETTINGS );

		// Update value in options array.
		unset( $theme_options[ $option ] );

		update_option( ASTRA_THEME_SETTINGS, $theme_options );

		do_action( "astra_after_delete_option_{$option}", $option );
	}
}

/**
 * Return Theme options from postmeta.
 */
if ( ! function_exists( 'astra_get_option_meta' ) ) {

	/**
	 * Return Theme options from postmeta.
	 *
	 * @param  string  $option_id Option ID.
	 * @param  string  $default   Option default value.
	 * @param  boolean $only_meta Get only meta value.
	 * @param  string  $extension Is value from extension.
	 * @param  string  $post_id   Get value from specific post by post ID.
	 * @return Mixed             Return option value.
	 */
	function astra_get_option_meta( $option_id, $default = '', $only_meta = false, $extension = '', $post_id = '' ) {

		$post_id = ( '' != $post_id ) ? $post_id : astra_get_post_id();

		$value = astra_get_option( $option_id, $default );

		// Get value from option 'post-meta'.
		if ( is_singular() || ( is_home() && ! is_front_page() ) ) {

			$value = get_post_meta( $post_id, $option_id, true );

			if ( empty( $value ) || 'default' == $value ) {

				if ( true === $only_meta ) {
					return false;
				}

				$value = astra_get_option( $option_id, $default );
			}
		}

		/**
		 * Dynamic filter astra_get_option_meta_$option.
		 * $option_id is the name of the Astra Meta Setting.
		 *
		 * @since  1.0.20
		 * @var Mixed.
		 */
		return apply_filters( "astra_get_option_meta_{$option_id}", $value, $default, $default );
	}
}

/**
 * Helper function to get the current post id.
 */
if ( ! function_exists( 'astra_get_post_id' ) ) {

	/**
	 * Get post ID.
	 *
	 * @param  string $post_id_override Get override post ID.
	 * @return number                   Post ID.
	 */
	function astra_get_post_id( $post_id_override = '' ) {

		if ( null == Astra_Theme_Options::$post_id ) {
			global $post;

			$post_id = 0;

			if ( is_home() ) {
				$post_id = get_option( 'page_for_posts' );
			} elseif ( is_archive() ) {
				global $wp_query;
				$post_id = $wp_query->get_queried_object_id();
			} elseif ( isset( $post->ID ) && ! is_search() && ! is_category() ) {
				$post_id = $post->ID;
			}

			Astra_Theme_Options::$post_id = $post_id;
		}

		return apply_filters( 'astra_get_post_id', Astra_Theme_Options::$post_id, $post_id_override );
	}
}


/**
 * Display classes for primary div
 */
if ( ! function_exists( 'astra_primary_class' ) ) {

	/**
	 * Display classes for primary div
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return void        Echo classes.
	 */
	function astra_primary_class( $class = '' ) {

		// Separates classes with a single space, collates classes for body element.
		echo 'class="' . esc_attr( join( ' ', astra_get_primary_class( $class ) ) ) . '"';
	}
}

/**
 * Retrieve the classes for the primary element as an array.
 */
if ( ! function_exists( 'astra_get_primary_class' ) ) {

	/**
	 * Retrieve the classes for the primary element as an array.
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array        Return array of classes.
	 */
	function astra_get_primary_class( $class = '' ) {

		// array of class names.
		$classes = array();

		// default class for content area.
		$classes[] = 'content-area';

		// primary base class.
		$classes[] = 'primary';

		if ( ! empty( $class ) ) {
			if ( ! is_array( $class ) ) {
				$class = preg_split( '#\s+#', $class );
			}
			$classes = array_merge( $classes, $class );
		} else {

			// Ensure that we always coerce class to being an array.
			$class = array();
		}

		// Filter primary div class names.
		$classes = apply_filters( 'astra_primary_class', $classes, $class );

		$classes = array_map( 'sanitize_html_class', $classes );

		return array_unique( $classes );
	}
}

/**
 * Display classes for secondary div
 */
if ( ! function_exists( 'astra_secondary_class' ) ) {

	/**
	 * Retrieve the classes for the secondary element as an array.
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return void        echo classes.
	 */
	function astra_secondary_class( $class = '' ) {

		// Separates classes with a single space, collates classes for body element.
		echo 'class="' . esc_attr( join( ' ', astra_get_secondary_class( $class ) ) ) . '"';
	}
}

/**
 * Retrieve the classes for the secondary element as an array.
 */
if ( ! function_exists( 'astra_get_secondary_class' ) ) {

	/**
	 * Retrieve the classes for the secondary element as an array.
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array        Return array of classes.
	 */
	function astra_get_secondary_class( $class = '' ) {

		// array of class names.
		$classes = array();

		// default class from widget area.
		$classes[] = 'widget-area';

		// secondary base class.
		$classes[] = 'secondary';

		if ( ! empty( $class ) ) {
			if ( ! is_array( $class ) ) {
				$class = preg_split( '#\s+#', $class );
			}
			$classes = array_merge( $classes, $class );
		} else {

			// Ensure that we always coerce class to being an array.
			$class = array();
		}

		// Filter secondary div class names.
		$classes = apply_filters( 'astra_secondary_class', $classes, $class );

		$classes = array_map( 'sanitize_html_class', $classes );

		return array_unique( $classes );
	}
}

/**
 * Get post format
 */
if ( ! function_exists( 'astra_get_post_format' ) ) {

	/**
	 * Get post format
	 *
	 * @param  string $post_format_override Override post formate.
	 * @return string                       Return post format.
	 */
	function astra_get_post_format( $post_format_override = '' ) {

		if ( ( is_home() ) || is_archive() ) {
			$post_format = 'blog';
		} else {
			$post_format = get_post_format();
		}

		return apply_filters( 'astra_get_post_format', $post_format, $post_format_override );
	}
}

/**
 * Wrapper function for get_the_title() for blog post.
 */
if ( ! function_exists( 'astra_the_post_title' ) ) {

	/**
	 * Wrapper function for get_the_title() for blog post.
	 *
	 * Displays title only if the page title bar is disabled.
	 *
	 * @since 1.0.15
	 * @param string $before Optional. Content to prepend to the title.
	 * @param string $after  Optional. Content to append to the title.
	 * @param int    $post_id Optional, default to 0. Post id.
	 * @param bool   $echo   Optional, default to true.Whether to display or return.
	 * @return string|void String if $echo parameter is false.
	 */
	function astra_the_post_title( $before = '', $after = '', $post_id = 0, $echo = true ) {

		$enabled = apply_filters( 'astra_the_post_title_enabled', true );
		if ( $enabled ) {

			$title  = astra_get_the_title( $post_id );
			$before = apply_filters( 'astra_the_post_title_before', $before );
			$after  = apply_filters( 'astra_the_post_title_after', $after );

			// This will work same as `the_title` function but with Custom Title if exits.
			if ( $echo ) {
				echo $before . $title . $after; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				return $before . $title . $after;
			}
		}
	}
}

/**
 * Wrapper function for the_title()
 */
if ( ! function_exists( 'astra_the_title' ) ) {

	/**
	 * Wrapper function for the_title()
	 *
	 * Displays title only if the page title bar is disabled.
	 *
	 * @param string $before Optional. Content to prepend to the title.
	 * @param string $after  Optional. Content to append to the title.
	 * @param int    $post_id Optional, default to 0. Post id.
	 * @param bool   $echo   Optional, default to true.Whether to display or return.
	 * @return string|void String if $echo parameter is false.
	 */
	function astra_the_title( $before = '', $after = '', $post_id = 0, $echo = true ) {

		$title             = '';
		$post_type         = strval( get_post_type() );
		$blog_post_title   = astra_get_option( 'ast-dynamic-archive-' . $post_type . '-structure', array( 'ast-dynamic-archive-' . $post_type . '-title', 'ast-dynamic-archive-' . $post_type . '-description' ) );
		$single_post_title = astra_get_option( 'ast-dynamic-single-' . $post_type . '-structure', 'page' === $post_type ? array( 'ast-dynamic-single-' . $post_type . '-image', 'ast-dynamic-single-' . $post_type . '-title' ) : array( 'ast-dynamic-single-' . $post_type . '-title', 'ast-dynamic-single-' . $post_type . '-meta' ) );

		if ( ( ! is_singular() && ( in_array( 'ast-dynamic-archive-' . $post_type . '-title', $blog_post_title ) || in_array( 'ast-dynamic-archive-' . $post_type . '-meta', $blog_post_title ) ) )
			|| ( is_singular() && ( in_array( 'ast-dynamic-single-' . $post_type . '-title', $single_post_title ) || in_array( 'ast-dynamic-single-' . $post_type . '-meta', $single_post_title ) ) )
		) {
			if ( apply_filters( 'astra_the_title_enabled', true ) ) {

				$title  = astra_get_the_title( $post_id );
				$before = apply_filters( 'astra_the_title_before', $before );
				$after  = apply_filters( 'astra_the_title_after', $after );

				$title = $before . $title . $after;
			}
		}

		// This will work same as `the_title` function but with Custom Title if exits.
		if ( $echo ) {
			echo $title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $title;
		}
	}
}

/**
 * Wrapper function for get_the_title()
 */
if ( ! function_exists( 'astra_get_the_title' ) ) {

	/**
	 * Wrapper function for get_the_title()
	 *
	 * Return title for Title Bar and Normal Title.
	 *
	 * @param int  $post_id Optional, default to 0. Post id.
	 * @param bool $echo   Optional, default to false. Whether to display or return.
	 * @return string|void String if $echo parameter is false.
	 */
	function astra_get_the_title( $post_id = 0, $echo = false ) {

		$title = '';
		if ( $post_id || is_singular() ) {
			$title = get_the_title( $post_id );
		} else {
			if ( is_front_page() && is_home() ) {
				// Default homepage.
				$title = apply_filters( 'astra_the_default_home_page_title', esc_html__( 'Home', 'astra' ) );
			} elseif ( is_home() ) {
				// blog page.
				$title = apply_filters( 'astra_the_blog_home_page_title', get_the_title( get_option( 'page_for_posts', true ) ) );
			} elseif ( is_404() ) {
				// for 404 page - title always display.
				$title = apply_filters( 'astra_the_404_page_title', esc_html__( 'This page doesn\'t seem to exist.', 'astra' ) );

				// for search page - title always display.
			} elseif ( is_search() ) {

				/* translators: 1: search string */
				$title = apply_filters( 'astra_the_search_page_title', sprintf( astra_get_option( 'section-search-page-title-custom-title' ) . ' %s', '<span>' . get_search_query() . '</span>' ) );

			} elseif ( class_exists( 'WooCommerce' ) && is_shop() ) {

				$title = woocommerce_page_title( false );

			} elseif ( is_archive() ) {

				$title = get_the_archive_title();

			}
		}

		$title = apply_filters( 'astra_the_title', $title, $post_id );

		// This will work same as `get_the_title` function but with Custom Title if exits.
		if ( $echo ) {
			echo $title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $title;
		}
	}
}

/**
 * Don't apply direct new layouts to legacy users.
 *
 * @since 4.0.0
 * @return boolean false if it is an existing user , true if not.
 */
function astra_use_dynamic_blog_layouts() {
	$astra_settings                         = get_option( ASTRA_THEME_SETTINGS );
	$astra_settings['dynamic-blog-layouts'] = isset( $astra_settings['dynamic-blog-layouts'] ) ? $astra_settings['dynamic-blog-layouts'] : true;
	return apply_filters( 'astra_get_option_dynamic_blog_layouts', $astra_settings['dynamic-blog-layouts'] );
}

/**
 * Get taxonomy archive banner for layout 1.
 *
 * @since 4.0.0
 */
function astra_get_taxonomy_banner_legacy_layout() {
	$post_type        = strval( get_post_type() );
	$banner_structure = is_search() ? astra_get_option( 'section-search-page-title-structure' ) : astra_get_option( 'ast-dynamic-archive-' . $post_type . '-structure', array( 'ast-dynamic-archive-' . $post_type . '-title', 'ast-dynamic-archive-' . $post_type . '-description' ) );

	if ( empty( $banner_structure ) ) {
		return;
	}

	?>
		<section class="ast-archive-description">
			<?php
			foreach ( $banner_structure as $metaval ) {
				$meta_key = 'archive-' . astra_get_last_meta_word( $metaval );
				switch ( $meta_key ) {
					case 'archive-title':
						do_action( 'astra_before_archive_title' );
						if ( is_search() ) {
							$title = apply_filters( 'astra_the_search_page_title', sprintf( /* translators: 1: search string */ astra_get_option( 'section-search-page-title-custom-title' ) . ' %s', '<span>' . get_search_query() . '</span>' ) );
							?>
							 <h1 class="page-title ast-archive-title"> <?php echo $title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> </h1>
																				  <?php
						} else {
							add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );
							the_archive_title( '<h1 class="page-title ast-archive-title">', '</h1>' );
							remove_filter( 'get_the_archive_title_prefix', '__return_empty_string' );
						}
						do_action( 'astra_after_archive_title' );
						break;
					case 'archive-breadcrumb':
						if ( ! is_author() ) {
							do_action( 'astra_before_archive_breadcrumb' );
							echo astra_get_breadcrumb(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							do_action( 'astra_after_archive_breadcrumb' );
						}
						break;
					case 'archive-description':
						do_action( 'astra_before_archive_description' );
						if ( is_search() ) {
							if ( have_posts() ) {
								echo wp_kses_post( wpautop( astra_get_option( 'section-search-page-title-found-custom-description' ) ) );
							} else {
								echo wp_kses_post( wpautop( astra_get_option( 'section-search-page-title-not-found-custom-description' ) ) );
							}
						} else {
							echo wp_kses_post( wpautop( get_the_archive_description() ) );
						}
						do_action( 'astra_after_archive_description' );
						break;
				}
			}
			?>
		</section>
	<?php
}

/**
 * Archive Page Title
 */
if ( ! function_exists( 'astra_archive_page_info' ) ) {

	/**
	 * Wrapper function for the_title()
	 *
	 * Displays title only if the page title bar is disabled.
	 */
	function astra_archive_page_info() {

		if ( apply_filters( 'astra_the_title_enabled', true ) ) {

			// Author.
			if ( is_author() ) {
				$author_name        = get_the_author() ? esc_attr( strval( get_the_author() ) ) : '';
				$author_name_html   = ( true === astra_check_is_structural_setup() && $author_name ) ? __( 'Author name: ', 'astra' ) . $author_name : $author_name;
				$author_description = get_the_author_meta( 'description' );
				/** @psalm-suppress RedundantConditionGivenDocblockType */
				$author_description_html = wp_kses_post( $author_description );
				?>

				<section class="ast-author-box ast-archive-description">
					<div class="ast-author-bio">
						<?php do_action( 'astra_before_archive_title' ); ?>
						<h1 class='page-title ast-archive-title'><?php echo esc_html( apply_filters( 'astra_author_page_title', $author_name_html ) ); ?></h1>
						<?php do_action( 'astra_after_archive_title' ); ?>
						<p><?php echo $author_description_html; ?></p>
						<?php do_action( 'astra_after_archive_description' ); ?>
					</div>
					<div class="ast-author-avatar">
						<?php echo get_avatar( get_the_author_meta( 'email' ), 120 ); ?>
					</div>
				</section>

				<?php

			} else {
				$taxonomy_banner_content      = astra_get_taxonomy_banner_legacy_layout();
				$taxonomy_banner_content_html = is_string( $taxonomy_banner_content ) ? wp_kses_post( $taxonomy_banner_content ) : '';
				echo $taxonomy_banner_content_html;
			}
		}
	}

	add_action( 'astra_archive_header', 'astra_archive_page_info' );
}

/**
 * Adjust the HEX color brightness
 */
if ( ! function_exists( 'astra_adjust_brightness' ) ) {

	/**
	 * Adjust Brightness
	 *
	 * @param  string $hex   Color code in HEX.
	 * @param  number $steps brightness value.
	 * @param  string $type  brightness is reverse or default.
	 * @return string        Color code in HEX.
	 */
	function astra_adjust_brightness( $hex, $steps, $type ) {

		// Get rgb vars.
		$hex = str_replace( '#', '', $hex );

		// Return if non hex.
		if ( function_exists( 'ctype_xdigit' ) && is_callable( 'ctype_xdigit' ) ) {
			if ( ! ctype_xdigit( $hex ) ) {
				return $hex;
			}
		} else {
			if ( ! preg_match( '/^[a-f0-9]{2,}$/i', $hex ) ) {
				return $hex;
			}
		}

		$shortcode_atts = array(
			'r' => hexdec( substr( $hex, 0, 2 ) ),
			'g' => hexdec( substr( $hex, 2, 2 ) ),
			'b' => hexdec( substr( $hex, 4, 2 ) ),
		);

		// Should we darken the color?
		if ( 'reverse' == $type && $shortcode_atts['r'] + $shortcode_atts['g'] + $shortcode_atts['b'] > 382 ) {
			$steps = -$steps;
		} elseif ( 'darken' == $type ) {
			$steps = -$steps;
		}

		// Build the new color.
		$steps = max( -255, min( 255, $steps ) );

		$shortcode_atts['r'] = max( 0, min( 255, $shortcode_atts['r'] + $steps ) );
		$shortcode_atts['g'] = max( 0, min( 255, $shortcode_atts['g'] + $steps ) );
		$shortcode_atts['b'] = max( 0, min( 255, $shortcode_atts['b'] + $steps ) );

		$r_hex = str_pad( dechex( $shortcode_atts['r'] ), 2, '0', STR_PAD_LEFT );
		$g_hex = str_pad( dechex( $shortcode_atts['g'] ), 2, '0', STR_PAD_LEFT );
		$b_hex = str_pad( dechex( $shortcode_atts['b'] ), 2, '0', STR_PAD_LEFT );

		return '#' . $r_hex . $g_hex . $b_hex;
	}
} // End if.

/**
 * Convert colors from HEX to RGBA
 */
if ( ! function_exists( 'astra_hex_to_rgba' ) ) :

	/**
	 * Convert colors from HEX to RGBA
	 *
	 * @param  string $color   Color code in HEX.
	 * @param  mixed  $opacity Color code opacity.
	 * @return string           Color code in RGB or RGBA.
	 */
	function astra_hex_to_rgba( $color, $opacity = false ) {

		$default = 'rgb(0,0,0)';

		// Return default if no color provided.
		if ( empty( $color ) ) {
			return $default;
		}

		// Sanitize $color if "#" is provided.
		if ( '#' == $color[0] ) {
			$color = substr( $color, 1 );
		}

		// Check if color has 6 or 3 characters and get values.
		if ( 6 == strlen( $color ) ) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( 3 == strlen( $color ) ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return $default;
		}

		// Convert HEX to RGB.
		$rgb = array_map( 'hexdec', $hex );

		// Check if opacity is set(RGBA or RGB).
		if ( $opacity ) {
			if ( 1 < abs( $opacity ) ) {
				$opacity = 1.0;
			}
			$output = 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
		} else {
			$output = 'rgb(' . implode( ',', $rgb ) . ')';
		}

		// Return RGB(a) color string.
		return $output;
	}

endif;


if ( ! function_exists( 'astra_enable_page_builder_compatibility' ) ) :

	/**
	 * Allow filter to enable/disable page builder compatibility.
	 *
	 * @see  https://wpastra.com/docs/recommended-settings-beaver-builder-astra/
	 * @see  https://wpastra.com/docs/recommended-settings-for-elementor/
	 *
	 * @since  1.2.2
	 * @return  bool True - If the page builder compatibility is enabled. False - IF the page builder compatibility is disabled.
	 */
	function astra_enable_page_builder_compatibility() {
		return apply_filters( 'astra_enable_page_builder_compatibility', true );
	}

endif;


if ( ! function_exists( 'astra_get_pro_url' ) ) :
	/**
	 * Returns an URL with utm tags
	 * the admin settings page.
	 *
	 * @param string $url    URL fo the site.
	 * @param string $source utm source.
	 * @param string $medium utm medium.
	 * @param string $campaign utm campaign.
	 * @return mixed
	 */
	function astra_get_pro_url( $url, $source = '', $medium = '', $campaign = '' ) {

		$astra_pro_url = trailingslashit( $url );

		// Set up our URL if we have a source.
		if ( ! empty( $source ) ) {
			$astra_pro_url = add_query_arg( 'utm_source', sanitize_text_field( $source ), $url );
		}
		// Set up our URL if we have a medium.
		if ( ! empty( $medium ) ) {
			$astra_pro_url = add_query_arg( 'utm_medium', sanitize_text_field( $medium ), $astra_pro_url );
		}
		// Set up our URL if we have a campaign.
		if ( ! empty( $campaign ) ) {
			$astra_pro_url = add_query_arg( 'utm_campaign', sanitize_text_field( $campaign ), $astra_pro_url );
		}

		$astra_pro_url = apply_filters( 'astra_get_pro_url', $astra_pro_url, $url );
		$astra_pro_url = remove_query_arg( 'bsf', $astra_pro_url );

		$ref = get_option( 'astra_partner_url_param', '' );
		if ( ! empty( $ref ) ) {
			$astra_pro_url = add_query_arg( 'bsf', sanitize_text_field( $ref ), $astra_pro_url );
		}

		return $astra_pro_url;
	}

endif;


/**
 * Search Form
 */
if ( ! function_exists( 'astra_get_search_form' ) ) :
	/**
	 * Display search form.
	 *
	 * @param bool $echo Default to echo and not return the form.
	 * @return string|void String when $echo is false.
	 */
	function astra_get_search_form( $echo = true ) {

		$form = get_search_form(
			array(
				'input_placeholder' => apply_filters( 'astra_search_field_placeholder', esc_attr_x( 'Search...', 'placeholder', 'astra' ) ),
				'data_attributes'   => apply_filters( 'astra_search_field_toggle_data_attrs', '' ),
				'input_value'       => get_search_query(),
				'show_input_submit' => false,
			)
		);

		/**
		 * Filters the HTML output of the search form.
		 *
		 * @param string $form The search form HTML output.
		 */
		$result = apply_filters( 'astra_get_search_form', $form );

		if ( null === $result ) {
			$result = $form;
		}

		if ( $echo ) {
			echo $result; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $result;
		}
	}

endif;

/**
 * Check if we're being delivered AMP
 *
 * @return bool
 */
function astra_is_amp_endpoint() {
	return function_exists( 'is_amp_endpoint' ) && is_amp_endpoint();
}

/*
 * Get Responsive Spacing
 */
if ( ! function_exists( 'astra_responsive_spacing' ) ) {

	/**
	 * Get Spacing value
	 *
	 * @param  array  $option    CSS value.
	 * @param  string $side  top | bottom | left | right.
	 * @param  string $device  CSS device.
	 * @param  string $default Default value.
	 * @param  string $prefix Prefix value.
	 * @return mixed
	 */
	function astra_responsive_spacing( $option, $side = '', $device = 'desktop', $default = '', $prefix = '' ) {

		if ( isset( $option[ $device ][ $side ] ) && isset( $option[ $device . '-unit' ] ) ) {
			$spacing = astra_get_css_value( $option[ $device ][ $side ], $option[ $device . '-unit' ], $default );
		} elseif ( is_numeric( $option ) ) {
			$spacing = astra_get_css_value( $option );
		} else {
			$spacing = ( ! is_array( $option ) ) ? $option : '';
		}

		if ( '' !== $prefix && '' !== $spacing ) {
			return $prefix . $spacing;
		}
		return $spacing;
	}
}

/**
 * Get the tablet breakpoint value.
 *
 * @param mixed $min min.
 * @param mixed $max max.
 *
 * @since 2.4.0
 *
 * @return number $breakpoint.
 */
function astra_get_tablet_breakpoint( $min = '', $max = '' ) {

	$update_breakpoint = astra_get_option( 'can-update-theme-tablet-breakpoint', true );

	// Change default for new users.
	$default = ( true === $update_breakpoint ) ? 921 : 768;

	$header_breakpoint = apply_filters( 'astra_tablet_breakpoint', $default );

	if ( '' !== $min ) {
		$header_breakpoint = $header_breakpoint - $min;
	} elseif ( '' !== $max ) {
		$header_breakpoint = $header_breakpoint + $max;
	}

	return absint( $header_breakpoint );
}

/**
 * Get the mobile breakpoint value.
 *
 * @param mixed $min min.
 * @param mixed $max max.
 *
 * @since 2.4.0
 *
 * @return number header_breakpoint.
 */
function astra_get_mobile_breakpoint( $min = '', $max = '' ) {

	$header_breakpoint = apply_filters( 'astra_mobile_breakpoint', 544 );

	if ( '' !== $min ) {
		$header_breakpoint = $header_breakpoint - $min;
	} elseif ( '' !== $max ) {
		$header_breakpoint = $header_breakpoint + $max;
	}

	return absint( $header_breakpoint );
}

/*
 * Apply CSS for the element
 */
if ( ! function_exists( 'astra_color_responsive_css' ) ) {

	/**
	 * Astra Responsive Colors
	 *
	 * @param  array  $setting      Responsive colors.
	 * @param  string $css_property CSS property.
	 * @param  string $selector     CSS selector.
	 * @return string               Dynamic responsive CSS.
	 */
	function astra_color_responsive_css( $setting, $css_property, $selector ) {
		$css = '';
		if ( isset( $setting['desktop'] ) && ! empty( $setting['desktop'] ) ) {
			$css .= $selector . '{' . $css_property . ':' . esc_attr( $setting['desktop'] ) . ';}';
		}
		if ( isset( $setting['tablet'] ) && ! empty( $setting['tablet'] ) ) {
			$css .= '@media (max-width:' . astra_get_tablet_breakpoint() . 'px) {' . $selector . '{' . $css_property . ':' . esc_attr( $setting['tablet'] ) . ';} }';
		}
		if ( isset( $setting['mobile'] ) && ! empty( $setting['mobile'] ) ) {
			$css .= '@media (max-width:' . astra_get_mobile_breakpoint() . 'px) {' . $selector . '{' . $css_property . ':' . esc_attr( $setting['mobile'] ) . ';} }';
		}
		return $css;
	}
}

if ( ! function_exists( 'astra_check_is_bb_themer_layout' ) ) :

	/**
	 * Check if layout is bb themer's layout
	 */
	function astra_check_is_bb_themer_layout() {

		$is_layout = false;

		$post_type = get_post_type();
		$post_id   = get_the_ID();

		if ( 'fl-theme-layout' === $post_type && $post_id ) {

			$is_layout = true;
		}

		return $is_layout;
	}

endif;


if ( ! function_exists( 'astra_is_white_labelled' ) ) :

	/**
	 * Check if white label option is enabled in astra pro plugin
	 */
	function astra_is_white_labelled() {

		if ( is_callable( 'Astra_Ext_White_Label_Markup::show_branding' ) && ! Astra_Ext_White_Label_Markup::show_branding() ) {
			return apply_filters( 'astra_is_white_labelled', true );
		}

		return apply_filters( 'astra_is_white_labelled', false );
	}

endif;

/**
 * Get the value for font-display property.
 *
 * @since 1.8.6
 * @return string
 */
function astra_get_fonts_display_property() {
	return apply_filters( 'astra_fonts_display_property', 'fallback' );
}

/**
 * Generate Responsive Background Color CSS.
 *
 * @param array  $bg_obj_res array of background object.
 * @param string $device CSS for which device.
 * @return array
 */
function astra_get_responsive_background_obj( $bg_obj_res, $device ) {

	$gen_bg_css = array();

	if ( ! is_array( $bg_obj_res ) ) {
		return;
	}

	$bg_obj      = $bg_obj_res[ $device ];
	$bg_img      = isset( $bg_obj['background-image'] ) ? $bg_obj['background-image'] : '';
	$bg_tab_img  = isset( $bg_obj_res['tablet']['background-image'] ) ? $bg_obj_res['tablet']['background-image'] : '';
	$bg_desk_img = isset( $bg_obj_res['desktop']['background-image'] ) ? $bg_obj_res['desktop']['background-image'] : '';
	$bg_color    = isset( $bg_obj['background-color'] ) ? $bg_obj['background-color'] : '';
	$tablet_css  = ( isset( $bg_obj_res['tablet']['background-image'] ) && $bg_obj_res['tablet']['background-image'] ) ? true : false;
	$desktop_css = ( isset( $bg_obj_res['desktop']['background-image'] ) && $bg_obj_res['desktop']['background-image'] ) ? true : false;

	$bg_type = ( isset( $bg_obj['background-type'] ) && $bg_obj['background-type'] ) ? $bg_obj['background-type'] : '';

	if ( '' !== $bg_type ) {
		switch ( $bg_type ) {
			case 'color':
				if ( '' !== $bg_img && '' !== $bg_color ) {
					$gen_bg_css['background-image'] = 'linear-gradient(to right, ' . $bg_color . ', ' . $bg_color . '), url(' . $bg_img . ');';
				} elseif ( 'mobile' === $device ) {
					if ( $desktop_css ) {
						$gen_bg_css['background-image'] = 'linear-gradient(to right, ' . $bg_color . ', ' . $bg_color . '), url(' . $bg_desk_img . ');';
					} elseif ( $tablet_css ) {
						$gen_bg_css['background-image'] = 'linear-gradient(to right, ' . $bg_color . ', ' . $bg_color . '), url(' . $bg_tab_img . ');';
					} else {
						if ( '' !== $bg_color ) {
							$gen_bg_css['background-color'] = $bg_color . ';';
							$gen_bg_css['background-image'] = 'none;';
						}
					}
				} elseif ( 'tablet' === $device ) {
					if ( $desktop_css ) {
						$gen_bg_css['background-image'] = 'linear-gradient(to right, ' . $bg_color . ', ' . $bg_color . '), url(' . $bg_desk_img . ');';
					} else {
						if ( '' !== $bg_color ) {
							$gen_bg_css['background-color'] = $bg_color . ';';
							$gen_bg_css['background-image'] = 'none;';
						}
					}
				} elseif ( '' === $bg_img ) {
					$gen_bg_css['background-color'] = $bg_color . ';';
					$gen_bg_css['background-image'] = 'none;';
				}
				break;

			case 'image':
				/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$overlay_type = isset( $bg_obj['overlay-type'] ) ? $bg_obj['overlay-type'] : 'none';
				/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$overlay_color = isset( $bg_obj['overlay-color'] ) ? $bg_obj['overlay-color'] : '';
				/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$overlay_grad = isset( $bg_obj['overlay-gradient'] ) ? $bg_obj['overlay-gradient'] : '';
				/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$overlay_opacity = isset( $bg_obj['overlay-opacity'] ) ? $bg_obj['overlay-opacity'] : '';
				/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

				if ( '' !== $bg_img ) {
					if ( 'none' !== $overlay_type ) {
						if ( 'classic' === $overlay_type && '' !== $overlay_color ) {
							$updated_overlay_color = $overlay_color;

							// Compatibility of overlay color opacity to HEX & VAR colors.
							if ( '' !== $overlay_opacity ) {
								$is_linked_with_gcp = 'var' === substr( $overlay_color, 0, 3 );

								if ( $is_linked_with_gcp ) {
									$astra_gcp_instance    = new Astra_Global_Palette();
									$updated_overlay_color = $astra_gcp_instance->get_color_by_palette_variable( $overlay_color );
								}

								if ( '#' === $updated_overlay_color[0] ) {
									$updated_overlay_color = astra_hex_to_rgba( $updated_overlay_color, $overlay_opacity );
								}
							}

							$gen_bg_css['background-image'] = 'linear-gradient(to right, ' . $updated_overlay_color . ', ' . $updated_overlay_color . '), url(' . $bg_img . ');';
						} elseif ( 'gradient' === $overlay_type && '' !== $overlay_grad ) {
							$gen_bg_css['background-image'] = $overlay_grad . ', url(' . $bg_img . ');';
						} else {
							$gen_bg_css['background-image'] = 'url(' . $bg_img . ');';
						}
					} else {
						$gen_bg_css['background-image'] = 'url(' . $bg_img . ');';
					}
				}
				break;

			case 'gradient':
				if ( isset( $bg_color ) ) {
					$gen_bg_css['background-image'] = $bg_color . ';';
				}
				break;

			default:
				break;
		}
	} elseif ( '' !== $bg_color ) {
		$gen_bg_css['background-color'] = $bg_color . ';';
	}

	if ( '' !== $bg_img ) {
		if ( isset( $bg_obj['background-repeat'] ) ) {
			$gen_bg_css['background-repeat'] = esc_attr( $bg_obj['background-repeat'] );
		}

		if ( isset( $bg_obj['background-position'] ) ) {
			$gen_bg_css['background-position'] = esc_attr( $bg_obj['background-position'] );
		}

		if ( isset( $bg_obj['background-size'] ) ) {
			$gen_bg_css['background-size'] = esc_attr( $bg_obj['background-size'] );
		}

		if ( isset( $bg_obj['background-attachment'] ) ) {
			$gen_bg_css['background-attachment'] = esc_attr( $bg_obj['background-attachment'] );
		}
	}

	return $gen_bg_css;
}

/**
 * Common function to check is pagination is enabled on current page.
 *
 * @since 3.0.1
 * @return boolean
 */
function astra_check_pagination_enabled() {
	global  $wp_query;

	return ( $wp_query->max_num_pages > 1 && apply_filters( 'astra_pagination_enabled', true ) );
}

/**
 * Verify is current post comments are enabled or not for applying dynamic CSS.
 *
 * @since 3.0.1
 * @return boolean
 */
function astra_check_current_post_comment_enabled() {
	return ( is_singular() && comments_open() );
}

/**
 * Dont apply zero size to existing user.
 *
 * @since 3.6.9
 * @return boolean false if it is an existing user , true if not.
 */
function astra_zero_font_size_case() {
	$astra_settings                                  = get_option( ASTRA_THEME_SETTINGS );
	$astra_settings['astra-zero-font-size-case-css'] = isset( $astra_settings['astra-zero-font-size-case-css'] ) ? false : true;
	return apply_filters( 'astra_zero_font_size_case', $astra_settings['astra-zero-font-size-case-css'] );
}

/**
 * Check the WordPress version.
 *
 * @since  2.5.4
 * @param string $version   WordPress version to compare with the current version.
 * @param mixed  $compare   Comparison value i.e > or < etc.
 * @return bool|null            True/False based on the  $version and $compare value.
 */
function astra_wp_version_compare( $version, $compare ) {
	return version_compare( get_bloginfo( 'version' ), $version, $compare );
}

/**
 * Check if existing setup is live with old block editor compatibilities.
 *
 * @return bool true|false.
 */
function astra_block_based_legacy_setup() {
	$astra_settings = get_option( ASTRA_THEME_SETTINGS );
	$legacy_setup   = ( isset( $astra_settings['blocks-legacy-setup'] ) && isset( $astra_settings['wp-blocks-ui'] ) && 'legacy' === $astra_settings['wp-blocks-ui'] ) ? true : false;
	return $legacy_setup;
}

/**
 * Check is new structural things are updated.
 *
 * @return bool true|false.
 */
function astra_check_is_structural_setup() {
	$astra_settings = get_option( ASTRA_THEME_SETTINGS );
	return apply_filters( 'astra_get_option_customizer-default-layout-update', isset( $astra_settings['customizer-default-layout-update'] ) ? false : true ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
}

/**
 * Check if the user is old sidebar user.
 *
 * @since 3.9.4
 * @return bool true|false.
 */
function astra_check_old_sidebar_user() {
	$astra_settings = get_option( ASTRA_THEME_SETTINGS );
	return apply_filters( 'astra_old_global_sidebar_defaults', isset( $astra_settings['astra-old-global-sidebar-default'] ) ? false : true );
}

/**
 * Check if user is old for hiding/showing password icon field for login my-account form.
 *
 * @since 3.9.2
 * @return bool true|false.
 */
function astra_load_woocommerce_login_form_password_icon() {
	$astra_settings = get_option( ASTRA_THEME_SETTINGS );
	return apply_filters( 'astra_get_option_woo-show-password-icon', isset( $astra_settings['woo-show-password-icon'] ) ? false : true ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
}

/**
 * Function to add narrow width properties in the frontend.
 *
 * @since 4.0.0
 * @param string $location container layout for single-post, archives, pages, page meta.
 * @param string $narrow_container_max_width  dynamic container width in px.
 * @return string Parsed CSS based on $location and $narrow_container_max_width.
 */
function astra_narrow_container_width( $location, $narrow_container_max_width ) {

	if ( 'narrow-container' === $location ) {

		$narrow_container_css = array(
			'.ast-narrow-container .site-content > .ast-container' => array(
				'max-width' => astra_get_css_value( $narrow_container_max_width, 'px' ),
			),
		);

		// Remove Sidebar for Narrow Width Container Layout.
		if ( 'narrow-container' === astra_get_content_layout() ) {
			add_filter(
				'astra_page_layout',
				function() { // phpcs:ignore PHPCompatibility.FunctionDeclarations.NewClosure.Found
					return 'no-sidebar';
				}
			);
		}

		return astra_parse_css( $narrow_container_css, astra_get_tablet_breakpoint( '', 1 ) );

	} else {
		return '';
	}
}

/**
 * Function which will return the Sidebar Layout to determine default body classes for Editor.
 *
 * @since 4.2.0
 * @param string $post_type Post Type.
 * @return string Sidebar Layout.
 */
function astra_get_sidebar_layout_for_editor( $post_type ) {

	$sidebar_layout = astra_get_option( 'single-' . $post_type . '-sidebar-layout' );

	if ( 'default' === $sidebar_layout ) {
		$sidebar_layout = astra_get_option( 'site-sidebar-layout' );
	}

	return $sidebar_layout;
}

/**
 * Gets the SVG for the duotone filter definition.
 *
 * @since 4.2.2
 *
 * @param string $filter_id The ID of the filter.
 * @param array  $color    An array of color strings.
 * @return string An SVG with a duotone filter definition.
 */
function astra_get_filter_svg( $filter_id, $color ) {

	$duotone_values = array(
		'r' => array(),
		'g' => array(),
		'b' => array(),
		'a' => array(),
	);

	/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	$duotone_values['r'][] = $color['r'] / 255;
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$duotone_values['g'][] = $color['g'] / 255;
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$duotone_values['b'][] = $color['b'] / 255;
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$duotone_values['a'][] = $color['a'];
	ob_start();

	?>

	<svg
		xmlns="http://www.w3.org/2000/svg"
		viewBox="0 0 0 0"
		width="0"
		height="0"
		focusable="false"
		role="none"
		style="visibility: hidden; position: absolute; left: -9999px; overflow: hidden;"
	>
		<defs>
			<filter id="<?php echo esc_attr( $filter_id ); ?>">
				<feColorMatrix
					color-interpolation-filters="sRGB"
					type="matrix"
					values="
						.299 .587 .114 0 0
						.299 .587 .114 0 0
						.299 .587 .114 0 0
						.299 .587 .114 0 0
					"
				/>
				<feComponentTransfer color-interpolation-filters="sRGB" >
					<feFuncR type="table" tableValues="<?php echo esc_attr( implode( ' ', $duotone_values['r'] ) ); ?> <?php echo esc_attr( implode( ' ', $duotone_values['r'] ) ); ?>" />
					<feFuncG type="table" tableValues="<?php echo esc_attr( implode( ' ', $duotone_values['g'] ) ); ?> <?php echo esc_attr( implode( ' ', $duotone_values['g'] ) ); ?>" />
					<feFuncB type="table" tableValues="<?php echo esc_attr( implode( ' ', $duotone_values['b'] ) ); ?> <?php echo esc_attr( implode( ' ', $duotone_values['b'] ) ); ?>" />
					<feFuncA type="table" tableValues="<?php echo esc_attr( implode( ' ', $duotone_values['a'] ) ); ?> <?php echo esc_attr( implode( ' ', $duotone_values['a'] ) ); ?>" />
				</feComponentTransfer>
				<feComposite in2="SourceGraphic" operator="in" />
			</filter>
		</defs>
	</svg>

	<?php

	$svg = ob_get_clean();

	// Clean up the whitespace.
	$svg = preg_replace( "/[\r\n\t ]+/", ' ', $svg );
	$svg = str_replace( '> <', '><', $svg );
	$svg = trim( $svg );

	return $svg;
}

/**
 * Converts HEX to RGB.
 *
 * @since 4.2.2
 *
 * @param string $hex Hex color.
 * @return array split version of rgb.
 */
function astra_hex_to_rgb( $hex ) {
	// @codingStandardsIgnoreStart
	/**
	 * @psalm-suppress PossiblyNullArrayAccess
	 */
	list($r, $g, $b) = sscanf( $hex, '#%02x%02x%02x' );

	// @codingStandardsIgnoreEnd
	return array(
		'r' => $r,
		'g' => $g,
		'b' => $b,
		'a' => 1,
	);
}

/**
 * Converts RGBA to split array RGBA.
 *
 * @since 4.2.2
 *
 * @param string $rgba RGBA value.
 * @return array split version of rgba.
 */
function astra_split_rgba( $rgba ) {
	// Remove the "rgba(" and ")" from the input string.
	$rgba = str_replace( array( 'rgba(', ')' ), '', $rgba );

	// Split the RGBA values by comma.
	$values = explode( ',', $rgba );

	// Convert each value from string to integer.
	$r = intval( $values[0] );
	$g = intval( $values[1] );
	$b = intval( $values[2] );
	$a = floatval( $values[3] );

	// Create the split RGBA string.
	return array(
		'r' => $r,
		'g' => $g,
		'b' => $b,
		'a' => $a,
	);
}

/**
 * Render svg mask.
 *
 * @since 4.2.2
 *
 * @param string $id id.
 * @param string $filter_name filter name.
 * @param string $color color.
 * @return mixed masked svg,
 */
function astra_render_svg_mask( $id, $filter_name, $color ) {

	if ( 0 === strpos( $color, 'var(--' ) ) {
		$agp = new Astra_Global_Palette();
		/** @psalm-suppress UndefinedFunction  */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$svg_color = astra_hex_to_rgb( $agp->get_color_by_palette_variable( $color ) );
	} elseif ( preg_match( '/^#[a-f0-9]{6}$/i', $color ) ) {
		/** @psalm-suppress UndefinedFunction  */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$svg_color = astra_hex_to_rgb( $color );
	} else {
		/** @psalm-suppress UndefinedFunction  */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$svg_color = astra_split_rgba( $color );
	}

	/** @psalm-suppress UndefinedFunction  */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	echo astra_get_filter_svg( $id, apply_filters( 'astra_' . $filter_name, $svg_color ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Returns an array of logo svg icons.
 *
 * @return array
 * @since 4.7.0
 */
function astra_get_logo_svg_icons_array() {
	static $ast_all_svg_icons = array();

	if ( $ast_all_svg_icons ) {
		return $ast_all_svg_icons;
	}

	$icons_dir = ASTRA_THEME_DIR . 'assets/svg/logo-svg-icons';

	for ( $i = 0; $i < 4; $i++ ) {

		$icons = include_once "{$icons_dir}/icons-v6-{$i}.php";

		foreach ( $icons as &$icon ) {
			$fallback            = isset( $icon['svg']['solid'] ) ? $icon['svg']['solid'] : array();
			$icon_brand_or_solid = isset( $icon['svg']['brands'] ) ? $icon['svg']['brands'] : $fallback;
			$path                = isset( $icon_brand_or_solid['path'] ) ? $icon_brand_or_solid['path'] : '';
			$width               = isset( $icon_brand_or_solid['width'] ) ? $icon_brand_or_solid['width'] : '';
			$height              = isset( $icon_brand_or_solid['height'] ) ? $icon_brand_or_solid['height'] : '';
			$view                = (bool) $width && (bool) $height ? "0 0 {$width} {$height}" : null;

			if ( $path && $view ) {
				ob_start();
				?>
				<svg xmlns="https://www.w3.org/2000/svg" viewBox= "<?php echo esc_attr( $view ); ?>"><path d="<?php echo esc_attr( $path ); ?>"></path></svg>
				<?php
				$icon['rendered'] = trim( ob_get_clean() );
			}
		}

		$ast_all_svg_icons = array_merge( $ast_all_svg_icons, $icons );
	}

	return $ast_all_svg_icons;
}
