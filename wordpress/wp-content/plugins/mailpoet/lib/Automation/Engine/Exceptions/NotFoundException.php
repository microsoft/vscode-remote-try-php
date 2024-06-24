<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Exceptions;

if (!defined('ABSPATH')) exit;


/**
 * USE: When the main resource we're interested in doesn't exist.
 * API: 404 Not Found
 */
class NotFoundException extends UnexpectedValueException {
  protected $statusCode = 404;
}
