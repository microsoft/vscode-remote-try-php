<?php declare(strict_types = 1);

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\UpdatedAtTrait;
use MailPoetVendor\Doctrine\Common\Collections\ArrayCollection;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;
use MailPoetVendor\Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="tags")
 */
class TagEntity {
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
   * @ORM\Column(type="string")
   * @var string
   */
  private $description;

  /**
   * @ORM\OneToMany(targetEntity="MailPoet\Entities\SubscriberTagEntity", mappedBy="tag", fetch="EXTRA_LAZY")
   * @var ArrayCollection<int, SubscriberTagEntity>
   */
  private $subscriberTags;

  public function __construct(
    string $name,
    string $description = ''
  ) {
    $this->name = $name;
    $this->description = $description;
    $this->subscriberTags = new ArrayCollection();
  }

  public function getName(): string {
    return $this->name;
  }

  public function setName(string $name): void {
    $this->name = $name;
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function setDescription(string $description): void {
    $this->description = $description;
  }
}
