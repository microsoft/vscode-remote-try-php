<?php
/**
 * Interface AttributeInterface
 *
 * @package Automattic\WooCommerce\Pinterest\Product\Attributes
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Product\Attributes;

use Automattic\WooCommerce\Pinterest\Admin\Product\Attributes\Input\AttributeInputInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Interface AttributeInterface
 */
interface AttributeInterface {

	/**
	 * Returns the attribute ID.
	 *
	 * @return string
	 */
	public static function get_id(): string;

	/**
	 * Return the attribute's value type. Must be a valid PHP type.
	 *
	 * @return string
	 *
	 * @link https://www.php.net/manual/en/function.settype.php
	 */
	public static function get_value_type(): string;

	/**
	 * Return the attribute's input class. Must be an instance of `AttributeInputInterface`.
	 *
	 * @return string
	 *
	 * @see AttributeInputInterface
	 */
	public static function get_input_type(): string;

	/**
	 * Return an array of WooCommerce product types that this attribute can be applied to.
	 *
	 * @return array
	 */
	public static function get_applicable_product_types(): array;

	/**
	 * Returns the attribute value.
	 *
	 * @return mixed
	 */
	public function get_value();

}
