<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Analytics;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\API;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Endpoints\AutomationFlowEndpoint;
use MailPoet\Automation\Integrations\MailPoet\Analytics\Endpoints\OverviewEndpoint;

class Analytics {

  /** @var WordPress */
  private $wordPress;

  public function __construct(
    WordPress $wordPress
  ) {
    $this->wordPress = $wordPress;
  }

  public function register(): void {
    $this->wordPress->addAction(Hooks::API_INITIALIZE, function (API $api) {
      $api->registerGetRoute('automation/analytics/automation_flow', AutomationFlowEndpoint::class);
      $api->registerGetRoute('automation/analytics/overview', OverviewEndpoint::class);
    });
  }
}
