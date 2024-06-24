<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver\API\SQLite;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use MailPoetVendor\Doctrine\DBAL\Driver\Exception;
use MailPoetVendor\Doctrine\DBAL\Exception\ConnectionException;
use MailPoetVendor\Doctrine\DBAL\Exception\DriverException;
use MailPoetVendor\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use MailPoetVendor\Doctrine\DBAL\Exception\InvalidFieldNameException;
use MailPoetVendor\Doctrine\DBAL\Exception\LockWaitTimeoutException;
use MailPoetVendor\Doctrine\DBAL\Exception\NonUniqueFieldNameException;
use MailPoetVendor\Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use MailPoetVendor\Doctrine\DBAL\Exception\ReadOnlyException;
use MailPoetVendor\Doctrine\DBAL\Exception\SyntaxErrorException;
use MailPoetVendor\Doctrine\DBAL\Exception\TableExistsException;
use MailPoetVendor\Doctrine\DBAL\Exception\TableNotFoundException;
use MailPoetVendor\Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use MailPoetVendor\Doctrine\DBAL\Query;
use function strpos;
final class ExceptionConverter implements ExceptionConverterInterface
{
 public function convert(Exception $exception, ?Query $query) : DriverException
 {
 if (strpos($exception->getMessage(), 'database is locked') !== \false) {
 return new LockWaitTimeoutException($exception, $query);
 }
 if (strpos($exception->getMessage(), 'must be unique') !== \false || strpos($exception->getMessage(), 'is not unique') !== \false || strpos($exception->getMessage(), 'are not unique') !== \false || strpos($exception->getMessage(), 'UNIQUE constraint failed') !== \false) {
 return new UniqueConstraintViolationException($exception, $query);
 }
 if (strpos($exception->getMessage(), 'may not be NULL') !== \false || strpos($exception->getMessage(), 'NOT NULL constraint failed') !== \false) {
 return new NotNullConstraintViolationException($exception, $query);
 }
 if (strpos($exception->getMessage(), 'no such table:') !== \false) {
 return new TableNotFoundException($exception, $query);
 }
 if (strpos($exception->getMessage(), 'already exists') !== \false) {
 return new TableExistsException($exception, $query);
 }
 if (strpos($exception->getMessage(), 'has no column named') !== \false) {
 return new InvalidFieldNameException($exception, $query);
 }
 if (strpos($exception->getMessage(), 'ambiguous column name') !== \false) {
 return new NonUniqueFieldNameException($exception, $query);
 }
 if (strpos($exception->getMessage(), 'syntax error') !== \false) {
 return new SyntaxErrorException($exception, $query);
 }
 if (strpos($exception->getMessage(), 'attempt to write a readonly database') !== \false) {
 return new ReadOnlyException($exception, $query);
 }
 if (strpos($exception->getMessage(), 'unable to open database file') !== \false) {
 return new ConnectionException($exception, $query);
 }
 if (strpos($exception->getMessage(), 'FOREIGN KEY constraint failed') !== \false) {
 return new ForeignKeyConstraintViolationException($exception, $query);
 }
 return new DriverException($exception, $query);
 }
}
