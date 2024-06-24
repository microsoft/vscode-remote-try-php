<?php
namespace MailPoetVendor\Carbon\Exceptions;
if (!defined('ABSPATH')) exit;
use InvalidArgumentException as BaseInvalidArgumentException;
use Throwable;
class ParseErrorException extends BaseInvalidArgumentException implements InvalidArgumentException
{
 protected $expected;
 protected $actual;
 protected $help;
 public function __construct($expected, $actual, $help = '', $code = 0, Throwable $previous = null)
 {
 $this->expected = $expected;
 $this->actual = $actual;
 $this->help = $help;
 $actual = $actual === '' ? 'data is missing' : "get '{$actual}'";
 parent::__construct(\trim("Format expected {$expected} but {$actual}\n{$help}"), $code, $previous);
 }
 public function getExpected() : string
 {
 return $this->expected;
 }
 public function getActual() : string
 {
 return $this->actual;
 }
 public function getHelp() : string
 {
 return $this->help;
 }
}
