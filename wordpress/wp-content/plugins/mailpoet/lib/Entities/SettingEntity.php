<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\UpdatedAtTrait;
use MailPoet\Util\Helpers;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;
use MailPoetVendor\Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="settings")
 */
class SettingEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use UpdatedAtTrait;

  /**
   * @ORM\Column(type="string")
   * @Assert\NotBlank()
   * @var string
   */
  private $name;

  /**
   * @ORM\Column(type="text", nullable=true)
   * @var string|null
   */
  private $value;

  /** @return string */
  public function getName() {
    return $this->name;
  }

  /** @param string $name */
  public function setName($name) {
    $this->name = $name;
  }

  /** @return mixed */
  public function getValue() {
    return $this->value !== null && is_serialized($this->value) ? unserialize($this->value) : $this->value;
  }

  /** @param mixed $value */
  public function setValue($value) {
    $value = Helpers::recursiveTrim($value);
    if (is_array($value)) {
      $value = serialize($value);
    }
    $this->value = $value;
  }
}
