<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\CustomFieldEntity;
use MailPoet\Entities\SubscriberCustomFieldEntity;
use MailPoet\Entities\SubscriberEntity;

/**
 * @extends Repository<SubscriberCustomFieldEntity>
 */
class SubscriberCustomFieldRepository extends Repository {
  protected function getEntityClassName() {
    return SubscriberCustomFieldEntity::class;
  }

  /**
   * @param string|array|null $value
   */
  public function createOrUpdate(SubscriberEntity $subscriber, CustomFieldEntity $customField, $value): SubscriberCustomFieldEntity {
    $subscriberCustomField = $this->findOneBy(['subscriber' => $subscriber, 'customField' => $customField]);
    if ($subscriberCustomField instanceof SubscriberCustomFieldEntity) {
      $subscriberCustomField->setValue($value);
    } else {
      $subscriberCustomField = new SubscriberCustomFieldEntity($subscriber, $customField, $value);
      $this->entityManager->persist($subscriberCustomField);
      $subscriber->getSubscriberCustomFields()->add($subscriberCustomField);
    }
    $this->entityManager->flush();
    return $subscriberCustomField;
  }
}
