<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Persister\Collection;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Cache\CollectionCacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\ConcurrentRegion;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\PersistentCollection;
use MailPoetVendor\Doctrine\ORM\Persisters\Collection\CollectionPersister;
use function spl_object_id;
class ReadWriteCachedCollectionPersister extends AbstractCollectionPersister
{
 public function __construct(CollectionPersister $persister, ConcurrentRegion $region, EntityManagerInterface $em, array $association)
 {
 parent::__construct($persister, $region, $em, $association);
 }
 public function afterTransactionComplete()
 {
 if (isset($this->queuedCache['update'])) {
 foreach ($this->queuedCache['update'] as $item) {
 $this->region->evict($item['key']);
 }
 }
 if (isset($this->queuedCache['delete'])) {
 foreach ($this->queuedCache['delete'] as $item) {
 $this->region->evict($item['key']);
 }
 }
 $this->queuedCache = [];
 }
 public function afterTransactionRolledBack()
 {
 if (isset($this->queuedCache['update'])) {
 foreach ($this->queuedCache['update'] as $item) {
 $this->region->evict($item['key']);
 }
 }
 if (isset($this->queuedCache['delete'])) {
 foreach ($this->queuedCache['delete'] as $item) {
 $this->region->evict($item['key']);
 }
 }
 $this->queuedCache = [];
 }
 public function delete(PersistentCollection $collection)
 {
 $ownerId = $this->uow->getEntityIdentifier($collection->getOwner());
 $key = new CollectionCacheKey($this->sourceEntity->rootEntityName, $this->association['fieldName'], $ownerId);
 $lock = $this->region->lock($key);
 $this->persister->delete($collection);
 if ($lock === null) {
 return;
 }
 $this->queuedCache['delete'][spl_object_id($collection)] = ['key' => $key, 'lock' => $lock];
 }
 public function update(PersistentCollection $collection)
 {
 $isInitialized = $collection->isInitialized();
 $isDirty = $collection->isDirty();
 if (!$isInitialized && !$isDirty) {
 return;
 }
 $this->persister->update($collection);
 $ownerId = $this->uow->getEntityIdentifier($collection->getOwner());
 $key = new CollectionCacheKey($this->sourceEntity->rootEntityName, $this->association['fieldName'], $ownerId);
 $lock = $this->region->lock($key);
 if ($lock === null) {
 return;
 }
 $this->queuedCache['update'][spl_object_id($collection)] = ['key' => $key, 'lock' => $lock];
 }
}
