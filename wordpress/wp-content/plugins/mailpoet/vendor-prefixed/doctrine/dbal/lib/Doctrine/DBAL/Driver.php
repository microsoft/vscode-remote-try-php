<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\DBAL\Schema\AbstractSchemaManager;
interface Driver
{
 public function connect(array $params, $username = null, $password = null, array $driverOptions = []);
 public function getDatabasePlatform();
 public function getSchemaManager(Connection $conn);
 public function getName();
 public function getDatabase(Connection $conn);
}
