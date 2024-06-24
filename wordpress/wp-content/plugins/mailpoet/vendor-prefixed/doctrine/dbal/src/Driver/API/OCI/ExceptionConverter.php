<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver\API\OCI;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use MailPoetVendor\Doctrine\DBAL\Driver\Exception;
use MailPoetVendor\Doctrine\DBAL\Exception\ConnectionException;
use MailPoetVendor\Doctrine\DBAL\Exception\DatabaseDoesNotExist;
use MailPoetVendor\Doctrine\DBAL\Exception\DatabaseObjectNotFoundException;
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
 case 1:
 case 2299:
 case 38911:
 return new UniqueConstraintViolationException($exception, $query);
 case 904:
 return new InvalidFieldNameException($exception, $query);
 case 918:
 case 960:
 return new NonUniqueFieldNameException($exception, $query);
 case 923:
 return new SyntaxErrorException($exception, $query);
 case 942:
 return new TableNotFoundException($exception, $query);
 case 955:
 return new TableExistsException($exception, $query);
 case 1017:
 case 12545:
 return new ConnectionException($exception, $query);
 case 1400:
 return new NotNullConstraintViolationException($exception, $query);
 case 1918:
 return new DatabaseDoesNotExist($exception, $query);
 case 2289:
 case 2443:
 case 4080:
 return new DatabaseObjectNotFoundException($exception, $query);
 case 2266:
 case 2291:
 case 2292:
 return new ForeignKeyConstraintViolationException($exception, $query);
 }
 return new DriverException($exception, $query);
 }
}
