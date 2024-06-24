<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Cache\Persister\CachedPersister;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\PersistentCollection;
use MailPoetVendor\Doctrine\ORM\Query;
use MailPoetVendor\Doctrine\ORM\UnitOfWork;
use function assert;
class DefaultCollectionHydrator implements CollectionHydrator
{
 private $em;
 private $uow;
 private static $hints = [Query::HINT_CACHE_ENABLED => \true];
 public function __construct(EntityManagerInterface $em)
 {
 $this->em = $em;
 $this->uow = $em->getUnitOfWork();
 }
 public function buildCacheEntry(ClassMetadata $metadata, CollectionCacheKey $key, $collection)
 {
 $data = [];
 foreach ($collection as $index => $entity) {
 $data[$index] = new EntityCacheKey($metadata->rootEntityName, $this->uow->getEntityIdentifier($entity));
 }
 return new CollectionCacheEntry($data);
 }
 public function loadCacheEntry(ClassMetadata $metadata, CollectionCacheKey $key, CollectionCacheEntry $entry, PersistentCollection $collection)
 {
 $assoc = $metadata->associationMappings[$key->association];
 $targetPersister = $this->uow->getEntityPersister($assoc['targetEntity']);
 assert($targetPersister instanceof CachedPersister);
 $targetRegion = $targetPersister->getCacheRegion();
 $list = [];
 $entityEntries = $targetRegion->getMultiple($entry);
 if ($entityEntries === null) {
 return null;
 }
 foreach ($entityEntries as $index => $entityEntry) {
 $entity = $this->uow->createEntity($entityEntry->class, $entityEntry->resolveAssociationEntries($this->em), self::$hints);
 $collection->hydrateSet($index, $entity);
 $list[$index] = $entity;
 }
 $this->uow->hydrationComplete();
 return $list;
 }
}
