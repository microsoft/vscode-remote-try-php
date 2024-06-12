<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Exceptions;

if (!defined('ABSPATH')) exit;


/**
 * USE: An application state that should not occur. Can be subclassed for feature-specific exceptions.
 * API: 500 Server Error
 */
class InvalidStateException extends RuntimeException {
}
