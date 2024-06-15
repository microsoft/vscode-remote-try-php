<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\Error as APIError;
use MailPoet\API\JSON\Response;
use MailPoet\API\JSON\ResponseBuilders\SendingQueuesResponseBuilder;
use MailPoet\Config\AccessControl;
use MailPoet\Cron\ActionScheduler\Actions\DaemonTrigger;
use MailPoet\Cron\CronHelper;
use MailPoet\Cron\CronTrigger;
use MailPoet\Cron\Triggers\WordPress;
use MailPoet\Cron\Workers\SendingQueue\SendingQueue as SendingQueueWorker;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Mailer\MailerFactory;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\NewsletterValidator;
use MailPoet\Newsletter\Sending\ScheduledTasksRepository;
use MailPoet\Newsletter\Sending\SendingQueuesRepository;
use MailPoet\Segments\SubscribersFinder;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\License\Features\Subscribers as SubscribersFeature;
use MailPoetVendor\Carbon\Carbon;

class SendingQueue extends APIEndpoint {
  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_EMAILS,
  ];

  /** @var SubscribersFeature */
  private $subscribersFeature;

  /** @var SubscribersFinder */
  private $subscribersFinder;

  /** @var NewslettersRepository */
  private $newsletterRepository;

  /** @var SendingQueuesRepository */
  private $sendingQueuesRepository;

  /** @var ScheduledTasksRepository */
  private $scheduledTasksRepository;

  /** @var MailerFactory */
  private $mailerFactory;

  /** @var NewsletterValidator */
  private $newsletterValidator;

  /** @var SettingsController */
  private $settings;

  /** @var DaemonTrigger */
  private $actionSchedulerDaemonTriggerAction;

  /** @var SendingQueuesResponseBuilder */
  private $sendingQueuesResponseBuilder;

  /** @var CronHelper */
  private $cronHelper;

  public function __construct(
    SubscribersFeature $subscribersFeature,
    NewslettersRepository $newsletterRepository,
    SendingQueuesRepository $sendingQueuesRepository,
    SubscribersFinder $subscribersFinder,
    ScheduledTasksRepository $scheduledTasksRepository,
    MailerFactory $mailerFactory,
    SettingsController $settings,
    DaemonTrigger $actionSchedulerDaemonTriggerAction,
    NewsletterValidator $newsletterValidator,
    SendingQueuesResponseBuilder $sendingQueuesResponseBuilder,
    CronHelper $cronHelper
  ) {
    $this->subscribersFeature = $subscribersFeature;
    $this->subscribersFinder = $subscribersFinder;
    $this->newsletterRepository = $newsletterRepository;
    $this->sendingQueuesRepository = $sendingQueuesRepository;
    $this->scheduledTasksRepository = $scheduledTasksRepository;
    $this->mailerFactory = $mailerFactory;
    $this->settings = $settings;
    $this->actionSchedulerDaemonTriggerAction = $actionSchedulerDaemonTriggerAction;
    $this->newsletterValidator = $newsletterValidator;
    $this->sendingQueuesResponseBuilder = $sendingQueuesResponseBuilder;
    $this->cronHelper = $cronHelper;
  }

  public function add($data = []) {
    if ($this->subscribersFeature->check()) {
      return $this->errorResponse([
        APIError::FORBIDDEN => __('Subscribers limit reached.', 'mailpoet'),
      ], [], Response::STATUS_FORBIDDEN);
    }
    $newsletterId = (isset($data['newsletter_id'])
      ? (int)$data['newsletter_id']
      : false
    );

    // check that the newsletter exists
    $newsletter = $this->newsletterRepository->findOneById($newsletterId);
    $this->newsletterRepository->prefetchOptions([$newsletter]);

    if (!$newsletter instanceof NewsletterEntity) {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This newsletter does not exist.', 'mailpoet'),
      ]);
    }

    $validationError = $this->newsletterValidator->validate($newsletter);
    if ($validationError) {
      return $this->errorResponse([
        APIError::BAD_REQUEST => $validationError,
      ]);
    }

    try {
      // check that the sending method has been configured properly by verifying that default mailer can be build
      $this->mailerFactory->getDefaultMailer();

      $sendingQueue = $this->sendingQueuesRepository->findOneByNewsletterAndTaskStatus($newsletter, null);

      if ($sendingQueue instanceof SendingQueueEntity) {
        return $this->errorResponse([
          APIError::NOT_FOUND => __('This newsletter is already being sent.', 'mailpoet'),
        ]);
      }

      $sendingQueue = $this->sendingQueuesRepository->findOneByNewsletterAndTaskStatus($newsletter, ScheduledTaskEntity::STATUS_SCHEDULED);

      if (is_null($sendingQueue)) {
        $scheduledTask = new ScheduledTaskEntity();
        $scheduledTask->setType(SendingQueueWorker::TASK_TYPE);
        $sendingQueue = new SendingQueueEntity();
        $sendingQueue->setNewsletter($newsletter);
        $sendingQueue->setTask($scheduledTask);

        $this->sendingQueuesRepository->persist($sendingQueue);
        $this->newsletterRepository->refresh($newsletter);
      } else {
        $scheduledTask = $sendingQueue->getTask();
      }

      if (!$scheduledTask instanceof ScheduledTaskEntity) {
        return $this->errorResponse([
          APIError::NOT_FOUND => __('Unable to find scheduled task associated with this newsletter.', 'mailpoet'),
        ]);
      }

      $scheduledTask->setPriority(ScheduledTaskEntity::PRIORITY_MEDIUM);
      $this->scheduledTasksRepository->persist($scheduledTask);
      $this->scheduledTasksRepository->flush();

      WordPress::resetRunInterval();
      if ((bool)$newsletter->getOptionValue('isScheduled')) {
        // set newsletter status
        $newsletter->setStatus(NewsletterEntity::STATUS_SCHEDULED);

        // set scheduled task status
        $scheduledTask->setStatus(ScheduledTaskEntity::STATUS_SCHEDULED);
        $scheduledTask->setScheduledAt(new Carbon($newsletter->getOptionValue('scheduledAt')));
      } else {
        $segments = $newsletter->getSegmentIds();

        $this->scheduledTasksRepository->refresh($scheduledTask);
        $subscribersCount = $this->subscribersFinder->addSubscribersToTaskFromSegments($scheduledTask, $segments, $newsletter->getFilterSegmentId());

        if (!$subscribersCount) {
          return $this->errorResponse([
            APIError::UNKNOWN => __('There are no subscribers in that list!', 'mailpoet'),
          ]);
        }

        $this->sendingQueuesRepository->updateCounts($sendingQueue);
        $scheduledTask->setStatus(null);
        $scheduledTask->setScheduledAt(null);

        // set newsletter status
        $newsletter->setStatus(NewsletterEntity::STATUS_SENDING);
      }
      $this->scheduledTasksRepository->persist($scheduledTask);
      $this->newsletterRepository->flush();

      $this->triggerSending($newsletter);
      return $this->successResponse(
        ($newsletter->getLatestQueue() instanceof SendingQueueEntity) ? $this->sendingQueuesResponseBuilder->build($newsletter->getLatestQueue()) : null
      );
    } catch (\Exception $e) {
      return $this->errorResponse([
        $e->getCode() => $e->getMessage(),
      ]);
    }
  }

  public function pause($data = []) {
    $newsletterId = (isset($data['newsletter_id'])
      ? (int)$data['newsletter_id']
      : false
    );
    $newsletter = $this->newsletterRepository->findOneById($newsletterId);

    if ($newsletter instanceof NewsletterEntity) {
      $queue = $newsletter->getLastUpdatedQueue();

      if (!$queue instanceof SendingQueueEntity) {
        return $this->errorResponse([
          APIError::UNKNOWN => __('This newsletter has not been sent yet.', 'mailpoet'),
        ]);
      } else {
        $this->sendingQueuesRepository->pause($queue);
        return $this->successResponse();
      }
    } else {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This newsletter does not exist.', 'mailpoet'),
      ]);
    }
  }

  public function resume($data = []) {
    if ($this->subscribersFeature->check()) {
      return $this->errorResponse([
        APIError::FORBIDDEN => __('Subscribers limit reached.', 'mailpoet'),
      ], [], Response::STATUS_FORBIDDEN);
    }
    $newsletterId = (isset($data['newsletter_id'])
      ? (int)$data['newsletter_id']
      : false
    );
    $newsletter = $this->newsletterRepository->findOneById($newsletterId);

    if ($newsletter instanceof NewsletterEntity) {
      $queue = $newsletter->getLastUpdatedQueue();

      if (!$queue instanceof SendingQueueEntity) {
        return $this->errorResponse([
          APIError::UNKNOWN => __('This newsletter has not been sent yet.', 'mailpoet'),
        ]);
      } else {
        $this->sendingQueuesRepository->resume($queue);
        $this->triggerSending($newsletter);
        return $this->successResponse();
      }
    } else {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This newsletter does not exist.', 'mailpoet'),
      ]);
    }
  }

  public function pingCron() {
    try {
      $cronPingResponse = $this->cronHelper->pingDaemon();
    } catch (\Exception $e) {
      return $this->errorResponse([
        APIError::UNKNOWN => $e->getMessage(),
      ]);
    }
    if (!$this->cronHelper->validatePingResponse($cronPingResponse)) {
      return $this->errorResponse([
        APIError::UNKNOWN => $cronPingResponse,
      ]);
    }
    return $this->successResponse();
  }

  /**
   * In case the newsletter was switched to sending trigger the background job immediately.
   * This is done so that user immediately sees that email is sending and doesn't have to wait on WP Cron to start it.
   */
  private function triggerSending(NewsletterEntity $newsletter): void {
    if (
      $newsletter->getStatus() === NewsletterEntity::STATUS_SENDING
      && $this->settings->get('cron_trigger.method') === CronTrigger::METHOD_ACTION_SCHEDULER
    ) {
      $this->actionSchedulerDaemonTriggerAction->process();
    }
  }
}
