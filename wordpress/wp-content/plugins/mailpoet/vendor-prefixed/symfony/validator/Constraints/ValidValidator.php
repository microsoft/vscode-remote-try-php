<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
class ValidValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Valid) {
 throw new UnexpectedTypeException($constraint, Valid::class);
 }
 if (null === $value) {
 return;
 }
 $this->context->getValidator()->inContext($this->context)->validate($value, null, $this->context->getGroup());
 }
}
