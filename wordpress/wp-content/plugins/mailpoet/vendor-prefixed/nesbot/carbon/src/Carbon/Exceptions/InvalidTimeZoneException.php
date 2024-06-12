<?php
namespace MailPoetVendor\Carbon\Exceptions;
if (!defined('ABSPATH')) exit;
use InvalidArgumentException as BaseInvalidArgumentException;
class InvalidTimeZoneException extends BaseInvalidArgumentException implements InvalidArgumentException
{
 //
}
