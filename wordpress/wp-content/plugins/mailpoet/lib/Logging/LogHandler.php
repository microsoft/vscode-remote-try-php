<?php declare(strict_types = 1);

namespace MailPoet\Logging;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\LogEntity;
use MailPoetVendor\Monolog\Handler\AbstractProcessingHandler;

class LogHandler extends AbstractProcessingHandler {
  /**
   * Logs older than this many days will be deleted
   */
  const DAYS_TO_KEEP_LOGS = 30;

  /**
   * How many records to delete on one run of purge routine
   */
  const PURGE_LIMIT = 1000;

  /**
   * Percentage value, what is the probability of running purge routine
   * @var int
   */
  const LOG_PURGE_PROBABILITY = 5;

  /** @var callable|null */
  private $randFunction;

  /** @var LogRepository */
  private $logRepository;

  public function __construct(
    LogRepository $logRepository,
    $level = \MailPoetVendor\Monolog\Logger::DEBUG,
    $bubble = \true,
    $randFunction = null
  ) {
    parent::__construct($level, $bubble);
    $this->randFunction = $randFunction;
    $this->logRepository = $logRepository;
  }

  protected function write(array $record): void {
    $message = is_string($record['formatted']) ? $record['formatted'] : null;
    $entity = new LogEntity();
    $entity->setName($record['channel']);
    $entity->setLevel((int)$record['level']);
    $entity->setMessage($message);
    $entity->setCreatedAt($record['datetime']);
    $entity->setRawMessage($record['message']);
    $entity->setContext($record['context']);
    $this->logRepository->saveLog($entity);

    if ($this->getRandom() <= self::LOG_PURGE_PROBABILITY) {
      $this->purgeOldLogs();
    }
  }

  private function getRandom() {
    if ($this->randFunction) {
      return call_user_func($this->randFunction, 0, 100);
    }
    return rand(0, 100);
  }

  private function purgeOldLogs() {
    $this->logRepository->purgeOldLogs(self::DAYS_TO_KEEP_LOGS, self::PURGE_LIMIT);
  }
}
