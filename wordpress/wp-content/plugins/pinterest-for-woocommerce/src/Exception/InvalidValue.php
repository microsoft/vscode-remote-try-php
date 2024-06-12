<?php
/**
 * Class InvalidValue
 *
 * @package Automattic\WooCommerce\Pinterest\Exception
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Exception;

use LogicException;

defined( 'ABSPATH' ) || exit;

/**
 * Class InvalidValue
 */
class InvalidValue extends LogicException implements PinterestException {

	/**
	 * Create a new instance of the exception when a value is not a positive integer.
	 *
	 * @param string $method The method that requires a positive integer.
	 *
	 * @return static
	 */
	public static function negative_integer( string $method ) {
		return new static( sprintf( 'The method "%s" requires a positive integer value.', $method ) );
	}

	/**
	 * Create a new instance of the exception when a value is not a string.
	 *
	 * @param string $key The name of the value.
	 *
	 * @return static
	 */
	public static function not_string( string $key ) {
		return new static( sprintf( 'The value of %s must be of type string.', $key ) );
	}

	/**
	 * Create a new instance of the exception when a value is not a string.
	 *
	 * @param string $key The name of the value.
	 *
	 * @return static
	 */
	public static function not_integer( string $key ): InvalidValue {
		return new static( sprintf( 'The value of %s must be of type integer.', $key ) );
	}

	/**
	 * Create a new instance of the exception when a value is not an instance of a given class.
	 *
	 * @param string $class_name The name of the class that the value must be an instance of.
	 * @param string $key        The name of the value.
	 *
	 * @return static
	 */
	public static function not_instance_of( string $class_name, string $key ) {
		return new static( sprintf( 'The value of %s must be an instance of %s.', $key, $class_name ) );
	}

	/**
	 * Create a new instance of the exception when a value is empty.
	 *
	 * @param string $key The name of the value.
	 *
	 * @return static
	 *
	 * @since 1.2.0
	 */
	public static function is_empty( string $key ): InvalidValue {
		return new static( sprintf( 'The value of %s can not be empty.', $key ) );
	}

	/**
	 * Create a new instance of the exception when a value is not from a predefined list of allowed values.
	 *
	 * @param mixed $key            The name of the value.
	 * @param array $allowed_values The list of allowed values.
	 *
	 * @return static
	 */
	public static function not_in_allowed_list( $key, array $allowed_values ): InvalidValue {
		return new static( sprintf( 'The value of %s must be either of [%s].', $key, implode( ', ', $allowed_values ) ) );
	}

	/**
	 * Create a new instance of the exception when a value isn't a valid product ID.
	 *
	 * @param mixed $value The provided product ID that isn't valid.
	 *
	 * @return static
	 */
	public static function not_valid_product_id( $value ): InvalidValue {
		return new static( sprintf( 'Invalid product ID: %s', $value ) );
	}
}
