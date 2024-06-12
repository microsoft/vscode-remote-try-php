<?php
/**
 * Feed file operation exception.
 *
 * @package Automattic\WooCommerce\Pinterest\Exception
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Exception;

use Exception;

/**
 * An exception thrown then something went wrong writing into a feed file.
 */
class FeedFileOperationsException extends Exception implements PinterestException {
	public const CODE_COULD_NOT_RENAME_ERROR = 10;

	public const CODE_COULD_NOT_OPEN_FILE_ERROR = 20;

	public const CODE_COULD_NOT_WRITE_FILE_ERROR = 30;
}
