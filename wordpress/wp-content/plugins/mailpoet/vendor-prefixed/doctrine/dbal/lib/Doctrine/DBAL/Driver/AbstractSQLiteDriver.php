<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Driver;
use MailPoetVendor\Doctrine\DBAL\Driver\DriverException as DeprecatedDriverException;
use MailPoetVendor\Doctrine\DBAL\Exception\ConnectionException;
use MailPoetVendor\Doctrine\DBAL\Exception\DriverException;
use MailPoetVendor\Doctrine\DBAL\Exception\InvalidFieldNameException;
use MailPoetVendor\Doctrine\DBAL\Exception\LockWaitTimeoutException;
use MailPoetVendor\Doctrine\DBAL\Exception\NonUniqueFieldNameException;
use MailPoetVendor\Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use MailPoetVendor\Doctrine\DBAL\Exception\ReadOnlyException;
use MailPoetVendor\Doctrine\DBAL\Exception\SyntaxErrorException;
use MailPoetVendor\Doctrine\DBAL\Exception\TableExistsException;
use MailPoetVendor\Doctrine\DBAL\Exception\TableNotFoundException;
use MailPoetVendor\Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use MailPoetVendor\Doctrine\DBAL\Platforms\SqlitePlatform;
use MailPoetVendor\Doctrine\DBAL\Schema\SqliteSchemaManager;
use function strpos;
abstract class AbstractSQLiteDriver implements Driver, ExceptionConverterDriver
{
 public function convertException($message, DeprecatedDriverException $exception)
 {
 if (strpos($exception->getMessage(), 'database is locked') !== \false) {
 return new LockWaitTimeoutException($message, $exception);
 }
 if (strpos($exception->getMessage(), 'must be unique') !== \false || strpos($exception->getMessage(), 'is not unique') !== \false || strpos($exception->getMessage(), 'are not unique') !== \false || strpos($exception->getMessage(), 'UNIQUE constraint failed') !== \false) {
 return new UniqueConstraintViolationException($message, $exception);
 }
 if (strpos($exception->getMessage(), 'may not be NULL') !== \false || strpos($exception->getMessage(), 'NOT NULL constraint failed') !== \false) {
 return new NotNullConstraintViolationException($message, $exception);
 }
 if (strpos($exception->getMessage(), 'no such table:') !== \false) {
 return new TableNotFoundException($message, $exception);
 }
 if (strpos($exception->getMessage(), 'already exists') !== \false) {
 return new TableExistsException($message, $exception);
 }
 if (strpos($exception->getMessage(), 'has no column named') !== \false) {
 return new InvalidFieldNameException($message, $exception);
 }
 if (strpos($exception->getMessage(), 'ambiguous column name') !== \false) {
 return new NonUniqueFieldNameException($message, $exception);
 }
 if (strpos($exception->getMessage(), 'syntax error') !== \false) {
 return new SyntaxErrorException($message, $exception);
 }
 if (strpos($exception->getMessage(), 'attempt to write a readonly database') !== \false) {
 return new ReadOnlyException($message, $exception);
 }
 if (strpos($exception->getMessage(), 'unable to open database file') !== \false) {
 return new ConnectionException($message, $exception);
 }
 return new DriverException($message, $exception);
 }
 public function getDatabase(Connection $conn)
 {
 $params = $conn->getParams();
 return $params['path'] ?? null;
 }
 public function getDatabasePlatform()
 {
 return new SqlitePlatform();
 }
 public function getSchemaManager(Connection $conn)
 {
 return new SqliteSchemaManager($conn);
 }
}
