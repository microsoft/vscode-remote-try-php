<?php
namespace MailPoetVendor\Symfony\Component\Validator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraints\ExpressionValidator;
class ConstraintValidatorFactory implements ConstraintValidatorFactoryInterface
{
 protected $validators = [];
 public function __construct()
 {
 }
 public function getInstance(Constraint $constraint)
 {
 $className = $constraint->validatedBy();
 if (!isset($this->validators[$className])) {
 $this->validators[$className] = 'validator.expression' === $className ? new ExpressionValidator() : new $className();
 }
 return $this->validators[$className];
 }
}
