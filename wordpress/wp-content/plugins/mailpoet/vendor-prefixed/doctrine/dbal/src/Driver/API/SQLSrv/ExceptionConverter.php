<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver\API\SQLSrv;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use MailPoetVendor\Doctrine\DBAL\Driver\Exception;
use MailPoetVendor\Doctrine\DBAL\Exception\ConnectionException;
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
 case 102:
 return new SyntaxErrorException($exception, $query);
 case 207:
 return new InvalidFieldNameException($exception, $query);
 case 208:
 return new TableNotFoundException($exception, $query);
 case 209:
 return new NonUniqueFieldNameException($exception, $query);
 case 515:
 return new NotNullConstraintViolationException($exception, $query);
 case 547:
 case 4712:
 return new ForeignKeyConstraintViolationException($exception, $query);
 case 2601:
 case 2627:
 return new UniqueConstraintViolationException($exception, $query);
 case 2714:
 return new TableExistsException($exception, $query);
 case 3701:
 case 15151:
 return new DatabaseObjectNotFoundException($exception, $query);
 case 11001:
 case 18456:
 return new ConnectionException($exception, $query);
 }
 return new DriverException($exception, $query);
 }
}
