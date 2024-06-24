<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Exceptions;

if (!defined('ABSPATH')) exit;


/**
 * USE: When the main action produces conflict (i.e. duplicate key).
 * API: 409 Conflict
 */
class ConflictException extends UnexpectedValueException {
  protected $statusCode = 409;
}
