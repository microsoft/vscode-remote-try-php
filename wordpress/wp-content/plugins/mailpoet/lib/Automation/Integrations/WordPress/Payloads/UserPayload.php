<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WordPress\Payloads;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration\Payload;
use WP_User;

class UserPayload implements Payload {
  /** @var WP_User */
  private $user;

  public function __construct(
    WP_User $user
  ) {
    $this->user = $user;
  }

  public function getId(): int {
    return $this->user->ID;
  }

  public function getUser(): WP_User {
    return $this->user;
  }

  public function getEmail(): ?string {
    return $this->user->user_email ?: null;
  }

  public function exists(): bool {
    return $this->user->exists();
  }

  /** @return string[] */
  public function getRoles(): array {
    return $this->user->roles;
  }
}
