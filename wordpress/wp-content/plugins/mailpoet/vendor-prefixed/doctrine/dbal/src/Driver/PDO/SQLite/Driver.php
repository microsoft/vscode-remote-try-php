<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver\PDO\SQLite;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\AbstractSQLiteDriver;
use MailPoetVendor\Doctrine\DBAL\Driver\API\SQLite\UserDefinedFunctions;
use MailPoetVendor\Doctrine\DBAL\Driver\PDO\Connection;
use MailPoetVendor\Doctrine\DBAL\Driver\PDO\Exception;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use PDO;
use PDOException;
use MailPoetVendor\SensitiveParameter;
use function array_intersect_key;
final class Driver extends AbstractSQLiteDriver
{
 public function connect( array $params)
 {
 $driverOptions = $params['driverOptions'] ?? [];
 $userDefinedFunctions = [];
 if (isset($driverOptions['userDefinedFunctions'])) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5742', 'The SQLite-specific driver option "userDefinedFunctions" is deprecated.' . ' Register function directly on the native connection instead.');
 $userDefinedFunctions = $driverOptions['userDefinedFunctions'];
 unset($driverOptions['userDefinedFunctions']);
 }
 try {
 $pdo = new PDO($this->constructPdoDsn(array_intersect_key($params, ['path' => \true, 'memory' => \true])), $params['user'] ?? '', $params['password'] ?? '', $driverOptions);
 } catch (PDOException $exception) {
 throw Exception::new($exception);
 }
 UserDefinedFunctions::register([$pdo, 'sqliteCreateFunction'], $userDefinedFunctions);
 return new Connection($pdo);
 }
 private function constructPdoDsn(array $params) : string
 {
 $dsn = 'sqlite:';
 if (isset($params['path'])) {
 $dsn .= $params['path'];
 } elseif (isset($params['memory'])) {
 $dsn .= ':memory:';
 }
 return $dsn;
 }
}
