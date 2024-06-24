<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Schema\Index;
use MailPoetVendor\Doctrine\DBAL\Schema\TableDiff;
use MailPoetVendor\Doctrine\DBAL\SQL\Parser;
use MailPoetVendor\Doctrine\DBAL\Types\Types;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
class MySQL57Platform extends MySQLPlatform
{
 public function hasNativeJsonType()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5509', '%s is deprecated.', __METHOD__);
 return \true;
 }
 public function getJsonTypeDeclarationSQL(array $column)
 {
 return 'JSON';
 }
 public function createSQLParser() : Parser
 {
 return new Parser(\true);
 }
 protected function getPreAlterTableRenameIndexForeignKeySQL(TableDiff $diff)
 {
 return [];
 }
 protected function getPostAlterTableRenameIndexForeignKeySQL(TableDiff $diff)
 {
 return [];
 }
 protected function getRenameIndexSQL($oldIndexName, Index $index, $tableName)
 {
 return ['ALTER TABLE ' . $tableName . ' RENAME INDEX ' . $oldIndexName . ' TO ' . $index->getQuotedName($this)];
 }
 protected function getReservedKeywordsClass()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4510', 'MySQL57Platform::getReservedKeywordsClass() is deprecated,' . ' use MySQL57Platform::createReservedKeywordsList() instead.');
 return Keywords\MySQL57Keywords::class;
 }
 protected function initializeDoctrineTypeMappings()
 {
 parent::initializeDoctrineTypeMappings();
 $this->doctrineTypeMapping['json'] = Types::JSON;
 }
}
