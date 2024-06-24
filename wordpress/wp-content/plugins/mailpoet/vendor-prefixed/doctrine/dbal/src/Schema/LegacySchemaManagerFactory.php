<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Schema;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Connection;
final class LegacySchemaManagerFactory implements SchemaManagerFactory
{
 public function createSchemaManager(Connection $connection) : AbstractSchemaManager
 {
 return $connection->getDriver()->getSchemaManager($connection, $connection->getDatabasePlatform());
 }
}
