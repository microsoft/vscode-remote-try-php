<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
class SequentiallyValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Sequentially) {
 throw new UnexpectedTypeException($constraint, Sequentially::class);
 }
 $context = $this->context;
 $validator = $context->getValidator()->inContext($context);
 $originalCount = $validator->getViolations()->count();
 foreach ($constraint->constraints as $c) {
 if ($originalCount !== $validator->validate($value, $c)->getViolations()->count()) {
 break;
 }
 }
 }
}
