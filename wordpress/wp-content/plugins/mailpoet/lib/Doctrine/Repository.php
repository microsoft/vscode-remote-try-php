<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Doctrine\Common\Collections\Collection;
use MailPoetVendor\Doctrine\Common\Collections\Criteria;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use MailPoetVendor\Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @template T of object
 */
abstract class Repository {
  /** @var EntityManager */
  protected $entityManager;

  /** @var ClassMetadata<object> */
  protected $classMetadata;

  /** @var DoctrineEntityRepository<T> */
  protected $doctrineRepository;

  /** @var string[] */
  protected $ignoreColumnsForUpdate = [
    'created_at',
  ];

  public function __construct(
    EntityManager $entityManager
  ) {
    $this->entityManager = $entityManager;
    $this->classMetadata = $entityManager->getClassMetadata($this->getEntityClassName());
    $this->doctrineRepository = new DoctrineEntityRepository($this->entityManager, $this->classMetadata);
  }

  /**
   * @param array $criteria
   * @param array|null $orderBy
   * @param int|null $limit
   * @param int|null $offset
   * @return T[]
   */
  public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
    return $this->doctrineRepository->findBy($criteria, $orderBy, $limit, $offset);
  }

  /**
   * @param Criteria $criteria
   * @return Collection<int, T>
   */
  public function matching(Criteria $criteria) {
    return $this->doctrineRepository->matching($criteria);
  }

  public function countBy(array $criteria): int {
    return $this->doctrineRepository->count($criteria);
  }

  /**
   * @param array $criteria
   * @param array|null $orderBy
   * @return T|null
   */
  public function findOneBy(array $criteria, array $orderBy = null) {
    return $this->doctrineRepository->findOneBy($criteria, $orderBy);
  }

  /**
   * @param mixed $id
   * @return T|null
   */
  public function findOneById($id) {
    return $this->doctrineRepository->find($id);
  }

  /**
   * @return T[]
   */
  public function findAll() {
    return $this->doctrineRepository->findAll();
  }

  /**
   * @param T $entity
   */
  public function persist($entity) {
    $this->entityManager->persist($entity);
  }

  public function truncate() {
    $tableName = $this->classMetadata->getTableName();
    $connection = $this->entityManager->getConnection();
    $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
    $q = "TRUNCATE $tableName";
    $connection->executeStatement($q);
    $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
  }

  /**
   * @param T $entity
   */
  public function remove($entity) {
    $this->entityManager->remove($entity);
  }

  /**
   * @param T $entity
   */
  public function refresh($entity) {
    $this->entityManager->refresh($entity);
  }

  /**
   * @param callable(T): bool|null $filter
   */
  public function refreshAll(callable $filter = null): void {
    $entities = $this->getAllFromIdentityMap();
    foreach ($entities as $entity) {
      if ($filter && !$filter($entity)) {
        continue;
      }
      $this->entityManager->refresh($entity);
    }
  }

  public function flush() {
    $this->entityManager->flush();
  }

  public function getReference($id) {
    return $this->entityManager->getReference($this->getEntityClassName(), $id);
  }

  /**
   * @param T $entity
   */
  public function detach($entity) {
    $this->entityManager->detach($entity);
  }

  /**
   * @param callable(T): bool|null $filter
   */
  public function detachAll(callable $filter = null): void {
    $entities = $this->getAllFromIdentityMap();
    foreach ($entities as $entity) {
      if ($filter && !$filter($entity)) {
        continue;
      }
      $this->entityManager->detach($entity);
    }
  }

  /** @return T[] */
  public function getAllFromIdentityMap(): array {
    $className = $this->getEntityClassName();
    $rootClassName = $this->entityManager->getClassMetadata($className)->rootEntityName;
    $entities = $this->entityManager->getUnitOfWork()->getIdentityMap()[$rootClassName] ?? [];

    $result = [];
    foreach ($entities as $entity) {
      if ($entity instanceof $className) {
        $result[] = $entity;
      }
    }
    return $result;
  }

  /**
   * @return class-string<T>
   */
  abstract protected function getEntityClassName();
}
