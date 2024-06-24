<?php
namespace MailPoetVendor\Symfony\Component\Validator\Exception;
if (!defined('ABSPATH')) exit;
class UnexpectedValueException extends UnexpectedTypeException
{
 private $expectedType;
 public function __construct($value, string $expectedType)
 {
 parent::__construct($value, $expectedType);
 $this->expectedType = $expectedType;
 }
 public function getExpectedType() : string
 {
 return $this->expectedType;
 }
}
