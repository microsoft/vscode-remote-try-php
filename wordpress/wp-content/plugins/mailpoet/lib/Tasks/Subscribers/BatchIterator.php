<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Tasks\Subscribers;

if (!defined('ABSPATH')) exit;


use MailPoet\DI\ContainerWrapper;
use MailPoet\Newsletter\Sending\ScheduledTaskSubscribersRepository;

/**
 * @implements \Iterator<null, array>
 */
class BatchIterator implements \Iterator, \Countable {
  private $taskId;
  private $batchSize;
  private $lastProcessedId = 0;
  private $batchLastId;

  /** @var ScheduledTaskSubscribersRepository */
  private $scheduledTaskSubscribersRepository;

  public function __construct(
    $taskId,
    $batchSize
  ) {
    if ($taskId <= 0) {
      throw new \Exception('Task ID must be greater than zero');
    } elseif ($batchSize <= 0) {
      throw new \Exception('Batch size must be greater than zero');
    }
    $this->taskId = (int)$taskId;
    $this->batchSize = (int)$batchSize;
    $this->scheduledTaskSubscribersRepository = ContainerWrapper::getInstance()->get(ScheduledTaskSubscribersRepository::class);
  }

  public function rewind(): void {
    $this->lastProcessedId = 0;
  }

  /**
   * @return mixed - it's required for PHP8.1 to prevent using ReturnTypeWillChange that cause an error in PHPStan with PHP7
   */
  #[\ReturnTypeWillChange]
  public function current() {
    $subscribers = $this->scheduledTaskSubscribersRepository->getSubscriberIdsBatchForTask($this->taskId, $this->lastProcessedId, $this->batchSize);
    $this->batchLastId = end($subscribers);
    return $subscribers;
  }

  /**
   * @return string|float|int|bool|null - it's required for PHP8.1 to prevent using ReturnTypeWillChange that cause an error in PHPStan with PHP7
   */
  #[\ReturnTypeWillChange]
  public function key() {
    return null;
  }

  public function next(): void {
    $this->lastProcessedId = $this->batchLastId;
  }

  public function valid(): bool {
    return $this->count() > 0;
  }

  public function count(): int {
    return $this->scheduledTaskSubscribersRepository->countSubscriberIdsBatchForTask($this->taskId, $this->lastProcessedId);
  }
}
