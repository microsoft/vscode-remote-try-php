<?php
namespace MailPoetVendor\Carbon\Exceptions;
if (!defined('ABSPATH')) exit;
use Throwable;
class UnitNotConfiguredException extends UnitException
{
 protected $unit;
 public function __construct($unit, $code = 0, Throwable $previous = null)
 {
 $this->unit = $unit;
 parent::__construct("Unit {$unit} have no configuration to get total from other units.", $code, $previous);
 }
 public function getUnit() : string
 {
 return $this->unit;
 }
}
