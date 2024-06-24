<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Event;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\EventArgs;
use MailPoetVendor\Doctrine\DBAL\Connection;
abstract class TransactionEventArgs extends EventArgs
{
 private Connection $connection;
 public function __construct(Connection $connection)
 {
 $this->connection = $connection;
 }
 public function getConnection() : Connection
 {
 return $this->connection;
 }
}
