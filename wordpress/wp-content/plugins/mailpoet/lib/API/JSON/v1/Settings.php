<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\Error as APIError;
use MailPoet\API\JSON\ErrorResponse;
use MailPoet\API\JSON\Response;
use MailPoet\API\JSON\SuccessResponse;
use MailPoet\Config\AccessControl;
use MailPoet\Config\ServicesChecker;
use MailPoet\Cron\Workers\SubscribersEngagementScore;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Form\FormMessageController;
use MailPoet\Mailer\Mailer;
use MailPoet\Mailer\MailerLog;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Sending\ScheduledTasksRepository;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Services\AuthorizedEmailsController;
use MailPoet\Services\AuthorizedSenderDomainController;
use MailPoet\Services\Bridge;
use MailPoet\Settings\SettingsChangeHandler;
use MailPoet\Settings\SettingsController;
use MailPoet\Settings\TrackingConfig;
use MailPoet\Statistics\StatisticsOpensRepository;
use MailPoet\Subscribers\ConfirmationEmailCustomizer;
use MailPoet\Subscribers\SubscribersCountsController;
use MailPoet\Util\Notices\DisabledMailFunctionNotice;
use MailPoet\WooCommerce\TransactionalEmails;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class Settings extends APIEndpoint {

  /** @var SettingsController */
  private $settings;

  /** @var Bridge */
  private $bridge;

  /** @var AuthorizedEmailsController */
  private $authorizedEmailsController;

  /** @var AuthorizedSenderDomainController */
  private $senderDomainController;

  /** @var TransactionalEmails */
  private $wcTransactionalEmails;

  /** @var ServicesChecker */
  private $servicesChecker;

  /** @var WPFunctions */
  private $wp;

  /** @var EntityManager */
  private $entityManager;

  /** @var StatisticsOpensRepository */
  private $statisticsOpensRepository;

  /** @var ScheduledTasksRepository */
  private $scheduledTasksRepository;

  /** @var FormMessageController */
  private $messageController;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var SubscribersCountsController */
  private $subscribersCountsController;

  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_SETTINGS,
  ];
  /**  @var NewslettersRepository */
  private $newsletterRepository;

  /** @var TrackingConfig */
  private $trackingConfig;

  /** @var SettingsChangeHandler */
  private $settingsChangeHandler;

  /** @var ConfirmationEmailCustomizer */
  private $confirmationEmailCustomizer;

  public function __construct(
    SettingsController $settings,
    Bridge $bridge,
    AuthorizedEmailsController $authorizedEmailsController,
    AuthorizedSenderDomainController $senderDomainController,
    TransactionalEmails $wcTransactionalEmails,
    WPFunctions $wp,
    EntityManager $entityManager,
    NewslettersRepository $newslettersRepository,
    StatisticsOpensRepository $statisticsOpensRepository,
    ScheduledTasksRepository $scheduledTasksRepository,
    FormMessageController $messageController,
    ServicesChecker $servicesChecker,
    SegmentsRepository $segmentsRepository,
    SettingsChangeHandler $settingsChangeHandler,
    SubscribersCountsController $subscribersCountsController,
    TrackingConfig $trackingConfig,
    ConfirmationEmailCustomizer $confirmationEmailCustomizer
  ) {
    $this->settings = $settings;
    $this->bridge = $bridge;
    $this->authorizedEmailsController = $authorizedEmailsController;
    $this->senderDomainController = $senderDomainController;
    $this->wcTransactionalEmails = $wcTransactionalEmails;
    $this->servicesChecker = $servicesChecker;
    $this->wp = $wp;
    $this->entityManager = $entityManager;
    $this->newsletterRepository = $newslettersRepository;
    $this->statisticsOpensRepository = $statisticsOpensRepository;
    $this->scheduledTasksRepository = $scheduledTasksRepository;
    $this->messageController = $messageController;
    $this->segmentsRepository = $segmentsRepository;
    $this->settingsChangeHandler = $settingsChangeHandler;
    $this->subscribersCountsController = $subscribersCountsController;
    $this->trackingConfig = $trackingConfig;
    $this->confirmationEmailCustomizer = $confirmationEmailCustomizer;
  }

  public function get() {
    return $this->successResponse($this->settings->getAll());
  }

  public function set($settings = []) {
    if (empty($settings)) {
      return $this->badRequest(
        [
          APIError::BAD_REQUEST =>
            __('You have not specified any settings to be saved.', 'mailpoet'),
        ]
      );
    } else {
      $oldSettings = $this->settings->getAll();
      $meta = [];
      $signupConfirmation = $this->settings->get('signup_confirmation.enabled');
      foreach ($settings as $name => $value) {
        $this->settings->set($name, $value);
      }

      $this->onSettingsChange($oldSettings, $this->settings->getAll());

      // when pending approval, leave this to cron / Key Activation tab logic
      if (!$this->servicesChecker->isMailPoetAPIKeyPendingApproval()) {
        $this->settingsChangeHandler->updateApiKeyState($settings);
      }

      $meta = $this->authorizedEmailsController->onSettingsSave($settings);
      if ($signupConfirmation !== $this->settings->get('signup_confirmation.enabled')) {
        $this->messageController->updateSuccessMessages();
      }

      // Tracking and re-engagement Emails
      $meta['showNotice'] = false;
      if ($oldSettings['tracking'] !== $this->settings->get('tracking')) {
        try {
          $meta = $this->updateReEngagementEmailStatus($this->settings->get('tracking'));
        } catch (\Exception $e) {
          return $this->badRequest([
            APIError::UNKNOWN => $e->getMessage()]);
        }
      }

      return $this->successResponse($this->settings->getAll(), $meta);
    }
  }

  public function delete(string $settingName): Response {
    if (empty($settingName)) {
      return $this->badRequest(
        [
          APIError::BAD_REQUEST =>
            __('You have not specified any setting to be deleted.', 'mailpoet'),
        ]
      );
    }

    $setting = $this->settings->get($settingName);

    if (is_null($setting)) {
      return $this->badRequest(
        [
          APIError::BAD_REQUEST =>
            __('Setting doesn\'t exist.', 'mailpoet'),
        ]
      );
    }

    $this->settings->delete($settingName);

    return $this->successResponse();
  }

  public function recalculateSubscribersScore() {
    $this->statisticsOpensRepository->resetSubscribersScoreCalculation();
    $this->statisticsOpensRepository->resetSegmentsScoreCalculation();
    $task = $this->scheduledTasksRepository->findOneBy([
      'type' => SubscribersEngagementScore::TASK_TYPE,
      'status' => ScheduledTaskEntity::STATUS_SCHEDULED,
    ]);
    if (!$task) {
      $task = new ScheduledTaskEntity();
      $task->setType(SubscribersEngagementScore::TASK_TYPE);
      $task->setStatus(ScheduledTaskEntity::STATUS_SCHEDULED);
    }
    $task->setScheduledAt(Carbon::createFromTimestamp($this->wp->currentTime('timestamp')));
    $this->entityManager->persist($task);
    $this->entityManager->flush();
    return $this->successResponse();
  }

  public function setAuthorizedFromAddress($data = []) {
    $address = $data['address'] ?? null;
    if (!$address) {
      return $this->badRequest([
        APIError::BAD_REQUEST => __('No email address specified.', 'mailpoet'),
      ]);
    }
    $address = trim($address);

    try {
      $this->authorizedEmailsController->setFromEmailAddress($address);
    } catch (\InvalidArgumentException $e) {
      return $this->badRequest([
        APIError::UNAUTHORIZED => __('Can’t use this email yet! Please authorize it first.', 'mailpoet'),
      ]);
    }

    if (!$this->servicesChecker->isMailPoetAPIKeyPendingApproval()) {
      MailerLog::resumeSending();
    }
    return $this->successResponse();
  }

  /**
   * Create POST request to Bridge endpoint to add email to user email authorization list
   */
  public function authorizeSenderEmailAddress($data = []) {
    $emailAddress = $data['email'] ?? null;

    if (!$emailAddress) {
      return $this->badRequest([
        APIError::BAD_REQUEST => __('No email address specified.', 'mailpoet'),
      ]);
    }

    $emailAddress = trim($emailAddress);

    try {
      $response = $this->authorizedEmailsController->createAuthorizedEmailAddress($emailAddress);
    } catch (\InvalidArgumentException $e) {
      if (
        $e->getMessage() === AuthorizedEmailsController::AUTHORIZED_EMAIL_ERROR_ALREADY_AUTHORIZED ||
        $e->getMessage() === AuthorizedEmailsController::AUTHORIZED_EMAIL_ERROR_PENDING_CONFIRMATION
      ) {
        // return true if the email is already authorized or pending confirmation
        $response = ['status' => true];
      } else {
        return $this->badRequest([
          APIError::BAD_REQUEST => $e->getMessage(),
        ]);
      }
    }

    return $this->successResponse($response);
  }

  public function confirmSenderEmailAddressIsAuthorized($data = []) {
    $emailAddress = $data['email'] ?? null;

    if (!$emailAddress) {
      return $this->badRequest([
        APIError::BAD_REQUEST => __('No email address specified.', 'mailpoet'),
      ]);
    }

    $emailAddress = trim($emailAddress);

    $response = ['isAuthorized' => $this->authorizedEmailsController->isEmailAddressAuthorized($emailAddress)];

    return $this->successResponse($response);
  }

  public function getAuthorizedSenderDomains($data = []) {
    $domain = $data['domain'] ?? null;

    if (!$domain) {
      return $this->badRequest([
        APIError::BAD_REQUEST => __('No sender domain specified.', 'mailpoet'),
      ]);
    }

    $domain = strtolower(trim($domain));

    $records = $this->bridge->getAuthorizedSenderDomains($domain);
    return $this->successResponse($records);
  }

  public function createAuthorizedSenderDomain($data = []) {
    $domain = $data['domain'] ?? null;

    if (!$domain) {
      return $this->badRequest([
        APIError::BAD_REQUEST => __('No sender domain specified.', 'mailpoet'),
      ]);
    }

    $domain = strtolower(trim($domain));

    try {
      $response = $this->senderDomainController->createAuthorizedSenderDomain($domain);
    } catch (\InvalidArgumentException $e) {
      if (
        $e->getMessage() === AuthorizedSenderDomainController::AUTHORIZED_SENDER_DOMAIN_ERROR_ALREADY_CREATED
      ) {
        // domain already created
        $response = $this->senderDomainController->getDomainRecords($domain);
      } else {
        return $this->badRequest([
          APIError::BAD_REQUEST => $e->getMessage(),
        ]);
      }
    }

    return $this->successResponse($response);
  }

  public function verifyAuthorizedSenderDomain($data = []) {
    $domain = $data['domain'] ?? null;

    if (!$domain) {
      return $this->badRequest([
        APIError::BAD_REQUEST => __('No sender domain specified.', 'mailpoet'),
      ]);
    }

    $domain = strtolower(trim($domain));

    try {
      $response = $this->senderDomainController->verifyAuthorizedSenderDomain($domain);
    } catch (\InvalidArgumentException $e) {
      if (
        $e->getMessage() === AuthorizedSenderDomainController::AUTHORIZED_SENDER_DOMAIN_ERROR_ALREADY_VERIFIED
      ) {
        // domain already verified, we have to wrap this in the format returned by the api
        $response = ['ok' => true, 'dns' => $this->senderDomainController->getDomainRecords($domain)];
      } else {
        return $this->badRequest([
          APIError::BAD_REQUEST => $e->getMessage(),
        ]);
      }
    }

    if (!$response['ok']) {
      // sender domain verification error. probably an improper setup
      return $this->badRequest([
        APIError::BAD_REQUEST => $response['message'] ?? __('Sender domain verification failed.', 'mailpoet'),
      ], $response);
    }

    return $this->successResponse($response);
  }

  private function onSettingsChange($oldSettings, $newSettings) {
    // Recalculate inactive subscribers
    $oldInactivationInterval = $oldSettings['deactivate_subscriber_after_inactive_days'];
    $newInactivationInterval = $newSettings['deactivate_subscriber_after_inactive_days'];
    if ($oldInactivationInterval !== $newInactivationInterval) {
      $this->settingsChangeHandler->onInactiveSubscribersIntervalChange();
    }

    $oldSendingMethod = $oldSettings['mta_group'];
    $newSendingMethod = $newSettings['mta_group'];
    if (($oldSendingMethod !== $newSendingMethod) && ($newSendingMethod === 'mailpoet')) {
      $this->settingsChangeHandler->onMSSActivate($newSettings);
    }

    if (($oldSendingMethod !== $newSendingMethod)) {
      $sendingMethodSet = $newSettings['mta']['method'] ?? null;
      if ($sendingMethodSet === 'PHPMail') {
        // check for valid mail function
        $this->settings->set(DisabledMailFunctionNotice::QUEUE_DISABLED_MAIL_FUNCTION_CHECK, true);
      } else {
        // when the user switch to a new sending method
        // do not display the DisabledMailFunctionNotice
        $this->settings->set(DisabledMailFunctionNotice::QUEUE_DISABLED_MAIL_FUNCTION_CHECK, false);
        $this->settings->set(DisabledMailFunctionNotice::DISABLED_MAIL_FUNCTION_CHECK, false); // do not display notice
      }
    }

    // Sync WooCommerce Customers list
    $oldSubscribeOldWoocommerceCustomers = isset($oldSettings['mailpoet_subscribe_old_woocommerce_customers']['enabled'])
      ? $oldSettings['mailpoet_subscribe_old_woocommerce_customers']['enabled']
      : '0';
    $newSubscribeOldWoocommerceCustomers = isset($newSettings['mailpoet_subscribe_old_woocommerce_customers']['enabled'])
      ? $newSettings['mailpoet_subscribe_old_woocommerce_customers']['enabled']
      : '0';
    if ($oldSubscribeOldWoocommerceCustomers !== $newSubscribeOldWoocommerceCustomers) {
      $this->settingsChangeHandler->onSubscribeOldWoocommerceCustomersChange();
    }

    if (!empty($newSettings['woocommerce']['use_mailpoet_editor'])) {
      $this->wcTransactionalEmails->init();
    }

    if (!empty($newSettings['signup_confirmation']['use_mailpoet_editor'])) {
      $this->confirmationEmailCustomizer->init();
    }
  }

  public function recalculateSubscribersCountsCache() {
    $segments = $this->segmentsRepository->findAll();
    foreach ($segments as $segment) {
      $this->subscribersCountsController->recalculateSegmentStatisticsCache($segment);
    }
    $this->subscribersCountsController->recalculateSubscribersWithoutSegmentStatisticsCache();
    // remove redundancies from cache
      $this->subscribersCountsController->removeRedundancyFromStatisticsCache();
    return $this->successResponse();
  }

  /**
   * @throws \Exception
   */
  public function updateReEngagementEmailStatus($newTracking): array {
    if (!empty($newTracking['level']) && $this->trackingConfig->isEmailTrackingEnabled($newTracking['level'])) {
      return $this->reactivateReEngagementEmails();
    }
    try {
      return $this->deactivateReEngagementEmails();
    } catch (\Exception $e) {
      throw new \Exception(
        sprintf(
          // translators: %s is the error message.
          __('Unable to deactivate re-engagement emails: %s', 'mailpoet'),
          $e->getMessage()
        )
      );
    }
  }

  /**
   * @throws \Exception
   */
  public function deactivateReEngagementEmails(): array {
    $reEngagementEmails = $this->newsletterRepository->findActiveByTypes(([NewsletterEntity::TYPE_RE_ENGAGEMENT]));
    if (!$reEngagementEmails) {
      return [
        'showNotice' => false,
        'action' => 'deactivate',
      ];
    }

    foreach ($reEngagementEmails as $reEngagementEmail) {
      $reEngagementEmail->setStatus(NewsletterEntity::STATUS_DRAFT);
      $this->entityManager->persist($reEngagementEmail);
      $this->entityManager->flush();
    }
    return [
      'showNotice' => true,
      'action' => 'deactivate',
    ];
  }

  public function reactivateReEngagementEmails(): array {
    $draftReEngagementEmails = $this->newsletterRepository->findDraftByTypes(([NewsletterEntity::TYPE_RE_ENGAGEMENT]));
    return [
      'showNotice' => !!$draftReEngagementEmails,
      'action' => 'reactivate',
    ];
  }

  /**
   * Prepares the settings to set up MSS with the given key and calls the set method.
   *
   * @param string $apiKey
   * @return ErrorResponse|SuccessResponse
   */
  public function setKeyAndSetupMss(string $apiKey) {
    $new_settings = [
      'mta_group' => 'mailpoet',
      'mta' => [
        'method' => Mailer::METHOD_MAILPOET,
        'mailpoet_api_key' => $apiKey,
      ],
      'signup_confirmation' => [
        'enabled' => '1',
      ],
      'premium.premium_key' => $apiKey,
    ];
    return $this->set($new_settings);
  }
}
