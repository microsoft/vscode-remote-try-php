<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
class TimeValidator extends ConstraintValidator
{
 public const PATTERN = '/^(\\d{2}):(\\d{2}):(\\d{2})$/';
 public static function checkTime(int $hour, int $minute, float $second) : bool
 {
 return $hour >= 0 && $hour < 24 && $minute >= 0 && $minute < 60 && $second >= 0 && $second < 60;
 }
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Time) {
 throw new UnexpectedTypeException($constraint, Time::class);
 }
 if (null === $value || '' === $value) {
 return;
 }
 if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
 throw new UnexpectedValueException($value, 'string');
 }
 $value = (string) $value;
 if (!\preg_match(static::PATTERN, $value, $matches)) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Time::INVALID_FORMAT_ERROR)->addViolation();
 return;
 }
 if (!self::checkTime($matches[1], $matches[2], $matches[3])) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Time::INVALID_TIME_ERROR)->addViolation();
 }
 }
}
