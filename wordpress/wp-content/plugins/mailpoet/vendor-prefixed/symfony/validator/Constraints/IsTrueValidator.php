<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
class IsTrueValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof IsTrue) {
 throw new UnexpectedTypeException($constraint, IsTrue::class);
 }
 if (null === $value) {
 return;
 }
 if (\true !== $value && 1 !== $value && '1' !== $value) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(IsTrue::NOT_TRUE_ERROR)->addViolation();
 }
 }
}
