<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use function strpos;
use function strrpos;
use function strtolower;
use function substr;
class DefaultNamingStrategy implements NamingStrategy
{
 public function classToTableName($className)
 {
 if (strpos($className, '\\') !== \false) {
 return substr($className, strrpos($className, '\\') + 1);
 }
 return $className;
 }
 public function propertyToColumnName($propertyName, $className = null)
 {
 return $propertyName;
 }
 public function embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className = null, $embeddedClassName = null)
 {
 return $propertyName . '_' . $embeddedColumnName;
 }
 public function referenceColumnName()
 {
 return 'id';
 }
 public function joinColumnName($propertyName, $className = null)
 {
 return $propertyName . '_' . $this->referenceColumnName();
 }
 public function joinTableName($sourceEntity, $targetEntity, $propertyName = null)
 {
 return strtolower($this->classToTableName($sourceEntity) . '_' . $this->classToTableName($targetEntity));
 }
 public function joinKeyColumnName($entityName, $referencedColumnName = null)
 {
 return strtolower($this->classToTableName($entityName) . '_' . ($referencedColumnName ?: $this->referenceColumnName()));
 }
}
