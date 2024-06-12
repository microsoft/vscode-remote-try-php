<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
class NotBlankValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof NotBlank) {
 throw new UnexpectedTypeException($constraint, NotBlank::class);
 }
 if ($constraint->allowNull && null === $value) {
 return;
 }
 if (\is_string($value) && null !== $constraint->normalizer) {
 $value = ($constraint->normalizer)($value);
 }
 if (\false === $value || empty($value) && '0' != $value) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(NotBlank::IS_BLANK_ERROR)->addViolation();
 }
 }
}
