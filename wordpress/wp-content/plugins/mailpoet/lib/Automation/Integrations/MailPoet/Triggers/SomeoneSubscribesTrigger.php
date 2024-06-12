<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Triggers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Integration\Trigger;
use MailPoet\Automation\Integrations\MailPoet\Payloads\SegmentPayload;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SegmentSubject;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberSegmentEntity;
use MailPoet\InvalidStateException;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;
use MailPoet\WP\Functions as WPFunctions;

class SomeoneSubscribesTrigger implements Trigger {
  const KEY = 'mailpoet:someone-subscribes';

  /** @var WPFunctions */
  private $wp;

  /** @var SegmentsRepository  */
  private $segmentsRepository;

  public function __construct(
    WPFunctions $wp,
    SegmentsRepository $segmentsRepository
  ) {
    $this->wp = $wp;
    $this->segmentsRepository = $segmentsRepository;
  }

  public function getKey(): string {
    return 'mailpoet:someone-subscribes';
  }

  public function getName(): string {
    // translators: automation trigger title
    return __('Someone subscribes', 'mailpoet');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'segment_ids' => Builder::array(Builder::number()),
    ]);
  }

  public function getSubjectKeys(): array {
    return [
      SubscriberSubject::KEY,
      SegmentSubject::KEY,
    ];
  }

  public function validate(StepValidationArgs $args): void {
  }

  public function registerHooks(): void {
    $this->wp->addAction('mailpoet_segment_subscribed', [$this, 'handleSubscription'], 10, 2);
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
    $segmentId = $args->getSinglePayloadByClass(SegmentPayload::class)->getId();
    $segment = $this->segmentsRepository->findOneById($segmentId);
    if (!$segment || $segment->getType() !== SegmentEntity::TYPE_DEFAULT) {
      return false;
    }

    // Triggers when no segment IDs defined (= any segment) or the current segment paylo.
    $triggerArgs = $args->getStep()->getArgs();
    $segmentIds = $triggerArgs['segment_ids'] ?? [];
    return !is_array($segmentIds) || !$segmentIds || in_array($segmentId, $segmentIds, true);
  }
}
