<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
class CidrValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint) : void
 {
 if (!$constraint instanceof Cidr) {
 throw new UnexpectedTypeException($constraint, Cidr::class);
 }
 if (null === $value || '' === $value) {
 return;
 }
 if (!\is_string($value)) {
 throw new UnexpectedValueException($value, 'string');
 }
 $cidrParts = \explode('/', $value, 2);
 if (!isset($cidrParts[1]) || !\ctype_digit($cidrParts[1]) || '' === $cidrParts[0]) {
 $this->context->buildViolation($constraint->message)->setCode(Cidr::INVALID_CIDR_ERROR)->addViolation();
 return;
 }
 $ipAddress = $cidrParts[0];
 $netmask = (int) $cidrParts[1];
 $validV4 = Ip::V6 !== $constraint->version && \filter_var($ipAddress, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4) && $netmask <= 32;
 $validV6 = Ip::V4 !== $constraint->version && \filter_var($ipAddress, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6);
 if (!$validV4 && !$validV6) {
 $this->context->buildViolation($constraint->message)->setCode(Cidr::INVALID_CIDR_ERROR)->addViolation();
 return;
 }
 if ($netmask < $constraint->netmaskMin || $netmask > $constraint->netmaskMax) {
 $this->context->buildViolation($constraint->netmaskRangeViolationMessage)->setParameter('{{ min }}', $constraint->netmaskMin)->setParameter('{{ max }}', $constraint->netmaskMax)->setCode(Cidr::OUT_OF_RANGE_ERROR)->addViolation();
 }
 }
}
