<?php
namespace MailPoetVendor\Symfony\Component\Validator\Validator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Constraints\GroupSequence;
use MailPoetVendor\Symfony\Component\Validator\ConstraintViolationListInterface;
interface ContextualValidatorInterface
{
 public function atPath(string $path);
 public function validate($value, $constraints = null, $groups = null);
 public function validateProperty(object $object, string $propertyName, $groups = null);
 public function validatePropertyValue($objectOrClass, string $propertyName, $value, $groups = null);
 public function getViolations();
}
