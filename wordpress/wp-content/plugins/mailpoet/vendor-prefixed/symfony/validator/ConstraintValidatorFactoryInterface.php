<?php
namespace MailPoetVendor\Symfony\Component\Validator;
if (!defined('ABSPATH')) exit;
interface ConstraintValidatorFactoryInterface
{
 public function getInstance(Constraint $constraint);
}
