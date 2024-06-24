<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping\Builder;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Events;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\Mapping\MappingException;
use function class_exists;
use function get_class_methods;
class EntityListenerBuilder
{
 private static $events = [Events::preRemove => \true, Events::postRemove => \true, Events::prePersist => \true, Events::postPersist => \true, Events::preUpdate => \true, Events::postUpdate => \true, Events::postLoad => \true, Events::preFlush => \true];
 public static function bindEntityListener(ClassMetadata $metadata, $className)
 {
 $class = $metadata->fullyQualifiedClassName($className);
 if (!class_exists($class)) {
 throw MappingException::entityListenerClassNotFound($class, $className);
 }
 foreach (get_class_methods($class) as $method) {
 if (!isset(self::$events[$method])) {
 continue;
 }
 $metadata->addEntityListener($method, $class, $method);
 }
 }
}
