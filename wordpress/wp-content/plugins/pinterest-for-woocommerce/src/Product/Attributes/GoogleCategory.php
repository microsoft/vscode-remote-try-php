<?php
/**
 * Class GoogleCategory
 *
 * @package Automattic\WooCommerce\Pinterest\Product\Attributes
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Product\Attributes;

use Automattic\WooCommerce\Pinterest\Admin\Product\Attributes\Input\GoogleCategoryInput;

defined( 'ABSPATH' ) || exit;

/**
 * Class GoogleCategory
 */
class GoogleCategory extends AbstractAttribute {

	/**
	 * Returns the attribute ID.
	 *
	 * @return string
	 */
	public static function get_id(): string {
		return 'google_product_category';
	}

	/**
	 * Return an array of WooCommerce product types that this attribute can be applied to.
	 *
	 * @return array
	 */
	public static function get_applicable_product_types(): array {
		return array( 'simple', 'variable' );
	}

	/**
	 * Return the attribute's input class. Must be an instance of `AttributeInputInterface`.
	 *
	 * @return string
	 *
	 * @see AttributeInputInterface
	 */
	public static function get_input_type(): string {
		return GoogleCategoryInput::class;
	}

}
