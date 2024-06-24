<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver\PgSQL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\ServerInfoAwareConnection;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
use MailPoetVendor\Doctrine\DBAL\SQL\Parser;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\PgSql\Connection as PgSqlConnection;
use TypeError;
use function assert;
use function get_class;
use function gettype;
use function is_object;
use function is_resource;
use function pg_close;
use function pg_escape_bytea;
use function pg_escape_literal;
use function pg_get_result;
use function pg_last_error;
use function pg_result_error;
use function pg_send_prepare;
use function pg_send_query;
use function pg_version;
use function sprintf;
use function uniqid;
final class Connection implements ServerInfoAwareConnection
{
 private $connection;
 private Parser $parser;
 public function __construct($connection)
 {
 if (!is_resource($connection) && !$connection instanceof PgSqlConnection) {
 throw new TypeError(sprintf('Expected connection to be a resource or an instance of %s, got %s.', PgSqlConnection::class, is_object($connection) ? get_class($connection) : gettype($connection)));
 }
 $this->connection = $connection;
 $this->parser = new Parser(\false);
 }
 public function __destruct()
 {
 if (!isset($this->connection)) {
 return;
 }
 @pg_close($this->connection);
 }
 public function prepare(string $sql) : Statement
 {
 $visitor = new ConvertParameters();
 $this->parser->parse($sql, $visitor);
 $statementName = uniqid('dbal', \true);
 if (@pg_send_prepare($this->connection, $statementName, $visitor->getSQL()) !== \true) {
 throw new Exception(pg_last_error($this->connection));
 }
 $result = @pg_get_result($this->connection);
 assert($result !== \false);
 if ((bool) pg_result_error($result)) {
 throw Exception::fromResult($result);
 }
 return new Statement($this->connection, $statementName, $visitor->getParameterMap());
 }
 public function query(string $sql) : Result
 {
 if (@pg_send_query($this->connection, $sql) !== \true) {
 throw new Exception(pg_last_error($this->connection));
 }
 $result = @pg_get_result($this->connection);
 assert($result !== \false);
 if ((bool) pg_result_error($result)) {
 throw Exception::fromResult($result);
 }
 return new Result($result);
 }
 public function quote($value, $type = ParameterType::STRING)
 {
 if ($type === ParameterType::BINARY || $type === ParameterType::LARGE_OBJECT) {
 return sprintf("'%s'", pg_escape_bytea($this->connection, $value));
 }
 return pg_escape_literal($this->connection, $value);
 }
 public function exec(string $sql) : int
 {
 return $this->query($sql)->rowCount();
 }
 public function lastInsertId($name = null)
 {
 if ($name !== null) {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4687', 'The usage of Connection::lastInsertId() with a sequence name is deprecated.');
 return $this->query(sprintf('SELECT CURRVAL(%s)', $this->quote($name)))->fetchOne();
 }
 return $this->query('SELECT LASTVAL()')->fetchOne();
 }
 public function beginTransaction() : bool
 {
 $this->exec('BEGIN');
 return \true;
 }
 public function commit() : bool
 {
 $this->exec('COMMIT');
 return \true;
 }
 public function rollBack() : bool
 {
 $this->exec('ROLLBACK');
 return \true;
 }
 public function getServerVersion() : string
 {
 return (string) pg_version($this->connection)['server'];
 }
 public function getNativeConnection()
 {
 return $this->connection;
 }
}
