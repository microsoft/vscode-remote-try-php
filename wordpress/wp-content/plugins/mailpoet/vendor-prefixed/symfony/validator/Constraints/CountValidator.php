<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
class CountValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Count) {
 throw new UnexpectedTypeException($constraint, Count::class);
 }
 if (null === $value) {
 return;
 }
 if (!\is_array($value) && !$value instanceof \Countable) {
 throw new UnexpectedValueException($value, 'array|\\Countable');
 }
 $count = \count($value);
 if (null !== $constraint->max && $count > $constraint->max) {
 $exactlyOptionEnabled = $constraint->min == $constraint->max;
 $this->context->buildViolation($exactlyOptionEnabled ? $constraint->exactMessage : $constraint->maxMessage)->setParameter('{{ count }}', $count)->setParameter('{{ limit }}', $constraint->max)->setInvalidValue($value)->setPlural((int) $constraint->max)->setCode($exactlyOptionEnabled ? Count::NOT_EQUAL_COUNT_ERROR : Count::TOO_MANY_ERROR)->addViolation();
 return;
 }
 if (null !== $constraint->min && $count < $constraint->min) {
 $exactlyOptionEnabled = $constraint->min == $constraint->max;
 $this->context->buildViolation($exactlyOptionEnabled ? $constraint->exactMessage : $constraint->minMessage)->setParameter('{{ count }}', $count)->setParameter('{{ limit }}', $constraint->min)->setInvalidValue($value)->setPlural((int) $constraint->min)->setCode($exactlyOptionEnabled ? Count::NOT_EQUAL_COUNT_ERROR : Count::TOO_FEW_ERROR)->addViolation();
 return;
 }
 if (null !== $constraint->divisibleBy) {
 $this->context->getValidator()->inContext($this->context)->validate($count, [new DivisibleBy(['value' => $constraint->divisibleBy, 'message' => $constraint->divisibleByMessage])], $this->context->getGroup());
 }
 }
}
