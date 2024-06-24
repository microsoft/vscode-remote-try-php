<?php
namespace MailPoetVendor\Carbon\Exceptions;
if (!defined('ABSPATH')) exit;
use InvalidArgumentException as BaseInvalidArgumentException;
class NotAPeriodException extends BaseInvalidArgumentException implements InvalidArgumentException
{
 //
}
