<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
use Closure;
use MailPoetVendor\Doctrine\Common\EventManager;
use MailPoetVendor\Doctrine\DBAL\Cache\ArrayStatement;
use MailPoetVendor\Doctrine\DBAL\Cache\CacheException;
use MailPoetVendor\Doctrine\DBAL\Cache\QueryCacheProfile;
use MailPoetVendor\Doctrine\DBAL\Cache\ResultCacheStatement;
use MailPoetVendor\Doctrine\DBAL\Driver\Connection as DriverConnection;
use MailPoetVendor\Doctrine\DBAL\Driver\PDO\Statement as PDODriverStatement;
use MailPoetVendor\Doctrine\DBAL\Driver\PingableConnection;
use MailPoetVendor\Doctrine\DBAL\Driver\ResultStatement;
use MailPoetVendor\Doctrine\DBAL\Driver\ServerInfoAwareConnection;
use MailPoetVendor\Doctrine\DBAL\Exception\ConnectionLost;
use MailPoetVendor\Doctrine\DBAL\Exception\InvalidArgumentException;
use MailPoetVendor\Doctrine\DBAL\Exception\NoKeyValue;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;
use MailPoetVendor\Doctrine\DBAL\Schema\AbstractSchemaManager;
use MailPoetVendor\Doctrine\DBAL\Types\Type;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use PDO;
use Throwable;
use Traversable;
use function array_key_exists;
use function array_shift;
use function assert;
use function func_get_args;
use function implode;
use function is_int;
use function is_string;
use function key;
class Connection implements DriverConnection
{
 public const TRANSACTION_READ_UNCOMMITTED = TransactionIsolationLevel::READ_UNCOMMITTED;
 public const TRANSACTION_READ_COMMITTED = TransactionIsolationLevel::READ_COMMITTED;
 public const TRANSACTION_REPEATABLE_READ = TransactionIsolationLevel::REPEATABLE_READ;
 public const TRANSACTION_SERIALIZABLE = TransactionIsolationLevel::SERIALIZABLE;
 public const PARAM_INT_ARRAY = ParameterType::INTEGER + self::ARRAY_PARAM_OFFSET;
 public const PARAM_STR_ARRAY = ParameterType::STRING + self::ARRAY_PARAM_OFFSET;
 public const ARRAY_PARAM_OFFSET = 100;
 protected $_conn;
 protected $_config;
 protected $_eventManager;
 protected $_expr;
 private $autoCommit = \true;
 private $transactionNestingLevel = 0;
 private $transactionIsolationLevel;
 private $nestTransactionsWithSavepoints = \false;
 private $params;
 private $platform;
 protected $_schemaManager;
 protected $_driver;
 private $isRollbackOnly = \false;
 protected $defaultFetchMode = FetchMode::ASSOCIATIVE;
 public function __construct(array $params, Driver $driver, ?Configuration $config = null, ?EventManager $eventManager = null)
 {
 $this->_driver = $driver;
 $this->params = $params;
 if (isset($params['pdo'])) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3554', 'Passing a user provided PDO instance directly to Doctrine is deprecated.');
 if (!$params['pdo'] instanceof PDO) {
 throw Exception::invalidPdoInstance();
 }
 $this->_conn = $params['pdo'];
 $this->_conn->setAttribute(PDO::ATTR_STATEMENT_CLASS, [PDODriverStatement::class, []]);
 unset($this->params['pdo']);
 }
 if (isset($params['platform'])) {
 if (!$params['platform'] instanceof Platforms\AbstractPlatform) {
 throw Exception::invalidPlatformType($params['platform']);
 }
 $this->platform = $params['platform'];
 }
 // Create default config and event manager if none given
 if (!$config) {
 $config = new Configuration();
 }
 if (!$eventManager) {
 $eventManager = new EventManager();
 }
 $this->_config = $config;
 $this->_eventManager = $eventManager;
 $this->_expr = new Query\Expression\ExpressionBuilder($this);
 $this->autoCommit = $config->getAutoCommit();
 }
 public function getParams()
 {
 return $this->params;
 }
 public function getDatabase()
 {
 return $this->_driver->getDatabase($this);
 }
 public function getHost()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3580', 'Connection::getHost() is deprecated, get the database server host from application config ' . 'or as a last resort from internal Connection::getParams() API.');
 return $this->params['host'] ?? null;
 }
 public function getPort()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3580', 'Connection::getPort() is deprecated, get the database server port from application config ' . 'or as a last resort from internal Connection::getParams() API.');
 return $this->params['port'] ?? null;
 }
 public function getUsername()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3580', 'Connection::getUsername() is deprecated, get the username from application config ' . 'or as a last resort from internal Connection::getParams() API.');
 return $this->params['user'] ?? null;
 }
 public function getPassword()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3580', 'Connection::getPassword() is deprecated, get the password from application config ' . 'or as a last resort from internal Connection::getParams() API.');
 return $this->params['password'] ?? null;
 }
 public function getDriver()
 {
 return $this->_driver;
 }
 public function getConfiguration()
 {
 return $this->_config;
 }
 public function getEventManager()
 {
 return $this->_eventManager;
 }
 public function getDatabasePlatform()
 {
 if ($this->platform === null) {
 $this->platform = $this->detectDatabasePlatform();
 $this->platform->setEventManager($this->_eventManager);
 }
 return $this->platform;
 }
 public function getExpressionBuilder()
 {
 return $this->_expr;
 }
 public function connect()
 {
 if ($this->_conn !== null) {
 return \false;
 }
 $driverOptions = $this->params['driverOptions'] ?? [];
 $user = $this->params['user'] ?? null;
 $password = $this->params['password'] ?? null;
 $this->_conn = $this->_driver->connect($this->params, $user, $password, $driverOptions);
 $this->transactionNestingLevel = 0;
 if ($this->autoCommit === \false) {
 $this->beginTransaction();
 }
 if ($this->_eventManager->hasListeners(Events::postConnect)) {
 $eventArgs = new Event\ConnectionEventArgs($this);
 $this->_eventManager->dispatchEvent(Events::postConnect, $eventArgs);
 }
 return \true;
 }
 private function detectDatabasePlatform() : AbstractPlatform
 {
 $version = $this->getDatabasePlatformVersion();
 if ($version !== null) {
 assert($this->_driver instanceof VersionAwarePlatformDriver);
 return $this->_driver->createDatabasePlatformForVersion($version);
 }
 return $this->_driver->getDatabasePlatform();
 }
 private function getDatabasePlatformVersion()
 {
 // Driver does not support version specific platforms.
 if (!$this->_driver instanceof VersionAwarePlatformDriver) {
 return null;
 }
 // Explicit platform version requested (supersedes auto-detection).
 if (isset($this->params['serverVersion'])) {
 return $this->params['serverVersion'];
 }
 // If not connected, we need to connect now to determine the platform version.
 if ($this->_conn === null) {
 try {
 $this->connect();
 } catch (Throwable $originalException) {
 if (empty($this->params['dbname'])) {
 throw $originalException;
 }
 // The database to connect to might not yet exist.
 // Retry detection without database name connection parameter.
 $params = $this->params;
 unset($this->params['dbname']);
 try {
 $this->connect();
 } catch (Throwable $fallbackException) {
 // Either the platform does not support database-less connections
 // or something else went wrong.
 throw $originalException;
 } finally {
 $this->params = $params;
 }
 $serverVersion = $this->getServerVersion();
 // Close "temporary" connection to allow connecting to the real database again.
 $this->close();
 return $serverVersion;
 }
 }
 return $this->getServerVersion();
 }
 private function getServerVersion()
 {
 $connection = $this->getWrappedConnection();
 // Automatic platform version detection.
 if ($connection instanceof ServerInfoAwareConnection && !$connection->requiresQueryForServerVersion()) {
 return $connection->getServerVersion();
 }
 // Unable to detect platform version.
 return null;
 }
 public function isAutoCommit()
 {
 return $this->autoCommit === \true;
 }
 public function setAutoCommit($autoCommit)
 {
 $autoCommit = (bool) $autoCommit;
 // Mode not changed, no-op.
 if ($autoCommit === $this->autoCommit) {
 return;
 }
 $this->autoCommit = $autoCommit;
 // Commit all currently active transactions if any when switching auto-commit mode.
 if ($this->_conn === null || $this->transactionNestingLevel === 0) {
 return;
 }
 $this->commitAll();
 }
 public function setFetchMode($fetchMode)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4019', 'Default Fetch Mode configuration is deprecated, use explicit Connection::fetch*() APIs instead.');
 $this->defaultFetchMode = $fetchMode;
 }
 public function fetchAssoc($sql, array $params = [], array $types = [])
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4019', 'Connection::fetchAssoc() is deprecated, use Connection::fetchAssociative() API instead.');
 return $this->executeQuery($sql, $params, $types)->fetch(FetchMode::ASSOCIATIVE);
 }
 public function fetchArray($sql, array $params = [], array $types = [])
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4019', 'Connection::fetchArray() is deprecated, use Connection::fetchNumeric() API instead.');
 return $this->executeQuery($sql, $params, $types)->fetch(FetchMode::NUMERIC);
 }
 public function fetchColumn($sql, array $params = [], $column = 0, array $types = [])
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4019', 'Connection::fetchColumn() is deprecated, use Connection::fetchOne() API instead.');
 return $this->executeQuery($sql, $params, $types)->fetchColumn($column);
 }
 public function fetchAssociative(string $query, array $params = [], array $types = [])
 {
 try {
 $stmt = $this->ensureForwardCompatibilityStatement($this->executeQuery($query, $params, $types));
 return $stmt->fetchAssociative();
 } catch (Throwable $e) {
 $this->handleExceptionDuringQuery($e, $query, $params, $types);
 }
 }
 public function fetchNumeric(string $query, array $params = [], array $types = [])
 {
 try {
 $stmt = $this->ensureForwardCompatibilityStatement($this->executeQuery($query, $params, $types));
 return $stmt->fetchNumeric();
 } catch (Throwable $e) {
 $this->handleExceptionDuringQuery($e, $query, $params, $types);
 }
 }
 public function fetchOne(string $query, array $params = [], array $types = [])
 {
 try {
 $stmt = $this->ensureForwardCompatibilityStatement($this->executeQuery($query, $params, $types));
 return $stmt->fetchOne();
 } catch (Throwable $e) {
 $this->handleExceptionDuringQuery($e, $query, $params, $types);
 }
 }
 public function isConnected()
 {
 return $this->_conn !== null;
 }
 public function isTransactionActive()
 {
 return $this->transactionNestingLevel > 0;
 }
 private function addCriteriaCondition(array $criteria, array &$columns, array &$values, array &$conditions) : void
 {
 $platform = $this->getDatabasePlatform();
 foreach ($criteria as $columnName => $value) {
 if ($value === null) {
 $conditions[] = $platform->getIsNullExpression($columnName);
 continue;
 }
 $columns[] = $columnName;
 $values[] = $value;
 $conditions[] = $columnName . ' = ?';
 }
 }
 public function delete($table, array $criteria, array $types = [])
 {
 if (empty($criteria)) {
 throw InvalidArgumentException::fromEmptyCriteria();
 }
 $columns = $values = $conditions = [];
 $this->addCriteriaCondition($criteria, $columns, $values, $conditions);
 return $this->executeStatement('DELETE FROM ' . $table . ' WHERE ' . implode(' AND ', $conditions), $values, is_string(key($types)) ? $this->extractTypeValues($columns, $types) : $types);
 }
 public function close()
 {
 $this->_conn = null;
 }
 public function setTransactionIsolation($level)
 {
 $this->transactionIsolationLevel = $level;
 return $this->executeStatement($this->getDatabasePlatform()->getSetTransactionIsolationSQL($level));
 }
 public function getTransactionIsolation()
 {
 if ($this->transactionIsolationLevel === null) {
 $this->transactionIsolationLevel = $this->getDatabasePlatform()->getDefaultTransactionIsolationLevel();
 }
 return $this->transactionIsolationLevel;
 }
 public function update($table, array $data, array $criteria, array $types = [])
 {
 $columns = $values = $conditions = $set = [];
 foreach ($data as $columnName => $value) {
 $columns[] = $columnName;
 $values[] = $value;
 $set[] = $columnName . ' = ?';
 }
 $this->addCriteriaCondition($criteria, $columns, $values, $conditions);
 if (is_string(key($types))) {
 $types = $this->extractTypeValues($columns, $types);
 }
 $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $set) . ' WHERE ' . implode(' AND ', $conditions);
 return $this->executeStatement($sql, $values, $types);
 }
 public function insert($table, array $data, array $types = [])
 {
 if (empty($data)) {
 return $this->executeStatement('INSERT INTO ' . $table . ' () VALUES ()');
 }
 $columns = [];
 $values = [];
 $set = [];
 foreach ($data as $columnName => $value) {
 $columns[] = $columnName;
 $values[] = $value;
 $set[] = '?';
 }
 return $this->executeStatement('INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ')' . ' VALUES (' . implode(', ', $set) . ')', $values, is_string(key($types)) ? $this->extractTypeValues($columns, $types) : $types);
 }
 private function extractTypeValues(array $columnList, array $types)
 {
 $typeValues = [];
 foreach ($columnList as $columnIndex => $columnName) {
 $typeValues[] = $types[$columnName] ?? ParameterType::STRING;
 }
 return $typeValues;
 }
 public function quoteIdentifier($str)
 {
 return $this->getDatabasePlatform()->quoteIdentifier($str);
 }
 public function quote($value, $type = ParameterType::STRING)
 {
 $connection = $this->getWrappedConnection();
 [$value, $bindingType] = $this->getBindingInfo($value, $type);
 return $connection->quote($value, $bindingType);
 }
 public function fetchAll($sql, array $params = [], $types = [])
 {
 return $this->executeQuery($sql, $params, $types)->fetchAll();
 }
 public function fetchAllNumeric(string $query, array $params = [], array $types = []) : array
 {
 try {
 $stmt = $this->ensureForwardCompatibilityStatement($this->executeQuery($query, $params, $types));
 return $stmt->fetchAllNumeric();
 } catch (Throwable $e) {
 $this->handleExceptionDuringQuery($e, $query, $params, $types);
 }
 }
 public function fetchAllAssociative(string $query, array $params = [], array $types = []) : array
 {
 try {
 $stmt = $this->ensureForwardCompatibilityStatement($this->executeQuery($query, $params, $types));
 return $stmt->fetchAllAssociative();
 } catch (Throwable $e) {
 $this->handleExceptionDuringQuery($e, $query, $params, $types);
 }
 }
 public function fetchAllKeyValue(string $query, array $params = [], array $types = []) : array
 {
 $stmt = $this->executeQuery($query, $params, $types);
 $this->ensureHasKeyValue($stmt);
 $data = [];
 foreach ($stmt->fetchAll(FetchMode::NUMERIC) as [$key, $value]) {
 $data[$key] = $value;
 }
 return $data;
 }
 public function fetchAllAssociativeIndexed(string $query, array $params = [], array $types = []) : array
 {
 $stmt = $this->executeQuery($query, $params, $types);
 $data = [];
 foreach ($stmt->fetchAll(FetchMode::ASSOCIATIVE) as $row) {
 $data[array_shift($row)] = $row;
 }
 return $data;
 }
 public function fetchFirstColumn(string $query, array $params = [], array $types = []) : array
 {
 try {
 $stmt = $this->ensureForwardCompatibilityStatement($this->executeQuery($query, $params, $types));
 return $stmt->fetchFirstColumn();
 } catch (Throwable $e) {
 $this->handleExceptionDuringQuery($e, $query, $params, $types);
 }
 }
 public function iterateNumeric(string $query, array $params = [], array $types = []) : Traversable
 {
 try {
 $stmt = $this->ensureForwardCompatibilityStatement($this->executeQuery($query, $params, $types));
 yield from $stmt->iterateNumeric();
 } catch (Throwable $e) {
 $this->handleExceptionDuringQuery($e, $query, $params, $types);
 }
 }
 public function iterateAssociative(string $query, array $params = [], array $types = []) : Traversable
 {
 try {
 $stmt = $this->ensureForwardCompatibilityStatement($this->executeQuery($query, $params, $types));
 yield from $stmt->iterateAssociative();
 } catch (Throwable $e) {
 $this->handleExceptionDuringQuery($e, $query, $params, $types);
 }
 }
 public function iterateKeyValue(string $query, array $params = [], array $types = []) : Traversable
 {
 $stmt = $this->executeQuery($query, $params, $types);
 $this->ensureHasKeyValue($stmt);
 while (($row = $stmt->fetch(FetchMode::NUMERIC)) !== \false) {
 (yield $row[0] => $row[1]);
 }
 }
 public function iterateAssociativeIndexed(string $query, array $params = [], array $types = []) : Traversable
 {
 $stmt = $this->executeQuery($query, $params, $types);
 while (($row = $stmt->fetch(FetchMode::ASSOCIATIVE)) !== \false) {
 (yield array_shift($row) => $row);
 }
 }
 public function iterateColumn(string $query, array $params = [], array $types = []) : Traversable
 {
 try {
 $stmt = $this->ensureForwardCompatibilityStatement($this->executeQuery($query, $params, $types));
 yield from $stmt->iterateColumn();
 } catch (Throwable $e) {
 $this->handleExceptionDuringQuery($e, $query, $params, $types);
 }
 }
 public function prepare($sql)
 {
 try {
 $stmt = new Statement($sql, $this);
 } catch (Throwable $e) {
 $this->handleExceptionDuringQuery($e, $sql);
 }
 $stmt->setFetchMode($this->defaultFetchMode);
 return $stmt;
 }
 public function executeQuery($sql, array $params = [], $types = [], ?QueryCacheProfile $qcp = null)
 {
 if ($qcp !== null) {
 return $this->executeCacheQuery($sql, $params, $types, $qcp);
 }
 $connection = $this->getWrappedConnection();
 $logger = $this->_config->getSQLLogger();
 if ($logger) {
 $logger->startQuery($sql, $params, $types);
 }
 try {
 if ($params) {
 [$sql, $params, $types] = SQLParserUtils::expandListParameters($sql, $params, $types);
 $stmt = $connection->prepare($sql);
 if ($types) {
 $this->_bindTypedValues($stmt, $params, $types);
 $stmt->execute();
 } else {
 $stmt->execute($params);
 }
 } else {
 $stmt = $connection->query($sql);
 }
 } catch (Throwable $e) {
 $this->handleExceptionDuringQuery($e, $sql, $params, $types);
 }
 $stmt->setFetchMode($this->defaultFetchMode);
 if ($logger) {
 $logger->stopQuery();
 }
 return $this->ensureForwardCompatibilityStatement($stmt);
 }
 public function executeCacheQuery($sql, $params, $types, QueryCacheProfile $qcp)
 {
 $resultCache = $qcp->getResultCacheDriver() ?? $this->_config->getResultCacheImpl();
 if ($resultCache === null) {
 throw CacheException::noResultDriverConfigured();
 }
 $connectionParams = $this->params;
 unset($connectionParams['platform']);
 [$cacheKey, $realKey] = $qcp->generateCacheKeys($sql, $params, $types, $connectionParams);
 // fetch the row pointers entry
 $data = $resultCache->fetch($cacheKey);
 if ($data !== \false) {
 // is the real key part of this row pointers map or is the cache only pointing to other cache keys?
 if (isset($data[$realKey])) {
 $stmt = new ArrayStatement($data[$realKey]);
 } elseif (array_key_exists($realKey, $data)) {
 $stmt = new ArrayStatement([]);
 }
 }
 if (!isset($stmt)) {
 $stmt = new ResultCacheStatement($this->executeQuery($sql, $params, $types), $resultCache, $cacheKey, $realKey, $qcp->getLifetime());
 }
 $stmt->setFetchMode($this->defaultFetchMode);
 return $this->ensureForwardCompatibilityStatement($stmt);
 }
 private function ensureForwardCompatibilityStatement(ResultStatement $stmt)
 {
 return ForwardCompatibility\Result::ensure($stmt);
 }
 public function project($sql, array $params, Closure $function)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3823', 'Connection::project() is deprecated without replacement, implement data projections in your own code.');
 $result = [];
 $stmt = $this->executeQuery($sql, $params);
 while ($row = $stmt->fetch()) {
 $result[] = $function($row);
 }
 $stmt->closeCursor();
 return $result;
 }
 public function query()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4163', 'Connection::query() is deprecated, use Connection::executeQuery() instead.');
 $connection = $this->getWrappedConnection();
 $args = func_get_args();
 $logger = $this->_config->getSQLLogger();
 if ($logger) {
 $logger->startQuery($args[0]);
 }
 try {
 $statement = $connection->query(...$args);
 } catch (Throwable $e) {
 $this->handleExceptionDuringQuery($e, $args[0]);
 }
 $statement->setFetchMode($this->defaultFetchMode);
 if ($logger) {
 $logger->stopQuery();
 }
 return $statement;
 }
 public function executeUpdate($sql, array $params = [], array $types = [])
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4163', 'Connection::executeUpdate() is deprecated, use Connection::executeStatement() instead.');
 return $this->executeStatement($sql, $params, $types);
 }
 public function executeStatement($sql, array $params = [], array $types = [])
 {
 $connection = $this->getWrappedConnection();
 $logger = $this->_config->getSQLLogger();
 if ($logger) {
 $logger->startQuery($sql, $params, $types);
 }
 try {
 if ($params) {
 [$sql, $params, $types] = SQLParserUtils::expandListParameters($sql, $params, $types);
 $stmt = $connection->prepare($sql);
 if ($types) {
 $this->_bindTypedValues($stmt, $params, $types);
 $stmt->execute();
 } else {
 $stmt->execute($params);
 }
 $result = $stmt->rowCount();
 } else {
 $result = $connection->exec($sql);
 }
 } catch (Throwable $e) {
 $this->handleExceptionDuringQuery($e, $sql, $params, $types);
 }
 if ($logger) {
 $logger->stopQuery();
 }
 return $result;
 }
 public function exec($sql)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4163', 'Connection::exec() is deprecated, use Connection::executeStatement() instead.');
 $connection = $this->getWrappedConnection();
 $logger = $this->_config->getSQLLogger();
 if ($logger) {
 $logger->startQuery($sql);
 }
 try {
 $result = $connection->exec($sql);
 } catch (Throwable $e) {
 $this->handleExceptionDuringQuery($e, $sql);
 }
 if ($logger) {
 $logger->stopQuery();
 }
 return $result;
 }
 public function getTransactionNestingLevel()
 {
 return $this->transactionNestingLevel;
 }
 public function errorCode()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3507', 'Connection::errorCode() is deprecated, use getCode() or getSQLState() on Exception instead.');
 return $this->getWrappedConnection()->errorCode();
 }
 public function errorInfo()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3507', 'Connection::errorInfo() is deprecated, use getCode() or getSQLState() on Exception instead.');
 return $this->getWrappedConnection()->errorInfo();
 }
 public function lastInsertId($name = null)
 {
 return $this->getWrappedConnection()->lastInsertId($name);
 }
 public function transactional(Closure $func)
 {
 $this->beginTransaction();
 try {
 $res = $func($this);
 $this->commit();
 return $res;
 } catch (Throwable $e) {
 $this->rollBack();
 throw $e;
 }
 }
 public function setNestTransactionsWithSavepoints($nestTransactionsWithSavepoints)
 {
 if ($this->transactionNestingLevel > 0) {
 throw ConnectionException::mayNotAlterNestedTransactionWithSavepointsInTransaction();
 }
 if (!$this->getDatabasePlatform()->supportsSavepoints()) {
 throw ConnectionException::savepointsNotSupported();
 }
 $this->nestTransactionsWithSavepoints = (bool) $nestTransactionsWithSavepoints;
 }
 public function getNestTransactionsWithSavepoints()
 {
 return $this->nestTransactionsWithSavepoints;
 }
 protected function _getNestedTransactionSavePointName()
 {
 return 'DOCTRINE2_SAVEPOINT_' . $this->transactionNestingLevel;
 }
 public function beginTransaction()
 {
 $connection = $this->getWrappedConnection();
 ++$this->transactionNestingLevel;
 $logger = $this->_config->getSQLLogger();
 if ($this->transactionNestingLevel === 1) {
 if ($logger) {
 $logger->startQuery('"START TRANSACTION"');
 }
 $connection->beginTransaction();
 if ($logger) {
 $logger->stopQuery();
 }
 } elseif ($this->nestTransactionsWithSavepoints) {
 if ($logger) {
 $logger->startQuery('"SAVEPOINT"');
 }
 $this->createSavepoint($this->_getNestedTransactionSavePointName());
 if ($logger) {
 $logger->stopQuery();
 }
 }
 return \true;
 }
 public function commit()
 {
 if ($this->transactionNestingLevel === 0) {
 throw ConnectionException::noActiveTransaction();
 }
 if ($this->isRollbackOnly) {
 throw ConnectionException::commitFailedRollbackOnly();
 }
 $result = \true;
 $connection = $this->getWrappedConnection();
 $logger = $this->_config->getSQLLogger();
 if ($this->transactionNestingLevel === 1) {
 if ($logger) {
 $logger->startQuery('"COMMIT"');
 }
 $result = $connection->commit();
 if ($logger) {
 $logger->stopQuery();
 }
 } elseif ($this->nestTransactionsWithSavepoints) {
 if ($logger) {
 $logger->startQuery('"RELEASE SAVEPOINT"');
 }
 $this->releaseSavepoint($this->_getNestedTransactionSavePointName());
 if ($logger) {
 $logger->stopQuery();
 }
 }
 --$this->transactionNestingLevel;
 if ($this->autoCommit !== \false || $this->transactionNestingLevel !== 0) {
 return $result;
 }
 $this->beginTransaction();
 return $result;
 }
 private function commitAll() : void
 {
 while ($this->transactionNestingLevel !== 0) {
 if ($this->autoCommit === \false && $this->transactionNestingLevel === 1) {
 // When in no auto-commit mode, the last nesting commit immediately starts a new transaction.
 // Therefore we need to do the final commit here and then leave to avoid an infinite loop.
 $this->commit();
 return;
 }
 $this->commit();
 }
 }
 public function rollBack()
 {
 if ($this->transactionNestingLevel === 0) {
 throw ConnectionException::noActiveTransaction();
 }
 $connection = $this->getWrappedConnection();
 $logger = $this->_config->getSQLLogger();
 if ($this->transactionNestingLevel === 1) {
 if ($logger) {
 $logger->startQuery('"ROLLBACK"');
 }
 $this->transactionNestingLevel = 0;
 $connection->rollBack();
 $this->isRollbackOnly = \false;
 if ($logger) {
 $logger->stopQuery();
 }
 if ($this->autoCommit === \false) {
 $this->beginTransaction();
 }
 } elseif ($this->nestTransactionsWithSavepoints) {
 if ($logger) {
 $logger->startQuery('"ROLLBACK TO SAVEPOINT"');
 }
 $this->rollbackSavepoint($this->_getNestedTransactionSavePointName());
 --$this->transactionNestingLevel;
 if ($logger) {
 $logger->stopQuery();
 }
 } else {
 $this->isRollbackOnly = \true;
 --$this->transactionNestingLevel;
 }
 return \true;
 }
 public function createSavepoint($savepoint)
 {
 $platform = $this->getDatabasePlatform();
 if (!$platform->supportsSavepoints()) {
 throw ConnectionException::savepointsNotSupported();
 }
 $this->getWrappedConnection()->exec($platform->createSavePoint($savepoint));
 }
 public function releaseSavepoint($savepoint)
 {
 $platform = $this->getDatabasePlatform();
 if (!$platform->supportsSavepoints()) {
 throw ConnectionException::savepointsNotSupported();
 }
 if (!$platform->supportsReleaseSavepoints()) {
 return;
 }
 $this->getWrappedConnection()->exec($platform->releaseSavePoint($savepoint));
 }
 public function rollbackSavepoint($savepoint)
 {
 $platform = $this->getDatabasePlatform();
 if (!$platform->supportsSavepoints()) {
 throw ConnectionException::savepointsNotSupported();
 }
 $this->getWrappedConnection()->exec($platform->rollbackSavePoint($savepoint));
 }
 public function getWrappedConnection()
 {
 $this->connect();
 assert($this->_conn !== null);
 return $this->_conn;
 }
 public function getSchemaManager()
 {
 if ($this->_schemaManager === null) {
 $this->_schemaManager = $this->_driver->getSchemaManager($this);
 }
 return $this->_schemaManager;
 }
 public function setRollbackOnly()
 {
 if ($this->transactionNestingLevel === 0) {
 throw ConnectionException::noActiveTransaction();
 }
 $this->isRollbackOnly = \true;
 }
 public function isRollbackOnly()
 {
 if ($this->transactionNestingLevel === 0) {
 throw ConnectionException::noActiveTransaction();
 }
 return $this->isRollbackOnly;
 }
 public function convertToDatabaseValue($value, $type)
 {
 return Type::getType($type)->convertToDatabaseValue($value, $this->getDatabasePlatform());
 }
 public function convertToPHPValue($value, $type)
 {
 return Type::getType($type)->convertToPHPValue($value, $this->getDatabasePlatform());
 }
 private function _bindTypedValues($stmt, array $params, array $types)
 {
 // Check whether parameters are positional or named. Mixing is not allowed, just like in PDO.
 if (is_int(key($params))) {
 // Positional parameters
 $typeOffset = array_key_exists(0, $types) ? -1 : 0;
 $bindIndex = 1;
 foreach ($params as $value) {
 $typeIndex = $bindIndex + $typeOffset;
 if (isset($types[$typeIndex])) {
 $type = $types[$typeIndex];
 [$value, $bindingType] = $this->getBindingInfo($value, $type);
 $stmt->bindValue($bindIndex, $value, $bindingType);
 } else {
 $stmt->bindValue($bindIndex, $value);
 }
 ++$bindIndex;
 }
 } else {
 // Named parameters
 foreach ($params as $name => $value) {
 if (isset($types[$name])) {
 $type = $types[$name];
 [$value, $bindingType] = $this->getBindingInfo($value, $type);
 $stmt->bindValue($name, $value, $bindingType);
 } else {
 $stmt->bindValue($name, $value);
 }
 }
 }
 }
 private function getBindingInfo($value, $type) : array
 {
 if (is_string($type)) {
 $type = Type::getType($type);
 }
 if ($type instanceof Type) {
 $value = $type->convertToDatabaseValue($value, $this->getDatabasePlatform());
 $bindingType = $type->getBindingType();
 } else {
 $bindingType = $type ?? ParameterType::STRING;
 }
 return [$value, $bindingType];
 }
 public function resolveParams(array $params, array $types)
 {
 $resolvedParams = [];
 // Check whether parameters are positional or named. Mixing is not allowed, just like in PDO.
 if (is_int(key($params))) {
 // Positional parameters
 $typeOffset = array_key_exists(0, $types) ? -1 : 0;
 $bindIndex = 1;
 foreach ($params as $value) {
 $typeIndex = $bindIndex + $typeOffset;
 if (isset($types[$typeIndex])) {
 $type = $types[$typeIndex];
 [$value] = $this->getBindingInfo($value, $type);
 $resolvedParams[$bindIndex] = $value;
 } else {
 $resolvedParams[$bindIndex] = $value;
 }
 ++$bindIndex;
 }
 } else {
 // Named parameters
 foreach ($params as $name => $value) {
 if (isset($types[$name])) {
 $type = $types[$name];
 [$value] = $this->getBindingInfo($value, $type);
 $resolvedParams[$name] = $value;
 } else {
 $resolvedParams[$name] = $value;
 }
 }
 }
 return $resolvedParams;
 }
 public function createQueryBuilder()
 {
 return new Query\QueryBuilder($this);
 }
 public function ping()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4119', 'Retry and reconnecting lost connections now happens automatically, ping() will be removed in DBAL 3.');
 $connection = $this->getWrappedConnection();
 if ($connection instanceof PingableConnection) {
 return $connection->ping();
 }
 try {
 $this->query($this->getDatabasePlatform()->getDummySelectSQL());
 return \true;
 } catch (DBALException $e) {
 return \false;
 }
 }
 public function handleExceptionDuringQuery(Throwable $e, string $sql, array $params = [], array $types = []) : void
 {
 $this->throw(Exception::driverExceptionDuringQuery($this->_driver, $e, $sql, $this->resolveParams($params, $types)));
 }
 public function handleDriverException(Throwable $e) : void
 {
 $this->throw(Exception::driverException($this->_driver, $e));
 }
 private function throw(Exception $e) : void
 {
 if ($e instanceof ConnectionLost) {
 $this->close();
 }
 throw $e;
 }
 private function ensureHasKeyValue(ResultStatement $stmt) : void
 {
 $columnCount = $stmt->columnCount();
 if ($columnCount < 2) {
 throw NoKeyValue::fromColumnCount($columnCount);
 }
 }
}
