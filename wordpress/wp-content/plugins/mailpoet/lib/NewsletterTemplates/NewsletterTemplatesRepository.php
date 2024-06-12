<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\NewsletterTemplates;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterTemplateEntity;

/**
 * @extends Repository<NewsletterTemplateEntity>
 */
class NewsletterTemplatesRepository extends Repository {
  const RECENTLY_SENT_CATEGORIES = '["recent"]';
  const RECENTLY_SENT_COUNT = 12;

  protected function getEntityClassName() {
    return NewsletterTemplateEntity::class;
  }

  /**
   * @return NewsletterTemplateEntity[]
   */
  public function findAllForListing(): array {
    return $this->doctrineRepository->createQueryBuilder('nt')
      ->select('PARTIAL nt.{id,categories,thumbnail,name,readonly}')
      ->addOrderBy('nt.readonly', 'ASC')
      ->addOrderBy('nt.createdAt', 'DESC')
      ->addOrderBy('nt.id', 'DESC')
      ->getQuery()
      ->getResult();
  }

  public function createOrUpdate(array $data): NewsletterTemplateEntity {
    $template = !empty($data['newsletter_id'])
      ? $this->findOneBy(['newsletter' => (int)$data['newsletter_id']])
      : null;

    if (!$template) {
      $template = new NewsletterTemplateEntity($data['name'] ?? '');
      $this->entityManager->persist($template);
    }

    if (isset($data['newsletter_id'])) {
      $template->setNewsletter($this->entityManager->getReference(NewsletterEntity::class, (int)$data['newsletter_id']));
    }

    if (isset($data['name'])) {
      $template->setName($data['name']);
    }

    if (isset($data['thumbnail'])) {
      // Backward compatibility for importing templates exported from older versions
      if (strpos($data['thumbnail'], 'data:image') === 0) {
        $data['thumbnail_data'] = $data['thumbnail'];
      } else {
        $template->setThumbnail($data['thumbnail']);
      }
    }

    if (isset($data['thumbnail_data'])) {
      $template->setThumbnailData($data['thumbnail_data']);
    }

    if (isset($data['body'])) {
      $template->setBody(json_decode($data['body'], true));
    }

    if (isset($data['categories'])) {
      $template->setCategories($data['categories']);
    }

    $this->entityManager->flush();
    return $template;
  }

  public function cleanRecentlySent() {
    // fetch 'RECENTLY_SENT_COUNT' of most recent template IDs in 'RECENTLY_SENT_CATEGORIES'
    $recentIds = $this->doctrineRepository->createQueryBuilder('nt')
      ->select('nt.id')
      ->where('nt.categories = :categories')
      ->setParameter('categories', self::RECENTLY_SENT_CATEGORIES)
      ->orderBy('nt.id', 'DESC')
      ->setMaxResults(self::RECENTLY_SENT_COUNT)
      ->getQuery()
      ->getResult();

    // delete all 'RECENTLY_SENT_CATEGORIES' templates except the latest ones selected above
    $this->entityManager->createQueryBuilder()
      ->delete(NewsletterTemplateEntity::class, 'nt')
      ->where('nt.categories = :categories')
      ->andWhere('nt.id NOT IN (:recentIds)')
      ->setParameter('categories', self::RECENTLY_SENT_CATEGORIES)
      ->setParameter('recentIds', array_column($recentIds, 'id'))
      ->getQuery()
      ->execute();

    // delete was done via DQL, make sure the entities are also detached from the entity manager
    $this->detachAll(function (NewsletterTemplateEntity $entity) use ($recentIds) {
      return $entity->getCategories() === self::RECENTLY_SENT_CATEGORIES && !in_array($entity->getId(), $recentIds, true);
    });
  }

  public function getRecentlySentCount(): int {
    return (int)$this->doctrineRepository->createQueryBuilder('nt')
      ->select('COUNT(nt.id)')
      ->where('nt.categories = :categories')
      ->setParameter('categories', self::RECENTLY_SENT_CATEGORIES)
      ->getQuery()
      ->getSingleScalarResult();
  }

  public function getIdsOfEditableTemplates(): array {
    $result = $this->doctrineRepository->createQueryBuilder('nt')
      ->select('nt.id')
      ->where('nt.readonly = :readonly')
      ->setParameter('readonly', false)
      ->getQuery()
      ->getArrayResult();
    return array_column($result, 'id');
  }
}
