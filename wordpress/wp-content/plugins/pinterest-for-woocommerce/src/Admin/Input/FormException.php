<?php
/**
 * Class FormException
 *
 * @package Automattic\WooCommerce\Pinterest\Product
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Admin\Input;

use Automattic\WooCommerce\Pinterest\Exception\PinterestException;
use Exception;

defined( 'ABSPATH' ) || exit;

/**
 * Class FormException
 */
class FormException extends Exception implements PinterestException {
	/**
	 * Return a new instance of the exception when a submitted form is being modified.
	 *
	 * @return static
	 */
	public static function cannot_modify_submitted(): FormException {
		return new static( 'You cannot modify a submitted form.' );
	}
}
