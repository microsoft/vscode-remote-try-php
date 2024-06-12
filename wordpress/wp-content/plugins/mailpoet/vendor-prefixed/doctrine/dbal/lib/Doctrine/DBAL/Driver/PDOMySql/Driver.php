<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver\PDOMySql;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\AbstractMySQLDriver;
use MailPoetVendor\Doctrine\DBAL\Driver\PDO;
use MailPoetVendor\Doctrine\DBAL\Exception;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use PDOException;
class Driver extends AbstractMySQLDriver
{
 public function connect(array $params, $username = null, $password = null, array $driverOptions = [])
 {
 try {
 $conn = new PDO\Connection($this->constructPdoDsn($params), $username, $password, $driverOptions);
 } catch (PDOException $e) {
 throw Exception::driverException($this, $e);
 }
 return $conn;
 }
 protected function constructPdoDsn(array $params)
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
 public function getName()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3580', 'Driver::getName() is deprecated');
 return 'pdo_mysql';
 }
}
