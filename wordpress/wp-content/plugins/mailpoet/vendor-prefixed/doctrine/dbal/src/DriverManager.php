<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\EventManager;
use MailPoetVendor\Doctrine\DBAL\Driver\IBMDB2;
use MailPoetVendor\Doctrine\DBAL\Driver\Mysqli;
use MailPoetVendor\Doctrine\DBAL\Driver\OCI8;
use MailPoetVendor\Doctrine\DBAL\Driver\PDO;
use MailPoetVendor\Doctrine\DBAL\Driver\PgSQL;
use MailPoetVendor\Doctrine\DBAL\Driver\SQLite3;
use MailPoetVendor\Doctrine\DBAL\Driver\SQLSrv;
use MailPoetVendor\Doctrine\DBAL\Exception\MalformedDsnException;
use MailPoetVendor\Doctrine\DBAL\Tools\DsnParser;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\SensitiveParameter;
use function array_keys;
use function array_merge;
use function is_a;
final class DriverManager
{
 private const DRIVER_MAP = ['pdo_mysql' => PDO\MySQL\Driver::class, 'pdo_sqlite' => PDO\SQLite\Driver::class, 'pdo_pgsql' => PDO\PgSQL\Driver::class, 'pdo_oci' => PDO\OCI\Driver::class, 'oci8' => OCI8\Driver::class, 'ibm_db2' => IBMDB2\Driver::class, 'pdo_sqlsrv' => PDO\SQLSrv\Driver::class, 'mysqli' => Mysqli\Driver::class, 'pgsql' => PgSQL\Driver::class, 'sqlsrv' => SQLSrv\Driver::class, 'sqlite3' => SQLite3\Driver::class];
 private static array $driverSchemeAliases = [
 'db2' => 'ibm_db2',
 'mssql' => 'pdo_sqlsrv',
 'mysql' => 'pdo_mysql',
 'mysql2' => 'pdo_mysql',
 // Amazon RDS, for some weird reason
 'postgres' => 'pdo_pgsql',
 'postgresql' => 'pdo_pgsql',
 'pgsql' => 'pdo_pgsql',
 'sqlite' => 'pdo_sqlite',
 'sqlite3' => 'pdo_sqlite',
 ];
 private function __construct()
 {
 }
 public static function getConnection( array $params, ?Configuration $config = null, ?EventManager $eventManager = null) : Connection
 {
 // create default config and event manager, if not set
 $config ??= new Configuration();
 $eventManager ??= new EventManager();
 $params = self::parseDatabaseUrl($params);
 // URL support for PrimaryReplicaConnection
 if (isset($params['primary'])) {
 $params['primary'] = self::parseDatabaseUrl($params['primary']);
 }
 if (isset($params['replica'])) {
 foreach ($params['replica'] as $key => $replicaParams) {
 $params['replica'][$key] = self::parseDatabaseUrl($replicaParams);
 }
 }
 $driver = self::createDriver($params['driver'] ?? null, $params['driverClass'] ?? null);
 foreach ($config->getMiddlewares() as $middleware) {
 $driver = $middleware->wrap($driver);
 }
 $wrapperClass = $params['wrapperClass'] ?? Connection::class;
 if (!is_a($wrapperClass, Connection::class, \true)) {
 throw Exception::invalidWrapperClass($wrapperClass);
 }
 return new $wrapperClass($params, $driver, $config, $eventManager);
 }
 public static function getAvailableDrivers() : array
 {
 return array_keys(self::DRIVER_MAP);
 }
 private static function createDriver(?string $driver, ?string $driverClass) : Driver
 {
 if ($driverClass === null) {
 if ($driver === null) {
 throw Exception::driverRequired();
 }
 if (!isset(self::DRIVER_MAP[$driver])) {
 throw Exception::unknownDriver($driver, array_keys(self::DRIVER_MAP));
 }
 $driverClass = self::DRIVER_MAP[$driver];
 } elseif (!is_a($driverClass, Driver::class, \true)) {
 throw Exception::invalidDriverClass($driverClass);
 }
 return new $driverClass();
 }
 private static function parseDatabaseUrl( array $params) : array
 {
 if (!isset($params['url'])) {
 return $params;
 }
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5843', 'The "url" connection parameter is deprecated. Please use %s to parse a database url before calling %s.', DsnParser::class, self::class);
 $parser = new DsnParser(self::$driverSchemeAliases);
 try {
 $parsedParams = $parser->parse($params['url']);
 } catch (MalformedDsnException $e) {
 throw new Exception('Malformed parameter "url".', 0, $e);
 }
 if (isset($parsedParams['driver'])) {
 // The requested driver from the URL scheme takes precedence
 // over the default custom driver from the connection parameters (if any).
 unset($params['driverClass']);
 }
 $params = array_merge($params, $parsedParams);
 // If a schemeless connection URL is given, we require a default driver or default custom driver
 // as connection parameter.
 if (!isset($params['driverClass']) && !isset($params['driver'])) {
 throw Exception::driverRequired($params['url']);
 }
 return $params;
 }
}
