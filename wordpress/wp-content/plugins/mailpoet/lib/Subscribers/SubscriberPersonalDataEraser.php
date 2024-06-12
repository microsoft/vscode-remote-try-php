<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SubscriberEntity;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class SubscriberPersonalDataEraser {
  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var EntityManager */
  private $entityManager;

  /** @var SubscriberCustomFieldRepository */
  private $subscriberCustomFieldRepository;

  public function __construct(
    SubscribersRepository $subscribersRepository,
    EntityManager $entityManager,
    SubscriberCustomFieldRepository $subscriberCustomFieldRepository
  ) {
    $this->subscribersRepository = $subscribersRepository;
    $this->entityManager = $entityManager;
    $this->subscriberCustomFieldRepository = $subscriberCustomFieldRepository;
  }

  public function erase($email) {
    if (empty($email)) {
      return [
        'items_removed' => false,
        'items_retained' => false,
        'messages' => [],
        'done' => true,
      ];
    }
    $subscriber = $this->subscribersRepository->findOneBy(['email' => trim($email)]);
    $itemRemoved = false;
    $itemsRetained = true;
    if ($subscriber) {
      $this->eraseCustomFields($subscriber);
      $this->anonymizeSubscriberData($subscriber);
      $itemRemoved = true;
      $itemsRetained = false;
    }

    return [
      'items_removed' => $itemRemoved,
      'items_retained' => $itemsRetained,
      'messages' => [],
      'done' => true,
    ];
  }

  private function eraseCustomFields(SubscriberEntity $subscriber) {
    $customFields = $this->subscriberCustomFieldRepository->findBy(['subscriber' => $subscriber]);
    foreach ($customFields as $customField) {
      $customField->setValue('');
      $this->entityManager->persist($customField);
    }
    $this->entityManager->flush();
  }

  private function anonymizeSubscriberData(SubscriberEntity $subscriber) {
    $subscriber->setEmail(sprintf('deleted-%s@site.invalid', bin2hex(random_bytes(12)))); // phpcs:ignore
    $subscriber->setFirstName('Anonymous');
    $subscriber->setLastName('Anonymous');
    $subscriber->setStatus(SubscriberEntity::STATUS_UNSUBSCRIBED);
    $subscriber->setSubscribedIp('0.0.0.0');
    $subscriber->setConfirmedIp('0.0.0.0');
    $subscriber->setUnconfirmedData('');
    $this->entityManager->persist($subscriber);
    $this->entityManager->flush();
  }
}
