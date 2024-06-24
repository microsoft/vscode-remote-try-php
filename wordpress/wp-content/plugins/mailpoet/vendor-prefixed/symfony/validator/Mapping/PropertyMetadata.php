<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Exception\ValidatorException;
class PropertyMetadata extends MemberMetadata
{
 public function __construct(string $class, string $name)
 {
 if (!\property_exists($class, $name)) {
 throw new ValidatorException(\sprintf('Property "%s" does not exist in class "%s".', $name, $class));
 }
 parent::__construct($class, $name, $name);
 }
 public function getPropertyValue($object)
 {
 $reflProperty = $this->getReflectionMember($object);
 if (\PHP_VERSION_ID >= 70400 && $reflProperty->hasType() && !$reflProperty->isInitialized($object)) {
 // There is no way to check if a property has been unset or if it is uninitialized.
 // When trying to access an uninitialized property, __get method is triggered.
 // If __get method is not present, no fallback is possible
 // Otherwise we need to catch an Error in case we are trying to access an uninitialized but set property.
 if (!\method_exists($object, '__get')) {
 return null;
 }
 try {
 return $reflProperty->getValue($object);
 } catch (\Error $e) {
 return null;
 }
 }
 return $reflProperty->getValue($object);
 }
 protected function newReflectionMember($objectOrClassName)
 {
 $originalClass = \is_string($objectOrClassName) ? $objectOrClassName : \get_class($objectOrClassName);
 while (!\property_exists($objectOrClassName, $this->getName())) {
 $objectOrClassName = \get_parent_class($objectOrClassName);
 if (\false === $objectOrClassName) {
 throw new ValidatorException(\sprintf('Property "%s" does not exist in class "%s".', $this->getName(), $originalClass));
 }
 }
 $member = new \ReflectionProperty($objectOrClassName, $this->getName());
 $member->setAccessible(\true);
 return $member;
 }
}
