<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
class HostnameValidator extends ConstraintValidator
{
 private const RESERVED_TLDS = ['example', 'invalid', 'localhost', 'test'];
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Hostname) {
 throw new UnexpectedTypeException($constraint, Hostname::class);
 }
 if (null === $value || '' === $value) {
 return;
 }
 if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
 throw new UnexpectedValueException($value, 'string');
 }
 $value = (string) $value;
 if ('' === $value) {
 return;
 }
 if (!$this->isValid($value) || $constraint->requireTld && !$this->hasValidTld($value)) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Hostname::INVALID_HOSTNAME_ERROR)->addViolation();
 }
 }
 private function isValid(string $domain) : bool
 {
 return \false !== \filter_var($domain, \FILTER_VALIDATE_DOMAIN, \FILTER_FLAG_HOSTNAME);
 }
 private function hasValidTld(string $domain) : bool
 {
 return \false !== \strpos($domain, '.') && !\in_array(\substr($domain, \strrpos($domain, '.') + 1), self::RESERVED_TLDS, \true);
 }
}
