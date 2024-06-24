<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\API\ExceptionConverter;
use MailPoetVendor\Doctrine\DBAL\Driver\Connection as DriverConnection;
use MailPoetVendor\Doctrine\DBAL\Driver\Exception;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\DBAL\Schema\AbstractSchemaManager;
use MailPoetVendor\SensitiveParameter;
interface Driver
{
 public function connect( array $params);
 public function getDatabasePlatform();
 public function getSchemaManager(Connection $conn, AbstractPlatform $platform);
 public function getExceptionConverter() : ExceptionConverter;
}
