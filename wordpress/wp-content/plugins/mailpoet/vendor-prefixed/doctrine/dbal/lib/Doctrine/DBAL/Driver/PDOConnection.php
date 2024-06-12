<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\Connection as ConnectionInterface;
use MailPoetVendor\Doctrine\DBAL\Driver\PDO\Exception;
use MailPoetVendor\Doctrine\DBAL\Driver\PDO\Statement;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use PDO;
use PDOException;
use PDOStatement;
use ReturnTypeWillChange;
use function assert;
class PDOConnection extends PDO implements ConnectionInterface, ServerInfoAwareConnection
{
 use PDOQueryImplementation;
 public function __construct($dsn, $user = null, $password = null, ?array $options = null)
 {
 try {
 parent::__construct($dsn, (string) $user, (string) $password, (array) $options);
 $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, [Statement::class, []]);
 $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 } catch (PDOException $exception) {
 throw Exception::new($exception);
 }
 }
 #[\ReturnTypeWillChange]
 public function exec($sql)
 {
 try {
 $result = parent::exec($sql);
 assert($result !== \false);
 return $result;
 } catch (PDOException $exception) {
 throw Exception::new($exception);
 }
 }
 public function getServerVersion()
 {
 return PDO::getAttribute(PDO::ATTR_SERVER_VERSION);
 }
 #[\ReturnTypeWillChange]
 public function prepare($sql, $driverOptions = [])
 {
 try {
 $statement = parent::prepare($sql, $driverOptions);
 assert($statement instanceof PDOStatement);
 return $statement;
 } catch (PDOException $exception) {
 throw Exception::new($exception);
 }
 }
 #[\ReturnTypeWillChange]
 public function quote($value, $type = ParameterType::STRING)
 {
 return parent::quote($value, $type);
 }
 #[\ReturnTypeWillChange]
 public function lastInsertId($name = null)
 {
 try {
 if ($name === null) {
 return parent::lastInsertId();
 }
 return parent::lastInsertId($name);
 } catch (PDOException $exception) {
 throw Exception::new($exception);
 }
 }
 public function requiresQueryForServerVersion()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4114', 'ServerInfoAwareConnection::requiresQueryForServerVersion() is deprecated and removed in DBAL 3.');
 return \false;
 }
 private function doQuery(...$args) : PDOStatement
 {
 try {
 $stmt = parent::query(...$args);
 } catch (PDOException $exception) {
 throw Exception::new($exception);
 }
 assert($stmt instanceof PDOStatement);
 return $stmt;
 }
}
