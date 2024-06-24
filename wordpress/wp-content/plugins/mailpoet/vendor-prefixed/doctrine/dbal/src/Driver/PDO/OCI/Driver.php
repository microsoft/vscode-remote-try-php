<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver\PDO\OCI;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\AbstractOracleDriver;
use MailPoetVendor\Doctrine\DBAL\Driver\PDO\Connection;
use MailPoetVendor\Doctrine\DBAL\Driver\PDO\Exception;
use PDO;
use PDOException;
use MailPoetVendor\SensitiveParameter;
final class Driver extends AbstractOracleDriver
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
 $pdo = new PDO($this->constructPdoDsn($params), $params['user'] ?? '', $params['password'] ?? '', $driverOptions);
 } catch (PDOException $exception) {
 throw Exception::new($exception);
 }
 return new Connection($pdo);
 }
 private function constructPdoDsn(array $params) : string
 {
 $dsn = 'oci:dbname=' . $this->getEasyConnectString($params);
 if (isset($params['charset'])) {
 $dsn .= ';charset=' . $params['charset'];
 }
 return $dsn;
 }
}
