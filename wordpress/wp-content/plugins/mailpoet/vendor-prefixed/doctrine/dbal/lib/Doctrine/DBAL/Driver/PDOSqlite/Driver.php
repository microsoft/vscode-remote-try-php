<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver\PDOSqlite;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\AbstractSQLiteDriver;
use MailPoetVendor\Doctrine\DBAL\Driver\PDO;
use MailPoetVendor\Doctrine\DBAL\Exception;
use MailPoetVendor\Doctrine\DBAL\Platforms\SqlitePlatform;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use PDOException;
use function array_merge;
class Driver extends AbstractSQLiteDriver
{
 protected $_userDefinedFunctions = ['sqrt' => ['callback' => [SqlitePlatform::class, 'udfSqrt'], 'numArgs' => 1], 'mod' => ['callback' => [SqlitePlatform::class, 'udfMod'], 'numArgs' => 2], 'locate' => ['callback' => [SqlitePlatform::class, 'udfLocate'], 'numArgs' => -1]];
 public function connect(array $params, $username = null, $password = null, array $driverOptions = [])
 {
 if (isset($driverOptions['userDefinedFunctions'])) {
 $this->_userDefinedFunctions = array_merge($this->_userDefinedFunctions, $driverOptions['userDefinedFunctions']);
 unset($driverOptions['userDefinedFunctions']);
 }
 try {
 $pdo = new PDO\Connection($this->_constructPdoDsn($params), $username, $password, $driverOptions);
 } catch (PDOException $ex) {
 throw Exception::driverException($this, $ex);
 }
 foreach ($this->_userDefinedFunctions as $fn => $data) {
 $pdo->sqliteCreateFunction($fn, $data['callback'], $data['numArgs']);
 }
 return $pdo;
 }
 protected function _constructPdoDsn(array $params)
 {
 $dsn = 'sqlite:';
 if (isset($params['path'])) {
 $dsn .= $params['path'];
 } elseif (isset($params['memory'])) {
 $dsn .= ':memory:';
 }
 return $dsn;
 }
 public function getName()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3580', 'Driver::getName() is deprecated');
 return 'pdo_sqlite';
 }
}
