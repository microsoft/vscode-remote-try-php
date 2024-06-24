<?php declare(strict_types = 1);

namespace MailPoet\API\REST;

if (!defined('ABSPATH')) exit;


use MailPoet\Validator\Schema;

abstract class Endpoint {
  abstract public function handle(Request $request): Response;

  public function checkPermissions(): bool {
    return current_user_can('admin');
  }

  /** @return array<string, Schema> */
  public static function getRequestSchema(): array {
    return [];
  }
}
