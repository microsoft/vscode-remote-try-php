<?php
namespace MailPoetVendor\Doctrine\Persistence\Reflection;
if (!defined('ABSPATH')) exit;
use BackedEnum;
use ReflectionProperty;
use ReturnTypeWillChange;
class EnumReflectionProperty extends ReflectionProperty
{
 private $originalReflectionProperty;
 private $enumType;
 public function __construct(ReflectionProperty $originalReflectionProperty, string $enumType)
 {
 $this->originalReflectionProperty = $originalReflectionProperty;
 $this->enumType = $enumType;
 }
 #[\ReturnTypeWillChange]
 public function getValue($object = null)
 {
 if ($object === null) {
 return null;
 }
 $enum = $this->originalReflectionProperty->getValue($object);
 if ($enum === null) {
 return null;
 }
 return $enum->value;
 }
 public function setValue($object, $value = null) : void
 {
 if ($value !== null) {
 $value = $this->enumType::from($value);
 }
 $this->originalReflectionProperty->setValue($object, $value);
 }
}
