<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Types\Types;
class MariaDb1027Platform extends MySqlPlatform
{
 public function getJsonTypeDeclarationSQL(array $column) : string
 {
 return 'LONGTEXT';
 }
 protected function getReservedKeywordsClass() : string
 {
 return Keywords\MariaDb102Keywords::class;
 }
 protected function initializeDoctrineTypeMappings() : void
 {
 parent::initializeDoctrineTypeMappings();
 $this->doctrineTypeMapping['json'] = Types::JSON;
 }
}
