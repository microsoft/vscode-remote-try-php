<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver\PDO\MySQL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\AbstractMySQLDriver;
use MailPoetVendor\Doctrine\DBAL\Driver\PDO\Connection;
use MailPoetVendor\Doctrine\DBAL\Driver\PDO\Exception;
use PDO;
use PDOException;
use MailPoetVendor\SensitiveParameter;
final class Driver extends AbstractMySQLDriver
{
 public function connect( array $params)
 {
 $driverOptions = $params['driverOptions'] ?? [];
 if (!empty($params['persistent'])) {
 $driverOptions[PDO::ATTR_PERSISTENT] = \true;
 }
 $safeParams = $params;
 unset($safeParams['password'], $safeParams['url']);
 try {
 $pdo = new PDO($this->constructPdoDsn($safeParams), $params['user'] ?? '', $params['password'] ?? '', $driverOptions);
 } catch (PDOException $exception) {
 throw Exception::new($exception);
 }
 return new Connection($pdo);
 }
 private function constructPdoDsn(array $params) : string
 {
 $dsn = 'mysql:';
 if (isset($params['host']) && $params['host'] !== '') {
 $dsn .= 'host=' . $params['host'] . ';';
 }
 if (isset($params['port'])) {
 $dsn .= 'port=' . $params['port'] . ';';
 }
 if (isset($params['dbname'])) {
 $dsn .= 'dbname=' . $params['dbname'] . ';';
 }
 if (isset($params['unix_socket'])) {
 $dsn .= 'unix_socket=' . $params['unix_socket'] . ';';
 }
 if (isset($params['charset'])) {
 $dsn .= 'charset=' . $params['charset'] . ';';
 }
 return $dsn;
 }
}
