<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Data;

if (!defined('ABSPATH')) exit;


class AutomationTemplateCategory {
  /** @var string */
  private $slug;

  /** @var string */
  private $name;

  public function __construct(
    string $slug,
    string $name
  ) {
    $this->slug = $slug;
    $this->name = $name;
  }

  public function getSlug(): string {
    return $this->slug;
  }

  public function getName(): string {
    return $this->name;
  }
}
