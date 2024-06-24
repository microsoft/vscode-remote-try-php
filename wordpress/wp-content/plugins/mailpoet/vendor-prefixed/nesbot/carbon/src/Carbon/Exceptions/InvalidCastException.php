<?php
namespace MailPoetVendor\Carbon\Exceptions;
if (!defined('ABSPATH')) exit;
use InvalidArgumentException as BaseInvalidArgumentException;
class InvalidCastException extends BaseInvalidArgumentException implements InvalidArgumentException
{
 //
}
