<?php
/**
 * Interface WithValueOptionsInterface
 *
 * @package Automattic\WooCommerce\Pinterest\Product\Attributes
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Product\Attributes;

defined( 'ABSPATH' ) || exit;

/**
 * Interface WithValueOptionsInterface
 */
interface WithValueOptionsInterface {
	/**
	 * Return an array of values available to choose for the attribute.
	 *
	 * Note: array key is used as the option key.
	 *
	 * @return array
	 */
	public static function get_value_options(): array;
}
