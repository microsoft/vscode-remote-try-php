<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver\AbstractSQLiteDriver\Middleware;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver;
use MailPoetVendor\Doctrine\DBAL\Driver\Connection;
use MailPoetVendor\Doctrine\DBAL\Driver\Middleware;
use MailPoetVendor\Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;
use MailPoetVendor\SensitiveParameter;
class EnableForeignKeys implements Middleware
{
 public function wrap(Driver $driver) : Driver
 {
 return new class($driver) extends AbstractDriverMiddleware
 {
 public function connect( array $params) : Connection
 {
 $connection = parent::connect($params);
 $connection->exec('PRAGMA foreign_keys=ON');
 return $connection;
 }
 };
 }
}
