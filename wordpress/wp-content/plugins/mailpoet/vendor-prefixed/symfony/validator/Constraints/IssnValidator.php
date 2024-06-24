<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
class IssnValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Issn) {
 throw new UnexpectedTypeException($constraint, Issn::class);
 }
 if (null === $value || '' === $value) {
 return;
 }
 if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
 throw new UnexpectedValueException($value, 'string');
 }
 $value = (string) $value;
 $canonical = $value;
 // 1234-567X
 // ^
 if (isset($canonical[4]) && '-' === $canonical[4]) {
 // remove hyphen
 $canonical = \substr($canonical, 0, 4) . \substr($canonical, 5);
 } elseif ($constraint->requireHyphen) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Issn::MISSING_HYPHEN_ERROR)->addViolation();
 return;
 }
 $length = \strlen($canonical);
 if ($length < 8) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Issn::TOO_SHORT_ERROR)->addViolation();
 return;
 }
 if ($length > 8) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Issn::TOO_LONG_ERROR)->addViolation();
 return;
 }
 // 1234567X
 // ^^^^^^^ digits only
 if (!\ctype_digit(\substr($canonical, 0, 7))) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Issn::INVALID_CHARACTERS_ERROR)->addViolation();
 return;
 }
 // 1234567X
 // ^ digit, x or X
 if (!\ctype_digit($canonical[7]) && 'x' !== $canonical[7] && 'X' !== $canonical[7]) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Issn::INVALID_CHARACTERS_ERROR)->addViolation();
 return;
 }
 // 1234567X
 // ^ case-sensitive?
 if ($constraint->caseSensitive && 'x' === $canonical[7]) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Issn::INVALID_CASE_ERROR)->addViolation();
 return;
 }
 // Calculate a checksum. "X" equals 10.
 $checkSum = 'X' === $canonical[7] || 'x' === $canonical[7] ? 10 : $canonical[7];
 for ($i = 0; $i < 7; ++$i) {
 // Multiply the first digit by 8, the second by 7, etc.
 $checkSum += (8 - $i) * (int) $canonical[$i];
 }
 if (0 !== $checkSum % 11) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Issn::CHECKSUM_FAILED_ERROR)->addViolation();
 }
 }
}
