<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Exceptions;

if (!defined('ABSPATH')) exit;


/**
 * USE: When an action is forbidden for given actor (although generally valid).
 * API: 403 Forbidden
 */
class AccessDeniedException extends UnexpectedValueException {
  protected $statusCode = 403;
}
