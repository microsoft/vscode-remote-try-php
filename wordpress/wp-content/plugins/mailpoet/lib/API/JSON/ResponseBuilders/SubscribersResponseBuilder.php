<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON\ResponseBuilders;

if (!defined('ABSPATH')) exit;


use MailPoet\CustomFields\CustomFieldsRepository;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberCustomFieldEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Statistics\StatisticsUnsubscribesRepository;
use MailPoet\Subscribers\SubscriberCustomFieldRepository;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class SubscribersResponseBuilder {
  const DATE_FORMAT = 'Y-m-d H:i:s';

  /** @var StatisticsUnsubscribesRepository */
  private $statisticsUnsubscribesRepository;

  /** @var CustomFieldsRepository */
  private $customFieldsRepository;

  /** @var SubscriberCustomFieldRepository */
  private $subscriberCustomFieldRepository;

  /** @var EntityManager */
  private $entityManager;

  public function __construct(
    EntityManager $entityManager,
    CustomFieldsRepository $customFieldsRepository,
    SubscriberCustomFieldRepository $subscriberCustomFieldRepository,
    StatisticsUnsubscribesRepository $statisticsUnsubscribesRepository
  ) {
    $this->statisticsUnsubscribesRepository = $statisticsUnsubscribesRepository;
    $this->customFieldsRepository = $customFieldsRepository;
    $this->subscriberCustomFieldRepository = $subscriberCustomFieldRepository;
    $this->entityManager = $entityManager;
  }

  public function buildForListing(array $subscribers): array {
    $this->prefetchRelations($subscribers);
    $data = [];
    foreach ($subscribers as $subscriber) {
      $data[] = $this->buildListingItem($subscriber);
    }
    return $data;
  }

  private function buildListingItem(SubscriberEntity $subscriber): array {
    return [
      'id' => (string)$subscriber->getId(), // (string) for BC
      'email' => $subscriber->getEmail(),
      'first_name' => $subscriber->getFirstName(),
      'last_name' => $subscriber->getLastName(),
      'subscriptions' => $this->buildSubscriptions($subscriber),
      'status' => $subscriber->getStatus(),
      'count_confirmations' => $subscriber->getConfirmationsCount(),
      'wp_user_id' => $subscriber->getWpUserId(),
      'is_woocommerce_user' => $subscriber->getIsWoocommerceUser(),
      'created_at' => ($createdAt = $subscriber->getCreatedAt()) ? $createdAt->format(self::DATE_FORMAT) : null,
      'last_subscribed_at' => ($lastSubscribedAt = $subscriber->getLastSubscribedAt()) ? $lastSubscribedAt->format(self::DATE_FORMAT) : null,
      'engagement_score' => $subscriber->getEngagementScore(),
      'tags' => $this->buildTags($subscriber),
    ];
  }

  public function build(SubscriberEntity $subscriberEntity): array {
    $data = [
      'id' => (string)$subscriberEntity->getId(),
      'wp_user_id' => $subscriberEntity->getWpUserId(),
      'is_woocommerce_user' => $subscriberEntity->getIsWoocommerceUser(),
      'subscriptions' => $this->buildSubscriptions($subscriberEntity),
      'unsubscribes' => $this->buildUnsubscribes($subscriberEntity),
      'status' => $subscriberEntity->getStatus(),
      'last_name' => $subscriberEntity->getLastName(),
      'first_name' => $subscriberEntity->getFirstName(),
      'email' => $subscriberEntity->getEmail(),
      'created_at' => ($createdAt = $subscriberEntity->getCreatedAt()) ? $createdAt->format(self::DATE_FORMAT) : null,
      'updated_at' => ($updatedAt = $subscriberEntity->getUpdatedAt()) ? $updatedAt->format(self::DATE_FORMAT) : null,
      'deleted_at' => ($deletedAt = $subscriberEntity->getDeletedAt()) ? $deletedAt->format(self::DATE_FORMAT) : null,
      'subscribed_ip' => $subscriberEntity->getSubscribedIp(),
      'confirmed_ip' => $subscriberEntity->getConfirmedIp(),
      'confirmed_at' => ($confirmedAt = $subscriberEntity->getConfirmedAt()) ? $confirmedAt->format(self::DATE_FORMAT) : null,
      'last_subscribed_at' => ($lastSubscribedAt = $subscriberEntity->getLastSubscribedAt()) ? $lastSubscribedAt->format(self::DATE_FORMAT) : null,
      'unconfirmed_data' => $subscriberEntity->getUnconfirmedData(),
      'source' => $subscriberEntity->getSource(),
      'count_confirmations' => $subscriberEntity->getConfirmationsCount(),
      'unsubscribe_token' => $subscriberEntity->getUnsubscribeToken(),
      'link_token' => $subscriberEntity->getLinkToken(),
      'tags' => $this->buildTags($subscriberEntity),
    ];

    return $this->buildCustomFields($subscriberEntity, $data);
  }

  private function buildSubscriptions(SubscriberEntity $subscriberEntity): array {
    $result = [];
    foreach ($subscriberEntity->getSubscriberSegments() as $subscriberSegment) {
      $segment = $subscriberSegment->getSegment();
      if ($segment instanceof SegmentEntity) {
        $result[] = [
          'id' => $subscriberSegment->getId(),
          'subscriber_id' => (string)$subscriberEntity->getId(),
          'created_at' => ($createdAt = $subscriberSegment->getCreatedAt()) ? $createdAt->format(self::DATE_FORMAT) : null,
          'segment_id' => (string)$segment->getId(),
          'status' => $subscriberSegment->getStatus(),
          'updated_at' => $subscriberSegment->getUpdatedAt()->format(self::DATE_FORMAT),
        ];
      }
    }
    return $result;
  }

  private function buildUnsubscribes(SubscriberEntity $subscriberEntity): array {
    $unsubscribes = $this->statisticsUnsubscribesRepository->findBy([
      'subscriber' => $subscriberEntity,
    ], [
      'createdAt' => 'desc',
    ]);
    $result = [];
    foreach ($unsubscribes as $unsubscribe) {
      $mapped = [
        'source' => $unsubscribe->getSource(),
        'meta' => $unsubscribe->getMeta(),
        'createdAt' => $unsubscribe->getCreatedAt(),
      ];
      $newsletter = $unsubscribe->getNewsletter();
      if ($newsletter instanceof NewsletterEntity) {
        $mapped['newsletterId'] = $newsletter->getId();
        $mapped['newsletterSubject'] = $newsletter->getSubject();
      }
      $result[] = $mapped;
    }
    return $result;
  }

  private function buildCustomFields(SubscriberEntity $subscriberEntity, array $data): array {
    $customFields = $this->customFieldsRepository->findAll();

    foreach ($customFields as $customField) {
      $subscriberCustomField = $this->subscriberCustomFieldRepository->findOneBy(
        ['subscriber' => $subscriberEntity, 'customField' => $customField]
      );
      if ($subscriberCustomField instanceof SubscriberCustomFieldEntity) {
        $data['cf_' . $customField->getId()] = $subscriberCustomField->getValue();
      }
    }
    return $data;
  }

  private function buildTags(SubscriberEntity $subscriber): array {
    $result = [];
    foreach ($subscriber->getSubscriberTags() as $subscriberTag) {
      $tag = $subscriberTag->getTag();
      if (!$tag) {
        continue;
      }
      $result[] = [
        'id' => $subscriberTag->getId(),
        'subscriber_id' => (string)$subscriber->getId(),
        'tag_id' => (string)$tag->getId(),
        'created_at' => ($createdAt = $subscriberTag->getCreatedAt()) ? $createdAt->format(self::DATE_FORMAT) : null,
        'updated_at' => $subscriberTag->getUpdatedAt()->format(self::DATE_FORMAT),
        'name' => $tag->getName(),
      ];
    }
    return $result;
  }

  /**
   * @param SubscriberEntity[] $subscribers
   */
  private function prefetchRelations(array $subscribers): void {
    // Prefetch subscriptions
    $this->entityManager->createQueryBuilder()
      ->select('PARTIAL s.{id}, ssg, sg')
      ->from(SubscriberEntity::class, 's')
      ->leftJoin('s.subscriberSegments', 'ssg')
      ->leftJoin('ssg.segment', 'sg')
      ->where('s.id IN (:subscribers)')
      ->setParameter('subscribers', $subscribers)
      ->getQuery()
      ->getResult();
    // Prefetch tags
    $this->entityManager->createQueryBuilder()
      ->select('PARTIAL s.{id}, st, t')
      ->from(SubscriberEntity::class, 's')
      ->leftJoin('s.subscriberTags', 'st')
      ->leftJoin('st.tag', 't')
      ->where('s.id IN (:subscribers)')
      ->setParameter('subscribers', $subscribers)
      ->getQuery()
      ->getResult();
  }
}
