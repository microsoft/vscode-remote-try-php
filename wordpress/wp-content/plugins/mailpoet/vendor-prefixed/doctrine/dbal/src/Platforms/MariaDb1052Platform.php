<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Schema\Index;
use MailPoetVendor\Doctrine\DBAL\Schema\TableDiff;
class MariaDb1052Platform extends MariaDb1043Platform
{
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
}
