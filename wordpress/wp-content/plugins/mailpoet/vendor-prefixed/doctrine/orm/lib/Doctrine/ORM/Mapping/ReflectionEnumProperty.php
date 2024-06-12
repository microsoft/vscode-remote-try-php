<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use BackedEnum;
use ReflectionProperty;
use ReturnTypeWillChange;
use ValueError;
use function assert;
use function get_class;
use function is_int;
use function is_string;
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
 return $enum->value;
 }
 public function setValue($object, $value = null) : void
 {
 if ($value !== null) {
 $enumType = $this->enumType;
 try {
 $value = $enumType::from($value);
 } catch (ValueError $e) {
 assert(is_string($value) || is_int($value));
 throw MappingException::invalidEnumValue(get_class($object), $this->originalReflectionProperty->getName(), (string) $value, $enumType, $e);
 }
 }
 $this->originalReflectionProperty->setValue($object, $value);
 }
}
