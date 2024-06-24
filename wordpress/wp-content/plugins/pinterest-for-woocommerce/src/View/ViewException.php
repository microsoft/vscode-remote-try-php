<?php
/**
 * Class ViewException
 *
 * @package Automattic\WooCommerce\Pinterest\Exception
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\View;

use Automattic\WooCommerce\Pinterest\Exception\PinterestException;
use Exception;

defined( 'ABSPATH' ) || exit;

/**
 * Class ViewException
 */
class ViewException extends Exception implements PinterestException {

	/**
	 * Create a new instance of the exception if the view file itself created
	 * an exception.
	 *
	 * @param string    $uri       URI of the file that is not accessible or
	 *                             not readable.
	 * @param Exception $exception Exception that was thrown by the view file.
	 *
	 * @return static
	 */
	public static function invalid_view_exception( string $uri, Exception $exception ) {
		$message = sprintf(
			'Could not load the View URI "%1$s". Reason: "%2$s".',
			$uri,
			$exception->getMessage()
		);

		return new static( $message, (int) $exception->getCode(), $exception );
	}

	/**
	 * Create a new instance of the exception for a file that is not accessible
	 * or not readable.
	 *
	 * @param string $path Path of the file that is not accessible or not
	 *                     readable.
	 *
	 * @return static
	 */
	public static function invalid_path( $path ) {
		$message = sprintf(
			'The view path "%s" is not accessible or readable.',
			$path
		);

		return new static( $message );
	}

	/**
	 * Create a new instance of the exception for a context property that is
	 * not recognized.
	 *
	 * @param string $property Name of the context property that was not recognized.
	 *
	 * @return static
	 */
	public static function invalid_context_property( string $property ) {
		$message = sprintf(
			'The property "%s" could not be found within the context of the currently rendered view.',
			$property
		);

		return new static( $message );
	}
}
