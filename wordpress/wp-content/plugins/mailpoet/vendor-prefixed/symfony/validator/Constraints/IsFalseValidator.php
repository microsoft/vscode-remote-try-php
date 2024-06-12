<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
class IsFalseValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof IsFalse) {
 throw new UnexpectedTypeException($constraint, IsFalse::class);
 }
 if (null === $value || \false === $value || 0 === $value || '0' === $value) {
 return;
 }
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(IsFalse::NOT_FALSE_ERROR)->addViolation();
 }
}
