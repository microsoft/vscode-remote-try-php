<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Schema;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Connection;
interface SchemaManagerFactory
{
 public function createSchemaManager(Connection $connection) : AbstractSchemaManager;
}
