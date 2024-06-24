<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
interface QuoteStrategy
{
 public function getColumnName($fieldName, ClassMetadata $class, AbstractPlatform $platform);
 public function getTableName(ClassMetadata $class, AbstractPlatform $platform);
 public function getSequenceName(array $definition, ClassMetadata $class, AbstractPlatform $platform);
 public function getJoinTableName(array $association, ClassMetadata $class, AbstractPlatform $platform);
 public function getJoinColumnName(array $joinColumn, ClassMetadata $class, AbstractPlatform $platform);
 public function getReferencedJoinColumnName(array $joinColumn, ClassMetadata $class, AbstractPlatform $platform);
 public function getIdentifierColumnNames(ClassMetadata $class, AbstractPlatform $platform);
 public function getColumnAlias($columnName, $counter, AbstractPlatform $platform, ?ClassMetadata $class = null);
}
