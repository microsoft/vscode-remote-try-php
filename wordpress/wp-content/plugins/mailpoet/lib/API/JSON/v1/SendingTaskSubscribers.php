<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\Error as APIError;
use MailPoet\API\JSON\ResponseBuilders\ScheduledTaskSubscriberResponseBuilder;
use MailPoet\Config\AccessControl;
use MailPoet\Cron\CronHelper;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Listing;
use MailPoet\Newsletter\Sending\ScheduledTaskSubscribersListingRepository;
use MailPoet\Newsletter\Sending\ScheduledTaskSubscribersRepository;
use MailPoet\Newsletter\Sending\SendingQueuesRepository;
use MailPoet\Settings\SettingsController;
use MailPoet\WP\Functions as WPFunctions;

class SendingTaskSubscribers extends APIEndpoint {
  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_EMAILS,
  ];

  /** @var Listing\Handler */
  private $listingHandler;

  /** @var SettingsController */
  private $settings;

  /** @var CronHelper */
  private $cronHelper;

  /** @var WPFunctions */
  private $wp;

  /** @var SendingQueuesRepository */
  private $sendingQueuesRepository;

  /** @var ScheduledTaskSubscribersRepository */
  private $scheduledTaskSubscribersRepository;

  /** @var ScheduledTaskSubscribersListingRepository */
  private $taskSubscribersListingRepository;

  /** @var ScheduledTaskSubscriberResponseBuilder */
  private $scheduledTaskSubscriberResponseBuilder;

  public function __construct(
    Listing\Handler $listingHandler,
    SettingsController $settings,
    CronHelper $cronHelper,
    SendingQueuesRepository $sendingQueuesRepository,
    ScheduledTaskSubscribersListingRepository $taskSubscribersListingRepository,
    ScheduledTaskSubscriberResponseBuilder $scheduledTaskSubscriberResponseBuilder,
    ScheduledTaskSubscribersRepository $scheduledTaskSubscribersRepository,
    WPFunctions $wp
  ) {
    $this->listingHandler = $listingHandler;
    $this->settings = $settings;
    $this->cronHelper = $cronHelper;
    $this->sendingQueuesRepository = $sendingQueuesRepository;
    $this->taskSubscribersListingRepository = $taskSubscribersListingRepository;
    $this->scheduledTaskSubscriberResponseBuilder = $scheduledTaskSubscriberResponseBuilder;
    $this->scheduledTaskSubscribersRepository = $scheduledTaskSubscribersRepository;
    $this->wp = $wp;
  }

  public function listing($data = []) {
    $newsletterId = !empty($data['params']['id']) ? (int)$data['params']['id'] : false;
    if (empty($newsletterId)) {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('Newsletter not found!', 'mailpoet'),
      ]);
    }
    $tasksIds = $this->sendingQueuesRepository->getTaskIdsByNewsletterId($newsletterId);

    if (empty($tasksIds)) {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This email has not been sent yet.', 'mailpoet'),
      ]);
    }
    $data['params']['task_ids'] = $tasksIds;
    $definition = $this->listingHandler->getListingDefinition($data);
    $items = $this->taskSubscribersListingRepository->getData($definition);
    $groups = $this->taskSubscribersListingRepository->getGroups($definition);
    $filters = $this->taskSubscribersListingRepository->getFilters($definition);
    $count = $this->taskSubscribersListingRepository->getCount($definition);

    return $this->successResponse($this->scheduledTaskSubscriberResponseBuilder->buildForListing($items), [
      'count' => $count,
      'filters' => $filters,
      'groups' => $groups,
      'mta_log' => $this->settings->get('mta_log'),
      'mta_method' => $this->settings->get('mta.method'),
      'cron_accessible' => $this->cronHelper->isDaemonAccessible(),
      'current_time' => $this->wp->currentTime('mysql'),
    ]);
  }

  public function resend($data = []) {
    $taskId = !empty($data['taskId']) ? (int)$data['taskId'] : 0;
    $subscriberId = !empty($data['subscriberId']) ? (int)$data['subscriberId'] : 0;

    $taskSubscriber = $this->scheduledTaskSubscribersRepository->findOneBy([
      'task' => $taskId,
      'subscriber' => $subscriberId,
      'failed' => 1,
      ]);

    $sendingQueue = $this->sendingQueuesRepository->findOneBy(['task' => $taskId]);

    if (
      !$taskSubscriber
      || !$taskSubscriber->getTask()
      || !$sendingQueue
    ) {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('Failed sending task not found!', 'mailpoet'),
      ]);
    }

    $newsletter = $sendingQueue->getNewsletter();
    if (!$newsletter) {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('Newsletter not found!', 'mailpoet'),
      ]);
    }

    $taskSubscriber->resetToUnprocessed();
    $taskSubscriber->getTask()->setStatus(null);
    $newsletter->setStatus(NewsletterEntity::STATUS_SENDING);
    // Each repository flushes all changes
    $this->scheduledTaskSubscribersRepository->flush();
    return $this->successResponse([]);
  }
}
