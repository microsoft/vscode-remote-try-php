<?php
namespace MailPoetVendor\Doctrine\Persistence\Reflection;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Proxy\Proxy;
use ReflectionProperty;
use ReturnTypeWillChange;
class RuntimePublicReflectionProperty extends ReflectionProperty
{
 #[\ReturnTypeWillChange]
 public function getValue($object = null)
 {
 $name = $this->getName();
 if ($object instanceof Proxy && !$object->__isInitialized()) {
 $originalInitializer = $object->__getInitializer();
 $object->__setInitializer(null);
 $val = $object->{$name} ?? null;
 $object->__setInitializer($originalInitializer);
 return $val;
 }
 return isset($object->{$name}) ? parent::getValue($object) : null;
 }
 #[\ReturnTypeWillChange]
 public function setValue($object, $value = null)
 {
 if (!($object instanceof Proxy && !$object->__isInitialized())) {
 parent::setValue($object, $value);
 return;
 }
 $originalInitializer = $object->__getInitializer();
 $object->__setInitializer(null);
 parent::setValue($object, $value);
 $object->__setInitializer($originalInitializer);
 }
}
