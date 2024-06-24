<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
class IsinValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Isin) {
 throw new UnexpectedTypeException($constraint, Isin::class);
 }
 if (null === $value || '' === $value) {
 return;
 }
 if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
 throw new UnexpectedValueException($value, 'string');
 }
 $value = \strtoupper($value);
 if (Isin::VALIDATION_LENGTH !== \strlen($value)) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Isin::INVALID_LENGTH_ERROR)->addViolation();
 return;
 }
 if (!\preg_match(Isin::VALIDATION_PATTERN, $value)) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Isin::INVALID_PATTERN_ERROR)->addViolation();
 return;
 }
 if (!$this->isCorrectChecksum($value)) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Isin::INVALID_CHECKSUM_ERROR)->addViolation();
 }
 }
 private function isCorrectChecksum(string $input) : bool
 {
 $characters = \str_split($input);
 foreach ($characters as $i => $char) {
 $characters[$i] = \intval($char, 36);
 }
 $number = \implode('', $characters);
 return 0 === $this->context->getValidator()->validate($number, new Luhn())->count();
 }
}
