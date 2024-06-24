<?php declare(strict_types = 1);

namespace MailPoet\Tags;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\TagEntity;

/**
 * @extends Repository<TagEntity>
 */
class TagRepository extends Repository {
  protected function getEntityClassName() {
    return TagEntity::class;
  }

  public function createOrUpdate(array $data = []): TagEntity {
    if (!$data['name']) {
      throw new \InvalidArgumentException('Missing name');
    }
    $tag = $this->findOneBy([
      'name' => $data['name'],
    ]);
    if (!$tag) {
      $tag = new TagEntity($data['name']);
      $this->persist($tag);
    }

    try {
      $this->flush();
    } catch (\Exception $e) {
      throw new \RuntimeException("Error when saving tag " . $data['name']);
    }
    return $tag;
  }

  public function getSubscriberStatisticsCount(?string $status, bool $isDeleted): array {
    $qb = $this->entityManager->createQueryBuilder()
      ->select('t.id, t.name, COUNT(st) AS subscribersCount')
      ->from(TagEntity::class, 't')
      ->leftJoin('t.subscriberTags', 'st')
      ->join('st.subscriber', 's')
      ->groupBy('t.id')
      ->orderBy('t.name');

    if ($isDeleted) {
      $qb->andWhere('s.deletedAt IS NOT NULL');
    } else {
      $qb->andWhere('s.deletedAt IS NULL');
    }

    if ($status) {
      $qb->andWhere('s.status = :status')
        ->setParameter('status', $status);
    }

    return $qb->getQuery()->getArrayResult();
  }
}
