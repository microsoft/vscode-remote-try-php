<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Intl\Countries;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
class CountryValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Country) {
 throw new UnexpectedTypeException($constraint, Country::class);
 }
 if (null === $value || '' === $value) {
 return;
 }
 if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
 throw new UnexpectedValueException($value, 'string');
 }
 $value = (string) $value;
 if ($constraint->alpha3 ? !Countries::alpha3CodeExists($value) : !Countries::exists($value)) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Country::NO_SUCH_COUNTRY_ERROR)->addViolation();
 }
 }
}
