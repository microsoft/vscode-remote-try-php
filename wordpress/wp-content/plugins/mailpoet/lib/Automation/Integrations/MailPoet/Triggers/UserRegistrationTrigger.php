<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Triggers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Integration\Trigger;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\MailPoet\Payloads\SegmentPayload;
use MailPoet\Automation\Integrations\MailPoet\Payloads\SubscriberPayload;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SegmentSubject;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberSegmentEntity;
use MailPoet\InvalidStateException;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class UserRegistrationTrigger implements Trigger {
  const KEY = 'mailpoet:wp-user-registered';

  /** @var WordPress */
  private $wp;

  private $subscribersRepository;

  public function __construct(
    WordPress $wp,
    SubscribersRepository $subscribersRepository
  ) {
    $this->wp = $wp;
    $this->subscribersRepository = $subscribersRepository;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation trigger title
    return __('WordPress user registers', 'mailpoet');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'roles' => Builder::array(Builder::string()),
    ]);
  }

  public function getSubjectKeys(): array {
    return [
      SegmentSubject::KEY,
      SubscriberSubject::KEY,
    ];
  }

  public function validate(StepValidationArgs $args): void {
  }

  public function registerHooks(): void {
    $this->wp->addAction('mailpoet_segment_subscribed', [$this, 'handleSubscription']);
  }

  public function handleSubscription(SubscriberSegmentEntity $subscriberSegment): void {
    $segment = $subscriberSegment->getSegment();
    $subscriber = $subscriberSegment->getSubscriber();

    if (!$segment || !$subscriber) {
      throw new InvalidStateException();
    }

    $this->wp->doAction(Hooks::TRIGGER, $this, [
      new Subject(SegmentSubject::KEY, ['segment_id' => $segment->getId()]),
      new Subject(SubscriberSubject::KEY, ['subscriber_id' => $subscriber->getId()]),
    ]);
  }

  public function isTriggeredBy(StepRunArgs $args): bool {
    $segmentPayload = $args->getSinglePayloadByClass(SegmentPayload::class);
    if ($segmentPayload->getType() !== SegmentEntity::TYPE_WP_USERS) {
      return false;
    }

    $subscriberPayload = $args->getSinglePayloadByClass(SubscriberPayload::class);
    $this->subscribersRepository->refresh($subscriberPayload->getSubscriber());
    if (!$subscriberPayload->isWPUser()) {
      return false;
    }

    $user = $this->wp->getUserBy('id', (int)$subscriberPayload->getWpUserId());
    if (!$user) {
      return false;
    }

    $triggerArgs = $args->getStep()->getArgs();
    $roles = $triggerArgs['roles'] ?? [];
    return !is_array($roles) || !$roles || count(array_intersect($user->roles, $roles)) > 0;
  }
}
