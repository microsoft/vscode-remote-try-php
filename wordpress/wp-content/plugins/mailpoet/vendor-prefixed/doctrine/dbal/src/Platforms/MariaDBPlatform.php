<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Types\Types;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
class MariaDBPlatform extends MySQLPlatform
{
 public function getDefaultValueDeclarationSQL($column)
 {
 return AbstractPlatform::getDefaultValueDeclarationSQL($column);
 }
 public function getJsonTypeDeclarationSQL(array $column) : string
 {
 return 'LONGTEXT';
 }
 protected function getReservedKeywordsClass() : string
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4510', 'MariaDb1027Platform::getReservedKeywordsClass() is deprecated,' . ' use MariaDb1027Platform::createReservedKeywordsList() instead.');
 return Keywords\MariaDb102Keywords::class;
 }
 protected function initializeDoctrineTypeMappings() : void
 {
 parent::initializeDoctrineTypeMappings();
 $this->doctrineTypeMapping['json'] = Types::JSON;
 }
}
