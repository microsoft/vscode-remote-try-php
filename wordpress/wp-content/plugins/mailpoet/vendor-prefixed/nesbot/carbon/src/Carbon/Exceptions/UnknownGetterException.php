<?php
namespace MailPoetVendor\Carbon\Exceptions;
if (!defined('ABSPATH')) exit;
use InvalidArgumentException as BaseInvalidArgumentException;
use Throwable;
class UnknownGetterException extends BaseInvalidArgumentException implements InvalidArgumentException
{
 protected $getter;
 public function __construct($getter, $code = 0, Throwable $previous = null)
 {
 $this->getter = $getter;
 parent::__construct("Unknown getter '{$getter}'", $code, $previous);
 }
 public function getGetter() : string
 {
 return $this->getter;
 }
}
