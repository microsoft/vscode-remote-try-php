<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\MailPoet\Actions\SendEmailAction;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Analytics;
use MailPoet\Automation\Integrations\MailPoet\Hooks\AutomationEditorLoadingHooks;
use MailPoet\Automation\Integrations\MailPoet\Hooks\CreateAutomationRunHook;
use MailPoet\Automation\Integrations\MailPoet\Subjects\NewsletterLinkSubject;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SegmentSubject;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject;
use MailPoet\Automation\Integrations\MailPoet\SubjectTransformers\CommentSubjectToSubscriberSubjectTransformer;
use MailPoet\Automation\Integrations\MailPoet\SubjectTransformers\OrderSubjectToSegmentSubjectTransformer;
use MailPoet\Automation\Integrations\MailPoet\SubjectTransformers\OrderSubjectToSubscriberSubjectTransformer;
use MailPoet\Automation\Integrations\MailPoet\SubjectTransformers\SubscriberSubjectToWordPressUserSubjectTransformer;
use MailPoet\Automation\Integrations\MailPoet\Templates\TemplatesFactory;
use MailPoet\Automation\Integrations\MailPoet\Triggers\SomeoneSubscribesTrigger;
use MailPoet\Automation\Integrations\MailPoet\Triggers\UserRegistrationTrigger;

class MailPoetIntegration implements Integration {
  /** @var ContextFactory */
  private $contextFactory;

  /** @var SegmentSubject */
  private $segmentSubject;

  /** @var SubscriberSubject */
  private $subscriberSubject;

  /** @var NewsletterLinkSubject */
  private $emailLinkSubject;

  /** @var SomeoneSubscribesTrigger */
  private $someoneSubscribesTrigger;

  /** @var UserRegistrationTrigger  */
  private $userRegistrationTrigger;

  /** @var SendEmailAction */
  private $sendEmailAction;

  /** @var AutomationEditorLoadingHooks  */
  private $automationEditorLoadingHooks;

  /** @var CreateAutomationRunHook */
  private $createAutomationRunHook;

  /** @var OrderSubjectToSubscriberSubjectTransformer */
  private $orderToSubscriberTransformer;

  /** @var OrderSubjectToSegmentSubjectTransformer */
  private $orderToSegmentTransformer;

  /** @var SubscriberSubjectToWordPressUserSubjectTransformer */
  private $subscriberToWordPressUserTransformer;

  /** @var CommentSubjectToSubscriberSubjectTransformer */
  private $commentToSubscriberTransformer;

  /** @var TemplatesFactory */
  private $templatesFactory;

  /** @var Analytics */
  private $registerAnalytics;

  /** @var WordPress */
  private $wordPress;

  public function __construct(
    ContextFactory $contextFactory,
    SegmentSubject $segmentSubject,
    SubscriberSubject $subscriberSubject,
    NewsletterLinkSubject $emailLinkSubject,
    OrderSubjectToSubscriberSubjectTransformer $orderToSubscriberTransformer,
    OrderSubjectToSegmentSubjectTransformer $orderToSegmentTransformer,
    SubscriberSubjectToWordPressUserSubjectTransformer $subscriberToWordPressUserTransformer,
    CommentSubjectToSubscriberSubjectTransformer $commentToSubscriberTransformer,
    SomeoneSubscribesTrigger $someoneSubscribesTrigger,
    UserRegistrationTrigger $userRegistrationTrigger,
    SendEmailAction $sendEmailAction,
    AutomationEditorLoadingHooks $automationEditorLoadingHooks,
    CreateAutomationRunHook $createAutomationRunHook,
    TemplatesFactory $templatesFactory,
    Analytics $registerAnalytics,
    WordPress $wordPress
  ) {
    $this->contextFactory = $contextFactory;
    $this->segmentSubject = $segmentSubject;
    $this->subscriberSubject = $subscriberSubject;
    $this->emailLinkSubject = $emailLinkSubject;
    $this->orderToSubscriberTransformer = $orderToSubscriberTransformer;
    $this->orderToSegmentTransformer = $orderToSegmentTransformer;
    $this->subscriberToWordPressUserTransformer = $subscriberToWordPressUserTransformer;
    $this->commentToSubscriberTransformer = $commentToSubscriberTransformer;
    $this->someoneSubscribesTrigger = $someoneSubscribesTrigger;
    $this->userRegistrationTrigger = $userRegistrationTrigger;
    $this->sendEmailAction = $sendEmailAction;
    $this->automationEditorLoadingHooks = $automationEditorLoadingHooks;
    $this->createAutomationRunHook = $createAutomationRunHook;
    $this->templatesFactory = $templatesFactory;
    $this->registerAnalytics = $registerAnalytics;
    $this->wordPress = $wordPress;
  }

  public function register(Registry $registry): void {
    $registry->addContextFactory('mailpoet', function () {
      return $this->contextFactory->getContextData();
    });

    $registry->addSubject($this->segmentSubject);
    $registry->addSubject($this->subscriberSubject);
    $registry->addSubject($this->emailLinkSubject);
    $registry->addTrigger($this->someoneSubscribesTrigger);
    $registry->addTrigger($this->userRegistrationTrigger);
    $registry->addAction($this->sendEmailAction);
    $registry->addSubjectTransformer($this->orderToSubscriberTransformer);
    $registry->addSubjectTransformer($this->orderToSegmentTransformer);
    $registry->addSubjectTransformer($this->subscriberToWordPressUserTransformer);
    $registry->addSubjectTransformer($this->commentToSubscriberTransformer);

    foreach ($this->templatesFactory->createTemplates() as $template) {
      $registry->addTemplate($template);
    }

    // sync step args (subject, preheader, etc.) to email settings
    $registry->onBeforeAutomationStepSave(
      [$this->sendEmailAction, 'saveEmailSettings'],
      $this->sendEmailAction->getKey()
    );

    // execute send email step progress when email is sent
    $this->wordPress->addAction('mailpoet_automation_email_sent', [$this->sendEmailAction, 'handleEmailSent']);

    $this->automationEditorLoadingHooks->init();
    $this->createAutomationRunHook->init();

    $this->registerAnalytics->register();
  }
}
