<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\UpdatedAtTrait;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="feature_flags", uniqueConstraints={@ORM\UniqueConstraint(name="name",columns={"name"})})
 */
class FeatureFlagEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use UpdatedAtTrait;

  /**
   * @ORM\Column(type="string", nullable=false, unique=true)
   * @var string
   */
  private $name;

  /**
   * @ORM\Column(type="boolean", nullable=true)
   * @var bool|null
   */
  private $value;

  /**
   * @param string $name
   * @param bool|null $value
   */
  public function __construct(
    $name,
    $value = null
  ) {
    $this->name = $name;
    $this->value = $value;
  }

  /** @return string */
  public function getName() {
    return $this->name;
  }

  /** @param string $name */
  public function setName($name) {
    $this->name = $name;
  }

  /** @return bool|null */
  public function getValue() {
    return $this->value;
  }

  /** @param bool|null $value */
  public function setValue($value) {
    $this->value = $value;
  }
}
