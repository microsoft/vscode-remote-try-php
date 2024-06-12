<?php
namespace MailPoetVendor\Symfony\Component\Validator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Context\ExecutionContextInterface;
interface ConstraintValidatorInterface
{
 public function initialize(ExecutionContextInterface $context);
 public function validate($value, Constraint $constraint);
}
