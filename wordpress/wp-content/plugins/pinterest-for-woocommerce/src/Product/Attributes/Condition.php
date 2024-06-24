<?php
/**
 * Class Condition
 *
 * @package Automattic\WooCommerce\Pinterest\Product\Attributes
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Product\Attributes;

use Automattic\WooCommerce\Pinterest\Admin\Product\Attributes\Input\ConditionInput;

defined( 'ABSPATH' ) || exit;

/**
 * Class Condition
 */
class Condition extends AbstractAttribute implements WithValueOptionsInterface {

	/**
	 * Returns the attribute ID.
	 *
	 * @return string
	 */
	public static function get_id(): string {
		return 'condition';
	}

	/**
	 * Return an array of WooCommerce product types that this attribute can be applied to.
	 *
	 * @return array
	 */
	public static function get_applicable_product_types(): array {
		return array( 'simple', 'variation' );
	}

	/**
	 * Return an array of values available to choose for the attribute.
	 *
	 * Note: array key is used as the option key.
	 *
	 * @return array
	 */
	public static function get_value_options(): array {
		return array(
			'new'         => __( 'New', 'pinterest-for-woocommerce' ),
			'refurbished' => __( 'Refurbished', 'pinterest-for-woocommerce' ),
			'used'        => __( 'Used', 'pinterest-for-woocommerce' ),
		);
	}

	/**
	 * Return the attribute's input class. Must be an instance of `AttributeInputInterface`.
	 *
	 * @return string
	 *
	 * @see AttributeInputInterface
	 */
	public static function get_input_type(): string {
		return ConditionInput::class;
	}

}
