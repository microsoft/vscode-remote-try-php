<?php
namespace MailPoetVendor\Doctrine\Persistence\Mapping;
if (!defined('ABSPATH')) exit;
use ReflectionClass;
use ReflectionProperty;
interface ReflectionService
{
 public function getParentClasses($class);
 public function getClassShortName($class);
 public function getClassNamespace($class);
 public function getClass($class);
 public function getAccessibleProperty($class, $property);
 public function hasPublicMethod($class, $method);
}
