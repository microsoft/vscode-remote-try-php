<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\SafeToOneAssociationLoadTrait;
use MailPoet\Doctrine\EntityTraits\UpdatedAtTrait;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;
use MailPoetVendor\Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="newsletter_templates")
 */
class NewsletterTemplateEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use UpdatedAtTrait;
  use SafeToOneAssociationLoadTrait;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\NewsletterEntity")
   * @var NewsletterEntity|null
   */
  private $newsletter;

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
  private $categories = '[]';

  /**
   * @ORM\Column(type="json", nullable=true)
   * @Assert\NotBlank()
   * @var array|null
   */
  private $body;

  /**
   * @ORM\Column(type="string", nullable=true)
   * @var string|null
   */
  private $thumbnail;

  /**
   * @ORM\Column(type="string", nullable=true)
   * @var string|null
   */
  private $thumbnailData;

  /**
   * @ORM\Column(type="boolean")
   * @var bool
   */
  private $readonly = false;

  public function __construct(
    string $name
  ) {
    $this->name = $name;
  }

  /**
   * @return NewsletterEntity|null
   */
  public function getNewsletter() {
    $this->safelyLoadToOneAssociation('newsletter');
    return $this->newsletter;
  }

  /**
   * @param NewsletterEntity|null $newsletter
   */
  public function setNewsletter($newsletter) {
    $this->newsletter = $newsletter;
  }

  public function getName(): string {
    return $this->name;
  }

  public function setName(string $name) {
    $this->name = $name;
  }

  public function getCategories(): string {
    return $this->categories;
  }

  public function setCategories(string $categories) {
    $this->categories = $categories;
  }

  /**
   * @return array|null
   */
  public function getBody() {
    return $this->body;
  }

  /**
   * @param array|null $body
   */
  public function setBody($body) {
    $this->body = $body;
  }

  /**
   * @return string|null
   */
  public function getThumbnail() {
    return $this->thumbnail;
  }

  /**
   * @param string|null $thumbnail
   */
  public function setThumbnail($thumbnail) {
    $this->thumbnail = $thumbnail;
  }

  public function getThumbnailData(): ?string {
    return $this->thumbnailData;
  }

  public function setThumbnailData(string $thumbnailData): void {
    $this->thumbnailData = $thumbnailData;
  }

  public function getReadonly(): bool {
    return $this->readonly;
  }

  public function setReadonly(bool $readonly) {
    $this->readonly = $readonly;
  }
}
