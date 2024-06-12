<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Logging;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\LogEntity;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\InvalidStateException;
use MailPoet\Util\Helpers;
use MailPoet\WP\Functions;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\DBAL\Driver\PDO\Connection;
use MailPoetVendor\Doctrine\ORM\EntityManager;

/**
 * @extends Repository<LogEntity>
 */
class LogRepository extends Repository {
  /** @var Functions */
  private $wp;

  public function __construct(
    EntityManager $entityManager,
    Functions $wp
  ) {
    parent::__construct($entityManager);
    $this->wp = $wp;
  }

  public function saveLog(LogEntity $log): void {
    // Save log entity using DBAL to avoid calling "flush()" on the entity manager.
    // Calling "flush()" can have unintended side effects, such as saving unwanted
    // changes or trying to save entities that were detached from the entity manager.
    $this->entityManager->getConnection()->insert(
      $this->entityManager->getClassMetadata(LogEntity::class)->getTableName(),
      [
        'name' => $log->getName(),
        'level' => $log->getLevel(),
        'message' => $log->getMessage(),
        'raw_message' => $log->getRawMessage(),
        'context' => json_encode($log->getContext()),
        'created_at' => (
          $log->getCreatedAt() ?? Carbon::createFromTimestamp($this->wp->currentTime('timestamp'))
        )->format('Y-m-d H:i:s'),
      ],
    );

    // sync the changes with the entity manager
    if ($this->entityManager->isOpen()) {
      $lastInsertId = (int)$this->entityManager->getConnection()->lastInsertId();
      $log->setId($lastInsertId);
      $this->entityManager->getUnitOfWork()->registerManaged($log, ['id' => $log->getId()], []);
      $this->entityManager->refresh($log);
    }
  }

  /**
   * @param \DateTimeInterface|null $dateFrom
   * @param \DateTimeInterface|null $dateTo
   * @param string|null $search
   * @param string $offset
   * @param string $limit
   * @return LogEntity[]
   */
  public function getLogs(
    \DateTimeInterface $dateFrom = null,
    \DateTimeInterface $dateTo = null,
    string $search = null,
    string $offset = null,
    string $limit = null
  ): array {
    $query = $this->doctrineRepository->createQueryBuilder('l')
      ->select('l');

    if ($dateFrom instanceof \DateTimeInterface) {
      $query
        ->andWhere('l.createdAt >= :dateFrom')
        ->setParameter('dateFrom', $dateFrom->format('Y-m-d 00:00:00'));
    }
    if ($dateTo instanceof \DateTimeInterface) {
      $query
        ->andWhere('l.createdAt <= :dateTo')
        ->setParameter('dateTo', $dateTo->format('Y-m-d 23:59:59'));
    }
    if ($search) {
      $search = Helpers::escapeSearch($search);
      $query
        ->andWhere('l.name LIKE :search or l.message LIKE :search')
        ->setParameter('search', "%$search%");
    }

    $query->orderBy('l.createdAt', 'desc');
    if ($offset !== null) {
      $query->setFirstResult((int)$offset);
    }
    if ($limit === null) {
      $query->setMaxResults(500);
    } else {
      $query->setMaxResults((int)$limit);
    }


    return $query->getQuery()->getResult();
  }

  public function purgeOldLogs(int $daysToKeepLogs, int $limit = 1000) {
    $logsTable = $this->entityManager->getClassMetadata(LogEntity::class)->getTableName();
    $this->entityManager->getConnection()->executeStatement(
      "
      DELETE FROM $logsTable
      WHERE `created_at` < :date
      ORDER BY `id` ASC LIMIT :limit
    ",
      [
      'date' => Carbon::now()->subDays($daysToKeepLogs)->toDateTimeString(),
      'limit' => $limit,
      ],
      [
      'date' => Connection::PARAM_STR,
      'limit' => Connection::PARAM_INT,
      ]
    );
  }

  public function getRawMessagesForNewsletter(NewsletterEntity $newsletter, string $topic): array {
    return $this->entityManager->createQueryBuilder()
      ->select('DISTINCT logs.rawMessage message')
      ->from(LogEntity::class, 'logs')
      ->where('logs.name = :topic')
      ->andWhere('logs.context LIKE :context')
      ->orderBy('logs.createdAt')
      ->setParameter('context', json_encode(['newsletter_id' => $newsletter->getId()]))
      ->setParameter('topic', $topic)
      ->getQuery()
      ->getSingleColumnResult();
  }

  public function persist($entity): void {
    throw new InvalidStateException('Use saveLog() instead to avoid unintended side effects');
  }

  public function flush(): void {
    throw new InvalidStateException('Use saveLog() instead to avoid unintended side effects');
  }

  protected function getEntityClassName() {
    return LogEntity::class;
  }
}
