<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscription;

if (!defined('ABSPATH')) exit;


use MailPoet\CustomFields\CustomFieldsRepository;
use MailPoet\Entities\StatisticsUnsubscribeEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\SubscriberSegmentEntity;
use MailPoet\Form\Util\FieldNameObfuscator;
use MailPoet\Newsletter\Scheduler\WelcomeScheduler;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Statistics\Track\Unsubscribes;
use MailPoet\Subscribers\LinkTokens;
use MailPoet\Subscribers\NewSubscriberNotificationMailer;
use MailPoet\Subscribers\SubscriberSaveController;
use MailPoet\Subscribers\SubscriberSegmentRepository;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Util\Url as UrlHelper;

class Manage {

  /** @var UrlHelper */
  private $urlHelper;

  /** @var FieldNameObfuscator */
  private $fieldNameObfuscator;

  /** @var LinkTokens */
  private $linkTokens;

  /** @var Unsubscribes */
  private $unsubscribesTracker;

  /** @var NewSubscriberNotificationMailer */
  private $newSubscriberNotificationMailer;

  /** @var WelcomeScheduler */
  private $welcomeScheduler;

  /** @var CustomFieldsRepository */
  private $customFieldsRepository;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var SubscriberSegmentRepository */
  private $subscriberSegmentRepository;

  /** @var SubscriberSaveController */
  private $subscriberSaveController;

  public function __construct(
    UrlHelper $urlHelper,
    FieldNameObfuscator $fieldNameObfuscator,
    LinkTokens $linkTokens,
    Unsubscribes $unsubscribesTracker,
    NewSubscriberNotificationMailer $newSubscriberNotificationMailer,
    WelcomeScheduler $welcomeScheduler,
    CustomFieldsRepository $customFieldsRepository,
    SegmentsRepository $segmentsRepository,
    SubscribersRepository $subscribersRepository,
    SubscriberSegmentRepository $subscriberSegmentRepository,
    SubscriberSaveController $subscriberSaveController
  ) {
    $this->urlHelper = $urlHelper;
    $this->fieldNameObfuscator = $fieldNameObfuscator;
    $this->unsubscribesTracker = $unsubscribesTracker;
    $this->linkTokens = $linkTokens;
    $this->newSubscriberNotificationMailer = $newSubscriberNotificationMailer;
    $this->welcomeScheduler = $welcomeScheduler;
    $this->segmentsRepository = $segmentsRepository;
    $this->subscribersRepository = $subscribersRepository;
    $this->subscriberSegmentRepository = $subscriberSegmentRepository;
    $this->customFieldsRepository = $customFieldsRepository;
    $this->subscriberSaveController = $subscriberSaveController;
  }

  public function onSave() {
    $action = (isset($_POST['action']) ? sanitize_text_field(wp_unslash($_POST['action'])) : '');
    $token = (isset($_POST['token']) ? sanitize_text_field(wp_unslash($_POST['token'])) : '');

    if ($action !== 'mailpoet_subscription_update' || empty($_POST['data'])) {
      $this->urlHelper->redirectBack();
    }

    $sanitize = function($value) {
      if (is_array($value)) {
        foreach ($value as $k => $v) {
          $value[sanitize_text_field($k)] = sanitize_text_field($v);
        }
        return $value;
      };
      return sanitize_text_field($value);
    };

    // custom sanitization via $sanitize
    //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $subscriberData = array_map($sanitize, wp_unslash((array)$_POST['data']));
    $subscriberData = $this->fieldNameObfuscator->deobfuscateFormPayload($subscriberData);

    $result = [];
    if (!empty($subscriberData['email'])) {
      $subscriber = $this->subscribersRepository->findOneBy(['email' => $subscriberData['email']]);

      if (
        ($subscriberData['status'] === SubscriberEntity::STATUS_UNSUBSCRIBED)
        && ($subscriber instanceof SubscriberEntity)
        && ($subscriber->getStatus() === SubscriberEntity::STATUS_SUBSCRIBED)
      ) {
        $this->unsubscribesTracker->track(
          (int)$subscriber->getId(),
          StatisticsUnsubscribeEntity::SOURCE_MANAGE
        );
      }

      if ($subscriber && $this->linkTokens->verifyToken($subscriber, $token)) {
        if ($subscriberData['email'] !== Pages::DEMO_EMAIL) {
          $subscriber = $this->subscriberSaveController->createOrUpdate($subscriberData, $subscriber);
          $this->subscriberSaveController->updateCustomFields($this->filterOutEmptyMandatoryFields($subscriberData), $subscriber);
          $this->updateSubscriptions($subscriber, $subscriberData);
        }
      }
      $result = ['success' => true];
    }

    $this->urlHelper->redirectBack($result);
  }

  private function updateSubscriptions(SubscriberEntity $subscriber, array $subscriberData): void {
    $segmentsIds = [];
    if (isset($subscriberData['segments']) && is_array($subscriberData['segments'])) {
      $segmentsIds = $subscriberData['segments'];
    }

    // Unsubscribe from all other segments already subscribed to
    // but don't change disallowed segments
    foreach ($subscriber->getSubscriberSegments() as $subscriberSegment) {
      $segment = $subscriberSegment->getSegment();
      if (!$segment) {
        continue;
      }

      if (empty($segment->getDisplayInManageSubscriptionPage())) {
        continue;
      }
      if (!in_array($segment->getId(), $segmentsIds)) {
        $this->subscriberSegmentRepository->createOrUpdate(
          $subscriber,
          $segment,
          SubscriberEntity::STATUS_UNSUBSCRIBED
        );
      }
    }

    // Store new segments for notifications
    $subscriberSegments = $this->subscriberSegmentRepository->findBy([
      'status' => SubscriberEntity::STATUS_SUBSCRIBED,
      'subscriber' => $subscriber,
    ]);
    $currentSegmentIds = array_filter(array_map(function (SubscriberSegmentEntity $subscriberSegment): ?string {
      $segment = $subscriberSegment->getSegment();
      return $segment ? (string)$segment->getId() : null;
    }, $subscriberSegments));
    $newSegmentIds = array_diff($segmentsIds, $currentSegmentIds);

    foreach ($segmentsIds as $segmentId) {
      $segment = $this->segmentsRepository->findOneById($segmentId);
      if (!$segment) {
        continue;
      }
      // Allow subscribing only to allowed segments
      if (empty($segment->getDisplayInManageSubscriptionPage())) {
        continue;
      }
      $this->subscriberSegmentRepository->createOrUpdate(
        $subscriber,
        $segment,
        SubscriberEntity::STATUS_SUBSCRIBED
      );
    }

    if ($subscriber->getStatus() === SubscriberEntity::STATUS_SUBSCRIBED && $newSegmentIds) {
      $newSegments = $this->segmentsRepository->findBy(['id' => $newSegmentIds]);
      $this->newSubscriberNotificationMailer->send($subscriber, $newSegments);
      $this->welcomeScheduler->scheduleSubscriberWelcomeNotification(
        $subscriber->getId(),
        $newSegmentIds
      );
    }
  }

  private function filterOutEmptyMandatoryFields(array $subscriberData): array {
    $mandatory = $this->getMandatory();
    foreach ($mandatory as $name) {
      if (!isset($subscriberData[$name])) {
        continue;
      }
      if (is_array($subscriberData[$name]) && count(array_filter($subscriberData[$name])) === 0) {
        unset($subscriberData[$name]);
      }
      if (is_string($subscriberData[$name]) && strlen(trim($subscriberData[$name])) === 0) {
        unset($subscriberData[$name]);
      }
    }
    return $subscriberData;
  }

  /**
   * @return string[]
   */
  private function getMandatory(): array {
    $mandatory = [];
    $requiredCustomFields = $this->customFieldsRepository->findAll();
    foreach ($requiredCustomFields as $customField) {
      $params = $customField->getParams();
      if (
        is_array($params)
        && isset($params['required'])
        && $params['required']
      ) {
        $mandatory[] = 'cf_' . $customField->getId();
      }
    }
    return $mandatory;
  }
}
