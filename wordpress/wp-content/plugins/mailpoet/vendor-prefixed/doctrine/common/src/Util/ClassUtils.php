<?php
namespace MailPoetVendor\Doctrine\Common\Util;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Persistence\Proxy;
use ReflectionClass;
use function get_class;
use function get_parent_class;
use function ltrim;
use function rtrim;
use function strrpos;
use function substr;
class ClassUtils
{
 public static function getRealClass($className)
 {
 $pos = strrpos($className, '\\' . Proxy::MARKER . '\\');
 if ($pos === \false) {
 return $className;
 }
 return substr($className, $pos + Proxy::MARKER_LENGTH + 2);
 }
 public static function getClass($object)
 {
 return self::getRealClass(get_class($object));
 }
 public static function getParentClass($className)
 {
 return get_parent_class(self::getRealClass($className));
 }
 public static function newReflectionClass($className)
 {
 return new ReflectionClass(self::getRealClass($className));
 }
 public static function newReflectionObject($object)
 {
 return self::newReflectionClass(self::getClass($object));
 }
 public static function generateProxyClassName($className, $proxyNamespace)
 {
 return rtrim($proxyNamespace, '\\') . '\\' . Proxy::MARKER . '\\' . ltrim($className, '\\');
 }
}
