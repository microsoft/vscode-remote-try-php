<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Driver\API\ExceptionConverter;
use MailPoetVendor\Doctrine\DBAL\Driver\API\MySQL;
use MailPoetVendor\Doctrine\DBAL\Exception;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\DBAL\Platforms\MariaDb1027Platform;
use MailPoetVendor\Doctrine\DBAL\Platforms\MariaDb1043Platform;
use MailPoetVendor\Doctrine\DBAL\Platforms\MariaDb1052Platform;
use MailPoetVendor\Doctrine\DBAL\Platforms\MariaDb1060Platform;
use MailPoetVendor\Doctrine\DBAL\Platforms\MySQL57Platform;
use MailPoetVendor\Doctrine\DBAL\Platforms\MySQL80Platform;
use MailPoetVendor\Doctrine\DBAL\Platforms\MySQLPlatform;
use MailPoetVendor\Doctrine\DBAL\Schema\MySQLSchemaManager;
use MailPoetVendor\Doctrine\DBAL\VersionAwarePlatformDriver;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function assert;
use function preg_match;
use function stripos;
use function version_compare;
abstract class AbstractMySQLDriver implements VersionAwarePlatformDriver
{
 public function createDatabasePlatformForVersion($version)
 {
 $mariadb = stripos($version, 'mariadb') !== \false;
 if ($mariadb) {
 $mariaDbVersion = $this->getMariaDbMysqlVersionNumber($version);
 if (version_compare($mariaDbVersion, '10.6.0', '>=')) {
 return new MariaDb1060Platform();
 }
 if (version_compare($mariaDbVersion, '10.5.2', '>=')) {
 return new MariaDb1052Platform();
 }
 if (version_compare($mariaDbVersion, '10.4.3', '>=')) {
 return new MariaDb1043Platform();
 }
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/6110', 'Support for MariaDB < 10.4 is deprecated and will be removed in DBAL 4.' . ' Consider upgrading to a more recent version of MariaDB.');
 if (version_compare($mariaDbVersion, '10.2.7', '>=')) {
 return new MariaDb1027Platform();
 }
 } else {
 $oracleMysqlVersion = $this->getOracleMysqlVersionNumber($version);
 if (version_compare($oracleMysqlVersion, '8', '>=')) {
 if (!version_compare($version, '8.0.0', '>=')) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/dbal/pull/5779', 'Version detection logic for MySQL will change in DBAL 4. ' . 'Please specify the version as the server reports it, e.g. "8.0.31" instead of "8".');
 }
 return new MySQL80Platform();
 }
 if (version_compare($oracleMysqlVersion, '5.7.9', '>=')) {
 if (!version_compare($version, '5.7.9', '>=')) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/dbal/pull/5779', 'Version detection logic for MySQL will change in DBAL 4. ' . 'Please specify the version as the server reports it, e.g. "5.7.40" instead of "5.7".');
 }
 return new MySQL57Platform();
 }
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5072', 'MySQL 5.6 support is deprecated and will be removed in DBAL 4.' . ' Consider upgrading to MySQL 5.7 or later.');
 }
 return $this->getDatabasePlatform();
 }
 private function getOracleMysqlVersionNumber(string $versionString) : string
 {
 if (preg_match('/^(?P<major>\\d+)(?:\\.(?P<minor>\\d+)(?:\\.(?P<patch>\\d+))?)?/', $versionString, $versionParts) === 0) {
 throw Exception::invalidPlatformVersionSpecified($versionString, '<major_version>.<minor_version>.<patch_version>');
 }
 $majorVersion = $versionParts['major'];
 $minorVersion = $versionParts['minor'] ?? 0;
 $patchVersion = $versionParts['patch'] ?? null;
 if ($majorVersion === '5' && $minorVersion === '7') {
 $patchVersion ??= '9';
 }
 return $majorVersion . '.' . $minorVersion . '.' . $patchVersion;
 }
 private function getMariaDbMysqlVersionNumber(string $versionString) : string
 {
 if (stripos($versionString, 'MariaDB') === 0) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/dbal/pull/5779', 'Version detection logic for MySQL will change in DBAL 4. ' . 'Please specify the version as the server reports it, ' . 'e.g. "10.9.3-MariaDB" instead of "mariadb-10.9".');
 }
 if (preg_match('/^(?:5\\.5\\.5-)?(mariadb-)?(?P<major>\\d+)\\.(?P<minor>\\d+)\\.(?P<patch>\\d+)/i', $versionString, $versionParts) === 0) {
 throw Exception::invalidPlatformVersionSpecified($versionString, '^(?:5\\.5\\.5-)?(mariadb-)?<major_version>.<minor_version>.<patch_version>');
 }
 return $versionParts['major'] . '.' . $versionParts['minor'] . '.' . $versionParts['patch'];
 }
 public function getDatabasePlatform()
 {
 return new MySQLPlatform();
 }
 public function getSchemaManager(Connection $conn, AbstractPlatform $platform)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5458', 'AbstractMySQLDriver::getSchemaManager() is deprecated.' . ' Use MySQLPlatform::createSchemaManager() instead.');
 assert($platform instanceof AbstractMySQLPlatform);
 return new MySQLSchemaManager($conn, $platform);
 }
 public function getExceptionConverter() : ExceptionConverter
 {
 return new MySQL\ExceptionConverter();
 }
}
