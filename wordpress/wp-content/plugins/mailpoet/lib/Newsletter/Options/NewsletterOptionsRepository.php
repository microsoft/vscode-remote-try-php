<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Options;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterOptionEntity;
use MailPoet\Entities\NewsletterOptionFieldEntity;

/**
 * @extends Repository<NewsletterOptionEntity>
 */
class NewsletterOptionsRepository extends Repository {
  protected function getEntityClassName() {
    return NewsletterOptionEntity::class;
  }

  /**
   * @return NewsletterOptionEntity[]
   */
  public function findWelcomeNotificationsForSegments(array $segmentIds): array {
    return $this->entityManager->createQueryBuilder()
      ->select('no')
      ->from(NewsletterOptionEntity::class, 'no')
      ->join('no.newsletter', 'n')
      ->join('no.optionField', 'nof')
      ->where('n.deletedAt IS NULL')
      ->andWhere('n.type = :typeWelcome')
      ->andWhere('nof.name = :nameSegment')
      ->andWhere('no.value IN (:segmentIds)')
      ->setParameter('typeWelcome', NewsletterEntity::TYPE_WELCOME)
      ->setParameter('nameSegment', NewsletterOptionFieldEntity::NAME_SEGMENT)
      ->setParameter('segmentIds', $segmentIds)
      ->getQuery()->getResult();
  }

  /**
   * @return NewsletterOptionEntity[]
   */
  public function findAutomaticEmailsForSegments(array $segmentIds): array {
    return $this->entityManager->createQueryBuilder()
      ->select('no')
      ->from(NewsletterOptionEntity::class, 'no')
      ->join('no.newsletter', 'n')
      ->join('no.optionField', 'nof')
      ->where('n.deletedAt IS NULL')
      ->andWhere('n.type = :typeAutomatic')
      ->andWhere('nof.name = :nameSegment')
      ->andWhere('no.value IN (:segmentIds)')
      ->setParameter('typeAutomatic', NewsletterEntity::TYPE_AUTOMATIC)
      ->setParameter('nameSegment', NewsletterOptionFieldEntity::NAME_SEGMENT)
      ->setParameter('segmentIds', $segmentIds)
      ->getQuery()->getResult();
  }

  /** @param int[] $ids */
  public function deleteByNewsletterIds(array $ids): void {
    $this->entityManager->createQueryBuilder()
      ->delete(NewsletterOptionEntity::class, 'o')
      ->where('o.newsletter IN (:ids)')
      ->setParameter('ids', $ids)
      ->getQuery()
      ->execute();

    // delete was done via DQL, make sure the entities are also detached from the entity manager
    $this->detachAll(function (NewsletterOptionEntity $entity) use ($ids) {
      $newsletter = $entity->getNewsletter();
      return $newsletter && in_array($newsletter->getId(), $ids, true);
    });
  }
}
