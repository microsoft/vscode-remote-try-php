<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
class IsbnValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Isbn) {
 throw new UnexpectedTypeException($constraint, Isbn::class);
 }
 if (null === $value || '' === $value) {
 return;
 }
 if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
 throw new UnexpectedValueException($value, 'string');
 }
 $value = (string) $value;
 $canonical = \str_replace('-', '', $value);
 // Explicitly validate against ISBN-10
 if (Isbn::ISBN_10 === $constraint->type) {
 if (\true !== ($code = $this->validateIsbn10($canonical))) {
 $this->context->buildViolation($this->getMessage($constraint, $constraint->type))->setParameter('{{ value }}', $this->formatValue($value))->setCode($code)->addViolation();
 }
 return;
 }
 // Explicitly validate against ISBN-13
 if (Isbn::ISBN_13 === $constraint->type) {
 if (\true !== ($code = $this->validateIsbn13($canonical))) {
 $this->context->buildViolation($this->getMessage($constraint, $constraint->type))->setParameter('{{ value }}', $this->formatValue($value))->setCode($code)->addViolation();
 }
 return;
 }
 // Try both ISBNs
 // First, try ISBN-10
 $code = $this->validateIsbn10($canonical);
 // The ISBN can only be an ISBN-13 if the value was too long for ISBN-10
 if (Isbn::TOO_LONG_ERROR === $code) {
 // Try ISBN-13 now
 $code = $this->validateIsbn13($canonical);
 // If too short, this means we have 11 or 12 digits
 if (Isbn::TOO_SHORT_ERROR === $code) {
 $code = Isbn::TYPE_NOT_RECOGNIZED_ERROR;
 }
 }
 if (\true !== $code) {
 $this->context->buildViolation($this->getMessage($constraint))->setParameter('{{ value }}', $this->formatValue($value))->setCode($code)->addViolation();
 }
 }
 protected function validateIsbn10(string $isbn)
 {
 // Choose an algorithm so that ERROR_INVALID_CHARACTERS is preferred
 // over ERROR_TOO_SHORT/ERROR_TOO_LONG
 // Otherwise "0-45122-5244" passes, but "0-45122_5244" reports
 // "too long"
 // Error priority:
 // 1. ERROR_INVALID_CHARACTERS
 // 2. ERROR_TOO_SHORT/ERROR_TOO_LONG
 // 3. ERROR_CHECKSUM_FAILED
 $checkSum = 0;
 for ($i = 0; $i < 10; ++$i) {
 // If we test the length before the loop, we get an ERROR_TOO_SHORT
 // when actually an ERROR_INVALID_CHARACTERS is wanted, e.g. for
 // "0-45122_5244" (typo)
 if (!isset($isbn[$i])) {
 return Isbn::TOO_SHORT_ERROR;
 }
 if ('X' === $isbn[$i]) {
 $digit = 10;
 } elseif (\ctype_digit($isbn[$i])) {
 $digit = $isbn[$i];
 } else {
 return Isbn::INVALID_CHARACTERS_ERROR;
 }
 $checkSum += $digit * (10 - $i);
 }
 if (isset($isbn[$i])) {
 return Isbn::TOO_LONG_ERROR;
 }
 return 0 === $checkSum % 11 ? \true : Isbn::CHECKSUM_FAILED_ERROR;
 }
 protected function validateIsbn13(string $isbn)
 {
 // Error priority:
 // 1. ERROR_INVALID_CHARACTERS
 // 2. ERROR_TOO_SHORT/ERROR_TOO_LONG
 // 3. ERROR_CHECKSUM_FAILED
 if (!\ctype_digit($isbn)) {
 return Isbn::INVALID_CHARACTERS_ERROR;
 }
 $length = \strlen($isbn);
 if ($length < 13) {
 return Isbn::TOO_SHORT_ERROR;
 }
 if ($length > 13) {
 return Isbn::TOO_LONG_ERROR;
 }
 $checkSum = 0;
 for ($i = 0; $i < 13; $i += 2) {
 $checkSum += $isbn[$i];
 }
 for ($i = 1; $i < 12; $i += 2) {
 $checkSum += $isbn[$i] * 3;
 }
 return 0 === $checkSum % 10 ? \true : Isbn::CHECKSUM_FAILED_ERROR;
 }
 protected function getMessage(Isbn $constraint, string $type = null)
 {
 if (null !== $constraint->message) {
 return $constraint->message;
 } elseif (Isbn::ISBN_10 === $type) {
 return $constraint->isbn10Message;
 } elseif (Isbn::ISBN_13 === $type) {
 return $constraint->isbn13Message;
 }
 return $constraint->bothIsbnMessage;
 }
}
