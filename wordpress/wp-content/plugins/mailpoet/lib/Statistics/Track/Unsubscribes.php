<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Statistics\Track;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\StatisticsUnsubscribeEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Newsletter\Sending\SendingQueuesRepository;
use MailPoet\Statistics\StatisticsUnsubscribesRepository;
use MailPoet\Subscribers\SubscribersRepository;

class Unsubscribes {
  /** @var SendingQueuesRepository */
  private $sendingQueuesRepository;

  /** @var StatisticsUnsubscribesRepository */
  private $statisticsUnsubscribesRepository;

  /**
   * @var SubscribersRepository
   */
  private $subscribersRepository;

  public function __construct(
    SendingQueuesRepository $sendingQueuesRepository,
    StatisticsUnsubscribesRepository $statisticsUnsubscribesRepository,
    SubscribersRepository $subscribersRepository
  ) {
    $this->sendingQueuesRepository = $sendingQueuesRepository;
    $this->statisticsUnsubscribesRepository = $statisticsUnsubscribesRepository;
    $this->subscribersRepository = $subscribersRepository;
  }

  public function track(
    int $subscriberId,
    string $source,
    int $queueId = null,
    string $meta = null,
    string $method = StatisticsUnsubscribeEntity::METHOD_UNKNOWN
  ) {
    $queue = null;
    $statistics = null;
    if ($queueId) {
      $queue = $this->sendingQueuesRepository->findOneById($queueId);
    }
    $subscriber = $this->subscribersRepository->findOneById($subscriberId);
    if (!$subscriber instanceof SubscriberEntity) {
      return;
    }
    if (($queue instanceof SendingQueueEntity)) {
      $newsletter = $queue->getNewsletter();
      if ($newsletter instanceof NewsletterEntity) {
        $statistics = $this->statisticsUnsubscribesRepository->findOneBy(
          [
            'queue' => $queue,
            'newsletter' => $newsletter,
            'subscriber' => $subscriber,
          ]
        );
        if (!$statistics) {
          $statistics = new StatisticsUnsubscribeEntity($newsletter, $queue, $subscriber);
        }
      }
    }

    if ($statistics === null) {
      $statistics = new StatisticsUnsubscribeEntity(null, null, $subscriber);
    }
    if ($meta !== null) {
      $statistics->setMeta($meta);
    }
    $statistics->setSource($source);
    $statistics->setMethod($method);
    $this->statisticsUnsubscribesRepository->persist($statistics);
    $this->statisticsUnsubscribesRepository->flush();
  }
}
