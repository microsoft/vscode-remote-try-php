<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\MP\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\ResponseBuilders\SubscribersResponseBuilder;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\StatisticsUnsubscribeEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Listing\ListingDefinition;
use MailPoet\Newsletter\Scheduler\WelcomeScheduler;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Settings\SettingsController;
use MailPoet\Statistics\Track\Unsubscribes;
use MailPoet\Subscribers\ConfirmationEmailMailer;
use MailPoet\Subscribers\NewSubscriberNotificationMailer;
use MailPoet\Subscribers\RequiredCustomFieldValidator;
use MailPoet\Subscribers\Source;
use MailPoet\Subscribers\SubscriberListingRepository;
use MailPoet\Subscribers\SubscriberSaveController;
use MailPoet\Subscribers\SubscriberSegmentRepository;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;

class Subscribers {
  const CONTEXT_SUBSCRIBE = 'subscribe';
  const CONTEXT_UNSUBSCRIBE = 'unsubscribe';

  /** @var SettingsController */
  private $settings;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var SubscriberSegmentRepository */
  private $subscribersSegmentRepository;

  /** @var ConfirmationEmailMailer */
  private $confirmationEmailMailer;

  /** @var WelcomeScheduler */
  private $welcomeScheduler;

  /** @var SubscribersResponseBuilder */
  private $subscribersResponseBuilder;

  /** @var NewSubscriberNotificationMailer */
  private $newSubscriberNotificationMailer;

  /** @var SubscriberSaveController */
  private $subscriberSaveController;

  /** @var RequiredCustomFieldValidator */
  private $requiredCustomFieldsValidator;

  /** @var WPFunctions */
  private $wp;

  /** @var SubscriberListingRepository */
  private $subscriberListingRepository;

  /** @var Unsubscribes */
  private $unsubscribesTracker;

  public function __construct (
    ConfirmationEmailMailer $confirmationEmailMailer,
    NewSubscriberNotificationMailer $newSubscriberNotificationMailer,
    SegmentsRepository $segmentsRepository,
    SettingsController $settings,
    SubscriberSegmentRepository $subscriberSegmentRepository,
    SubscribersRepository $subscribersRepository,
    SubscriberSaveController $subscriberSaveController,
    SubscribersResponseBuilder $subscribersResponseBuilder,
    WelcomeScheduler $welcomeScheduler,
    RequiredCustomFieldValidator $requiredCustomFieldsValidator,
    SubscriberListingRepository $subscriberListingRepository,
    WPFunctions $wp,
    Unsubscribes $unsubscribesTracker
  ) {
    $this->confirmationEmailMailer = $confirmationEmailMailer;
    $this->newSubscriberNotificationMailer = $newSubscriberNotificationMailer;
    $this->segmentsRepository = $segmentsRepository;
    $this->settings = $settings;
    $this->subscribersSegmentRepository = $subscriberSegmentRepository;
    $this->subscribersRepository = $subscribersRepository;
    $this->subscriberSaveController = $subscriberSaveController;
    $this->subscribersResponseBuilder = $subscribersResponseBuilder;
    $this->welcomeScheduler = $welcomeScheduler;
    $this->requiredCustomFieldsValidator = $requiredCustomFieldsValidator;
    $this->wp = $wp;
    $this->subscriberListingRepository = $subscriberListingRepository;
    $this->unsubscribesTracker = $unsubscribesTracker;
  }

  public function getSubscriber($subscriberIdOrEmail): array {
    $subscriber = $this->findSubscriber($subscriberIdOrEmail);
    return $this->subscribersResponseBuilder->build($subscriber);
  }

  public function addSubscriber(array $data, array $listIds = [], array $options = []): array {
    $sendConfirmationEmail = !(isset($options['send_confirmation_email']) && $options['send_confirmation_email'] === false);
    $scheduleWelcomeEmail = !(isset($options['schedule_welcome_email']) && $options['schedule_welcome_email'] === false);
    $skipSubscriberNotification = (isset($options['skip_subscriber_notification']) && $options['skip_subscriber_notification'] === true);

    // throw exception when subscriber email is missing
    if (empty($data['email'])) {
      throw new APIException(
        __('Subscriber email address is required.', 'mailpoet'),
        APIException::EMAIL_ADDRESS_REQUIRED
      );
    }

    // throw exception when subscriber already exists
    if ($this->subscribersRepository->findOneBy(['email' => $data['email']])) {
      throw new APIException(
        __('This subscriber already exists.', 'mailpoet'),
        APIException::SUBSCRIBER_EXISTS
      );
    }

    [$defaultFields, $customFields] = $this->extractCustomFieldsFromFromSubscriberData($data);

    $this->requiredCustomFieldsValidator->validate($customFields);

    // filter out all incoming data that we don't want to change, like status ...
    $defaultFields = array_intersect_key($defaultFields, array_flip(['email', 'first_name', 'last_name', 'subscribed_ip']));

    if (empty($defaultFields['subscribed_ip'])) {
      $defaultFields['subscribed_ip'] = Helpers::getIP();
    }
    $defaultFields['source'] = Source::API;

    try {
      $subscriberEntity = $this->subscriberSaveController->createOrUpdate($defaultFields, null);
    } catch (\Exception $e) {
      throw new APIException(
      // translators: %s is an error message.
        sprintf(__('Failed to add subscriber: %s', 'mailpoet'), $e->getMessage()),
        APIException::FAILED_TO_SAVE_SUBSCRIBER
      );
    }

    try {
      $this->subscriberSaveController->updateCustomFields($customFields, $subscriberEntity);
    } catch (\Exception $e) {
      throw new APIException(
      // translators: %s is an error message
        sprintf(__('Failed to save subscriber custom fields: %s', 'mailpoet'), $e->getMessage()),
        APIException::FAILED_TO_SAVE_SUBSCRIBER
      );
    }

    // subscribe to segments and optionally: 1) send confirmation email, 2) schedule welcome email(s)
    if (!empty($listIds)) {
      $this->subscribeToLists($subscriberEntity->getId(), $listIds, [
        'send_confirmation_email' => $sendConfirmationEmail,
        'schedule_welcome_email' => $scheduleWelcomeEmail,
        'skip_subscriber_notification' => $skipSubscriberNotification,
      ]);
    }
    return $this->subscribersResponseBuilder->build($subscriberEntity);
  }

  /**
   * @throws APIException
   */
  public function subscribeToLists(
    $subscriberId,
    array $listIds,
    array $options = []
  ): array {
    $scheduleWelcomeEmail = !((isset($options['schedule_welcome_email']) && $options['schedule_welcome_email'] === false));
    $sendConfirmationEmail = !((isset($options['send_confirmation_email']) && $options['send_confirmation_email'] === false));
    $skipSubscriberNotification = isset($options['skip_subscriber_notification']) && $options['skip_subscriber_notification'] === true;
    $signupConfirmationEnabled = (bool)$this->settings->get('signup_confirmation.enabled');

    $this->checkSubscriberAndListParams($subscriberId, $listIds);
    $subscriber = $this->findSubscriber($subscriberId);
    $foundSegments = $this->getAndValidateSegments($listIds, self::CONTEXT_SUBSCRIBE);

    // restore trashed subscriber
    if ($subscriber->getDeletedAt()) {
      $subscriber->setDeletedAt(null);
    }

    $this->subscribersSegmentRepository->subscribeToSegments($subscriber, $foundSegments);

    // set status depending on signup confirmation setting
    if ($subscriber->getStatus() !== SubscriberEntity::STATUS_SUBSCRIBED) {
      if ($signupConfirmationEnabled === true) {
        $subscriber->setStatus(SubscriberEntity::STATUS_UNCONFIRMED);
      } else {
        $subscriber->setStatus(SubscriberEntity::STATUS_SUBSCRIBED);
      }
      try {
        $this->subscribersRepository->flush();
      } catch (\Exception $e) {
        throw new APIException(
          // translators: %s is the error message
          sprintf(__('Failed to save a status of a subscriber : %s', 'mailpoet'), $e->getMessage()),
          APIException::FAILED_TO_SAVE_SUBSCRIBER
        );
      }

      // when global status changes to subscribed, fire subscribed hook for all subscribed segments
      /** @var SubscriberEntity $subscriber - From some reason PHPStan evaluates $subscriber->getStatus() as mixed */
      if ($subscriber->getStatus() === SubscriberEntity::STATUS_SUBSCRIBED) {
        $subscriberSegments = $subscriber->getSubscriberSegments();
        foreach ($subscriberSegments as $subscriberSegment) {
          if ($subscriberSegment->getStatus() === SubscriberEntity::STATUS_SUBSCRIBED) {
            $this->wp->doAction('mailpoet_segment_subscribed', $subscriberSegment);
          }
        }
      }
    }

    // schedule welcome email
    $foundSegmentsIds = array_map(
      function(SegmentEntity $segment) {
        return $segment->getId();
      },
      $foundSegments
    );
    if ($scheduleWelcomeEmail && $subscriber->getStatus() === SubscriberEntity::STATUS_SUBSCRIBED) {
      $this->_scheduleWelcomeNotification($subscriber, $foundSegmentsIds);
    }

    // send confirmation email
    if ($sendConfirmationEmail) {
      $this->_sendConfirmationEmail($subscriber);
    }

    if (!$skipSubscriberNotification && ($subscriber->getStatus() === SubscriberEntity::STATUS_SUBSCRIBED)) {
      $this->newSubscriberNotificationMailer->send($subscriber, $this->segmentsRepository->findBy(['id' => $foundSegmentsIds]));
    }

    $this->subscribersRepository->refresh($subscriber);
    return $this->subscribersResponseBuilder->build($subscriber);
  }

  public function unsubscribe($subscriberIdOrEmail): array {
    $this->checkSubscriberParam($subscriberIdOrEmail);
    $subscriber = $this->findSubscriber($subscriberIdOrEmail);

    if ($subscriber->getStatus() === SubscriberEntity::STATUS_UNSUBSCRIBED) {
      throw new APIException(__('This subscriber is already unsubscribed.', 'mailpoet'), APIException::SUBSCRIBER_ALREADY_UNSUBSCRIBED);
    }

    $this->unsubscribesTracker->track(
      (int)$subscriber->getId(),
      StatisticsUnsubscribeEntity::SOURCE_MP_API
    );

    $subscriber->setStatus(SubscriberEntity::STATUS_UNSUBSCRIBED);
    $this->subscribersRepository->persist($subscriber);
    $this->subscribersRepository->flush();

    $this->subscribersSegmentRepository->unsubscribeFromSegments($subscriber);

    return $this->subscribersResponseBuilder->build($subscriber);
  }

  public function unsubscribeFromLists($subscriberIdOrEmail, array $listIds): array {
    $this->checkSubscriberAndListParams($subscriberIdOrEmail, $listIds);
    $subscriber = $this->findSubscriber($subscriberIdOrEmail);
    $foundSegments = $this->getAndValidateSegments($listIds, self::CONTEXT_UNSUBSCRIBE);
    $this->subscribersSegmentRepository->unsubscribeFromSegments($subscriber, $foundSegments);

    return $this->subscribersResponseBuilder->build($subscriber);
  }

  public function getSubscribers(array $filter, int $limit, int $offset): array {
    $listingDefinition = $this->buildListingDefinition($filter, $limit, $offset);
    $subscribers = $this->subscriberListingRepository->getData($listingDefinition);
    $result = [];
    foreach ($subscribers as $subscriber) {
      $result[] = $this->subscribersResponseBuilder->build($subscriber);
    }
    return $result;
  }

  public function getSubscribersCount(array $filter): int {
    $listingDefinition = $this->buildListingDefinition($filter);
    return $this->subscriberListingRepository->getCount($listingDefinition);
  }

  /**
   * @param array $filter {
   *     Filters to retrieve subscribers.
   *
   *     @type string        $status       One of values: subscribed, unconfirmed, unsubscribed, inactive, bounced
   *     @type int           $listId       id of a list or dynamic segment
   *     @type \DateTime|int $minUpdatedAt DateTime object or timestamp of last update of subscriber.
   * }
   */
  private function buildListingDefinition(array $filter, int $limit = 50, int $offset = 0): ListingDefinition {
    $group = isset($filter['status']) && is_string($filter['status']) ? $filter['status'] : null;
    $listingFilters = [];
    // Set filtering by listId
    if (isset($filter['listId']) && is_int($filter['listId'])) {
      $listingFilters['segment'] = $filter['listId'];
    }
    // Set filtering by minimal updatedAt
    if (isset($filter['minUpdatedAt'])) {
      if ($filter['minUpdatedAt'] instanceof \DateTime) {
        $listingFilters['minUpdatedAt'] = $filter['minUpdatedAt'];
      } elseif (is_int($filter['minUpdatedAt'])) {
        $listingFilters['minUpdatedAt'] = Carbon::createFromTimestamp($filter['minUpdatedAt']);
      }
    }

    return new ListingDefinition($group, $listingFilters, null, [], 'id', 'asc', $offset, $limit);
  }

  /**
   * @throws APIException
   */
  protected function _scheduleWelcomeNotification(SubscriberEntity $subscriber, array $segments) {
    try {
      $this->welcomeScheduler->scheduleSubscriberWelcomeNotification($subscriber->getId(), $segments);
    } catch (\Throwable $e) {
      throw new APIException(
        // translators: %s is an error message
        sprintf(__('Subscriber added, but welcome email failed to send: %s', 'mailpoet'), $e->getMessage()),
        APIException::WELCOME_FAILED_TO_SEND
      );
    }
  }

  /**
   * @throws APIException
   */
  protected function _sendConfirmationEmail(SubscriberEntity $subscriberEntity) {
    try {
      $this->confirmationEmailMailer->sendConfirmationEmailOnce($subscriberEntity);
    } catch (\Exception $e) {
      throw new APIException(
        // translators: %s is the error message
        sprintf(__('Subscriber added to lists, but confirmation email failed to send: %s', 'mailpoet'), strtolower($e->getMessage())),
        APIException::CONFIRMATION_FAILED_TO_SEND
      );
    }
  }

  /**
   * @throws APIException
   */
  private function checkSubscriberAndListParams($subscriberIdOrEmail, array $listIds): void {
    if (empty($listIds)) {
      throw new APIException(__('At least one segment ID is required.', 'mailpoet'), APIException::SEGMENT_REQUIRED);
    }
    $this->checkSubscriberParam($subscriberIdOrEmail);
  }

  /**
   * @throws APIException
   */
  private function checkSubscriberParam($subscriberIdOrEmail): void {
    if (empty($subscriberIdOrEmail)) {
      throw new APIException(__('A subscriber is required.', 'mailpoet'), APIException::SUBSCRIBER_NOT_EXISTS);
    }
  }

  /**
   * @throws APIException
   */
  private function findSubscriber($subscriberIdOrEmail): SubscriberEntity {
    // throw exception when subscriber does not exist
    $subscriber = null;
    if (is_int($subscriberIdOrEmail) || (string)(int)$subscriberIdOrEmail === $subscriberIdOrEmail) {
      $subscriber = $this->subscribersRepository->findOneById($subscriberIdOrEmail);
    } else if (strlen(trim($subscriberIdOrEmail)) > 0) {
      $subscriber = $this->subscribersRepository->findOneBy(['email' => $subscriberIdOrEmail]);
    }

    if (!$subscriber) {
      throw new APIException(__('This subscriber does not exist.', 'mailpoet'), APIException::SUBSCRIBER_NOT_EXISTS);
    }

    return $subscriber;
  }

  /**
   * @return SegmentEntity[]
   * @throws APIException
   */
  private function getAndValidateSegments(array $listIds, string $context): array {
    // throw exception when none of the segments exist
    $foundSegments = $this->segmentsRepository->findBy(['id' => $listIds]);
    if (!$foundSegments) {
      $exception = _n('This list does not exist.', 'These lists do not exist.', count($listIds), 'mailpoet');
      throw new APIException($exception, APIException::LIST_NOT_EXISTS);
    }

    // throw exception when trying to subscribe to WP Users or WooCommerce Customers segments
    $foundSegmentsIds = [];
    foreach ($foundSegments as $foundSegment) {
      if ($foundSegment->getType() === SegmentEntity::TYPE_WP_USERS) {
        if ($context === self::CONTEXT_SUBSCRIBE) {
          // translators: %d is the ID of the segment
          $message = __("Can't subscribe to a WordPress Users list with ID '%d'.", 'mailpoet');
        } else {
          // translators: %d is the ID of the segment
          $message = __("Can't unsubscribe from a WordPress Users list with ID '%d'.", 'mailpoet');
        }
        throw new APIException(sprintf($message, $foundSegment->getId()), APIException::SUBSCRIBING_TO_WP_LIST_NOT_ALLOWED);
      }
      if ($foundSegment->getType() === SegmentEntity::TYPE_WC_USERS) {
        if ($context === self::CONTEXT_SUBSCRIBE) {
          // translators: %d is the ID of the segment
          $message = __("Can't subscribe to a WooCommerce Customers list with ID '%d'.", 'mailpoet');
        } else {
          // translators: %d is the ID of the segment
          $message = __("Can't unsubscribe from a WooCommerce Customers list with ID '%d'.", 'mailpoet');
        }
        throw new APIException(sprintf($message, $foundSegment->getId()), APIException::SUBSCRIBING_TO_WC_LIST_NOT_ALLOWED);
      }
      if ($foundSegment->getType() !== SegmentEntity::TYPE_DEFAULT) {
        if ($context === self::CONTEXT_SUBSCRIBE) {
          // translators: %d is the ID of the segment
          $message = __("Can't subscribe to a list with ID '%d'.", 'mailpoet');
        } else {
          // translators: %d is the ID of the segment
          $message = __("Can't unsubscribe from a list with ID '%d'.", 'mailpoet');
        }
        throw new APIException(sprintf($message, $foundSegment->getId()), APIException::SUBSCRIBING_TO_LIST_NOT_ALLOWED);
      }
      $foundSegmentsIds[] = $foundSegment->getId();
    }

    // throw an exception when one or more segments do not exist
    if (count($foundSegmentsIds) !== count($listIds)) {
      $missingIds = array_values(array_diff($listIds, $foundSegmentsIds));
      $exception = sprintf(
        // translators: %s is the count of lists
        _n("List with ID '%s' does not exist.", "Lists with IDs '%s' do not exist.", count($missingIds), 'mailpoet'),
        implode(', ', $missingIds)
      );
      throw new APIException(sprintf($exception, implode(', ', $missingIds)), APIException::LIST_NOT_EXISTS);
    }

    return $foundSegments;
  }

  /**
   * Splits subscriber data into two arrays with basic data (index 0) and custom fields data (index 1)
   * @return array<int, array>
   */
  private function extractCustomFieldsFromFromSubscriberData($data): array {
    $customFields = [];
    foreach ($data as $key => $value) {
      if (strpos($key, 'cf_') === 0) {
        $customFields[$key] = $value;
        unset($data[$key]);
      }
    }
    return [$data, $customFields];
  }
}
