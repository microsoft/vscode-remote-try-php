<?php declare(strict_types = 1);

namespace MailPoet\AdminPages\Pages;

if (!defined('ABSPATH')) exit;


use MailPoet\AdminPages\AssetsController;
use MailPoet\AdminPages\PageRenderer;
use MailPoet\AutomaticEmails\AutomaticEmails;
use MailPoet\Automation\Engine\Data\AutomationTemplate;
use MailPoet\Automation\Engine\Data\AutomationTemplateCategory;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Segments\SegmentsSimpleListRepository;
use MailPoet\Settings\UserFlagsController;
use MailPoet\WP\Functions as WPFunctions;

class Automation {
  /** @var AssetsController */
  private $assetsController;

  private AutomaticEmails $automaticEmails;

  /** @var PageRenderer */
  private $pageRenderer;

  /** @var WPFunctions */
  private $wp;

  /** @var AutomationStorage */
  private $automationStorage;

  /** @var Registry  */
  private $registry;

  private NewslettersRepository $newslettersRepository;

  private SegmentsSimpleListRepository $segmentsListRepository;

  private UserFlagsController $userFlagsController;

  public function __construct(
    AssetsController $assetsController,
    AutomaticEmails $automaticEmails,
    PageRenderer $pageRenderer,
    WPFunctions $wp,
    AutomationStorage $automationStorage,
    Registry $registry,
    NewslettersRepository $newslettersRepository,
    SegmentsSimpleListRepository $segmentsListRepository,
    UserFlagsController $userFlagsController
  ) {
    $this->assetsController = $assetsController;
    $this->automaticEmails = $automaticEmails;
    $this->pageRenderer = $pageRenderer;
    $this->wp = $wp;
    $this->automationStorage = $automationStorage;
    $this->registry = $registry;
    $this->newslettersRepository = $newslettersRepository;
    $this->segmentsListRepository = $segmentsListRepository;
    $this->userFlagsController = $userFlagsController;
  }

  public function render() {
    global $wp_roles; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    $this->assetsController->setupAutomationListingDependencies();
    $this->pageRenderer->displayPage('automation.html', [
      'locale_full' => $this->wp->getLocale(),
      'api' => [
        'root' => rtrim($this->wp->escUrlRaw($this->wp->restUrl()), '/'),
        'nonce' => $this->wp->wpCreateNonce('wp_rest'),
      ],
      'automationCount' => $this->automationStorage->getAutomationCount(),
      'legacyAutomationCount' => $this->newslettersRepository->countBy([
        'type' => [NewsletterEntity::TYPE_WELCOME, NewsletterEntity::TYPE_AUTOMATIC],
      ]),
      'templates' => array_map(
        function(AutomationTemplate $template): array {
          return $template->toArray();
        },
        array_values($this->registry->getTemplates())
      ),
      'template_categories' => array_map(
        function (AutomationTemplateCategory $category): array {
          return [
            'slug' => $category->getSlug(),
            'name' => $category->getName(),
          ];
        },
        array_values($this->registry->getTemplateCategories())
      ),
      'registry' => $this->buildRegistry(),
      'context' => $this->buildContext(),
      'segments' => $this->segmentsListRepository->getListWithSubscribedSubscribersCounts(),
      'roles' => $wp_roles->get_names() + ['mailpoet_all' => __('In any WordPress role', 'mailpoet')],
      'automatic_emails' => $this->automaticEmails->getAutomaticEmails(),
      'legacy_automations_notice_dismissed' => (bool)$this->userFlagsController->get('legacy_automations_notice_dismissed'),
    ]);
  }

  private function buildRegistry(): array {
    $steps = [];
    foreach ($this->registry->getSteps() as $key => $step) {
      $steps[$key] = [
        'key' => $step->getKey(),
        'name' => $step->getName(),
        'args_schema' => $step->getArgsSchema()->toArray(),
      ];
    }

    $subjects = [];
    foreach ($this->registry->getSubjects() as $key => $subject) {
      $subjects[$key] = [
        'key' => $subject->getKey(),
        'name' => $subject->getName(),
        'args_schema' => $subject->getArgsSchema()->toArray(),
        'field_keys' => array_map(function ($field) {
          return $field->getKey();
        }, $subject->getFields()),
      ];
    }

    return [
      'steps' => $steps,
      'subjects' => $subjects,
    ];
  }

  private function buildContext(): array {
    $data = [];
    foreach ($this->registry->getContextFactories() as $key => $factory) {
      $data[$key] = $factory();
    }
    return $data;
  }
}
