<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
class LuhnValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Luhn) {
 throw new UnexpectedTypeException($constraint, Luhn::class);
 }
 if (null === $value || '' === $value) {
 return;
 }
 // Work with strings only, because long numbers are represented as floats
 // internally and don't work with strlen()
 if (!\is_string($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
 throw new UnexpectedValueException($value, 'string');
 }
 $value = (string) $value;
 if (!\ctype_digit($value)) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Luhn::INVALID_CHARACTERS_ERROR)->addViolation();
 return;
 }
 $checkSum = 0;
 $length = \strlen($value);
 // Starting with the last digit and walking left, add every second
 // digit to the check sum
 // e.g. 7 9 9 2 7 3 9 8 7 1 3
 // ^ ^ ^ ^ ^ ^
 // = 7 + 9 + 7 + 9 + 7 + 3
 for ($i = $length - 1; $i >= 0; $i -= 2) {
 $checkSum += $value[$i];
 }
 // Starting with the second last digit and walking left, double every
 // second digit and add it to the check sum
 // For doubles greater than 9, sum the individual digits
 // e.g. 7 9 9 2 7 3 9 8 7 1 3
 // ^ ^ ^ ^ ^
 // = 1+8 + 4 + 6 + 1+6 + 2
 for ($i = $length - 2; $i >= 0; $i -= 2) {
 $checkSum += \array_sum(\str_split((int) $value[$i] * 2));
 }
 if (0 === $checkSum || 0 !== $checkSum % 10) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Luhn::CHECKSUM_FAILED_ERROR)->addViolation();
 }
 }
}
