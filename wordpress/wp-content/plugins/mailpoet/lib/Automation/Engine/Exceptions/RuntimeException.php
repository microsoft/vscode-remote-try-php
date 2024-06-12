<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Exceptions;

if (!defined('ABSPATH')) exit;


/**
 * USE: Generic runtime error. When possible, use a more specific exception instead.
 * API: 500 Server Error
 */
class RuntimeException extends Exception {
}
