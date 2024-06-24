<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver\API\PostgreSQL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use MailPoetVendor\Doctrine\DBAL\Driver\Exception;
use MailPoetVendor\Doctrine\DBAL\Exception\ConnectionException;
use MailPoetVendor\Doctrine\DBAL\Exception\DatabaseDoesNotExist;
use MailPoetVendor\Doctrine\DBAL\Exception\DeadlockException;
use MailPoetVendor\Doctrine\DBAL\Exception\DriverException;
use MailPoetVendor\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use MailPoetVendor\Doctrine\DBAL\Exception\InvalidFieldNameException;
use MailPoetVendor\Doctrine\DBAL\Exception\NonUniqueFieldNameException;
use MailPoetVendor\Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use MailPoetVendor\Doctrine\DBAL\Exception\SchemaDoesNotExist;
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
 switch ($exception->getSQLState()) {
 case '40001':
 case '40P01':
 return new DeadlockException($exception, $query);
 case '0A000':
 // Foreign key constraint violations during a TRUNCATE operation
 // are considered "feature not supported" in PostgreSQL.
 if (strpos($exception->getMessage(), 'truncate') !== \false) {
 return new ForeignKeyConstraintViolationException($exception, $query);
 }
 break;
 case '23502':
 return new NotNullConstraintViolationException($exception, $query);
 case '23503':
 return new ForeignKeyConstraintViolationException($exception, $query);
 case '23505':
 return new UniqueConstraintViolationException($exception, $query);
 case '3D000':
 return new DatabaseDoesNotExist($exception, $query);
 case '3F000':
 return new SchemaDoesNotExist($exception, $query);
 case '42601':
 return new SyntaxErrorException($exception, $query);
 case '42702':
 return new NonUniqueFieldNameException($exception, $query);
 case '42703':
 return new InvalidFieldNameException($exception, $query);
 case '42P01':
 return new TableNotFoundException($exception, $query);
 case '42P07':
 return new TableExistsException($exception, $query);
 case '08006':
 return new ConnectionException($exception, $query);
 }
 // Prior to fixing https://bugs.php.net/bug.php?id=64705 (PHP 7.4.10),
 // in some cases (mainly connection errors) the PDO exception wouldn't provide a SQLSTATE via its code.
 // We have to match against the SQLSTATE in the error message in these cases.
 if ($exception->getCode() === 7 && strpos($exception->getMessage(), 'SQLSTATE[08006]') !== \false) {
 return new ConnectionException($exception, $query);
 }
 return new DriverException($exception, $query);
 }
}
