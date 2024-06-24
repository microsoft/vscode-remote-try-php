<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver\API\MySQL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use MailPoetVendor\Doctrine\DBAL\Driver\Exception;
use MailPoetVendor\Doctrine\DBAL\Exception\ConnectionException;
use MailPoetVendor\Doctrine\DBAL\Exception\ConnectionLost;
use MailPoetVendor\Doctrine\DBAL\Exception\DatabaseDoesNotExist;
use MailPoetVendor\Doctrine\DBAL\Exception\DeadlockException;
use MailPoetVendor\Doctrine\DBAL\Exception\DriverException;
use MailPoetVendor\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use MailPoetVendor\Doctrine\DBAL\Exception\InvalidFieldNameException;
use MailPoetVendor\Doctrine\DBAL\Exception\LockWaitTimeoutException;
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
 case 1008:
 return new DatabaseDoesNotExist($exception, $query);
 case 1213:
 return new DeadlockException($exception, $query);
 case 1205:
 return new LockWaitTimeoutException($exception, $query);
 case 1050:
 return new TableExistsException($exception, $query);
 case 1051:
 case 1146:
 return new TableNotFoundException($exception, $query);
 case 1216:
 case 1217:
 case 1451:
 case 1452:
 case 1701:
 return new ForeignKeyConstraintViolationException($exception, $query);
 case 1062:
 case 1557:
 case 1569:
 case 1586:
 return new UniqueConstraintViolationException($exception, $query);
 case 1054:
 case 1166:
 case 1611:
 return new InvalidFieldNameException($exception, $query);
 case 1052:
 case 1060:
 case 1110:
 return new NonUniqueFieldNameException($exception, $query);
 case 1064:
 case 1149:
 case 1287:
 case 1341:
 case 1342:
 case 1343:
 case 1344:
 case 1382:
 case 1479:
 case 1541:
 case 1554:
 case 1626:
 return new SyntaxErrorException($exception, $query);
 case 1044:
 case 1045:
 case 1046:
 case 1049:
 case 1095:
 case 1142:
 case 1143:
 case 1227:
 case 1370:
 case 1429:
 case 2002:
 case 2005:
 case 2054:
 return new ConnectionException($exception, $query);
 case 2006:
 return new ConnectionLost($exception, $query);
 case 1048:
 case 1121:
 case 1138:
 case 1171:
 case 1252:
 case 1263:
 case 1364:
 case 1566:
 return new NotNullConstraintViolationException($exception, $query);
 }
 return new DriverException($exception, $query);
 }
}
