<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
class NotNullValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof NotNull) {
 throw new UnexpectedTypeException($constraint, NotNull::class);
 }
 if (null === $value) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(NotNull::IS_NULL_ERROR)->addViolation();
 }
 }
}
