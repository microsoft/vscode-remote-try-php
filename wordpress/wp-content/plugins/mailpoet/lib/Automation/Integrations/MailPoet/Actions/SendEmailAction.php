<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Actions;

if (!defined('ABSPATH')) exit;


use MailPoet\AutomaticEmails\WooCommerce\Events\AbandonedCart;
use MailPoet\Automation\Engine\Control\AutomationController;
use MailPoet\Automation\Engine\Control\StepRunController;
use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Exceptions\NotFoundException;
use MailPoet\Automation\Engine\Integration\Action;
use MailPoet\Automation\Engine\Integration\ValidationException;
use MailPoet\Automation\Integrations\MailPoet\Payloads\SegmentPayload;
use MailPoet\Automation\Integrations\MailPoet\Payloads\SubscriberPayload;
use MailPoet\Automation\Integrations\WooCommerce\Payloads\AbandonedCartPayload;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterOptionEntity;
use MailPoet\Entities\NewsletterOptionFieldEntity;
use MailPoet\Entities\ScheduledTaskSubscriberEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\InvalidStateException;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Options\NewsletterOptionFieldsRepository;
use MailPoet\Newsletter\Options\NewsletterOptionsRepository;
use MailPoet\Newsletter\Scheduler\AutomationEmailScheduler;
use MailPoet\Settings\SettingsController;
use MailPoet\Subscribers\SubscriberSegmentRepository;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;
use Throwable;

class SendEmailAction implements Action {
  const KEY = 'mailpoet:send-email';

  private const TRANSACTIONAL_TRIGGERS = [
    'woocommerce:order-status-changed',
    'woocommerce:order-created',
    'woocommerce:order-completed',
    'woocommerce:order-cancelled',
    'woocommerce:abandoned-cart',
    'woocommerce-subscriptions:subscription-created',
    'woocommerce-subscriptions:subscription-expired',
    'woocommerce-subscriptions:subscription-payment-failed',
    'woocommerce-subscriptions:subscription-renewed',
    'woocommerce-subscriptions:subscription-status-changed',
    'woocommerce-subscriptions:trial-ended',
    'woocommerce-subscriptions:trial-started',
  ];

  /** @var AutomationController */
  private $automationController;

  /** @var SettingsController */
  private $settings;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var SubscriberSegmentRepository */
  private $subscriberSegmentRepository;

  /** @var SubscribersRepository  */
  private $subscribersRepository;

  /** @var AutomationEmailScheduler */
  private $automationEmailScheduler;

  /** @var NewsletterOptionsRepository */
  private $newsletterOptionsRepository;

  /** @var NewsletterOptionFieldsRepository */
  private $newsletterOptionFieldsRepository;

  public function __construct(
    AutomationController $automationController,
    SettingsController $settings,
    NewslettersRepository $newslettersRepository,
    SubscriberSegmentRepository $subscriberSegmentRepository,
    SubscribersRepository $subscribersRepository,
    AutomationEmailScheduler $automationEmailScheduler,
    NewsletterOptionsRepository $newsletterOptionsRepository,
    NewsletterOptionFieldsRepository $newsletterOptionFieldsRepository
  ) {
    $this->automationController = $automationController;
    $this->settings = $settings;
    $this->newslettersRepository = $newslettersRepository;
    $this->subscriberSegmentRepository = $subscriberSegmentRepository;
    $this->subscribersRepository = $subscribersRepository;
    $this->automationEmailScheduler = $automationEmailScheduler;
    $this->newsletterOptionsRepository = $newsletterOptionsRepository;
    $this->newsletterOptionFieldsRepository = $newsletterOptionFieldsRepository;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation action title
    return __('Send email', 'mailpoet');
  }

  public function getArgsSchema(): ObjectSchema {
    $nameDefault = $this->settings->get('sender.name');
    $addressDefault = $this->settings->get('sender.address');
    $replyToNameDefault = $this->settings->get('reply_to.name');
    $replyToAddressDefault = $this->settings->get('reply_to.address');

    $nonEmptyString = Builder::string()->required()->minLength(1);
    return Builder::object([
      // required fields
      'email_id' => Builder::integer()->required(),
      'name' => $nonEmptyString->default(__('Send email', 'mailpoet')),
      'subject' => $nonEmptyString->default(__('Subject', 'mailpoet')),
      'preheader' => Builder::string()->required()->default(''),
      'sender_name' => $nonEmptyString->default($nameDefault),
      'sender_address' => $nonEmptyString->formatEmail()->default($addressDefault),

      // optional fields
      'reply_to_name' => ($replyToNameDefault && $replyToNameDefault !== $nameDefault)
        ? Builder::string()->minLength(1)->default($replyToNameDefault)
        : Builder::string()->minLength(1),
      'reply_to_address' => ($replyToAddressDefault && $replyToAddressDefault !== $addressDefault)
        ? Builder::string()->formatEmail()->default($replyToAddressDefault)
        : Builder::string()->formatEmail(),
      'ga_campaign' => Builder::string()->minLength(1),
    ]);
  }

  public function getSubjectKeys(): array {
    return [
      'mailpoet:subscriber',
    ];
  }

  public function validate(StepValidationArgs $args): void {
    try {
      $this->getEmailForStep($args->getStep());
    } catch (InvalidStateException $exception) {
      $emailId = $args->getStep()->getArgs()['email_id'] ?? '';
      if (empty($emailId)) {
        throw ValidationException::create()
          ->withError('email_id', __("Automation email not found.", 'mailpoet'));
      }
      throw ValidationException::create()
        ->withError(
          'email_id',
          // translators: %s is the ID of email.
          sprintf(__("Automation email with ID '%s' not found.", 'mailpoet'), $emailId)
        );
    }
  }

  public function run(StepRunArgs $args, StepRunController $controller): void {
    $newsletter = $this->getEmailForStep($args->getStep());
    $subscriber = $this->getSubscriber($args);

    // sync sending status with the automation step
    if (!$args->isFirstRun()) {
      $this->checkSendingStatus($newsletter, $subscriber);
      return;
    }

    $subscriberStatus = $subscriber->getStatus();
    if ($newsletter->getType() !== NewsletterEntity::TYPE_AUTOMATION_TRANSACTIONAL && $subscriberStatus !== SubscriberEntity::STATUS_SUBSCRIBED) {
      throw InvalidStateException::create()->withMessage(sprintf("Cannot schedule a newsletter for subscriber ID '%s' because their status is '%s'.", $subscriber->getId(), $subscriberStatus));
    }

    if ($subscriberStatus === SubscriberEntity::STATUS_BOUNCED) {
      throw InvalidStateException::create()->withMessage(sprintf("Cannot schedule an email for subscriber ID '%s' because their status is '%s'.", $subscriber->getId(), $subscriberStatus));
    }

    $meta = $this->getNewsletterMeta($args);
    try {
      $this->automationEmailScheduler->createSendingTask($newsletter, $subscriber, $meta);
    } catch (Throwable $e) {
      throw InvalidStateException::create()->withMessage('Could not create sending task.');
    }

    // schedule a progress run to sync email sending status to the automation step
    // (1 month is a timout, the progress will normally be executed after sending)
    $controller->scheduleProgress(time() + MONTH_IN_SECONDS);
  }

  /** @param mixed $data */
  public function handleEmailSent($data): void {
    if (!is_array($data)) {
      throw InvalidStateException::create()->withMessage(
        sprintf('Invalid automation step data. Array expected, got: %s', gettype($data))
      );
    }

    $runId = $data['run_id'] ?? null;
    if (!is_int($runId)) {
      throw InvalidStateException::create()->withMessage(
        sprintf("Invalid automation step data. Expected 'run_id' to be an integer, got: %s", gettype($runId))
      );
    }

    $stepId = $data['step_id'] ?? null;
    if (!is_string($stepId)) {
      throw InvalidStateException::create()->withMessage(
        sprintf("Invalid automation step data. Expected 'step_id' to be a string, got: %s", gettype($runId))
      );
    }

    $this->automationController->enqueueProgress($runId, $stepId);
  }

  private function checkSendingStatus(NewsletterEntity $newsletter, SubscriberEntity $subscriber): void {
    $scheduledTaskSubscriber = $this->automationEmailScheduler->getScheduledTaskSubscriber($newsletter, $subscriber);
    if (!$scheduledTaskSubscriber) {
      throw InvalidStateException::create()->withMessage('Email failed to schedule.');
    }

    // email sending failed
    if ($scheduledTaskSubscriber->getFailed() === ScheduledTaskSubscriberEntity::FAIL_STATUS_FAILED) {
      throw InvalidStateException::create()->withMessage(
        sprintf('Email failed to send. Error: %s', $scheduledTaskSubscriber->getError() ?: 'Unknown error')
      );
    }

    // email was never sent
    if ($scheduledTaskSubscriber->getProcessed() !== ScheduledTaskSubscriberEntity::STATUS_PROCESSED) {
      $error = 'Email sending process timed out.';
      $this->automationEmailScheduler->saveError($scheduledTaskSubscriber, $error);
      throw InvalidStateException::create()->withMessage($error);
    }

    // email was sent, complete the run
  }

  private function getNewsletterMeta(StepRunArgs $args): array {
    $meta = [
      'automation' => [
        'id' => $args->getAutomation()->getId(),
        'run_id' => $args->getAutomationRun()->getId(),
        'step_id' => $args->getStep()->getId(),
        'run_number' => $args->getRunNumber(),
      ],
    ];

    if ($this->automationHasAbandonedCartTrigger($args->getAutomation())) {
      $payload = $args->getSinglePayloadByClass(AbandonedCartPayload::class);
      $meta[AbandonedCart::TASK_META_NAME] = $payload->getProductIds();
    }

    return $meta;
  }

  private function getSubscriber(StepRunArgs $args): SubscriberEntity {
    $subscriberId = $args->getSinglePayloadByClass(SubscriberPayload::class)->getId();
    try {
      $segmentId = $args->getSinglePayloadByClass(SegmentPayload::class)->getId();
    } catch (NotFoundException $e) {
      $segmentId = null;
    }

    // Without segment, fetch subscriber by ID (needed e.g. for "mailpoet:custom-trigger").
    // Transactional emails don't need to be checked against segment, no matter if it's set.
    if (!$segmentId || $this->isTransactional($args->getStep(), $args->getAutomation())) {
      $subscriber = $this->subscribersRepository->findOneById($subscriberId);
      if (!$subscriber) {
        throw InvalidStateException::create();
      }
      return $subscriber;
    }

    // With segment, fetch subscriber segment and check if they are subscribed.
    $subscriberSegment = $this->subscriberSegmentRepository->findOneBy([
      'subscriber' => $subscriberId,
      'segment' => $segmentId,
      'status' => SubscriberEntity::STATUS_SUBSCRIBED,
    ]);

    if (!$subscriberSegment) {
      throw InvalidStateException::create()->withMessage(sprintf("Subscriber ID '%s' is not subscribed to segment ID '%s'.", $subscriberId, $segmentId));
    }

    $subscriber = $subscriberSegment->getSubscriber();
    if (!$subscriber) {
      throw InvalidStateException::create();
    }
    return $subscriber;
  }

  public function saveEmailSettings(Step $step, Automation $automation): void {
    $args = $step->getArgs();
    if (!isset($args['email_id']) || !$args['email_id']) {
      return;
    }

    $email = $this->getEmailForStep($step);
    $email->setType($this->isTransactional($step, $automation) ? NewsletterEntity::TYPE_AUTOMATION_TRANSACTIONAL : NewsletterEntity::TYPE_AUTOMATION);
    $email->setStatus(NewsletterEntity::STATUS_ACTIVE);
    $email->setSubject($args['subject'] ?? '');
    $email->setPreheader($args['preheader'] ?? '');
    $email->setSenderName($args['sender_name'] ?? '');
    $email->setSenderAddress($args['sender_address'] ?? '');
    $email->setReplyToName($args['reply_to_name'] ?? '');
    $email->setReplyToAddress($args['reply_to_address'] ?? '');
    $email->setGaCampaign($args['ga_campaign'] ?? '');
    $this->storeNewsletterOption(
      $email,
      NewsletterOptionFieldEntity::NAME_GROUP,
      $this->automationHasWooCommerceTrigger($automation) ? 'woocommerce' : null
    );
    $this->storeNewsletterOption(
      $email,
      NewsletterOptionFieldEntity::NAME_EVENT,
      $this->automationHasAbandonedCartTrigger($automation) ? 'woocommerce_abandoned_shopping_cart' : null
    );

    $this->newslettersRepository->persist($email);
    $this->newslettersRepository->flush();
  }

  private function storeNewsletterOption(NewsletterEntity $newsletter, string $optionName, string $optionValue = null): void {
    $options = $newsletter->getOptions()->toArray();
    foreach ($options as $key => $option) {
      if ($option->getName() === $optionName) {
        if ($optionValue) {
          $option->setValue($optionValue);
          return;
        }
        $newsletter->getOptions()->remove($key);
        $this->newsletterOptionsRepository->remove($option);
        return;
      }
    }

    if (!$optionValue) {
      return;
    }

    $field = $this->newsletterOptionFieldsRepository->findOneBy([
      'name' => $optionName,
      'newsletterType' => $newsletter->getType(),
    ]);
    if (!$field) {
      return;
    }
    $option = new NewsletterOptionEntity($newsletter, $field);
    $option->setValue($optionValue);
    $this->newsletterOptionsRepository->persist($option);
    $newsletter->getOptions()->add($option);
  }

  private function isTransactional(Step $step, Automation $automation): bool {
    $triggers = $automation->getTriggers();
    $transactionalTriggers = array_filter(
      $triggers,
      function(Step $step): bool {
        return in_array($step->getKey(), self::TRANSACTIONAL_TRIGGERS, true);
      }
    );

    if (!$triggers || count($transactionalTriggers) !== count($triggers)) {
      return false;
    }

    foreach ($transactionalTriggers as $trigger) {
      if (!in_array($step->getId(), $trigger->getNextStepIds(), true)) {
        return false;
      }
    }
    return true;
  }

  private function automationHasWooCommerceTrigger(Automation $automation): bool {
    return (bool)array_filter(
      $automation->getTriggers(),
      function(Step $step): bool {
        return strpos($step->getKey(), 'woocommerce:') === 0;
      }
    );
  }

  private function automationHasAbandonedCartTrigger(Automation $automation): bool {
    return (bool)array_filter(
      $automation->getTriggers(),
      function(Step $step): bool {
        return in_array($step->getKey(), ['woocommerce:abandoned-cart'], true);
      }
    );
  }

  private function getEmailForStep(Step $step): NewsletterEntity {
    $emailId = $step->getArgs()['email_id'] ?? null;
    if (!$emailId) {
      throw InvalidStateException::create();
    }

    $email = $this->newslettersRepository->findOneBy([
      'id' => $emailId,
    ]);
    if (!$email || !in_array($email->getType(), [NewsletterEntity::TYPE_AUTOMATION, NewsletterEntity::TYPE_AUTOMATION_TRANSACTIONAL], true)) {
      throw InvalidStateException::create()->withMessage(
        // translators: %s is the ID of email.
        sprintf(__("Automation email with ID '%s' not found.", 'mailpoet'), $emailId)
      );
    }
    return $email;
  }
}
