<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\SafeToOneAssociationLoadTrait;
use MailPoet\Doctrine\EntityTraits\UpdatedAtTrait;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="newsletter_posts")
 */
class NewsletterPostEntity {
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
   * @ORM\Column(type="integer")
   * @var int
   */
  private $postId;

  public function __construct(
    NewsletterEntity $newsletter,
    int $postId
  ) {
    $this->newsletter = $newsletter;
    $this->postId = $postId;
  }

  /**
   * @return NewsletterEntity|null
   */
  public function getNewsletter() {
    $this->safelyLoadToOneAssociation('newsletter');
    return $this->newsletter;
  }

  /**
   * @return int
   */
  public function getPostId(): int {
    return $this->postId;
  }
}
