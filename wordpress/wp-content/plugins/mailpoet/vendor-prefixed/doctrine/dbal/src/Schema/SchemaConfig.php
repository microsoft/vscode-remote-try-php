<?php
namespace MailPoetVendor\Doctrine\DBAL\Schema;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
class SchemaConfig
{
 protected $hasExplicitForeignKeyIndexes = \false;
 protected $maxIdentifierLength = 63;
 protected $name;
 protected $defaultTableOptions = [];
 public function hasExplicitForeignKeyIndexes()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4822', 'SchemaConfig::hasExplicitForeignKeyIndexes() is deprecated.');
 return $this->hasExplicitForeignKeyIndexes;
 }
 public function setExplicitForeignKeyIndexes($flag)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4822', 'SchemaConfig::setExplicitForeignKeyIndexes() is deprecated.');
 $this->hasExplicitForeignKeyIndexes = (bool) $flag;
 }
 public function setMaxIdentifierLength($length)
 {
 $this->maxIdentifierLength = (int) $length;
 }
 public function getMaxIdentifierLength()
 {
 return $this->maxIdentifierLength;
 }
 public function getName()
 {
 return $this->name;
 }
 public function setName($name)
 {
 $this->name = $name;
 }
 public function getDefaultTableOptions()
 {
 return $this->defaultTableOptions;
 }
 public function setDefaultTableOptions(array $defaultTableOptions)
 {
 $this->defaultTableOptions = $defaultTableOptions;
 }
}
