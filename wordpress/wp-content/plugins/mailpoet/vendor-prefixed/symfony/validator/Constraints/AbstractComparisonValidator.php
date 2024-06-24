<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use MailPoetVendor\Symfony\Component\PropertyAccess\PropertyAccess;
use MailPoetVendor\Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
abstract class AbstractComparisonValidator extends ConstraintValidator
{
 private $propertyAccessor;
 public function __construct(PropertyAccessorInterface $propertyAccessor = null)
 {
 $this->propertyAccessor = $propertyAccessor;
 }
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof AbstractComparison) {
 throw new UnexpectedTypeException($constraint, AbstractComparison::class);
 }
 if (null === $value) {
 return;
 }
 if ($path = $constraint->propertyPath) {
 if (null === ($object = $this->context->getObject())) {
 return;
 }
 try {
 $comparedValue = $this->getPropertyAccessor()->getValue($object, $path);
 } catch (NoSuchPropertyException $e) {
 throw new ConstraintDefinitionException(\sprintf('Invalid property path "%s" provided to "%s" constraint: ', $path, \get_debug_type($constraint)) . $e->getMessage(), 0, $e);
 }
 } else {
 $comparedValue = $constraint->value;
 }
 // Convert strings to DateTimes if comparing another DateTime
 // This allows to compare with any date/time value supported by
 // the DateTime constructor:
 // https://php.net/datetime.formats
 if (\is_string($comparedValue) && $value instanceof \DateTimeInterface) {
 // If $value is immutable, convert the compared value to a DateTimeImmutable too, otherwise use DateTime
 $dateTimeClass = $value instanceof \DateTimeImmutable ? \DateTimeImmutable::class : \DateTime::class;
 try {
 $comparedValue = new $dateTimeClass($comparedValue);
 } catch (\Exception $e) {
 throw new ConstraintDefinitionException(\sprintf('The compared value "%s" could not be converted to a "%s" instance in the "%s" constraint.', $comparedValue, $dateTimeClass, \get_debug_type($constraint)));
 }
 }
 if (!$this->compareValues($value, $comparedValue)) {
 $violationBuilder = $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value, self::OBJECT_TO_STRING | self::PRETTY_DATE))->setParameter('{{ compared_value }}', $this->formatValue($comparedValue, self::OBJECT_TO_STRING | self::PRETTY_DATE))->setParameter('{{ compared_value_type }}', $this->formatTypeOf($comparedValue))->setCode($this->getErrorCode());
 if (null !== $path) {
 $violationBuilder->setParameter('{{ compared_value_path }}', $path);
 }
 $violationBuilder->addViolation();
 }
 }
 private function getPropertyAccessor() : PropertyAccessorInterface
 {
 if (null === $this->propertyAccessor) {
 $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
 }
 return $this->propertyAccessor;
 }
 protected abstract function compareValues($value1, $value2);
 protected function getErrorCode()
 {
 return null;
 }
}
