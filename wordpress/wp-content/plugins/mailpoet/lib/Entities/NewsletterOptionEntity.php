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
 * @ORM\Table(name="newsletter_option")
 */
class NewsletterOptionEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use UpdatedAtTrait;
  use SafeToOneAssociationLoadTrait;


  /**
   * @ORM\Column(type="text", nullable=true)
   * @var string|null
   */
  private $value;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\NewsletterEntity", inversedBy="options")
   * @var NewsletterEntity|null
   */
  private $newsletter;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\NewsletterOptionFieldEntity")
   * @var NewsletterOptionFieldEntity|null
   */
  private $optionField;

  public function __construct(
    NewsletterEntity $newsletter,
    NewsletterOptionFieldEntity $optionField
  ) {
    $this->newsletter = $newsletter;
    $this->optionField = $optionField;
  }

  /**
   * @return string|null
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * @return string|null
   */
  public function getName() {
    $optionField = $this->getOptionField();
    if ($optionField === null) {
      return null;
    }
    return $optionField->getName();
  }

  /**
   * @param string|null $value
   */
  public function setValue($value) {
    $this->value = $value;
  }

  /**
   * @return NewsletterEntity|null
   */
  public function getNewsletter() {
    $this->safelyLoadToOneAssociation('newsletter');
    return $this->newsletter;
  }

  /**
   * @return NewsletterOptionFieldEntity|null
   */
  public function getOptionField() {
    $this->safelyLoadToOneAssociation('optionField');
    return $this->optionField;
  }
}
