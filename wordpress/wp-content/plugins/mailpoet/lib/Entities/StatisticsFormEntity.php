<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\SafeToOneAssociationLoadTrait;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="statistics_forms")
 */
class StatisticsFormEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use SafeToOneAssociationLoadTrait;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\FormEntity")
   * @ORM\JoinColumn(name="form_id", referencedColumnName="id")
   * @var FormEntity|null
   */
  private $form;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\SubscriberEntity")
   * @ORM\JoinColumn(name="subscriber_id", referencedColumnName="id")
   * @var SubscriberEntity|null
   */
  private $subscriber;

  public function __construct(
    FormEntity $form,
    SubscriberEntity $subscriber
  ) {
    $this->form = $form;
    $this->subscriber = $subscriber;
  }

  public function getForm(): ?FormEntity {
    $this->safelyLoadToOneAssociation('form');
    return $this->form;
  }

  public function getSubscriber(): ?SubscriberEntity {
    $this->safelyLoadToOneAssociation('form');
    return $this->subscriber;
  }
}
