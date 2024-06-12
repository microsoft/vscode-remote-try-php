<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
class UlidValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Ulid) {
 throw new UnexpectedTypeException($constraint, Ulid::class);
 }
 if (null === $value || '' === $value) {
 return;
 }
 if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
 throw new UnexpectedValueException($value, 'string');
 }
 $value = (string) $value;
 if (26 !== \strlen($value)) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(26 > \strlen($value) ? Ulid::TOO_SHORT_ERROR : Ulid::TOO_LONG_ERROR)->addViolation();
 }
 if (\strlen($value) !== \strspn($value, '0123456789ABCDEFGHJKMNPQRSTVWXYZabcdefghjkmnpqrstvwxyz')) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Ulid::INVALID_CHARACTERS_ERROR)->addViolation();
 }
 // Largest valid ULID is '7ZZZZZZZZZZZZZZZZZZZZZZZZZ'
 // Cf https://github.com/ulid/spec#overflow-errors-when-parsing-base32-strings
 if ($value[0] > '7') {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Ulid::TOO_LARGE_ERROR)->addViolation();
 }
 }
}
