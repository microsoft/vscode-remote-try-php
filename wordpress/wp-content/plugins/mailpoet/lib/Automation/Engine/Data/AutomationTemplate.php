<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Data;

if (!defined('ABSPATH')) exit;


class AutomationTemplate {
  public const TYPE_DEFAULT = 'default';
  public const TYPE_PREMIUM = 'premium';
  public const TYPE_COMING_SOON = 'coming-soon';

  /** @var string */
  private $slug;

  /** @var string */
  private $category;

  /** @var string */
  private $name;

  /** @var string */
  private $description;

  /** @var callable(): Automation */
  private $automationFactory;

  /** @var array<string, int|bool> */
  private $requiredCapabilities;

  /** @var string */
  private $type;

  /**
   * @param callable(): Automation $automationFactory
   * @param array<string, int|bool> $requiredCapabilities
   */
  public function __construct(
    string $slug,
    string $category,
    string $name,
    string $description,
    callable $automationFactory,
    array $requiredCapabilities = [],
    string $type = self::TYPE_DEFAULT
  ) {
    $this->slug = $slug;
    $this->category = $category;
    $this->name = $name;
    $this->description = $description;
    $this->automationFactory = $automationFactory;
    $this->requiredCapabilities = $requiredCapabilities;
    $this->type = $type;
  }

  public function getSlug(): string {
    return $this->slug;
  }

  public function getName(): string {
    return $this->name;
  }

  public function getCategory(): string {
    return $this->category;
  }

  public function getType(): string {
    return $this->type;
  }

  public function getDescription(): string {
    return $this->description;
  }

  /** @return array<string, int|bool> */
  public function getRequiredCapabilities(): array {
    return $this->requiredCapabilities;
  }

  public function createAutomation(): Automation {
    return ($this->automationFactory)();
  }

  public function toArray(): array {
    return [
      'slug' => $this->getSlug(),
      'name' => $this->getName(),
      'category' => $this->getCategory(),
      'type' => $this->getType(),
      'required_capabilities' => $this->getRequiredCapabilities(),
      'description' => $this->getDescription(),
    ];
  }
}
