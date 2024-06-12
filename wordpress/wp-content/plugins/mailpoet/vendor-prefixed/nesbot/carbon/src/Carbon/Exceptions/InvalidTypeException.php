<?php
namespace MailPoetVendor\Carbon\Exceptions;
if (!defined('ABSPATH')) exit;
use InvalidArgumentException as BaseInvalidArgumentException;
class InvalidTypeException extends BaseInvalidArgumentException implements InvalidArgumentException
{
 //
}
