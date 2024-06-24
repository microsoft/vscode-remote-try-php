<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Triggers\AbandonedCart;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Integration\Trigger;
use MailPoet\Automation\Engine\Storage\AutomationRunStorage;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SegmentSubject;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject;
use MailPoet\Automation\Integrations\WooCommerce\Payloads\AbandonedCartPayload;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\AbandonedCartSubject;
use MailPoet\Cron\Workers\Automations\AbandonedCartWorker;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;
use MailPoetVendor\Carbon\Carbon;

class AbandonedCartTrigger implements Trigger {

  const KEY = 'woocommerce:abandoned-cart';

  /** @var AbandonedCartHandler */
  private $abandonedCartHandler;

  /** @var WordPress */
  private $wp;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var AutomationRunStorage */
  private $automationRunStorage;

  public function __construct(
    AbandonedCartHandler $abandonedCartHandler,
    AutomationRunStorage $automationRunStorage,
    SegmentsRepository $segmentsRepository,
    WordPress $wp
  ) {
    $this->abandonedCartHandler = $abandonedCartHandler;
    $this->automationRunStorage = $automationRunStorage;
    $this->segmentsRepository = $segmentsRepository;
    $this->wp = $wp;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation trigger title
    return __('User abandons cart', 'mailpoet');
  }

  public function getSubjectKeys(): array {
    return [
      SubscriberSubject::KEY,
      AbandonedCartSubject::KEY,
      SegmentSubject::KEY,
    ];
  }

  public function registerHooks(): void {
    $this->abandonedCartHandler->registerHooks();
    $this->wp->addAction(
      AbandonedCartWorker::ACTION,
      [
        $this,
        'handle',
      ],
      10,
      4
    );
  }

  /**
   * @param SubscriberEntity $subscriber
   * @param int[] $productIds
   * @param \DateTime $lastAcivityAt
   * @return void
   */
  public function handle(
    SubscriberEntity $subscriber,
    array $productIds,
    \DateTime $lastAcivityAt
  ): void {

    if (!$productIds) {
      return;
    }

    $wooSegment = $this->segmentsRepository->getWooCommerceSegment();

    $subjects = [
      new Subject(AbandonedCartSubject::KEY, ['user_id' => $subscriber->getWpUserId(), 'last_activity_at' => $lastAcivityAt->format(\DateTime::W3C), 'product_ids' => $productIds]),
      new Subject(SubscriberSubject::KEY, ['subscriber_id' => $subscriber->getId()]),
      new Subject(SegmentSubject::KEY, ['segment_id' => $wooSegment->getId()]),
    ];
    $this->wp->doAction(Hooks::TRIGGER, $this, $subjects);
  }

  public function isTriggeredBy(StepRunArgs $args): bool {
    $abandonedCartSubject = $args->getSingleSubjectEntryByClass(AbandonedCartSubject::class);
    $abandonedCartPayload = $args->getSinglePayloadByClass(AbandonedCartPayload::class);
    $lastActivityAt = $abandonedCartPayload->getLastActivityAt();

    $compareDate = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'))->subMinutes($args->getStep()->getArgs()['wait']);
    if ($lastActivityAt > $compareDate) {
      return false;
    }
    $automation = $args->getAutomation();
    $existingRuns = $this->automationRunStorage->getCountByAutomationAndSubject(
      $automation,
      $abandonedCartSubject->getSubjectData()
    );
    if ($existingRuns) {
      return false;
    }

    return true;
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'wait' => Builder::integer()->required()->minimum(1)->default(30),
    ]);
  }

  public function validate(StepValidationArgs $args): void {
  }
}
