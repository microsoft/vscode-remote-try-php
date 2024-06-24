<?php
namespace MailPoetVendor\Carbon\Exceptions;
if (!defined('ABSPATH')) exit;
use InvalidArgumentException as BaseInvalidArgumentException;
use Throwable;
class InvalidDateException extends BaseInvalidArgumentException implements InvalidArgumentException
{
 private $field;
 private $value;
 public function __construct($field, $value, $code = 0, Throwable $previous = null)
 {
 $this->field = $field;
 $this->value = $value;
 parent::__construct($field . ' : ' . $value . ' is not a valid value.', $code, $previous);
 }
 public function getField()
 {
 return $this->field;
 }
 public function getValue()
 {
 return $this->value;
 }
}
