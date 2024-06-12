<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Driver;
use MailPoetVendor\Doctrine\DBAL\Driver\DriverException as DeprecatedDriverException;
use MailPoetVendor\Doctrine\DBAL\Exception;
use MailPoetVendor\Doctrine\DBAL\Exception\ConnectionException;
use MailPoetVendor\Doctrine\DBAL\Exception\ConnectionLost;
use MailPoetVendor\Doctrine\DBAL\Exception\DeadlockException;
use MailPoetVendor\Doctrine\DBAL\Exception\DriverException;
use MailPoetVendor\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use MailPoetVendor\Doctrine\DBAL\Exception\InvalidFieldNameException;
use MailPoetVendor\Doctrine\DBAL\Exception\LockWaitTimeoutException;
use MailPoetVendor\Doctrine\DBAL\Exception\NonUniqueFieldNameException;
use MailPoetVendor\Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use MailPoetVendor\Doctrine\DBAL\Exception\SyntaxErrorException;
use MailPoetVendor\Doctrine\DBAL\Exception\TableExistsException;
use MailPoetVendor\Doctrine\DBAL\Exception\TableNotFoundException;
use MailPoetVendor\Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use MailPoetVendor\Doctrine\DBAL\Platforms\MariaDb1027Platform;
use MailPoetVendor\Doctrine\DBAL\Platforms\MySQL57Platform;
use MailPoetVendor\Doctrine\DBAL\Platforms\MySQL80Platform;
use MailPoetVendor\Doctrine\DBAL\Platforms\MySqlPlatform;
use MailPoetVendor\Doctrine\DBAL\Schema\MySqlSchemaManager;
use MailPoetVendor\Doctrine\DBAL\VersionAwarePlatformDriver;
use function assert;
use function preg_match;
use function stripos;
use function version_compare;
abstract class AbstractMySQLDriver implements Driver, ExceptionConverterDriver, VersionAwarePlatformDriver
{
 public function convertException($message, DeprecatedDriverException $exception)
 {
 switch ($exception->getErrorCode()) {
 case '1213':
 return new DeadlockException($message, $exception);
 case '1205':
 return new LockWaitTimeoutException($message, $exception);
 case '1050':
 return new TableExistsException($message, $exception);
 case '1051':
 case '1146':
 return new TableNotFoundException($message, $exception);
 case '1216':
 case '1217':
 case '1451':
 case '1452':
 case '1701':
 return new ForeignKeyConstraintViolationException($message, $exception);
 case '1062':
 case '1557':
 case '1569':
 case '1586':
 return new UniqueConstraintViolationException($message, $exception);
 case '1054':
 case '1166':
 case '1611':
 return new InvalidFieldNameException($message, $exception);
 case '1052':
 case '1060':
 case '1110':
 return new NonUniqueFieldNameException($message, $exception);
 case '1064':
 case '1149':
 case '1287':
 case '1341':
 case '1342':
 case '1343':
 case '1344':
 case '1382':
 case '1479':
 case '1541':
 case '1554':
 case '1626':
 return new SyntaxErrorException($message, $exception);
 case '1044':
 case '1045':
 case '1046':
 case '1049':
 case '1095':
 case '1142':
 case '1143':
 case '1227':
 case '1370':
 case '1429':
 case '2002':
 case '2005':
 return new ConnectionException($message, $exception);
 case '2006':
 return new ConnectionLost($message, $exception);
 case '1048':
 case '1121':
 case '1138':
 case '1171':
 case '1252':
 case '1263':
 case '1364':
 case '1566':
 return new NotNullConstraintViolationException($message, $exception);
 }
 return new DriverException($message, $exception);
 }
 public function createDatabasePlatformForVersion($version)
 {
 $mariadb = stripos($version, 'mariadb') !== \false;
 if ($mariadb && version_compare($this->getMariaDbMysqlVersionNumber($version), '10.2.7', '>=')) {
 return new MariaDb1027Platform();
 }
 if (!$mariadb) {
 $oracleMysqlVersion = $this->getOracleMysqlVersionNumber($version);
 if (version_compare($oracleMysqlVersion, '8', '>=')) {
 return new MySQL80Platform();
 }
 if (version_compare($oracleMysqlVersion, '5.7.9', '>=')) {
 return new MySQL57Platform();
 }
 }
 return $this->getDatabasePlatform();
 }
 private function getOracleMysqlVersionNumber(string $versionString) : string
 {
 if (!preg_match('/^(?P<major>\\d+)(?:\\.(?P<minor>\\d+)(?:\\.(?P<patch>\\d+))?)?/', $versionString, $versionParts)) {
 throw Exception::invalidPlatformVersionSpecified($versionString, '<major_version>.<minor_version>.<patch_version>');
 }
 $majorVersion = $versionParts['major'];
 $minorVersion = $versionParts['minor'] ?? 0;
 $patchVersion = $versionParts['patch'] ?? null;
 if ($majorVersion === '5' && $minorVersion === '7' && $patchVersion === null) {
 $patchVersion = '9';
 }
 return $majorVersion . '.' . $minorVersion . '.' . $patchVersion;
 }
 private function getMariaDbMysqlVersionNumber(string $versionString) : string
 {
 if (!preg_match('/^(?:5\\.5\\.5-)?(mariadb-)?(?P<major>\\d+)\\.(?P<minor>\\d+)\\.(?P<patch>\\d+)/i', $versionString, $versionParts)) {
 throw Exception::invalidPlatformVersionSpecified($versionString, '^(?:5\\.5\\.5-)?(mariadb-)?<major_version>.<minor_version>.<patch_version>');
 }
 return $versionParts['major'] . '.' . $versionParts['minor'] . '.' . $versionParts['patch'];
 }
 public function getDatabase(Connection $conn)
 {
 $params = $conn->getParams();
 if (isset($params['dbname'])) {
 return $params['dbname'];
 }
 $database = $conn->query('SELECT DATABASE()')->fetchColumn();
 assert($database !== \false);
 return $database;
 }
 public function getDatabasePlatform()
 {
 return new MySqlPlatform();
 }
 public function getSchemaManager(Connection $conn)
 {
 return new MySqlSchemaManager($conn);
 }
}
