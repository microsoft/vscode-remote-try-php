<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
class IpValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Ip) {
 throw new UnexpectedTypeException($constraint, Ip::class);
 }
 if (null === $value || '' === $value) {
 return;
 }
 if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
 throw new UnexpectedValueException($value, 'string');
 }
 $value = (string) $value;
 if (null !== $constraint->normalizer) {
 $value = ($constraint->normalizer)($value);
 }
 switch ($constraint->version) {
 case Ip::V4:
 $flag = \FILTER_FLAG_IPV4;
 break;
 case Ip::V6:
 $flag = \FILTER_FLAG_IPV6;
 break;
 case Ip::V4_NO_PRIV:
 $flag = \FILTER_FLAG_IPV4 | \FILTER_FLAG_NO_PRIV_RANGE;
 break;
 case Ip::V6_NO_PRIV:
 $flag = \FILTER_FLAG_IPV6 | \FILTER_FLAG_NO_PRIV_RANGE;
 break;
 case Ip::ALL_NO_PRIV:
 $flag = \FILTER_FLAG_NO_PRIV_RANGE;
 break;
 case Ip::V4_NO_RES:
 $flag = \FILTER_FLAG_IPV4 | \FILTER_FLAG_NO_RES_RANGE;
 break;
 case Ip::V6_NO_RES:
 $flag = \FILTER_FLAG_IPV6 | \FILTER_FLAG_NO_RES_RANGE;
 break;
 case Ip::ALL_NO_RES:
 $flag = \FILTER_FLAG_NO_RES_RANGE;
 break;
 case Ip::V4_ONLY_PUBLIC:
 $flag = \FILTER_FLAG_IPV4 | \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE;
 break;
 case Ip::V6_ONLY_PUBLIC:
 $flag = \FILTER_FLAG_IPV6 | \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE;
 break;
 case Ip::ALL_ONLY_PUBLIC:
 $flag = \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE;
 break;
 default:
 $flag = 0;
 break;
 }
 if (!\filter_var($value, \FILTER_VALIDATE_IP, $flag)) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Ip::INVALID_IP_ERROR)->addViolation();
 }
 }
}
