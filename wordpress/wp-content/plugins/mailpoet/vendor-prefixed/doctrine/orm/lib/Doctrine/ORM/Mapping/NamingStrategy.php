<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
interface NamingStrategy
{
 public function classToTableName($className);
 public function propertyToColumnName($propertyName, $className = null);
 public function embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className = null, $embeddedClassName = null);
 public function referenceColumnName();
 public function joinColumnName($propertyName);
 public function joinTableName($sourceEntity, $targetEntity, $propertyName = null);
 public function joinKeyColumnName($entityName, $referencedColumnName = null);
}
