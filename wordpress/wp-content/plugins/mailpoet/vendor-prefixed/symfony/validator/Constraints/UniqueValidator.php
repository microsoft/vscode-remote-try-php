<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
class UniqueValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Unique) {
 throw new UnexpectedTypeException($constraint, Unique::class);
 }
 if (null === $value) {
 return;
 }
 if (!\is_array($value) && !$value instanceof \IteratorAggregate) {
 throw new UnexpectedValueException($value, 'array|IteratorAggregate');
 }
 $collectionElements = [];
 $normalizer = $this->getNormalizer($constraint);
 foreach ($value as $element) {
 $element = $normalizer($element);
 if (\in_array($element, $collectionElements, \true)) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Unique::IS_NOT_UNIQUE)->addViolation();
 return;
 }
 $collectionElements[] = $element;
 }
 }
 private function getNormalizer(Unique $unique) : callable
 {
 if (null === $unique->normalizer) {
 return static function ($value) {
 return $value;
 };
 }
 return $unique->normalizer;
 }
}
