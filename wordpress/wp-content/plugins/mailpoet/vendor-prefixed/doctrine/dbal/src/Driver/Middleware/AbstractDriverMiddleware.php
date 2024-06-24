<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver\Middleware;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Driver;
use MailPoetVendor\Doctrine\DBAL\Driver\API\ExceptionConverter;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\DBAL\VersionAwarePlatformDriver;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\SensitiveParameter;
abstract class AbstractDriverMiddleware implements VersionAwarePlatformDriver
{
 private Driver $wrappedDriver;
 public function __construct(Driver $wrappedDriver)
 {
 $this->wrappedDriver = $wrappedDriver;
 }
 public function connect( array $params)
 {
 return $this->wrappedDriver->connect($params);
 }
 public function getDatabasePlatform()
 {
 return $this->wrappedDriver->getDatabasePlatform();
 }
 public function getSchemaManager(Connection $conn, AbstractPlatform $platform)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5458', 'AbstractDriverMiddleware::getSchemaManager() is deprecated.' . ' Use AbstractPlatform::createSchemaManager() instead.');
 return $this->wrappedDriver->getSchemaManager($conn, $platform);
 }
 public function getExceptionConverter() : ExceptionConverter
 {
 return $this->wrappedDriver->getExceptionConverter();
 }
 public function createDatabasePlatformForVersion($version)
 {
 if ($this->wrappedDriver instanceof VersionAwarePlatformDriver) {
 return $this->wrappedDriver->createDatabasePlatformForVersion($version);
 }
 return $this->wrappedDriver->getDatabasePlatform();
 }
}
