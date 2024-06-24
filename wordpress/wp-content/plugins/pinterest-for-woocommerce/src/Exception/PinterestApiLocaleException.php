<?php
/**
 * PinterestApiLocaleException interface.
 *
 * @package Automattic\WooCommerce\Pinterest\Exception
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Exception;

use Exception;

/**
 * Exception thrown when the the application locale is not supported by the API.
 */
class PinterestApiLocaleException extends Exception implements PinterestException {}
