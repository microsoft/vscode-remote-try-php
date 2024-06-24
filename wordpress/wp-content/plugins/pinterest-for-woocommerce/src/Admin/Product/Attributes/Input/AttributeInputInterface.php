<?php
/**
 * Class AttributeInputInterface
 *
 * @package Automattic\WooCommerce\Pinterest\Admin\Product\Attributes\Input
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Admin\Product\Attributes\Input;

defined( 'ABSPATH' ) || exit;

/**
 * Class AttributeInputInterface
 */
interface AttributeInputInterface {

	/**
	 * Returns a name for the attribute input.
	 *
	 * @return string
	 */
	public static function get_name(): string;

	/**
	 * Returns a short description for the attribute input.
	 *
	 * @return string
	 */
	public static function get_description(): string;

	/**
	 * Returns the input class used for the attribute input.
	 *
	 * Must be an instance of `InputInterface`.
	 *
	 * @return string
	 */
	public static function get_input_type(): string;

}
