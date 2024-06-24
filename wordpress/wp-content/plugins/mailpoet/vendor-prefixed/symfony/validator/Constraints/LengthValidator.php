<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
class LengthValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Length) {
 throw new UnexpectedTypeException($constraint, Length::class);
 }
 if (null === $value || '' === $value && $constraint->allowEmptyString) {
 return;
 }
 if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
 throw new UnexpectedValueException($value, 'string');
 }
 $stringValue = (string) $value;
 if (null !== $constraint->normalizer) {
 $stringValue = ($constraint->normalizer)($stringValue);
 }
 try {
 $invalidCharset = !@\mb_check_encoding($stringValue, $constraint->charset);
 } catch (\ValueError $e) {
 if (!\str_starts_with($e->getMessage(), 'mb_check_encoding(): Argument #2 ($encoding) must be a valid encoding')) {
 throw $e;
 }
 $invalidCharset = \true;
 }
 if ($invalidCharset) {
 $this->context->buildViolation($constraint->charsetMessage)->setParameter('{{ value }}', $this->formatValue($stringValue))->setParameter('{{ charset }}', $constraint->charset)->setInvalidValue($value)->setCode(Length::INVALID_CHARACTERS_ERROR)->addViolation();
 return;
 }
 $length = \mb_strlen($stringValue, $constraint->charset);
 if (null !== $constraint->max && $length > $constraint->max) {
 $exactlyOptionEnabled = $constraint->min == $constraint->max;
 $this->context->buildViolation($exactlyOptionEnabled ? $constraint->exactMessage : $constraint->maxMessage)->setParameter('{{ value }}', $this->formatValue($stringValue))->setParameter('{{ limit }}', $constraint->max)->setInvalidValue($value)->setPlural((int) $constraint->max)->setCode($exactlyOptionEnabled ? Length::NOT_EQUAL_LENGTH_ERROR : Length::TOO_LONG_ERROR)->addViolation();
 return;
 }
 if (null !== $constraint->min && $length < $constraint->min) {
 $exactlyOptionEnabled = $constraint->min == $constraint->max;
 $this->context->buildViolation($exactlyOptionEnabled ? $constraint->exactMessage : $constraint->minMessage)->setParameter('{{ value }}', $this->formatValue($stringValue))->setParameter('{{ limit }}', $constraint->min)->setInvalidValue($value)->setPlural((int) $constraint->min)->setCode($exactlyOptionEnabled ? Length::NOT_EQUAL_LENGTH_ERROR : Length::TOO_SHORT_ERROR)->addViolation();
 }
 }
}
