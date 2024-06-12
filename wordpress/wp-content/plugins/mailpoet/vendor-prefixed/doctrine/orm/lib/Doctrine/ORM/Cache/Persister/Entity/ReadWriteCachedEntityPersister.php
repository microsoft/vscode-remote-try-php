<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Persister\Entity;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Cache\ConcurrentRegion;
use MailPoetVendor\Doctrine\ORM\Cache\EntityCacheKey;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\Persisters\Entity\EntityPersister;
class ReadWriteCachedEntityPersister extends AbstractEntityPersister
{
 public function __construct(EntityPersister $persister, ConcurrentRegion $region, EntityManagerInterface $em, ClassMetadata $class)
 {
 parent::__construct($persister, $region, $em, $class);
 }
 public function afterTransactionComplete()
 {
 $isChanged = \true;
 if (isset($this->queuedCache['update'])) {
 foreach ($this->queuedCache['update'] as $item) {
 $this->region->evict($item['key']);
 $isChanged = \true;
 }
 }
 if (isset($this->queuedCache['delete'])) {
 foreach ($this->queuedCache['delete'] as $item) {
 $this->region->evict($item['key']);
 $isChanged = \true;
 }
 }
 if ($isChanged) {
 $this->timestampRegion->update($this->timestampKey);
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
 public function delete($entity)
 {
 $key = new EntityCacheKey($this->class->rootEntityName, $this->uow->getEntityIdentifier($entity));
 $lock = $this->region->lock($key);
 $deleted = $this->persister->delete($entity);
 if ($deleted) {
 $this->region->evict($key);
 }
 if ($lock === null) {
 return $deleted;
 }
 $this->queuedCache['delete'][] = ['lock' => $lock, 'key' => $key];
 return $deleted;
 }
 public function update($entity)
 {
 $key = new EntityCacheKey($this->class->rootEntityName, $this->uow->getEntityIdentifier($entity));
 $lock = $this->region->lock($key);
 $this->persister->update($entity);
 if ($lock === null) {
 return;
 }
 $this->queuedCache['update'][] = ['lock' => $lock, 'key' => $key];
 }
}
