<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping\Driver;
if (!defined('ABSPATH')) exit;
use Attribute;
use MailPoetVendor\Doctrine\ORM\Mapping\Annotation;
use LogicException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use function assert;
use function is_string;
use function is_subclass_of;
use function sprintf;
final class AttributeReader
{
 private array $isRepeatableAttribute = [];
 public function getClassAttributes(ReflectionClass $class) : array
 {
 return $this->convertToAttributeInstances($class->getAttributes());
 }
 public function getMethodAttributes(ReflectionMethod $method) : array
 {
 return $this->convertToAttributeInstances($method->getAttributes());
 }
 public function getPropertyAttributes(ReflectionProperty $property) : array
 {
 return $this->convertToAttributeInstances($property->getAttributes());
 }
 public function getPropertyAttribute(ReflectionProperty $property, $attributeName)
 {
 if ($this->isRepeatable($attributeName)) {
 throw new LogicException(sprintf('The attribute "%s" is repeatable. Call getPropertyAttributeCollection() instead.', $attributeName));
 }
 return $this->getPropertyAttributes($property)[$attributeName] ?? ($this->isRepeatable($attributeName) ? new RepeatableAttributeCollection() : null);
 }
 public function getPropertyAttributeCollection(ReflectionProperty $property, string $attributeName) : RepeatableAttributeCollection
 {
 if (!$this->isRepeatable($attributeName)) {
 throw new LogicException(sprintf('The attribute "%s" is not repeatable. Call getPropertyAttribute() instead.', $attributeName));
 }
 return $this->getPropertyAttributes($property)[$attributeName] ?? new RepeatableAttributeCollection();
 }
 private function convertToAttributeInstances(array $attributes) : array
 {
 $instances = [];
 foreach ($attributes as $attribute) {
 $attributeName = $attribute->getName();
 assert(is_string($attributeName));
 // Make sure we only get Doctrine Attributes
 if (!is_subclass_of($attributeName, Annotation::class)) {
 continue;
 }
 $instance = $attribute->newInstance();
 assert($instance instanceof Annotation);
 if ($this->isRepeatable($attributeName)) {
 if (!isset($instances[$attributeName])) {
 $instances[$attributeName] = new RepeatableAttributeCollection();
 }
 $collection = $instances[$attributeName];
 assert($collection instanceof RepeatableAttributeCollection);
 $collection[] = $instance;
 } else {
 $instances[$attributeName] = $instance;
 }
 }
 return $instances;
 }
 private function isRepeatable(string $attributeClassName) : bool
 {
 if (isset($this->isRepeatableAttribute[$attributeClassName])) {
 return $this->isRepeatableAttribute[$attributeClassName];
 }
 $reflectionClass = new ReflectionClass($attributeClassName);
 $attribute = $reflectionClass->getAttributes()[0]->newInstance();
 return $this->isRepeatableAttribute[$attributeClassName] = ($attribute->flags & Attribute::IS_REPEATABLE) > 0;
 }
}
