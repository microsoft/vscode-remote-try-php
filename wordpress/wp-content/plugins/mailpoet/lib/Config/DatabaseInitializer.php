<?php declare(strict_types = 1);

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Doctrine\DBAL\Connection;

class DatabaseInitializer {
  /** @var Connection */
  private $connection;

  public function __construct(
    Connection $connection
  ) {
    $this->connection = $connection;
  }

  public function initializeConnection() {
    // pass the same PDO connection to legacy Database object
    $database = new Database();
    $database->init($this->connection->getWrappedConnection());
  }
}
