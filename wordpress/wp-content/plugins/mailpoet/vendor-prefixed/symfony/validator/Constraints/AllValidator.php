<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
class AllValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof All) {
 throw new UnexpectedTypeException($constraint, All::class);
 }
 if (null === $value) {
 return;
 }
 if (!\is_array($value) && !$value instanceof \Traversable) {
 throw new UnexpectedValueException($value, 'iterable');
 }
 $context = $this->context;
 $validator = $context->getValidator()->inContext($context);
 foreach ($value as $key => $element) {
 $validator->atPath('[' . $key . ']')->validate($element, $constraint->constraints);
 }
 }
}
