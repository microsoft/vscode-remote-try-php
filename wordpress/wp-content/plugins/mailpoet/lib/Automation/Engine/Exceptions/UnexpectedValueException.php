<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Exceptions;

if (!defined('ABSPATH')) exit;


/**
 * USE: When wrong data VALUE is received.
 * API: 400 Bad Request
 */
class UnexpectedValueException extends RuntimeException {
  protected $statusCode = 400;
}
