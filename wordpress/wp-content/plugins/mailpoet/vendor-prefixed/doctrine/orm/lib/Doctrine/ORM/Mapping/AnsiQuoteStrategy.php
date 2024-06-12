<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\ORM\Internal\SQLResultCasing;
class AnsiQuoteStrategy implements QuoteStrategy
{
 use SQLResultCasing;
 public function getColumnName($fieldName, ClassMetadata $class, AbstractPlatform $platform)
 {
 return $class->fieldMappings[$fieldName]['columnName'];
 }
 public function getTableName(ClassMetadata $class, AbstractPlatform $platform)
 {
 return $class->table['name'];
 }
 public function getSequenceName(array $definition, ClassMetadata $class, AbstractPlatform $platform)
 {
 return $definition['sequenceName'];
 }
 public function getJoinColumnName(array $joinColumn, ClassMetadata $class, AbstractPlatform $platform)
 {
 return $joinColumn['name'];
 }
 public function getReferencedJoinColumnName(array $joinColumn, ClassMetadata $class, AbstractPlatform $platform)
 {
 return $joinColumn['referencedColumnName'];
 }
 public function getJoinTableName(array $association, ClassMetadata $class, AbstractPlatform $platform)
 {
 return $association['joinTable']['name'];
 }
 public function getIdentifierColumnNames(ClassMetadata $class, AbstractPlatform $platform)
 {
 return $class->identifier;
 }
 public function getColumnAlias($columnName, $counter, AbstractPlatform $platform, ?ClassMetadata $class = null)
 {
 return $this->getSQLResultCasing($platform, $columnName . '_' . $counter);
 }
}
