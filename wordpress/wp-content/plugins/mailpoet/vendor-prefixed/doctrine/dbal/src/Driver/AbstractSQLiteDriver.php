<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Driver;
use MailPoetVendor\Doctrine\DBAL\Driver\API\ExceptionConverter;
use MailPoetVendor\Doctrine\DBAL\Driver\API\SQLite;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\DBAL\Platforms\SqlitePlatform;
use MailPoetVendor\Doctrine\DBAL\Schema\SqliteSchemaManager;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function assert;
abstract class AbstractSQLiteDriver implements Driver
{
 public function getDatabasePlatform()
 {
 return new SqlitePlatform();
 }
 public function getSchemaManager(Connection $conn, AbstractPlatform $platform)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5458', 'AbstractSQLiteDriver::getSchemaManager() is deprecated.' . ' Use SqlitePlatform::createSchemaManager() instead.');
 assert($platform instanceof SqlitePlatform);
 return new SqliteSchemaManager($conn, $platform);
 }
 public function getExceptionConverter() : ExceptionConverter
 {
 return new SQLite\ExceptionConverter();
 }
}
