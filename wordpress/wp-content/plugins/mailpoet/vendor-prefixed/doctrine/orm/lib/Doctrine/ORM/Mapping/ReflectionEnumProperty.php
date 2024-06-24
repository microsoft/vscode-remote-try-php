<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use BackedEnum;
use ReflectionProperty;
use ReturnTypeWillChange;
use ValueError;
use function array_map;
use function get_class;
use function is_array;
class ReflectionEnumProperty extends ReflectionProperty
{
 private $originalReflectionProperty;
 private $enumType;
 public function __construct(ReflectionProperty $originalReflectionProperty, string $enumType)
 {
 $this->originalReflectionProperty = $originalReflectionProperty;
 $this->enumType = $enumType;
 parent::__construct($originalReflectionProperty->getDeclaringClass()->getName(), $originalReflectionProperty->getName());
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
 if (is_array($enum)) {
 return array_map(static function (BackedEnum $item) : mixed {
 return $item->value;
 }, $enum);
 }
 return $enum->value;
 }
 public function setValue($object, $value = null) : void
 {
 if ($value !== null) {
 if (is_array($value)) {
 $value = array_map(function ($item) use($object) : BackedEnum {
 return $this->initializeEnumValue($object, $item);
 }, $value);
 } else {
 $value = $this->initializeEnumValue($object, $value);
 }
 }
 $this->originalReflectionProperty->setValue($object, $value);
 }
 private function initializeEnumValue($object, $value) : BackedEnum
 {
 if ($value instanceof BackedEnum) {
 return $value;
 }
 $enumType = $this->enumType;
 try {
 return $enumType::from($value);
 } catch (ValueError $e) {
 throw MappingException::invalidEnumValue(get_class($object), $this->originalReflectionProperty->getName(), (string) $value, $enumType, $e);
 }
 }
}
