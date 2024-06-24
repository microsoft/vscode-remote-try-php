<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping\Reflection;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Persistence\Mapping\ReflectionService;
use ReflectionClass;
use ReflectionProperty;
use function array_combine;
use function array_filter;
use function array_map;
use function array_merge;
final class ReflectionPropertiesGetter
{
 private $properties = [];
 private $reflectionService;
 public function __construct(ReflectionService $reflectionService)
 {
 $this->reflectionService = $reflectionService;
 }
 public function getProperties($className) : array
 {
 if (isset($this->properties[$className])) {
 return $this->properties[$className];
 }
 return $this->properties[$className] = array_merge(
 // first merge because `array_merge` expects >= 1 params
 ...array_merge([[]], array_map([$this, 'getClassProperties'], $this->getHierarchyClasses($className)))
 );
 }
 private function getHierarchyClasses(string $className) : array
 {
 $classes = [];
 $parentClassName = $className;
 while ($parentClassName && ($currentClass = $this->reflectionService->getClass($parentClassName))) {
 $classes[] = $currentClass;
 $parentClassName = null;
 $parentClass = $currentClass->getParentClass();
 if ($parentClass) {
 $parentClassName = $parentClass->getName();
 }
 }
 return $classes;
 }
 // phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod
 private function getClassProperties(ReflectionClass $reflectionClass) : array
 {
 // phpcs:enable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod
 $properties = $reflectionClass->getProperties();
 return array_filter(array_filter(array_map([$this, 'getAccessibleProperty'], array_combine(array_map([$this, 'getLogicalName'], $properties), $properties))), [$this, 'isInstanceProperty']);
 }
 private function isInstanceProperty(ReflectionProperty $reflectionProperty) : bool
 {
 return !$reflectionProperty->isStatic();
 }
 private function getAccessibleProperty(ReflectionProperty $property) : ?ReflectionProperty
 {
 return $this->reflectionService->getAccessibleProperty($property->getDeclaringClass()->getName(), $property->getName());
 }
 private function getLogicalName(ReflectionProperty $property) : string
 {
 $propertyName = $property->getName();
 if ($property->isPublic()) {
 return $propertyName;
 }
 if ($property->isProtected()) {
 return "\x00*\x00" . $propertyName;
 }
 return "\x00" . $property->getDeclaringClass()->getName() . "\x00" . $propertyName;
 }
}
