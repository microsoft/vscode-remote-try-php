<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use InvalidArgumentException;
use function get_class;
use function gettype;
use function is_object;
use function sprintf;
use function trim;
class DefaultEntityListenerResolver implements EntityListenerResolver
{
 private $instances = [];
 public function clear($className = null)
 {
 if ($className === null) {
 $this->instances = [];
 return;
 }
 $className = trim($className, '\\');
 if (isset($this->instances[$className])) {
 unset($this->instances[$className]);
 }
 }
 public function register($object)
 {
 if (!is_object($object)) {
 throw new InvalidArgumentException(sprintf('An object was expected, but got "%s".', gettype($object)));
 }
 $this->instances[get_class($object)] = $object;
 }
 public function resolve($className)
 {
 $className = trim($className, '\\');
 if (isset($this->instances[$className])) {
 return $this->instances[$className];
 }
 return $this->instances[$className] = new $className();
 }
}
