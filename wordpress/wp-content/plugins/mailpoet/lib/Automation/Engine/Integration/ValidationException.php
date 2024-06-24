<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Integration;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Exceptions\UnexpectedValueException;

class ValidationException extends UnexpectedValueException {
}
