<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Statistics\Track;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\StatisticsOpenEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\UserAgentEntity;
use MailPoet\Statistics\StatisticsOpensRepository;
use MailPoet\Statistics\UserAgentsRepository;
use MailPoet\Subscribers\SubscribersRepository;

class Opens {
  /** @var StatisticsOpensRepository */
  private $statisticsOpensRepository;

  /** @var UserAgentsRepository */
  private $userAgentsRepository;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  public function __construct(
    StatisticsOpensRepository $statisticsOpensRepository,
    UserAgentsRepository $userAgentsRepository,
    SubscribersRepository $subscribersRepository
  ) {
    $this->statisticsOpensRepository = $statisticsOpensRepository;
    $this->userAgentsRepository = $userAgentsRepository;
    $this->subscribersRepository = $subscribersRepository;
  }

  public function track($data, $displayImage = true) {
    if (!$data) {
      return $this->returnResponse($displayImage);
    }
    /** @var SubscriberEntity $subscriber */
    $subscriber = $data->subscriber;
    /** @var SendingQueueEntity $queue */
    $queue = $data->queue;
    /** @var NewsletterEntity $newsletter */
    $newsletter = $data->newsletter;
    $wpUserPreview = ($data->preview && ($subscriber->isWPUser()));
    // log statistics only if the action did not come from
    // a WP user previewing the newsletter
    if (!$wpUserPreview) {
      $oldStatistics = $this->statisticsOpensRepository->findOneBy([
        'subscriber' => $subscriber->getId(),
        'newsletter' => $newsletter->getId(),
        'queue' => $queue->getId(),
      ]);
      // Open was already tracked
      if ($oldStatistics) {
        if (!empty($data->userAgent)) {
          $userAgent = $this->userAgentsRepository->findOrCreate($data->userAgent);
          if (
            $userAgent->getUserAgentType() === UserAgentEntity::USER_AGENT_TYPE_HUMAN
            || $oldStatistics->getUserAgentType() === UserAgentEntity::USER_AGENT_TYPE_MACHINE
          ) {
            $oldStatistics->setUserAgent($userAgent);
            $oldStatistics->setUserAgentType($userAgent->getUserAgentType());
            $this->statisticsOpensRepository->flush();
          }
        }
        $this->subscribersRepository->maybeUpdateLastOpenAt($subscriber);
        return $this->returnResponse($displayImage);
      }
      $statistics = new StatisticsOpenEntity($newsletter, $queue, $subscriber);
      if (!empty($data->userAgent)) {
        $userAgent = $this->userAgentsRepository->findOrCreate($data->userAgent);
        $statistics->setUserAgent($userAgent);
        $statistics->setUserAgentType($userAgent->getUserAgentType());
      }
      $this->statisticsOpensRepository->persist($statistics);
      $this->statisticsOpensRepository->flush();
      $this->subscribersRepository->maybeUpdateLastOpenAt($subscriber);
      $this->statisticsOpensRepository->recalculateSubscriberScore($subscriber);
    }
    return $this->returnResponse($displayImage);
  }

  public function returnResponse($displayImage) {
    if (!$displayImage) return;
    // return 1x1 pixel transparent gif image
    header('Content-Type: image/gif');

    // Output of base64_decode is predetermined and safe in this case
    // phpcs:ignore WordPressDotOrg.sniffs.OutputEscaping.UnescapedOutputParameter, WordPress.Security.EscapeOutput.OutputNotEscaped
    echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');
    exit;
  }
}
