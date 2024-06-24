<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
class CompoundValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Compound) {
 throw new UnexpectedTypeException($constraint, Compound::class);
 }
 $context = $this->context;
 $validator = $context->getValidator()->inContext($context);
 $validator->validate($value, $constraint->constraints);
 }
}
