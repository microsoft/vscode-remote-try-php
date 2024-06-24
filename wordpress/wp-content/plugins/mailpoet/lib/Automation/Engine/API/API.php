<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\API;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\API as MailPoetApi;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\WordPress;

class API extends MailPoetApi {
  /** @var MailPoetApi */
  private $api;

  /** @var WordPress */
  private $wordPress;

  public function __construct(
    MailPoetApi $api,
    WordPress $wordPress
  ) {
    $this->api = $api;
    $this->wordPress = $wordPress;
  }

  public function initialize(): void {
    $this->wordPress->addAction(MailPoetApi::REST_API_INIT_ACTION, function () {
      $this->wordPress->doAction(Hooks::API_INITIALIZE, [$this]);
    });
  }

  public function registerGetRoute(string $route, string $endpoint): void {
    $this->api->registerGetRoute($route, $endpoint);
  }

  public function registerPostRoute(string $route, string $endpoint): void {
    $this->api->registerPostRoute($route, $endpoint);
  }

  public function registerPutRoute(string $route, string $endpoint): void {
    $this->api->registerPutRoute($route, $endpoint);
  }

  public function registerPatchRoute(string $route, string $endpoint): void {
    $this->api->registerPatchRoute($route, $endpoint);
  }

  public function registerDeleteRoute(string $route, string $endpoint): void {
    $this->api->registerDeleteRoute($route, $endpoint);
  }
}
