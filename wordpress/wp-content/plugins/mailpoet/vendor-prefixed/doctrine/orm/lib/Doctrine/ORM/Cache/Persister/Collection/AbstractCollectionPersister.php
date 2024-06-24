<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Persister\Collection;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Collections\Collection;
use MailPoetVendor\Doctrine\Common\Collections\Criteria;
use MailPoetVendor\Doctrine\Common\Util\ClassUtils;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\Cache\CollectionCacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\CollectionHydrator;
use MailPoetVendor\Doctrine\ORM\Cache\EntityCacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\Logging\CacheLogger;
use MailPoetVendor\Doctrine\ORM\Cache\Persister\Entity\CachedEntityPersister;
use MailPoetVendor\Doctrine\ORM\Cache\Region;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadataFactory;
use MailPoetVendor\Doctrine\ORM\PersistentCollection;
use MailPoetVendor\Doctrine\ORM\Persisters\Collection\CollectionPersister;
use MailPoetVendor\Doctrine\ORM\UnitOfWork;
use function array_values;
use function assert;
use function count;
abstract class AbstractCollectionPersister implements CachedCollectionPersister
{
 protected $uow;
 protected $metadataFactory;
 protected $persister;
 protected $sourceEntity;
 protected $targetEntity;
 protected $association;
 protected $queuedCache = [];
 protected $region;
 protected $regionName;
 protected $hydrator;
 protected $cacheLogger;
 public function __construct(CollectionPersister $persister, Region $region, EntityManagerInterface $em, array $association)
 {
 $configuration = $em->getConfiguration();
 $cacheConfig = $configuration->getSecondLevelCacheConfiguration();
 $cacheFactory = $cacheConfig->getCacheFactory();
 $this->region = $region;
 $this->persister = $persister;
 $this->association = $association;
 $this->regionName = $region->getName();
 $this->uow = $em->getUnitOfWork();
 $this->metadataFactory = $em->getMetadataFactory();
 $this->cacheLogger = $cacheConfig->getCacheLogger();
 $this->hydrator = $cacheFactory->buildCollectionHydrator($em, $association);
 $this->sourceEntity = $em->getClassMetadata($association['sourceEntity']);
 $this->targetEntity = $em->getClassMetadata($association['targetEntity']);
 }
 public function getCacheRegion()
 {
 return $this->region;
 }
 public function getSourceEntityMetadata()
 {
 return $this->sourceEntity;
 }
 public function getTargetEntityMetadata()
 {
 return $this->targetEntity;
 }
 public function loadCollectionCache(PersistentCollection $collection, CollectionCacheKey $key)
 {
 $cache = $this->region->get($key);
 if ($cache === null) {
 return null;
 }
 return $this->hydrator->loadCacheEntry($this->sourceEntity, $key, $cache, $collection);
 }
 public function storeCollectionCache(CollectionCacheKey $key, $elements)
 {
 $associationMapping = $this->sourceEntity->associationMappings[$key->association];
 $targetPersister = $this->uow->getEntityPersister($this->targetEntity->rootEntityName);
 assert($targetPersister instanceof CachedEntityPersister);
 $targetRegion = $targetPersister->getCacheRegion();
 $targetHydrator = $targetPersister->getEntityHydrator();
 // Only preserve ordering if association configured it
 if (!(isset($associationMapping['indexBy']) && $associationMapping['indexBy'])) {
 // Elements may be an array or a Collection
 $elements = array_values($elements instanceof Collection ? $elements->getValues() : $elements);
 }
 $entry = $this->hydrator->buildCacheEntry($this->targetEntity, $key, $elements);
 foreach ($entry->identifiers as $index => $entityKey) {
 if ($targetRegion->contains($entityKey)) {
 continue;
 }
 $class = $this->targetEntity;
 $className = ClassUtils::getClass($elements[$index]);
 if ($className !== $this->targetEntity->name) {
 $class = $this->metadataFactory->getMetadataFor($className);
 }
 $entity = $elements[$index];
 $entityEntry = $targetHydrator->buildCacheEntry($class, $entityKey, $entity);
 $targetRegion->put($entityKey, $entityEntry);
 }
 $cached = $this->region->put($key, $entry);
 if ($this->cacheLogger && $cached) {
 $this->cacheLogger->collectionCachePut($this->regionName, $key);
 }
 }
 public function contains(PersistentCollection $collection, $element)
 {
 return $this->persister->contains($collection, $element);
 }
 public function containsKey(PersistentCollection $collection, $key)
 {
 return $this->persister->containsKey($collection, $key);
 }
 public function count(PersistentCollection $collection)
 {
 $ownerId = $this->uow->getEntityIdentifier($collection->getOwner());
 $key = new CollectionCacheKey($this->sourceEntity->rootEntityName, $this->association['fieldName'], $ownerId);
 $entry = $this->region->get($key);
 if ($entry !== null) {
 return count($entry->identifiers);
 }
 return $this->persister->count($collection);
 }
 public function get(PersistentCollection $collection, $index)
 {
 return $this->persister->get($collection, $index);
 }
 public function slice(PersistentCollection $collection, $offset, $length = null)
 {
 return $this->persister->slice($collection, $offset, $length);
 }
 public function loadCriteria(PersistentCollection $collection, Criteria $criteria)
 {
 return $this->persister->loadCriteria($collection, $criteria);
 }
 protected function evictCollectionCache(PersistentCollection $collection)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9512', 'The method %s() is deprecated and will be removed without replacement.');
 $key = new CollectionCacheKey($this->sourceEntity->rootEntityName, $this->association['fieldName'], $this->uow->getEntityIdentifier($collection->getOwner()));
 $this->region->evict($key);
 if ($this->cacheLogger) {
 $this->cacheLogger->collectionCachePut($this->regionName, $key);
 }
 }
 protected function evictElementCache($targetEntity, $element)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9512', 'The method %s() is deprecated and will be removed without replacement.');
 $targetPersister = $this->uow->getEntityPersister($targetEntity);
 assert($targetPersister instanceof CachedEntityPersister);
 $targetRegion = $targetPersister->getCacheRegion();
 $key = new EntityCacheKey($targetEntity, $this->uow->getEntityIdentifier($element));
 $targetRegion->evict($key);
 if ($this->cacheLogger) {
 $this->cacheLogger->entityCachePut($targetRegion->getName(), $key);
 }
 }
}
