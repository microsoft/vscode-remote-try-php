<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
class JsonValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Json) {
 throw new UnexpectedTypeException($constraint, Json::class);
 }
 if (null === $value || '' === $value) {
 return;
 }
 if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
 throw new UnexpectedTypeException($value, 'string');
 }
 $value = (string) $value;
 \json_decode($value);
 if (\JSON_ERROR_NONE !== \json_last_error()) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Json::INVALID_JSON_ERROR)->addViolation();
 }
 }
}
