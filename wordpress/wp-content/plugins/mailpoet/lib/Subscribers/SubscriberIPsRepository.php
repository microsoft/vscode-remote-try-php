<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\SubscriberIPEntity;
use MailPoetVendor\Carbon\Carbon;

/**
 * @extends Repository<SubscriberIPEntity>
 */
class SubscriberIPsRepository extends Repository {
  protected function getEntityClassName() {
    return SubscriberIPEntity::class;
  }

  public function findOneByIPAndCreatedAtAfterTimeInSeconds(string $ip, int $seconds): ?SubscriberIPEntity {
    return $this->entityManager->createQueryBuilder()
      ->select('sip')
      ->from(SubscriberIPEntity::class, 'sip')
      ->where('sip.ip = :ip')
      ->andWhere('sip.createdAt >= :timeThreshold')
      ->setParameter('ip', $ip)
      ->setParameter('timeThreshold', (new Carbon())->subSeconds($seconds))
      ->setMaxResults(1)
      ->getQuery()
      ->getOneOrNullResult();
  }

  public function getCountByIPAndCreatedAtAfterTimeInSeconds(string $ip, int $seconds): int {
    return (int)$this->entityManager->createQueryBuilder()
      ->select('COUNT(sip)')
      ->from(SubscriberIPEntity::class, 'sip')
      ->where('sip.ip = :ip')
      ->andWhere('sip.createdAt >= :timeThreshold')
      ->setParameter('ip', $ip)
      ->setParameter('timeThreshold', (new Carbon())->subSeconds($seconds))
      ->getQuery()
      ->getSingleScalarResult();
  }

  public function deleteCreatedAtBeforeTimeInSeconds(int $seconds): int {
    return (int)$this->entityManager->createQueryBuilder()
      ->delete()
      ->from(SubscriberIPEntity::class, 'sip')
      ->where('sip.createdAt < :timeThreshold')
      ->setParameter('timeThreshold', (new Carbon())->subSeconds($seconds))
      ->getQuery()
      ->execute();
  }
}
