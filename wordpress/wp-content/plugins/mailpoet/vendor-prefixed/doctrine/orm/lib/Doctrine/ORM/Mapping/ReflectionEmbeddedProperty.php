<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Instantiator\Instantiator;
use ReflectionProperty;
use ReturnTypeWillChange;
class ReflectionEmbeddedProperty extends ReflectionProperty
{
 private $parentProperty;
 private $childProperty;
 private $embeddedClass;
 private $instantiator;
 public function __construct(ReflectionProperty $parentProperty, ReflectionProperty $childProperty, $embeddedClass)
 {
 $this->parentProperty = $parentProperty;
 $this->childProperty = $childProperty;
 $this->embeddedClass = (string) $embeddedClass;
 parent::__construct($childProperty->getDeclaringClass()->getName(), $childProperty->getName());
 }
 #[\ReturnTypeWillChange]
 public function getValue($object = null)
 {
 $embeddedObject = $this->parentProperty->getValue($object);
 if ($embeddedObject === null) {
 return null;
 }
 return $this->childProperty->getValue($embeddedObject);
 }
 #[\ReturnTypeWillChange]
 public function setValue($object, $value = null)
 {
 $embeddedObject = $this->parentProperty->getValue($object);
 if ($embeddedObject === null) {
 $this->instantiator = $this->instantiator ?: new Instantiator();
 $embeddedObject = $this->instantiator->instantiate($this->embeddedClass);
 $this->parentProperty->setValue($object, $embeddedObject);
 }
 $this->childProperty->setValue($embeddedObject, $value);
 }
}
