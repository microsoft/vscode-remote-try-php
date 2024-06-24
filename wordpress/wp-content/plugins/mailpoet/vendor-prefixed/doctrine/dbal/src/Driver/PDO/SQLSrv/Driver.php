<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver\PDO\SQLSrv;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\AbstractSQLServerDriver;
use MailPoetVendor\Doctrine\DBAL\Driver\AbstractSQLServerDriver\Exception\PortWithoutHost;
use MailPoetVendor\Doctrine\DBAL\Driver\Exception;
use MailPoetVendor\Doctrine\DBAL\Driver\PDO\Connection as PDOConnection;
use MailPoetVendor\Doctrine\DBAL\Driver\PDO\Exception as PDOException;
use PDO;
use MailPoetVendor\SensitiveParameter;
use function is_int;
use function sprintf;
final class Driver extends AbstractSQLServerDriver
{
 public function connect( array $params)
 {
 $driverOptions = $dsnOptions = [];
 if (isset($params['driverOptions'])) {
 foreach ($params['driverOptions'] as $option => $value) {
 if (is_int($option)) {
 $driverOptions[$option] = $value;
 } else {
 $dsnOptions[$option] = $value;
 }
 }
 }
 if (!empty($params['persistent'])) {
 $driverOptions[PDO::ATTR_PERSISTENT] = \true;
 }
 $safeParams = $params;
 unset($safeParams['password'], $safeParams['url']);
 try {
 $pdo = new PDO($this->constructDsn($safeParams, $dsnOptions), $params['user'] ?? '', $params['password'] ?? '', $driverOptions);
 } catch (\PDOException $exception) {
 throw PDOException::new($exception);
 }
 return new Connection(new PDOConnection($pdo));
 }
 private function constructDsn(array $params, array $connectionOptions) : string
 {
 $dsn = 'sqlsrv:server=';
 if (isset($params['host'])) {
 $dsn .= $params['host'];
 if (isset($params['port'])) {
 $dsn .= ',' . $params['port'];
 }
 } elseif (isset($params['port'])) {
 throw PortWithoutHost::new();
 }
 if (isset($params['dbname'])) {
 $connectionOptions['Database'] = $params['dbname'];
 }
 if (isset($params['MultipleActiveResultSets'])) {
 $connectionOptions['MultipleActiveResultSets'] = $params['MultipleActiveResultSets'] ? 'true' : 'false';
 }
 return $dsn . $this->getConnectionOptionsDsn($connectionOptions);
 }
 private function getConnectionOptionsDsn(array $connectionOptions) : string
 {
 $connectionOptionsDsn = '';
 foreach ($connectionOptions as $paramName => $paramValue) {
 $connectionOptionsDsn .= sprintf(';%s=%s', $paramName, $paramValue);
 }
 return $connectionOptionsDsn;
 }
}
