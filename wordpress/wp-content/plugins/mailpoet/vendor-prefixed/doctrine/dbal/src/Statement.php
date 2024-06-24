<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\DBAL\Types\Type;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function func_num_args;
use function is_string;
class Statement
{
 protected $sql;
 protected $params = [];
 protected $types = [];
 protected $stmt;
 protected $platform;
 protected $conn;
 public function __construct(Connection $conn, Driver\Statement $statement, string $sql)
 {
 $this->conn = $conn;
 $this->stmt = $statement;
 $this->sql = $sql;
 $this->platform = $conn->getDatabasePlatform();
 }
 public function bindValue($param, $value, $type = ParameterType::STRING)
 {
 $this->params[$param] = $value;
 $this->types[$param] = $type;
 $bindingType = ParameterType::STRING;
 if ($type !== null) {
 if (is_string($type)) {
 $type = Type::getType($type);
 }
 $bindingType = $type;
 if ($type instanceof Type) {
 $value = $type->convertToDatabaseValue($value, $this->platform);
 $bindingType = $type->getBindingType();
 }
 }
 try {
 return $this->stmt->bindValue($param, $value, $bindingType);
 } catch (Driver\Exception $e) {
 throw $this->conn->convertException($e);
 }
 }
 public function bindParam($param, &$variable, $type = ParameterType::STRING, $length = null)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5563', '%s is deprecated. Use bindValue() instead.', __METHOD__);
 $this->params[$param] = $variable;
 $this->types[$param] = $type;
 try {
 if (func_num_args() > 3) {
 return $this->stmt->bindParam($param, $variable, $type, $length);
 }
 return $this->stmt->bindParam($param, $variable, $type);
 } catch (Driver\Exception $e) {
 throw $this->conn->convertException($e);
 }
 }
 public function execute($params = null) : Result
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4580', '%s() is deprecated, use Statement::executeQuery() or Statement::executeStatement() instead', __METHOD__);
 if ($params !== null) {
 $this->params = $params;
 }
 $logger = $this->conn->getConfiguration()->getSQLLogger();
 if ($logger !== null) {
 $logger->startQuery($this->sql, $this->params, $this->types);
 }
 try {
 return new Result($this->stmt->execute($params), $this->conn);
 } catch (Driver\Exception $ex) {
 throw $this->conn->convertExceptionDuringQuery($ex, $this->sql, $this->params, $this->types);
 } finally {
 if ($logger !== null) {
 $logger->stopQuery();
 }
 }
 }
 public function executeQuery(array $params = []) : Result
 {
 if (func_num_args() > 0) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5556', 'Passing $params to Statement::executeQuery() is deprecated. Bind parameters using' . ' Statement::bindParam() or Statement::bindValue() instead.');
 }
 if ($params === []) {
 $params = null;
 // Workaround as long execute() exists and used internally.
 }
 return $this->execute($params);
 }
 public function executeStatement(array $params = []) : int
 {
 if (func_num_args() > 0) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5556', 'Passing $params to Statement::executeStatement() is deprecated. Bind parameters using' . ' Statement::bindParam() or Statement::bindValue() instead.');
 }
 if ($params === []) {
 $params = null;
 // Workaround as long execute() exists and used internally.
 }
 return $this->execute($params)->rowCount();
 }
 public function getWrappedStatement()
 {
 return $this->stmt;
 }
}
