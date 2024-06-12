<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers\SendingQueue\Tasks;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\Workers\SendingQueue\Tasks\Links as LinksTask;
use MailPoet\Cron\Workers\SendingQueue\Tasks\Posts as PostsTask;
use MailPoet\Cron\Workers\SendingQueue\Tasks\Shortcodes as ShortcodesTask;
use MailPoet\DI\ContainerWrapper;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Logging\LoggerFactory;
use MailPoet\Mailer\MailerLog;
use MailPoet\Newsletter\Links\Links as NewsletterLinks;
use MailPoet\Newsletter\NewsletterDeleteController;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Renderer\PostProcess\OpenTracking;
use MailPoet\Newsletter\Renderer\Renderer;
use MailPoet\Newsletter\Sending\ScheduledTasksRepository;
use MailPoet\Newsletter\Sending\SendingQueuesRepository;
use MailPoet\RuntimeException;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Settings\TrackingConfig;
use MailPoet\Statistics\GATracking;
use MailPoet\Util\Helpers;
use MailPoet\Util\pQuery\DomNode;
use MailPoet\Util\pQuery\pQuery;
use MailPoet\WP\Emoji;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;

class Newsletter {
  public $trackingEnabled;
  public $trackingImageInserted;

  /** @var WPFunctions */
  private $wp;

  /** @var PostsTask */
  private $postsTask;

  /** @var GATracking */
  private $gaTracking;

  /** @var LoggerFactory */
  private $loggerFactory;

  /** @var Renderer */
  private $renderer;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var NewsletterDeleteController  */
  private $newsletterDeleteController;

  /** @var Emoji */
  private $emoji;

  /** @var LinksTask */
  private $linksTask;

  /** @var NewsletterLinks */
  private $newsletterLinks;

  /** @var SendingQueuesRepository */
  private $sendingQueuesRepository;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var ScheduledTasksRepository */
  private $scheduledTasksRepository;

  public function __construct(
    WPFunctions $wp = null,
    PostsTask $postsTask = null,
    GATracking $gaTracking = null,
    Emoji $emoji = null
  ) {
    $trackingConfig = ContainerWrapper::getInstance()->get(TrackingConfig::class);
    $this->trackingEnabled = $trackingConfig->isEmailTrackingEnabled();
    if ($wp === null) {
      $wp = new WPFunctions;
    }
    $this->wp = $wp;
    if ($postsTask === null) {
      $postsTask = new PostsTask;
    }
    $this->postsTask = $postsTask;
    if ($gaTracking === null) {
      $gaTracking = ContainerWrapper::getInstance()->get(GATracking::class);
    }
    $this->gaTracking = $gaTracking;
    $this->loggerFactory = LoggerFactory::getInstance();
    if ($emoji === null) {
      $emoji = new Emoji();
    }
    $this->emoji = $emoji;
    $this->renderer = ContainerWrapper::getInstance()->get(Renderer::class);
    $this->newslettersRepository = ContainerWrapper::getInstance()->get(NewslettersRepository::class);
    $this->newsletterDeleteController = ContainerWrapper::getInstance()->get(NewsletterDeleteController::class);
    $this->linksTask = ContainerWrapper::getInstance()->get(LinksTask::class);
    $this->newsletterLinks = ContainerWrapper::getInstance()->get(NewsletterLinks::class);
    $this->sendingQueuesRepository = ContainerWrapper::getInstance()->get(SendingQueuesRepository::class);
    $this->segmentsRepository = ContainerWrapper::getInstance()->get(SegmentsRepository::class);
    $this->scheduledTasksRepository = ContainerWrapper::getInstance()->get(ScheduledTasksRepository::class);
  }

  public function getNewsletterFromQueue(ScheduledTaskEntity $task): ?NewsletterEntity {
    // get existing active or sending newsletter
    $queue = $task->getSendingQueue();
    $newsletter = $queue ? $queue->getNewsletter() : null;

    if (
      is_null($newsletter)
      || $newsletter->getDeletedAt() !== null
      || !in_array($newsletter->getStatus(), [NewsletterEntity::STATUS_ACTIVE, NewsletterEntity::STATUS_SENDING])
      || $newsletter->getStatus() === NewsletterEntity::STATUS_CORRUPT
    ) {
      $this->recoverFromInvalidState($task);
      return null;
    }

    // if this is a notification history, get existing active or sending parent newsletter
    if ($newsletter->getType() == NewsletterEntity::TYPE_NOTIFICATION_HISTORY) {
      $parentNewsletter = $newsletter->getParent();

      if (
        is_null($parentNewsletter)
        || $parentNewsletter->getDeletedAt() !== null
        || !in_array($parentNewsletter->getStatus(), [NewsletterEntity::STATUS_ACTIVE, NewsletterEntity::STATUS_SENDING])
      ) {
        return null;
      }
    }

    return $newsletter;
  }

  /**
   * Pre-processes the newsletter before sending.
   * - Renders the newsletter
   * - Adds tracking
   * - Extracts links
   * - Checks if the newsletter is a post notification and if it contains at least 1 ALC post.
   *   If not it deletes the notification history record and all associate entities.
   *
   * @return NewsletterEntity|false - Returns false only if the newsletter is a post notification history and was deleted.
   *
   */
  public function preProcessNewsletter(NewsletterEntity $newsletter, ScheduledTaskEntity $task) {
    // return the newsletter if it was previously rendered
    $queue = $task->getSendingQueue();
    if (!$queue) {
      throw new RuntimeException('Can‘t pre-process newsletter without queue.');
    }
    if ($queue->getNewsletterRenderedBody() !== null) {
      return $newsletter;
    }
    $this->loggerFactory->getLogger(LoggerFactory::TOPIC_NEWSLETTERS)->info(
      'pre-processing newsletter',
      ['newsletter_id' => $newsletter->getId(), 'task_id' => $task->getId()]
    );

    $campaignId = null;

    // if tracking is enabled, do additional processing
    if ($this->trackingEnabled) {
      // hook to the newsletter post-processing filter and add tracking image
      $this->trackingImageInserted = OpenTracking::addTrackingImage();
      // render newsletter
      $renderedNewsletter = $this->renderer->render($newsletter, $queue);
      $renderedNewsletter = $this->wp->applyFilters(
        'mailpoet_sending_newsletter_render_after_pre_process',
        $renderedNewsletter,
        $newsletter
      );
      if (is_array($renderedNewsletter)) {
        $campaignId = $this->calculateCampaignId($newsletter, $renderedNewsletter);
      }
      $renderedNewsletter = $this->gaTracking->applyGATracking($renderedNewsletter, $newsletter);
      // hash and save all links
      $renderedNewsletter = $this->linksTask->process($renderedNewsletter, $newsletter, $queue);
    } else {
      // render newsletter
      $renderedNewsletter = $this->renderer->render($newsletter, $queue);
      $renderedNewsletter = $this->wp->applyFilters(
        'mailpoet_sending_newsletter_render_after_pre_process',
        $renderedNewsletter,
        $newsletter
      );
      if (is_array($renderedNewsletter)) {
        $campaignId = $this->calculateCampaignId($newsletter, $renderedNewsletter);
      }
      $renderedNewsletter = $this->gaTracking->applyGATracking($renderedNewsletter, $newsletter);
    }

    // check if this is a post notification and if it contains at least 1 ALC post
    if (
      $newsletter->getType() === NewsletterEntity::TYPE_NOTIFICATION_HISTORY &&
      $this->postsTask->getAlcPostsCount($renderedNewsletter, $newsletter) === 0
    ) {
      // delete notification history record since it will never be sent
      $this->loggerFactory->getLogger(LoggerFactory::TOPIC_POST_NOTIFICATIONS)->info(
        'no posts in post notification, deleting it',
        ['newsletter_id' => $newsletter->getId(), 'task_id' => $task->getId()]
      );
      $this->newsletterDeleteController->bulkDelete([(int)$newsletter->getId()]);
      return false;
    }
    // extract and save newsletter posts
    $this->postsTask->extractAndSave($renderedNewsletter, $newsletter);

    if ($campaignId !== null) {
      $this->sendingQueuesRepository->saveCampaignId($queue, $campaignId);
    }

    $filterSegmentId = $newsletter->getFilterSegmentId();
    if ($filterSegmentId) {
      $filterSegment = $this->segmentsRepository->findOneById($filterSegmentId);
      if ($filterSegment instanceof SegmentEntity && $filterSegment->getType() === SegmentEntity::TYPE_DYNAMIC) {
        $this->sendingQueuesRepository->saveFilterSegmentMeta($queue, $filterSegment);
      }
    }

    // update queue with the rendered and pre-processed newsletter
    $queue->setNewsletterRenderedSubject(
      ShortcodesTask::process(
        $newsletter->getSubject(),
        $renderedNewsletter['html'],
        $newsletter,
        null,
        $queue
      )
    );

    // if the rendered subject is empty, use a default subject,
    // having no subject in a newsletter is considered spammy
    if (empty(trim((string)$queue->getNewsletterRenderedSubject()))) {
      $queue->setNewsletterRenderedSubject(__('No subject', 'mailpoet'));
    }
    $renderedNewsletter = $this->emoji->encodeEmojisInBody($renderedNewsletter);
    $queue->setNewsletterRenderedBody($renderedNewsletter);

    try {
      $this->sendingQueuesRepository->flush();
    } catch (\Throwable $e) {
      $this->stopNewsletterPreProcessing(sprintf('QUEUE-%d-SAVE', $queue->getId()));
    }
    return $newsletter;
  }

  /**
   * Shortcodes and links will be replaced in the subject, html and text body
   * to speed the processing, join content into a continuous string.
   */
  public function prepareNewsletterForSending(NewsletterEntity $newsletter, SubscriberEntity $subscriber, SendingQueueEntity $queue): array {
    $renderedNewsletter = $queue->getNewsletterRenderedBody();
    $renderedNewsletter = $this->emoji->decodeEmojisInBody($renderedNewsletter);
    $preparedNewsletter = Helpers::joinObject(
      [
        $queue->getNewsletterRenderedSubject(),
        $renderedNewsletter['html'],
        $renderedNewsletter['text'],
      ]
    );

    $preparedNewsletter = ShortcodesTask::process(
      $preparedNewsletter,
      null,
      $newsletter,
      $subscriber,
      $queue
    );
    if ($this->trackingEnabled) {
      $preparedNewsletter = $this->newsletterLinks->replaceSubscriberData(
        $subscriber->getId(),
        $queue->getId(),
        $preparedNewsletter
      );
    }
    $preparedNewsletter = Helpers::splitObject($preparedNewsletter);
    return [
      'id' => $newsletter->getId(),
      'subject' => $preparedNewsletter[0],
      'body' => [
        'html' => $preparedNewsletter[1],
        'text' => $preparedNewsletter[2],
      ],
    ];
  }

  public function markNewsletterAsSent(NewsletterEntity $newsletter) {
    // if it's a standard or notification history newsletter, update its status
    if (
      $newsletter->getType() === NewsletterEntity::TYPE_STANDARD ||
       $newsletter->getType() === NewsletterEntity::TYPE_NOTIFICATION_HISTORY
    ) {
      $newsletter->setStatus(NewsletterEntity::STATUS_SENT);
      $newsletter->setSentAt(Carbon::createFromTimestamp(WPFunctions::get()->currentTime('timestamp')));
      $this->newslettersRepository->persist($newsletter);
      $this->newslettersRepository->flush();
    }
  }

  public function stopNewsletterPreProcessing($errorCode = null) {
    MailerLog::processError(
      'queue_save',
      __('There was an error processing your newsletter during sending. If possible, please contact us and report this issue.', 'mailpoet'),
      $errorCode
    );
  }

  /**
   * @param NewsletterEntity $newsletter
   * @param array $renderedNewsletters - The pre-processed renderered newsletters, before link tracking has been added or shortcodes have been processed.
   *
   * @return string
   */
  public function calculateCampaignId(NewsletterEntity $newsletter, array $renderedNewsletters): string {
    $relevantContent = [
      $newsletter->getId(),
      $newsletter->getSubject(),
    ];

    if (isset($renderedNewsletters['text'])) {
      $relevantContent[] = $renderedNewsletters['text'];
    }

    // The text version of emails contains just the alt text of images, which could be the same for multiple images. In order to ensure
    // campaign IDs change when images change, we should consider all image URLs.
    if (isset($renderedNewsletters['html'])) {
      $html = pQuery::parseStr($renderedNewsletters['html']);
      if ($html instanceof DomNode) {
        foreach ($html->query('img') as $imageNode) {
          $src = $imageNode->getAttribute('src');
          if (is_string($src)) {
            $relevantContent[] = $src;
          }
        }
      }
    }
    return substr(md5(implode('|', $relevantContent)), 0, 16);
  }

  /**
   * This method recovers the scheduled task and newsletter from a state when sending cannot proceed.
   */
  private function recoverFromInvalidState(ScheduledTaskEntity $task): void {
    // When newsletter does not exist, we need to remove the scheduled task and sending queue.
    $queue = $task->getSendingQueue();
    $newsletter = $queue ? $queue->getNewsletter() : null;
    if (!$newsletter) {
      $this->scheduledTasksRepository->remove($task);
      if ($queue) {
        $this->sendingQueuesRepository->remove($queue);
      }
      $this->sendingQueuesRepository->flush();
      return;
    }

    // Only deleted newsletter or newsletter with unexpected state should pass here.
    // Because this state cannot proceed with sending, we need to pause the scheduled task.
    $task->setStatus(ScheduledTaskEntity::STATUS_PAUSED);
    $this->scheduledTasksRepository->flush();
  }
}
