<?php
/**
 * Helper.
 *
 * @package variation-swatches-woo
 * @since 1.0.0
 */

namespace CFVSW\Inc;

/**
 * Helper
 *
 * @since 1.0.0
 */
class Helper {

	/**
	 * Keep default values of all settings.
	 *
	 * @var array
	 * @since  1.0.0
	 */
	public $defaults = [
		CFVSW_GLOBAL => [
			'enable_swatches'      => true,
			'enable_swatches_shop' => true,
			'auto_convert'         => true,
			'min_width'            => 24,
			'min_height'           => 24,
			'border_radius'        => 3,
			'border_width'         => 1,
			'disable_attr_type'    => 'blur',
			'tooltip'              => true,
			'html_design'          => 'none',
			'font_size'            => 12,
			'disable_out_of_stock' => true,
		],
		CFVSW_SHOP   => [
			'override_global'      => false,
			'enable_swatches'      => true,
			'auto_convert'         => true,
			'min_width'            => 24,
			'min_height'           => 24,
			'border_radius'        => 24,
			'border_width'         => 1,
			'disable_attr_type'    => 'blur',
			'alignment'            => 'left',
			'label'                => false,
			'position'             => 'after_price',
			'limit'                => '',
			'font_size'            => 12,
			'special_attr_archive' => false,
			'special_attr_choose'  => '',
		],
		CFVSW_STYLE  => [
			'tooltip_background' => '#000000',
			'tooltip_font_color' => '#ffffff',
			'tooltip_font_size'  => 12,
			'tooltip_image'      => false,
			'border_color'       => '#000000',
			'label_font_size'    => '',
			'filters'            => false,
		],
	];

	/**
	 * Get attribute type from database from attribute name
	 *
	 * @param string $name attribute name of product attribute.
	 * @return mixed
	 * @since  1.0.0
	 */
	public function get_attr_type_by_name( $name = '' ) {
		if ( empty( $name ) || ! taxonomy_exists( $name ) ) {
			return '';
		}

		global $wpdb;
		$name = substr( $name, 3 );
		// Required custom result from database, was not possible with regular WordPress call.
		$type = $wpdb->get_var( $wpdb->prepare( 'SELECT attribute_type FROM ' . $wpdb->prefix . 'woocommerce_attribute_taxonomies WHERE attribute_name = %s', $name ) ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return is_null( $type ) ? '' : $type;
	}

	/**
	 * Sanitize multi-dimension array.
	 *
	 * @param string $function Function reference.
	 * @param array  $data_array Array what we need to sanitize.
	 * @return array
	 * @since  1.0.2
	 */
	public function sanitize_recursively( $function, $data_array ) {
		$response = [];
		foreach ( $data_array as $key => $data ) {
			$response[ $key ] = is_array( $data ) ? $this->sanitize_recursively( $function, $data ) : $function( $data );
		}

		return $response;
	}

	/**
	 * Remove blank array.
	 *
	 * @param string $array It is important to variable should be array.
	 * @return array
	 * @since  1.0.2
	 */
	public function remove_blank_array( $array ) {
		if ( empty( $array ) || ! is_array( $array ) ) {
			return $array;
		}
		foreach ( $array as $key => &$value ) {
			if ( empty( $value ) ) {
				unset( $array[ $key ] );
			} else {
				if ( is_array( $value ) ) {
					$value = $this->remove_blank_array( $value );
					if ( empty( $value ) ) {
						unset( $array[ $key ] );
					}
				}
			}
		}
		return $array;
	}

	/**
	 * Create slug.
	 *
	 * @param string $str Pass string.
	 * @return string
	 * @since  1.0.2
	 */
	public function create_slug( $str ) {
		if ( empty( $str ) ) {
			return false;
		}
		$remove_special_characters     = strtolower( trim( preg_replace( '/[^\w\s]+/u', '', $str ) ) );
		$replace_white_spaces_to_dash  = str_replace( ' ', '-', $remove_special_characters );
		$replace_multiple_dash_to_dash = preg_replace( '/-+/', '-', $replace_white_spaces_to_dash );
		return $replace_multiple_dash_to_dash;
	}

	/**
	 * Get option value from database and return value merged with default values
	 *
	 * @param string $option option name to get value from.
	 * @return array
	 * @since  1.0.0
	 */
	public function get_option( $option ) {
		$db_values = get_option( $option, [] );
		return wp_parse_args( $db_values, $this->defaults[ $option ] );
	}
}
