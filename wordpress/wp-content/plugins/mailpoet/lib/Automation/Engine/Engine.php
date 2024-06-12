<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\API\API;
use MailPoet\Automation\Engine\Control\StepHandler;
use MailPoet\Automation\Engine\Control\TriggerHandler;
use MailPoet\Automation\Engine\Endpoints\Automations\AutomationsCreateFromTemplateEndpoint;
use MailPoet\Automation\Engine\Endpoints\Automations\AutomationsDeleteEndpoint;
use MailPoet\Automation\Engine\Endpoints\Automations\AutomationsDuplicateEndpoint;
use MailPoet\Automation\Engine\Endpoints\Automations\AutomationsGetEndpoint;
use MailPoet\Automation\Engine\Endpoints\Automations\AutomationsPutEndpoint;
use MailPoet\Automation\Engine\Endpoints\Automations\AutomationTemplateGetEndpoint;
use MailPoet\Automation\Engine\Endpoints\Automations\AutomationTemplatesGetEndpoint;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Automation\Integrations\Core\CoreIntegration;
use MailPoet\Automation\Integrations\WordPress\WordPressIntegration;

class Engine {
  const CAPABILITY_MANAGE_AUTOMATIONS = 'mailpoet_manage_automations';

  /** @var API */
  private $api;

  /** @var CoreIntegration */
  private $coreIntegration;

  /** @var WordPressIntegration */
  private $wordPressIntegration;

  /** @var Registry */
  private $registry;

  /** @var StepHandler */
  private $stepHandler;

  /** @var TriggerHandler */
  private $triggerHandler;

  /** @var WordPress */
  private $wordPress;

  /** @var AutomationStorage */
  private $automationStorage;

  public function __construct(
    API $api,
    CoreIntegration $coreIntegration,
    WordPressIntegration $wordPressIntegration,
    Registry $registry,
    StepHandler $stepHandler,
    TriggerHandler $triggerHandler,
    WordPress $wordPress,
    AutomationStorage $automationStorage
  ) {
    $this->api = $api;
    $this->coreIntegration = $coreIntegration;
    $this->wordPressIntegration = $wordPressIntegration;
    $this->registry = $registry;
    $this->stepHandler = $stepHandler;
    $this->triggerHandler = $triggerHandler;
    $this->wordPress = $wordPress;
    $this->automationStorage = $automationStorage;
  }

  public function initialize(): void {
    $this->registerApiRoutes();

    $this->api->initialize();
    $this->stepHandler->initialize();
    $this->triggerHandler->initialize();

    $this->coreIntegration->register($this->registry);
    $this->wordPressIntegration->register($this->registry);
    $this->wordPress->doAction(Hooks::INITIALIZE, [$this->registry]);
    $this->registerActiveTriggerHooks();
  }

  private function registerApiRoutes(): void {
    $this->wordPress->addAction(Hooks::API_INITIALIZE, function (API $api) {
      $api->registerGetRoute('automations', AutomationsGetEndpoint::class);
      $api->registerPutRoute('automations/(?P<id>\d+)', AutomationsPutEndpoint::class);
      $api->registerDeleteRoute('automations/(?P<id>\d+)', AutomationsDeleteEndpoint::class);
      $api->registerPostRoute('automations/(?P<id>\d+)/duplicate', AutomationsDuplicateEndpoint::class);
      $api->registerPostRoute('automations/create-from-template', AutomationsCreateFromTemplateEndpoint::class);
      $api->registerGetRoute('automation-templates', AutomationTemplatesGetEndpoint::class);
      $api->registerGetRoute('automation-templates/(?P<slug>.+)', AutomationTemplateGetEndpoint::class);
    });
  }

  private function registerActiveTriggerHooks(): void {
    $triggerKeys = $this->automationStorage->getActiveTriggerKeys();
    foreach ($triggerKeys as $triggerKey) {
      $instance = $this->registry->getTrigger($triggerKey);
      if ($instance) {
        $instance->registerHooks();
      }
    }
  }
}
