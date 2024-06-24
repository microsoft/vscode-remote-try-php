<?php
namespace MailPoetVendor\Carbon\Exceptions;
if (!defined('ABSPATH')) exit;
use Throwable;
class UnknownUnitException extends UnitException
{
 protected $unit;
 public function __construct($unit, $code = 0, Throwable $previous = null)
 {
 $this->unit = $unit;
 parent::__construct("Unknown unit '{$unit}'.", $code, $previous);
 }
 public function getUnit() : string
 {
 return $this->unit;
 }
}
