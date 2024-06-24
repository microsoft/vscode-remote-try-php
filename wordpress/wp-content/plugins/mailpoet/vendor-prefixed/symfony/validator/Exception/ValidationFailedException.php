<?php
namespace MailPoetVendor\Symfony\Component\Validator\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\ConstraintViolationListInterface;
class ValidationFailedException extends RuntimeException
{
 private $violations;
 private $value;
 public function __construct($value, ConstraintViolationListInterface $violations)
 {
 $this->violations = $violations;
 $this->value = $value;
 parent::__construct($violations);
 }
 public function getValue()
 {
 return $this->value;
 }
 public function getViolations() : ConstraintViolationListInterface
 {
 return $this->violations;
 }
}
