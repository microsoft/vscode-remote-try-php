<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Logging;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\Connection as ConnectionInterface;
use MailPoetVendor\Doctrine\DBAL\Driver\Middleware\AbstractConnectionMiddleware;
use MailPoetVendor\Doctrine\DBAL\Driver\Result;
use MailPoetVendor\Doctrine\DBAL\Driver\Statement as DriverStatement;
use MailPoetVendor\Psr\Log\LoggerInterface;
final class Connection extends AbstractConnectionMiddleware
{
 private LoggerInterface $logger;
 public function __construct(ConnectionInterface $connection, LoggerInterface $logger)
 {
 parent::__construct($connection);
 $this->logger = $logger;
 }
 public function __destruct()
 {
 $this->logger->info('Disconnecting');
 }
 public function prepare(string $sql) : DriverStatement
 {
 return new Statement(parent::prepare($sql), $this->logger, $sql);
 }
 public function query(string $sql) : Result
 {
 $this->logger->debug('Executing query: {sql}', ['sql' => $sql]);
 return parent::query($sql);
 }
 public function exec(string $sql) : int
 {
 $this->logger->debug('Executing statement: {sql}', ['sql' => $sql]);
 return parent::exec($sql);
 }
 public function beginTransaction()
 {
 $this->logger->debug('Beginning transaction');
 return parent::beginTransaction();
 }
 public function commit()
 {
 $this->logger->debug('Committing transaction');
 return parent::commit();
 }
 public function rollBack()
 {
 $this->logger->debug('Rolling back transaction');
 return parent::rollBack();
 }
}
