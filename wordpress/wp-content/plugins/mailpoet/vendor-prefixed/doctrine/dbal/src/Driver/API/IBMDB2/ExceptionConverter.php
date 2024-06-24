<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver\API\IBMDB2;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use MailPoetVendor\Doctrine\DBAL\Driver\Exception;
use MailPoetVendor\Doctrine\DBAL\Exception\ConnectionException;
use MailPoetVendor\Doctrine\DBAL\Exception\DriverException;
use MailPoetVendor\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use MailPoetVendor\Doctrine\DBAL\Exception\InvalidFieldNameException;
use MailPoetVendor\Doctrine\DBAL\Exception\NonUniqueFieldNameException;
use MailPoetVendor\Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use MailPoetVendor\Doctrine\DBAL\Exception\SyntaxErrorException;
use MailPoetVendor\Doctrine\DBAL\Exception\TableExistsException;
use MailPoetVendor\Doctrine\DBAL\Exception\TableNotFoundException;
use MailPoetVendor\Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use MailPoetVendor\Doctrine\DBAL\Query;
final class ExceptionConverter implements ExceptionConverterInterface
{
 public function convert(Exception $exception, ?Query $query) : DriverException
 {
 switch ($exception->getCode()) {
 case -104:
 return new SyntaxErrorException($exception, $query);
 case -203:
 return new NonUniqueFieldNameException($exception, $query);
 case -204:
 return new TableNotFoundException($exception, $query);
 case -206:
 return new InvalidFieldNameException($exception, $query);
 case -407:
 return new NotNullConstraintViolationException($exception, $query);
 case -530:
 case -531:
 case -532:
 case -20356:
 return new ForeignKeyConstraintViolationException($exception, $query);
 case -601:
 return new TableExistsException($exception, $query);
 case -803:
 return new UniqueConstraintViolationException($exception, $query);
 case -1336:
 case -30082:
 return new ConnectionException($exception, $query);
 }
 return new DriverException($exception, $query);
 }
}
