<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine;

if (!defined('ABSPATH')) exit;


use MailPoet\Util\Helpers;
use MailPoetVendor\Doctrine\Common\EventManager;
use MailPoetVendor\Doctrine\DBAL\Configuration;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Driver;
use Throwable;

class SerializableConnection extends Connection {
  private $params;
  private $driver;
  private $config;
  private $eventManager;

  public function __construct(
    array $params,
    Driver $driver,
    Configuration $config = null,
    EventManager $eventManager = null
  ) {
    $this->params = $params;
    $this->driver = $driver;
    $this->config = $config;
    $this->eventManager = $eventManager;
    parent::__construct($params, $driver, $config, $eventManager);
  }

  public function __sleep() {
    return ['params', 'driver', 'config', 'eventManager'];
  }

  public function __wakeup() {
    parent::__construct($this->params, $this->driver, $this->config, $this->eventManager);
  }

  public function rollBack() {
    try {
      return parent::rollBack();
    } catch (Throwable $e) {
      $mySqlGoneAwayMessage = Helpers::mySqlGoneAwayExceptionHandler($e);
      if ($mySqlGoneAwayMessage) {
        throw new \Exception($mySqlGoneAwayMessage, (int)$e->getCode(), $e);
      }
      throw $e;
    }
  }

  public function handleExceptionDuringQuery(Throwable $e, string $sql, array $params = [], array $types = []): void {
    try {
      parent::handleExceptionDuringQuery($e, $sql, $params, $types);
    } catch (Throwable $err) {
      $mySqlGoneAwayMessage = Helpers::mySqlGoneAwayExceptionHandler($err);
      if ($mySqlGoneAwayMessage) {
        throw new \Exception($mySqlGoneAwayMessage, (int)$err->getCode(), $err);
      }
      throw $err;
    }
  }
}
