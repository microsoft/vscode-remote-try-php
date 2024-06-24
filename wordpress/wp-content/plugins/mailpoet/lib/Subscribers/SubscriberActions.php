<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SubscriberEntity;
use MailPoet\Newsletter\Scheduler\WelcomeScheduler;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\Helpers;

class SubscriberActions {

  /** @var SettingsController */
  private $settings;

  /** @var NewSubscriberNotificationMailer */
  private $newSubscriberNotificationMailer;

  /** @var ConfirmationEmailMailer */
  private $confirmationEmailMailer;

  /** @var WelcomeScheduler */
  private $welcomeScheduler;

  /** @var SubscriberSaveController */
  private $subscriberSaveController;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var SubscriberSegmentRepository */
  private $subscriberSegmentRepository;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  public function __construct(
    SettingsController $settings,
    NewSubscriberNotificationMailer $newSubscriberNotificationMailer,
    ConfirmationEmailMailer $confirmationEmailMailer,
    WelcomeScheduler $welcomeScheduler,
    SegmentsRepository $segmentsRepository,
    SubscriberSaveController $subscriberSaveController,
    SubscribersRepository $subscribersRepository,
    SubscriberSegmentRepository $subscriberSegmentRepository
  ) {
    $this->settings = $settings;
    $this->newSubscriberNotificationMailer = $newSubscriberNotificationMailer;
    $this->confirmationEmailMailer = $confirmationEmailMailer;
    $this->welcomeScheduler = $welcomeScheduler;
    $this->subscriberSaveController = $subscriberSaveController;
    $this->subscribersRepository = $subscribersRepository;
    $this->subscriberSegmentRepository = $subscriberSegmentRepository;
    $this->segmentsRepository = $segmentsRepository;
  }

  /**
   * Returns SubscriberEntity and associative array with some metadata related to the subscription (e.g. ['confirmationEmailResult' => $exception])
   * @return array{0: SubscriberEntity, 1: array{confirmationEmailResult: bool|\Exception}}
   */
  public function subscribe($subscriberData = [], $segmentIds = []): array {
    // filter out keys from the subscriber_data array
    // that should not be editable when subscribing
    $subscriberData = $this->subscriberSaveController->filterOutReservedColumns($subscriberData);

    $signupConfirmationEnabled = (bool)$this->settings->get(
      'signup_confirmation.enabled'
    );

    $subscriberData['subscribed_ip'] = Helpers::getIP();

    $subscriber = $this->subscribersRepository->findOneBy(['email' => $subscriberData['email']]);
    if (!$subscriber && !isset($subscriberData['source'])) {
      $subscriberData['source'] = Source::FORM;
    }

    if (!$subscriber || !$signupConfirmationEnabled) {
      // create new subscriber or update if no confirmation is required
      $subscriber = $this->subscriberSaveController->createOrUpdate($subscriberData, $subscriber);
      // custom fields should use the same approach as the subscriber main data that means to wait on confirmation
      $this->subscriberSaveController->updateCustomFields($subscriberData, $subscriber);
    } else {
      // store subscriber data to be updated after confirmation
      $unconfirmedData = $this->subscriberSaveController->filterOutReservedColumns($subscriberData);
      $unconfirmedData = json_encode($unconfirmedData);
      $subscriber->setUnconfirmedData($unconfirmedData ?: null);
    }

    // restore trashed subscriber
    if ($subscriber->getDeletedAt()) {
      $subscriber->setDeletedAt(null);
    }

    // set status depending on signup confirmation setting
    if ($subscriber->getStatus() !== SubscriberEntity::STATUS_SUBSCRIBED) {
      if ($signupConfirmationEnabled === true) {
        $subscriber->setStatus(SubscriberEntity::STATUS_UNCONFIRMED);
      } else {
        $subscriber->setStatus(SubscriberEntity::STATUS_SUBSCRIBED);
      }
    }

    $this->subscribersRepository->flush();

    $metaData = ['confirmationEmailResult' => false];
    // link subscriber to segments
    $segments = $this->segmentsRepository->findBy(['id' => $segmentIds]);
    $this->subscriberSegmentRepository->subscribeToSegments($subscriber, $segments);

    try {
      $metaData['confirmationEmailResult'] = $this->confirmationEmailMailer->sendConfirmationEmailOnce($subscriber);
    } catch (\Exception $e) {
      $metaData['confirmationEmailResult'] = $e;
    }

    // We want to send the notification on subscribe only when signupConfirmation is disabled
    if ($signupConfirmationEnabled === false && $subscriber->getStatus() === SubscriberEntity::STATUS_SUBSCRIBED) {
      $this->newSubscriberNotificationMailer->send($subscriber, $this->segmentsRepository->findBy(['id' => $segmentIds]));

      $this->welcomeScheduler->scheduleSubscriberWelcomeNotification(
        $subscriber->getId(),
        $segmentIds
      );
    }

    return [$subscriber, $metaData];
  }
}
