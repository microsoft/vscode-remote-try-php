<?php
/**
 * Trait ValidateInterface
 *
 * @package Automattic\WooCommerce\Pinterest\Exception
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Exception;

/**
 * Trait ValidateInterface
 */
trait ValidateInterface {

	/**
	 * Validate that a class implements a given interface.
	 *
	 * @param string $class     The class name.
	 * @param string $interface The interface name.
	 *
	 * @throws InvalidClass When the given class does not implement the interface.
	 */
	protected function validate_interface( string $class, string $interface ) {
		$implements = class_implements( $class );
		if ( ! array_key_exists( $interface, $implements ) ) {
			throw InvalidClass::should_implement( $class, $interface );
		}
	}

	/**
	 * Validate that an object is an instance of an interface.
	 *
	 * @param object $object    The object to validate.
	 * @param string $interface The interface name.
	 *
	 * @throws InvalidClass When the given object does not implement the interface.
	 */
	protected function validate_instanceof( $object, string $interface ) {
		$class = '';
		if ( is_object( $object ) ) {
			$class = get_class( $object );
		}

		if ( ! $object instanceof $interface ) {
			throw InvalidClass::should_implement( $class, $interface );
		}
	}

	/**
	 * Validate that an object is NOT an instance of an interface.
	 *
	 * @param object $object    The object to validate.
	 * @param string $interface The interface name.
	 *
	 * @throws InvalidClass When the given object implements the interface.
	 */
	protected function validate_not_instanceof( $object, string $interface ) {
		$class = '';
		if ( is_object( $object ) ) {
			$class = get_class( $object );
		}

		if ( $object instanceof $interface ) {
			throw InvalidClass::should_not_implement( $class, $interface );
		}
	}
}
