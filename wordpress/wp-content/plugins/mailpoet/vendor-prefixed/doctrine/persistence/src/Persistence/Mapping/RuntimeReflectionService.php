<?php
namespace MailPoetVendor\Doctrine\Persistence\Mapping;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Persistence\Reflection\RuntimePublicReflectionProperty;
use MailPoetVendor\Doctrine\Persistence\Reflection\TypedNoDefaultReflectionProperty;
use MailPoetVendor\Doctrine\Persistence\Reflection\TypedNoDefaultRuntimePublicReflectionProperty;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use function array_key_exists;
use function assert;
use function class_exists;
use function class_parents;
use function phpversion;
use function version_compare;
class RuntimeReflectionService implements ReflectionService
{
 private $supportsTypedPropertiesWorkaround;
 public function __construct()
 {
 $this->supportsTypedPropertiesWorkaround = version_compare((string) phpversion(), '7.4.0') >= 0;
 }
 public function getParentClasses($class)
 {
 if (!class_exists($class)) {
 throw MappingException::nonExistingClass($class);
 }
 $parents = class_parents($class);
 assert($parents !== \false);
 return $parents;
 }
 public function getClassShortName($class)
 {
 $reflectionClass = new ReflectionClass($class);
 return $reflectionClass->getShortName();
 }
 public function getClassNamespace($class)
 {
 $reflectionClass = new ReflectionClass($class);
 return $reflectionClass->getNamespaceName();
 }
 public function getClass($class)
 {
 return new ReflectionClass($class);
 }
 public function getAccessibleProperty($class, $property)
 {
 $reflectionProperty = new ReflectionProperty($class, $property);
 if ($this->supportsTypedPropertiesWorkaround && !array_key_exists($property, $this->getClass($class)->getDefaultProperties())) {
 if ($reflectionProperty->isPublic()) {
 $reflectionProperty = new TypedNoDefaultRuntimePublicReflectionProperty($class, $property);
 } else {
 $reflectionProperty = new TypedNoDefaultReflectionProperty($class, $property);
 }
 } elseif ($reflectionProperty->isPublic()) {
 $reflectionProperty = new RuntimePublicReflectionProperty($class, $property);
 }
 $reflectionProperty->setAccessible(\true);
 return $reflectionProperty;
 }
 public function hasPublicMethod($class, $method)
 {
 try {
 $reflectionMethod = new ReflectionMethod($class, $method);
 } catch (ReflectionException $e) {
 return \false;
 }
 return $reflectionMethod->isPublic();
 }
}
