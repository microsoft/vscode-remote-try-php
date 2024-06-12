<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Collections\AbstractLazyCollection;
use MailPoetVendor\Doctrine\Common\Collections\ArrayCollection;
use MailPoetVendor\Doctrine\Common\Collections\Collection;
use MailPoetVendor\Doctrine\Common\Collections\Criteria;
use MailPoetVendor\Doctrine\Common\Collections\Selectable;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use ReturnTypeWillChange;
use RuntimeException;
use function array_combine;
use function array_diff_key;
use function array_map;
use function array_values;
use function array_walk;
use function get_class;
use function is_object;
use function spl_object_id;
final class PersistentCollection extends AbstractLazyCollection implements Selectable
{
 private $snapshot = [];
 private $owner;
 private $association;
 private $em;
 private $backRefFieldName;
 private $typeClass;
 private $isDirty = \false;
 public function __construct(EntityManagerInterface $em, $class, Collection $collection)
 {
 $this->collection = $collection;
 $this->em = $em;
 $this->typeClass = $class;
 $this->initialized = \true;
 }
 public function setOwner($entity, array $assoc) : void
 {
 $this->owner = $entity;
 $this->association = $assoc;
 $this->backRefFieldName = $assoc['inversedBy'] ?: $assoc['mappedBy'];
 }
 public function getOwner()
 {
 return $this->owner;
 }
 public function getTypeClass() : Mapping\ClassMetadataInfo
 {
 return $this->typeClass;
 }
 public function hydrateAdd($element) : void
 {
 $this->collection->add($element);
 // If _backRefFieldName is set and its a one-to-many association,
 // we need to set the back reference.
 if ($this->backRefFieldName && $this->association['type'] === ClassMetadata::ONE_TO_MANY) {
 // Set back reference to owner
 $this->typeClass->reflFields[$this->backRefFieldName]->setValue($element, $this->owner);
 $this->em->getUnitOfWork()->setOriginalEntityProperty(spl_object_id($element), $this->backRefFieldName, $this->owner);
 }
 }
 public function hydrateSet($key, $element) : void
 {
 $this->collection->set($key, $element);
 // If _backRefFieldName is set, then the association is bidirectional
 // and we need to set the back reference.
 if ($this->backRefFieldName && $this->association['type'] === ClassMetadata::ONE_TO_MANY) {
 // Set back reference to owner
 $this->typeClass->reflFields[$this->backRefFieldName]->setValue($element, $this->owner);
 }
 }
 public function initialize() : void
 {
 if ($this->initialized || !$this->association) {
 return;
 }
 $this->doInitialize();
 $this->initialized = \true;
 }
 public function takeSnapshot() : void
 {
 $this->snapshot = $this->collection->toArray();
 $this->isDirty = \false;
 }
 public function getSnapshot() : array
 {
 return $this->snapshot;
 }
 public function getDeleteDiff() : array
 {
 $collectionItems = $this->collection->toArray();
 return array_values(array_diff_key(array_combine(array_map('spl_object_id', $this->snapshot), $this->snapshot), array_combine(array_map('spl_object_id', $collectionItems), $collectionItems)));
 }
 public function getInsertDiff() : array
 {
 $collectionItems = $this->collection->toArray();
 return array_values(array_diff_key(array_combine(array_map('spl_object_id', $collectionItems), $collectionItems), array_combine(array_map('spl_object_id', $this->snapshot), $this->snapshot)));
 }
 public function getMapping() : ?array
 {
 return $this->association;
 }
 private function changed() : void
 {
 if ($this->isDirty) {
 return;
 }
 $this->isDirty = \true;
 if ($this->association !== null && $this->association['isOwningSide'] && $this->association['type'] === ClassMetadata::MANY_TO_MANY && $this->owner && $this->em->getClassMetadata(get_class($this->owner))->isChangeTrackingNotify()) {
 $this->em->getUnitOfWork()->scheduleForDirtyCheck($this->owner);
 }
 }
 public function isDirty() : bool
 {
 return $this->isDirty;
 }
 public function setDirty($dirty) : void
 {
 $this->isDirty = $dirty;
 }
 public function setInitialized($bool) : void
 {
 $this->initialized = $bool;
 }
 public function remove($key)
 {
 // TODO: If the keys are persistent as well (not yet implemented)
 // and the collection is not initialized and orphanRemoval is
 // not used we can issue a straight SQL delete/update on the
 // association (table). Without initializing the collection.
 $removed = parent::remove($key);
 if (!$removed) {
 return $removed;
 }
 $this->changed();
 if ($this->association !== null && $this->association['type'] & ClassMetadata::TO_MANY && $this->owner && $this->association['orphanRemoval']) {
 $this->em->getUnitOfWork()->scheduleOrphanRemoval($removed);
 }
 return $removed;
 }
 public function removeElement($element) : bool
 {
 $removed = parent::removeElement($element);
 if (!$removed) {
 return $removed;
 }
 $this->changed();
 if ($this->association !== null && $this->association['type'] & ClassMetadata::TO_MANY && $this->owner && $this->association['orphanRemoval']) {
 $this->em->getUnitOfWork()->scheduleOrphanRemoval($element);
 }
 return $removed;
 }
 public function containsKey($key) : bool
 {
 if (!$this->initialized && $this->association['fetch'] === ClassMetadata::FETCH_EXTRA_LAZY && isset($this->association['indexBy'])) {
 $persister = $this->em->getUnitOfWork()->getCollectionPersister($this->association);
 return $this->collection->containsKey($key) || $persister->containsKey($this, $key);
 }
 return parent::containsKey($key);
 }
 public function contains($element) : bool
 {
 if (!$this->initialized && $this->association['fetch'] === ClassMetadata::FETCH_EXTRA_LAZY) {
 $persister = $this->em->getUnitOfWork()->getCollectionPersister($this->association);
 return $this->collection->contains($element) || $persister->contains($this, $element);
 }
 return parent::contains($element);
 }
 public function get($key)
 {
 if (!$this->initialized && $this->association['fetch'] === ClassMetadata::FETCH_EXTRA_LAZY && isset($this->association['indexBy'])) {
 if (!$this->typeClass->isIdentifierComposite && $this->typeClass->isIdentifier($this->association['indexBy'])) {
 return $this->em->find($this->typeClass->name, $key);
 }
 return $this->em->getUnitOfWork()->getCollectionPersister($this->association)->get($this, $key);
 }
 return parent::get($key);
 }
 public function count() : int
 {
 if (!$this->initialized && $this->association !== null && $this->association['fetch'] === ClassMetadata::FETCH_EXTRA_LAZY) {
 $persister = $this->em->getUnitOfWork()->getCollectionPersister($this->association);
 return $persister->count($this) + ($this->isDirty ? $this->collection->count() : 0);
 }
 return parent::count();
 }
 public function set($key, $value) : void
 {
 parent::set($key, $value);
 $this->changed();
 if (is_object($value) && $this->em) {
 $this->em->getUnitOfWork()->cancelOrphanRemoval($value);
 }
 }
 public function add($value) : bool
 {
 $this->collection->add($value);
 $this->changed();
 if (is_object($value) && $this->em) {
 $this->em->getUnitOfWork()->cancelOrphanRemoval($value);
 }
 return \true;
 }
 public function offsetExists($offset) : bool
 {
 return $this->containsKey($offset);
 }
 #[\ReturnTypeWillChange]
 public function offsetGet($offset)
 {
 return $this->get($offset);
 }
 public function offsetSet($offset, $value) : void
 {
 if (!isset($offset)) {
 $this->add($value);
 return;
 }
 $this->set($offset, $value);
 }
 #[\ReturnTypeWillChange]
 public function offsetUnset($offset)
 {
 return $this->remove($offset);
 }
 public function isEmpty() : bool
 {
 return $this->collection->isEmpty() && $this->count() === 0;
 }
 public function clear() : void
 {
 if ($this->initialized && $this->isEmpty()) {
 $this->collection->clear();
 return;
 }
 $uow = $this->em->getUnitOfWork();
 if ($this->association['type'] & ClassMetadata::TO_MANY && $this->association['orphanRemoval'] && $this->owner) {
 // we need to initialize here, as orphan removal acts like implicit cascadeRemove,
 // hence for event listeners we need the objects in memory.
 $this->initialize();
 foreach ($this->collection as $element) {
 $uow->scheduleOrphanRemoval($element);
 }
 }
 $this->collection->clear();
 $this->initialized = \true;
 // direct call, {@link initialize()} is too expensive
 if ($this->association['isOwningSide'] && $this->owner) {
 $this->changed();
 $uow->scheduleCollectionDeletion($this);
 $this->takeSnapshot();
 }
 }
 public function __sleep() : array
 {
 return ['collection', 'initialized'];
 }
 public function slice($offset, $length = null) : array
 {
 if (!$this->initialized && !$this->isDirty && $this->association['fetch'] === ClassMetadata::FETCH_EXTRA_LAZY) {
 $persister = $this->em->getUnitOfWork()->getCollectionPersister($this->association);
 return $persister->slice($this, $offset, $length);
 }
 return parent::slice($offset, $length);
 }
 public function __clone()
 {
 if (is_object($this->collection)) {
 $this->collection = clone $this->collection;
 }
 $this->initialize();
 $this->owner = null;
 $this->snapshot = [];
 $this->changed();
 }
 public function matching(Criteria $criteria) : Collection
 {
 if ($this->isDirty) {
 $this->initialize();
 }
 if ($this->initialized) {
 return $this->collection->matching($criteria);
 }
 if ($this->association['type'] === ClassMetadata::MANY_TO_MANY) {
 $persister = $this->em->getUnitOfWork()->getCollectionPersister($this->association);
 return new ArrayCollection($persister->loadCriteria($this, $criteria));
 }
 $builder = Criteria::expr();
 $ownerExpression = $builder->eq($this->backRefFieldName, $this->owner);
 $expression = $criteria->getWhereExpression();
 $expression = $expression ? $builder->andX($expression, $ownerExpression) : $ownerExpression;
 $criteria = clone $criteria;
 $criteria->where($expression);
 $criteria->orderBy($criteria->getOrderings() ?: $this->association['orderBy'] ?? []);
 $persister = $this->em->getUnitOfWork()->getEntityPersister($this->association['targetEntity']);
 return $this->association['fetch'] === ClassMetadata::FETCH_EXTRA_LAZY ? new LazyCriteriaCollection($persister, $criteria) : new ArrayCollection($persister->loadCriteria($criteria));
 }
 public function unwrap() : Collection
 {
 return $this->collection;
 }
 protected function doInitialize() : void
 {
 // Has NEW objects added through add(). Remember them.
 $newlyAddedDirtyObjects = [];
 if ($this->isDirty) {
 $newlyAddedDirtyObjects = $this->collection->toArray();
 }
 $this->collection->clear();
 $this->em->getUnitOfWork()->loadCollection($this);
 $this->takeSnapshot();
 if ($newlyAddedDirtyObjects) {
 $this->restoreNewObjectsInDirtyCollection($newlyAddedDirtyObjects);
 }
 }
 private function restoreNewObjectsInDirtyCollection(array $newObjects) : void
 {
 $loadedObjects = $this->collection->toArray();
 $newObjectsByOid = array_combine(array_map('spl_object_id', $newObjects), $newObjects);
 $loadedObjectsByOid = array_combine(array_map('spl_object_id', $loadedObjects), $loadedObjects);
 $newObjectsThatWereNotLoaded = array_diff_key($newObjectsByOid, $loadedObjectsByOid);
 if ($newObjectsThatWereNotLoaded) {
 // Reattach NEW objects added through add(), if any.
 array_walk($newObjectsThatWereNotLoaded, [$this->collection, 'add']);
 $this->isDirty = \true;
 }
 }
}
