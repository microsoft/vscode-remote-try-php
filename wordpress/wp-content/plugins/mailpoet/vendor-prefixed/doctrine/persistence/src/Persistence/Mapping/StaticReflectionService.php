<?php
namespace MailPoetVendor\Doctrine\Persistence\Mapping;
if (!defined('ABSPATH')) exit;
use function strpos;
use function strrev;
use function strrpos;
use function substr;
class StaticReflectionService implements ReflectionService
{
 public function getParentClasses($class)
 {
 return [];
 }
 public function getClassShortName($class)
 {
 $nsSeparatorLastPosition = strrpos($class, '\\');
 if ($nsSeparatorLastPosition !== \false) {
 $class = substr($class, $nsSeparatorLastPosition + 1);
 }
 return $class;
 }
 public function getClassNamespace($class)
 {
 $namespace = '';
 if (strpos($class, '\\') !== \false) {
 $namespace = strrev(substr(strrev($class), (int) strpos(strrev($class), '\\') + 1));
 }
 return $namespace;
 }
 public function getClass($class)
 {
 return null;
 }
 public function getAccessibleProperty($class, $property)
 {
 return null;
 }
 public function hasPublicMethod($class, $method)
 {
 return \true;
 }
}
