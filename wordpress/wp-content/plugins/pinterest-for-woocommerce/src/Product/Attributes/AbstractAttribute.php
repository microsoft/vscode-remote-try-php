<?php
/**
 * Class AbstractAttribute
 *
 * @package Automattic\WooCommerce\Pinterest\Product\Attributes
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Product\Attributes;

defined( 'ABSPATH' ) || exit;

/**
 * Class AbstractAttribute
 */
abstract class AbstractAttribute implements AttributeInterface {

	/**
	 * Attribute value.
	 *
	 * @var mixed
	 */
	protected $value = null;

	/**
	 * AbstractAttribute constructor.
	 *
	 * @param mixed $value Attribute value.
	 */
	public function __construct( $value = null ) {
		$this->set_value( $value );
	}

	/**
	 * Return the attribute type. Must be a valid PHP type.
	 *
	 * @return string
	 *
	 * @link https://www.php.net/manual/en/function.settype.php
	 */
	public static function get_value_type(): string {
		return 'string';
	}

	/**
	 * Returns the attribute value.
	 *
	 * @return mixed
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Set the attribute value.
	 *
	 * @param mixed $value Attribute value.
	 *
	 * @return $this
	 */
	public function set_value( $value ): AbstractAttribute {
		$this->value = $this->cast_value( $value );

		return $this;
	}

	/**
	 * Casts the value to the attribute value type and returns the result.
	 *
	 * @param mixed $value Attribute value.
	 *
	 * @return mixed
	 */
	protected function cast_value( $value ) {
		if ( is_string( $value ) ) {
			$value = trim( $value );

			if ( '' === $value ) {
				return null;
			}
		}

		$value_type = static::get_value_type();
		if ( in_array( $value_type, array( 'bool', 'boolean' ), true ) ) {
			$value = wc_string_to_bool( $value );
		} else {
			settype( $value, $value_type );
		}

		return $value;
	}

	/**
	 * Return an array of WooCommerce product types that this attribute can be applied to.
	 *
	 * @return array
	 */
	public static function get_applicable_product_types(): array {
		return array( 'simple', 'variable', 'variation' );
	}

	/**
	 * Magic method to convert to string.
	 *
	 * @return string
	 */
	public function __toString() {
		return (string) $this->get_value();
	}

}
