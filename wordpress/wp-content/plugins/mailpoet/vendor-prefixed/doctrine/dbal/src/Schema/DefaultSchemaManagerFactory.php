<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Schema;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Exception;
final class DefaultSchemaManagerFactory implements SchemaManagerFactory
{
 public function createSchemaManager(Connection $connection) : AbstractSchemaManager
 {
 return $connection->getDatabasePlatform()->createSchemaManager($connection);
 }
}
