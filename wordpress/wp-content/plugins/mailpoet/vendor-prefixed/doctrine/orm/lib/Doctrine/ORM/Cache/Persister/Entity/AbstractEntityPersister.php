<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Persister\Entity;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Collections\Criteria;
use MailPoetVendor\Doctrine\Common\Util\ClassUtils;
use MailPoetVendor\Doctrine\ORM\Cache;
use MailPoetVendor\Doctrine\ORM\Cache\CollectionCacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\EntityCacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\EntityHydrator;
use MailPoetVendor\Doctrine\ORM\Cache\Logging\CacheLogger;
use MailPoetVendor\Doctrine\ORM\Cache\Persister\CachedPersister;
use MailPoetVendor\Doctrine\ORM\Cache\QueryCacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\Region;
use MailPoetVendor\Doctrine\ORM\Cache\TimestampCacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\TimestampRegion;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadataFactory;
use MailPoetVendor\Doctrine\ORM\PersistentCollection;
use MailPoetVendor\Doctrine\ORM\Persisters\Entity\EntityPersister;
use MailPoetVendor\Doctrine\ORM\UnitOfWork;
use function assert;
use function serialize;
use function sha1;
abstract class AbstractEntityPersister implements CachedEntityPersister
{
 protected $uow;
 protected $metadataFactory;
 protected $persister;
 protected $class;
 protected $queuedCache = [];
 protected $region;
 protected $timestampRegion;
 protected $timestampKey;
 protected $hydrator;
 protected $cache;
 protected $cacheLogger;
 protected $regionName;
 protected $joinedAssociations;
 public function __construct(EntityPersister $persister, Region $region, EntityManagerInterface $em, ClassMetadata $class)
 {
 $configuration = $em->getConfiguration();
 $cacheConfig = $configuration->getSecondLevelCacheConfiguration();
 $cacheFactory = $cacheConfig->getCacheFactory();
 $this->class = $class;
 $this->region = $region;
 $this->persister = $persister;
 $this->cache = $em->getCache();
 $this->regionName = $region->getName();
 $this->uow = $em->getUnitOfWork();
 $this->metadataFactory = $em->getMetadataFactory();
 $this->cacheLogger = $cacheConfig->getCacheLogger();
 $this->timestampRegion = $cacheFactory->getTimestampRegion();
 $this->hydrator = $cacheFactory->buildEntityHydrator($em, $class);
 $this->timestampKey = new TimestampCacheKey($this->class->rootEntityName);
 }
 public function addInsert($entity)
 {
 $this->persister->addInsert($entity);
 }
 public function getInserts()
 {
 return $this->persister->getInserts();
 }
 public function getSelectSQL($criteria, $assoc = null, $lockMode = null, $limit = null, $offset = null, ?array $orderBy = null)
 {
 return $this->persister->getSelectSQL($criteria, $assoc, $lockMode, $limit, $offset, $orderBy);
 }
 public function getCountSQL($criteria = [])
 {
 return $this->persister->getCountSQL($criteria);
 }
 public function getInsertSQL()
 {
 return $this->persister->getInsertSQL();
 }
 public function getResultSetMapping()
 {
 return $this->persister->getResultSetMapping();
 }
 public function getSelectConditionStatementSQL($field, $value, $assoc = null, $comparison = null)
 {
 return $this->persister->getSelectConditionStatementSQL($field, $value, $assoc, $comparison);
 }
 public function exists($entity, ?Criteria $extraConditions = null)
 {
 if ($extraConditions === null) {
 $key = new EntityCacheKey($this->class->rootEntityName, $this->class->getIdentifierValues($entity));
 if ($this->region->contains($key)) {
 return \true;
 }
 }
 return $this->persister->exists($entity, $extraConditions);
 }
 public function getCacheRegion()
 {
 return $this->region;
 }
 public function getEntityHydrator()
 {
 return $this->hydrator;
 }
 public function storeEntityCache($entity, EntityCacheKey $key)
 {
 $class = $this->class;
 $className = ClassUtils::getClass($entity);
 if ($className !== $this->class->name) {
 $class = $this->metadataFactory->getMetadataFor($className);
 }
 $entry = $this->hydrator->buildCacheEntry($class, $key, $entity);
 $cached = $this->region->put($key, $entry);
 if ($this->cacheLogger && $cached) {
 $this->cacheLogger->entityCachePut($this->regionName, $key);
 }
 return $cached;
 }
 private function storeJoinedAssociations($entity) : void
 {
 if ($this->joinedAssociations === null) {
 $associations = [];
 foreach ($this->class->associationMappings as $name => $assoc) {
 if (isset($assoc['cache']) && $assoc['type'] & ClassMetadata::TO_ONE && ($assoc['fetch'] === ClassMetadata::FETCH_EAGER || !$assoc['isOwningSide'])) {
 $associations[] = $name;
 }
 }
 $this->joinedAssociations = $associations;
 }
 foreach ($this->joinedAssociations as $name) {
 $assoc = $this->class->associationMappings[$name];
 $assocEntity = $this->class->getFieldValue($entity, $name);
 if ($assocEntity === null) {
 continue;
 }
 $assocId = $this->uow->getEntityIdentifier($assocEntity);
 $assocMetadata = $this->metadataFactory->getMetadataFor($assoc['targetEntity']);
 $assocKey = new EntityCacheKey($assocMetadata->rootEntityName, $assocId);
 $assocPersister = $this->uow->getEntityPersister($assoc['targetEntity']);
 $assocPersister->storeEntityCache($assocEntity, $assocKey);
 }
 }
 protected function getHash($query, $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 {
 [$params] = $criteria instanceof Criteria ? $this->persister->expandCriteriaParameters($criteria) : $this->persister->expandParameters($criteria);
 return sha1($query . serialize($params) . serialize($orderBy) . $limit . $offset);
 }
 public function expandParameters($criteria)
 {
 return $this->persister->expandParameters($criteria);
 }
 public function expandCriteriaParameters(Criteria $criteria)
 {
 return $this->persister->expandCriteriaParameters($criteria);
 }
 public function getClassMetadata()
 {
 return $this->persister->getClassMetadata();
 }
 public function getManyToManyCollection(array $assoc, $sourceEntity, $offset = null, $limit = null)
 {
 return $this->persister->getManyToManyCollection($assoc, $sourceEntity, $offset, $limit);
 }
 public function getOneToManyCollection(array $assoc, $sourceEntity, $offset = null, $limit = null)
 {
 return $this->persister->getOneToManyCollection($assoc, $sourceEntity, $offset, $limit);
 }
 public function getOwningTable($fieldName)
 {
 return $this->persister->getOwningTable($fieldName);
 }
 public function executeInserts()
 {
 $this->queuedCache['insert'] = $this->persister->getInserts();
 return $this->persister->executeInserts();
 }
 public function load(array $criteria, $entity = null, $assoc = null, array $hints = [], $lockMode = null, $limit = null, ?array $orderBy = null)
 {
 if ($entity !== null || $assoc !== null || $hints !== [] || $lockMode !== null) {
 return $this->persister->load($criteria, $entity, $assoc, $hints, $lockMode, $limit, $orderBy);
 }
 //handle only EntityRepository#findOneBy
 $query = $this->persister->getSelectSQL($criteria, null, null, $limit, null, $orderBy);
 $hash = $this->getHash($query, $criteria, null, null, null);
 $rsm = $this->getResultSetMapping();
 $queryKey = new QueryCacheKey($hash, 0, Cache::MODE_NORMAL, $this->timestampKey);
 $queryCache = $this->cache->getQueryCache($this->regionName);
 $result = $queryCache->get($queryKey, $rsm);
 if ($result !== null) {
 if ($this->cacheLogger) {
 $this->cacheLogger->queryCacheHit($this->regionName, $queryKey);
 }
 return $result[0];
 }
 $result = $this->persister->load($criteria, $entity, $assoc, $hints, $lockMode, $limit, $orderBy);
 if ($result === null) {
 return null;
 }
 $cached = $queryCache->put($queryKey, $rsm, [$result]);
 if ($this->cacheLogger) {
 $this->cacheLogger->queryCacheMiss($this->regionName, $queryKey);
 if ($cached) {
 $this->cacheLogger->queryCachePut($this->regionName, $queryKey);
 }
 }
 return $result;
 }
 public function loadAll(array $criteria = [], ?array $orderBy = null, $limit = null, $offset = null)
 {
 $query = $this->persister->getSelectSQL($criteria, null, null, $limit, $offset, $orderBy);
 $hash = $this->getHash($query, $criteria, null, null, null);
 $rsm = $this->getResultSetMapping();
 $queryKey = new QueryCacheKey($hash, 0, Cache::MODE_NORMAL, $this->timestampKey);
 $queryCache = $this->cache->getQueryCache($this->regionName);
 $result = $queryCache->get($queryKey, $rsm);
 if ($result !== null) {
 if ($this->cacheLogger) {
 $this->cacheLogger->queryCacheHit($this->regionName, $queryKey);
 }
 return $result;
 }
 $result = $this->persister->loadAll($criteria, $orderBy, $limit, $offset);
 $cached = $queryCache->put($queryKey, $rsm, $result);
 if ($this->cacheLogger) {
 if ($result) {
 $this->cacheLogger->queryCacheMiss($this->regionName, $queryKey);
 }
 if ($cached) {
 $this->cacheLogger->queryCachePut($this->regionName, $queryKey);
 }
 }
 return $result;
 }
 public function loadById(array $identifier, $entity = null)
 {
 $cacheKey = new EntityCacheKey($this->class->rootEntityName, $identifier);
 $cacheEntry = $this->region->get($cacheKey);
 $class = $this->class;
 if ($cacheEntry !== null) {
 if ($cacheEntry->class !== $this->class->name) {
 $class = $this->metadataFactory->getMetadataFor($cacheEntry->class);
 }
 $cachedEntity = $this->hydrator->loadCacheEntry($class, $cacheKey, $cacheEntry, $entity);
 if ($cachedEntity !== null) {
 if ($this->cacheLogger) {
 $this->cacheLogger->entityCacheHit($this->regionName, $cacheKey);
 }
 return $cachedEntity;
 }
 }
 $entity = $this->persister->loadById($identifier, $entity);
 if ($entity === null) {
 return null;
 }
 $class = $this->class;
 $className = ClassUtils::getClass($entity);
 if ($className !== $this->class->name) {
 $class = $this->metadataFactory->getMetadataFor($className);
 }
 $cacheEntry = $this->hydrator->buildCacheEntry($class, $cacheKey, $entity);
 $cached = $this->region->put($cacheKey, $cacheEntry);
 if ($cached && ($this->joinedAssociations === null || $this->joinedAssociations)) {
 $this->storeJoinedAssociations($entity);
 }
 if ($this->cacheLogger) {
 if ($cached) {
 $this->cacheLogger->entityCachePut($this->regionName, $cacheKey);
 }
 $this->cacheLogger->entityCacheMiss($this->regionName, $cacheKey);
 }
 return $entity;
 }
 public function count($criteria = [])
 {
 return $this->persister->count($criteria);
 }
 public function loadCriteria(Criteria $criteria)
 {
 $orderBy = $criteria->getOrderings();
 $limit = $criteria->getMaxResults();
 $offset = $criteria->getFirstResult();
 $query = $this->persister->getSelectSQL($criteria);
 $hash = $this->getHash($query, $criteria, $orderBy, $limit, $offset);
 $rsm = $this->getResultSetMapping();
 $queryKey = new QueryCacheKey($hash, 0, Cache::MODE_NORMAL, $this->timestampKey);
 $queryCache = $this->cache->getQueryCache($this->regionName);
 $cacheResult = $queryCache->get($queryKey, $rsm);
 if ($cacheResult !== null) {
 if ($this->cacheLogger) {
 $this->cacheLogger->queryCacheHit($this->regionName, $queryKey);
 }
 return $cacheResult;
 }
 $result = $this->persister->loadCriteria($criteria);
 $cached = $queryCache->put($queryKey, $rsm, $result);
 if ($this->cacheLogger) {
 if ($result) {
 $this->cacheLogger->queryCacheMiss($this->regionName, $queryKey);
 }
 if ($cached) {
 $this->cacheLogger->queryCachePut($this->regionName, $queryKey);
 }
 }
 return $result;
 }
 public function loadManyToManyCollection(array $assoc, $sourceEntity, PersistentCollection $collection)
 {
 $persister = $this->uow->getCollectionPersister($assoc);
 $hasCache = $persister instanceof CachedPersister;
 if (!$hasCache) {
 return $this->persister->loadManyToManyCollection($assoc, $sourceEntity, $collection);
 }
 $ownerId = $this->uow->getEntityIdentifier($collection->getOwner());
 $key = $this->buildCollectionCacheKey($assoc, $ownerId);
 $list = $persister->loadCollectionCache($collection, $key);
 if ($list !== null) {
 if ($this->cacheLogger) {
 $this->cacheLogger->collectionCacheHit($persister->getCacheRegion()->getName(), $key);
 }
 return $list;
 }
 $list = $this->persister->loadManyToManyCollection($assoc, $sourceEntity, $collection);
 $persister->storeCollectionCache($key, $list);
 if ($this->cacheLogger) {
 $this->cacheLogger->collectionCacheMiss($persister->getCacheRegion()->getName(), $key);
 }
 return $list;
 }
 public function loadOneToManyCollection(array $assoc, $sourceEntity, PersistentCollection $collection)
 {
 $persister = $this->uow->getCollectionPersister($assoc);
 $hasCache = $persister instanceof CachedPersister;
 if (!$hasCache) {
 return $this->persister->loadOneToManyCollection($assoc, $sourceEntity, $collection);
 }
 $ownerId = $this->uow->getEntityIdentifier($collection->getOwner());
 $key = $this->buildCollectionCacheKey($assoc, $ownerId);
 $list = $persister->loadCollectionCache($collection, $key);
 if ($list !== null) {
 if ($this->cacheLogger) {
 $this->cacheLogger->collectionCacheHit($persister->getCacheRegion()->getName(), $key);
 }
 return $list;
 }
 $list = $this->persister->loadOneToManyCollection($assoc, $sourceEntity, $collection);
 $persister->storeCollectionCache($key, $list);
 if ($this->cacheLogger) {
 $this->cacheLogger->collectionCacheMiss($persister->getCacheRegion()->getName(), $key);
 }
 return $list;
 }
 public function loadOneToOneEntity(array $assoc, $sourceEntity, array $identifier = [])
 {
 return $this->persister->loadOneToOneEntity($assoc, $sourceEntity, $identifier);
 }
 public function lock(array $criteria, $lockMode)
 {
 $this->persister->lock($criteria, $lockMode);
 }
 public function refresh(array $id, $entity, $lockMode = null)
 {
 $this->persister->refresh($id, $entity, $lockMode);
 }
 protected function buildCollectionCacheKey(array $association, $ownerId)
 {
 $metadata = $this->metadataFactory->getMetadataFor($association['sourceEntity']);
 assert($metadata instanceof ClassMetadata);
 return new CollectionCacheKey($metadata->rootEntityName, $association['fieldName'], $ownerId);
 }
}
