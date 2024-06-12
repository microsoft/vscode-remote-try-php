<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
class DateValidator extends ConstraintValidator
{
 public const PATTERN = '/^(?<year>\\d{4})-(?<month>\\d{2})-(?<day>\\d{2})$/';
 public static function checkDate(int $year, int $month, int $day) : bool
 {
 return \checkdate($month, $day, $year);
 }
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Date) {
 throw new UnexpectedTypeException($constraint, Date::class);
 }
 if (null === $value || '' === $value) {
 return;
 }
 if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
 throw new UnexpectedValueException($value, 'string');
 }
 $value = (string) $value;
 if (!\preg_match(static::PATTERN, $value, $matches)) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Date::INVALID_FORMAT_ERROR)->addViolation();
 return;
 }
 if (!self::checkDate($matches['year'] ?? $matches[1], $matches['month'] ?? $matches[2], $matches['day'] ?? $matches[3])) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Date::INVALID_DATE_ERROR)->addViolation();
 }
 }
}
