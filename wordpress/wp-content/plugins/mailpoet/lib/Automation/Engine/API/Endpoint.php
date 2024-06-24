<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\API;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\Endpoint as MailPoetEndpoint;
use MailPoet\Automation\Engine\Engine;

abstract class Endpoint extends MailPoetEndpoint {
  public function checkPermissions(): bool {
    return current_user_can(Engine::CAPABILITY_MANAGE_AUTOMATIONS);
  }
}
