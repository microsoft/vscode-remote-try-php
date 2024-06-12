<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\EventManager;
use MailPoetVendor\Doctrine\DBAL\Driver\DrizzlePDOMySql;
use MailPoetVendor\Doctrine\DBAL\Driver\IBMDB2;
use MailPoetVendor\Doctrine\DBAL\Driver\Mysqli;
use MailPoetVendor\Doctrine\DBAL\Driver\OCI8;
use MailPoetVendor\Doctrine\DBAL\Driver\PDO;
use MailPoetVendor\Doctrine\DBAL\Driver\SQLAnywhere;
use MailPoetVendor\Doctrine\DBAL\Driver\SQLSrv;
use function array_keys;
use function array_merge;
use function class_implements;
use function in_array;
use function is_string;
use function is_subclass_of;
use function parse_str;
use function parse_url;
use function preg_replace;
use function rawurldecode;
use function str_replace;
use function strpos;
use function substr;
final class DriverManager
{
 private const DRIVER_MAP = ['pdo_mysql' => PDO\MySQL\Driver::class, 'pdo_sqlite' => PDO\SQLite\Driver::class, 'pdo_pgsql' => PDO\PgSQL\Driver::class, 'pdo_oci' => PDO\OCI\Driver::class, 'oci8' => OCI8\Driver::class, 'ibm_db2' => IBMDB2\Driver::class, 'pdo_sqlsrv' => PDO\SQLSrv\Driver::class, 'mysqli' => Mysqli\Driver::class, 'drizzle_pdo_mysql' => DrizzlePDOMySql\Driver::class, 'sqlanywhere' => SQLAnywhere\Driver::class, 'sqlsrv' => SQLSrv\Driver::class];
 private static $driverSchemeAliases = [
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
 public static function getConnection(array $params, ?Configuration $config = null, ?EventManager $eventManager = null) : Connection
 {
 // create default config and event manager, if not set
 if (!$config) {
 $config = new Configuration();
 }
 if (!$eventManager) {
 $eventManager = new EventManager();
 }
 $params = self::parseDatabaseUrl($params);
 // @todo: deprecated, notice thrown by connection constructor
 if (isset($params['master'])) {
 $params['master'] = self::parseDatabaseUrl($params['master']);
 }
 // @todo: deprecated, notice thrown by connection constructor
 if (isset($params['slaves'])) {
 foreach ($params['slaves'] as $key => $slaveParams) {
 $params['slaves'][$key] = self::parseDatabaseUrl($slaveParams);
 }
 }
 // URL support for PrimaryReplicaConnection
 if (isset($params['primary'])) {
 $params['primary'] = self::parseDatabaseUrl($params['primary']);
 }
 if (isset($params['replica'])) {
 foreach ($params['replica'] as $key => $replicaParams) {
 $params['replica'][$key] = self::parseDatabaseUrl($replicaParams);
 }
 }
 // URL support for PoolingShardConnection
 if (isset($params['global'])) {
 $params['global'] = self::parseDatabaseUrl($params['global']);
 }
 if (isset($params['shards'])) {
 foreach ($params['shards'] as $key => $shardParams) {
 $params['shards'][$key] = self::parseDatabaseUrl($shardParams);
 }
 }
 // check for existing pdo object
 if (isset($params['pdo']) && !$params['pdo'] instanceof \PDO) {
 throw Exception::invalidPdoInstance();
 }
 if (isset($params['pdo'])) {
 $params['pdo']->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
 $params['driver'] = 'pdo_' . $params['pdo']->getAttribute(\PDO::ATTR_DRIVER_NAME);
 }
 $driver = self::createDriver($params);
 $wrapperClass = Connection::class;
 if (isset($params['wrapperClass'])) {
 if (!is_subclass_of($params['wrapperClass'], $wrapperClass)) {
 throw Exception::invalidWrapperClass($params['wrapperClass']);
 }
 $wrapperClass = $params['wrapperClass'];
 }
 return new $wrapperClass($params, $driver, $config, $eventManager);
 }
 public static function getAvailableDrivers() : array
 {
 return array_keys(self::DRIVER_MAP);
 }
 private static function createDriver(array $params) : Driver
 {
 if (isset($params['driverClass'])) {
 $interfaces = class_implements($params['driverClass'], \true);
 if ($interfaces === \false || !in_array(Driver::class, $interfaces)) {
 throw Exception::invalidDriverClass($params['driverClass']);
 }
 return new $params['driverClass']();
 }
 if (isset($params['driver'])) {
 if (!isset(self::DRIVER_MAP[$params['driver']])) {
 throw Exception::unknownDriver($params['driver'], array_keys(self::DRIVER_MAP));
 }
 $class = self::DRIVER_MAP[$params['driver']];
 return new $class();
 }
 throw Exception::driverRequired();
 }
 private static function normalizeDatabaseUrlPath(string $urlPath) : string
 {
 // Trim leading slash from URL path.
 return substr($urlPath, 1);
 }
 private static function parseDatabaseUrl(array $params) : array
 {
 if (!isset($params['url'])) {
 return $params;
 }
 // (pdo_)?sqlite3?:///... => (pdo_)?sqlite3?://localhost/... or else the URL will be invalid
 $url = preg_replace('#^((?:pdo_)?sqlite3?):///#', '$1://localhost/', $params['url']);
 $url = parse_url($url);
 if ($url === \false) {
 throw new Exception('Malformed parameter "url".');
 }
 foreach ($url as $param => $value) {
 if (!is_string($value)) {
 continue;
 }
 $url[$param] = rawurldecode($value);
 }
 // If we have a connection URL, we have to unset the default PDO instance connection parameter (if any)
 // as we cannot merge connection details from the URL into the PDO instance (URL takes precedence).
 unset($params['pdo']);
 $params = self::parseDatabaseUrlScheme($url['scheme'] ?? null, $params);
 if (isset($url['host'])) {
 $params['host'] = $url['host'];
 }
 if (isset($url['port'])) {
 $params['port'] = $url['port'];
 }
 if (isset($url['user'])) {
 $params['user'] = $url['user'];
 }
 if (isset($url['pass'])) {
 $params['password'] = $url['pass'];
 }
 $params = self::parseDatabaseUrlPath($url, $params);
 $params = self::parseDatabaseUrlQuery($url, $params);
 return $params;
 }
 private static function parseDatabaseUrlPath(array $url, array $params) : array
 {
 if (!isset($url['path'])) {
 return $params;
 }
 $url['path'] = self::normalizeDatabaseUrlPath($url['path']);
 // If we do not have a known DBAL driver, we do not know any connection URL path semantics to evaluate
 // and therefore treat the path as regular DBAL connection URL path.
 if (!isset($params['driver'])) {
 return self::parseRegularDatabaseUrlPath($url, $params);
 }
 if (strpos($params['driver'], 'sqlite') !== \false) {
 return self::parseSqliteDatabaseUrlPath($url, $params);
 }
 return self::parseRegularDatabaseUrlPath($url, $params);
 }
 private static function parseDatabaseUrlQuery(array $url, array $params) : array
 {
 if (!isset($url['query'])) {
 return $params;
 }
 $query = [];
 parse_str($url['query'], $query);
 // simply ingest query as extra params, e.g. charset or sslmode
 return array_merge($params, $query);
 // parse_str wipes existing array elements
 }
 private static function parseRegularDatabaseUrlPath(array $url, array $params) : array
 {
 $params['dbname'] = $url['path'];
 return $params;
 }
 private static function parseSqliteDatabaseUrlPath(array $url, array $params) : array
 {
 if ($url['path'] === ':memory:') {
 $params['memory'] = \true;
 return $params;
 }
 $params['path'] = $url['path'];
 // pdo_sqlite driver uses 'path' instead of 'dbname' key
 return $params;
 }
 private static function parseDatabaseUrlScheme($scheme, array $params) : array
 {
 if ($scheme !== null) {
 // The requested driver from the URL scheme takes precedence
 // over the default custom driver from the connection parameters (if any).
 unset($params['driverClass']);
 // URL schemes must not contain underscores, but dashes are ok
 $driver = str_replace('-', '_', $scheme);
 // The requested driver from the URL scheme takes precedence over the
 // default driver from the connection parameters. If the driver is
 // an alias (e.g. "postgres"), map it to the actual name ("pdo-pgsql").
 // Otherwise, let checkParams decide later if the driver exists.
 $params['driver'] = self::$driverSchemeAliases[$driver] ?? $driver;
 return $params;
 }
 // If a schemeless connection URL is given, we require a default driver or default custom driver
 // as connection parameter.
 if (!isset($params['driverClass']) && !isset($params['driver'])) {
 throw Exception::driverRequired($params['url']);
 }
 return $params;
 }
}
