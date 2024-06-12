<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
use DateTimeInterface;
use MailPoetVendor\Doctrine\Common\Collections\ArrayCollection;
use MailPoetVendor\Doctrine\Common\Collections\Collection;
use MailPoetVendor\Doctrine\Common\EventManager;
use MailPoetVendor\Doctrine\Common\Proxy\Proxy;
use MailPoetVendor\Doctrine\DBAL\Connections\PrimaryReadReplicaConnection;
use MailPoetVendor\Doctrine\DBAL\LockMode;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\Cache\Persister\CachedPersister;
use MailPoetVendor\Doctrine\ORM\Event\LifecycleEventArgs;
use MailPoetVendor\Doctrine\ORM\Event\ListenersInvoker;
use MailPoetVendor\Doctrine\ORM\Event\OnFlushEventArgs;
use MailPoetVendor\Doctrine\ORM\Event\PostFlushEventArgs;
use MailPoetVendor\Doctrine\ORM\Event\PreFlushEventArgs;
use MailPoetVendor\Doctrine\ORM\Event\PreUpdateEventArgs;
use MailPoetVendor\Doctrine\ORM\Exception\UnexpectedAssociationValue;
use MailPoetVendor\Doctrine\ORM\Id\AssignedGenerator;
use MailPoetVendor\Doctrine\ORM\Internal\CommitOrderCalculator;
use MailPoetVendor\Doctrine\ORM\Internal\HydrationCompleteHandler;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\Mapping\MappingException;
use MailPoetVendor\Doctrine\ORM\Mapping\Reflection\ReflectionPropertiesGetter;
use MailPoetVendor\Doctrine\ORM\Persisters\Collection\CollectionPersister;
use MailPoetVendor\Doctrine\ORM\Persisters\Collection\ManyToManyPersister;
use MailPoetVendor\Doctrine\ORM\Persisters\Collection\OneToManyPersister;
use MailPoetVendor\Doctrine\ORM\Persisters\Entity\BasicEntityPersister;
use MailPoetVendor\Doctrine\ORM\Persisters\Entity\EntityPersister;
use MailPoetVendor\Doctrine\ORM\Persisters\Entity\JoinedSubclassPersister;
use MailPoetVendor\Doctrine\ORM\Persisters\Entity\SingleTablePersister;
use MailPoetVendor\Doctrine\ORM\Utility\IdentifierFlattener;
use MailPoetVendor\Doctrine\Persistence\Mapping\RuntimeReflectionService;
use MailPoetVendor\Doctrine\Persistence\NotifyPropertyChanged;
use MailPoetVendor\Doctrine\Persistence\ObjectManagerAware;
use MailPoetVendor\Doctrine\Persistence\PropertyChangedListener;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Throwable;
use UnexpectedValueException;
use function array_combine;
use function array_diff_key;
use function array_filter;
use function array_key_exists;
use function array_map;
use function array_merge;
use function array_pop;
use function array_sum;
use function array_values;
use function count;
use function current;
use function get_class;
use function get_debug_type;
use function implode;
use function in_array;
use function is_array;
use function is_object;
use function method_exists;
use function reset;
use function spl_object_id;
use function sprintf;
class UnitOfWork implements PropertyChangedListener
{
 public const STATE_MANAGED = 1;
 public const STATE_NEW = 2;
 public const STATE_DETACHED = 3;
 public const STATE_REMOVED = 4;
 public const HINT_DEFEREAGERLOAD = 'deferEagerLoad';
 private $identityMap = [];
 private $entityIdentifiers = [];
 private $originalEntityData = [];
 private $entityChangeSets = [];
 private $entityStates = [];
 private $scheduledForSynchronization = [];
 private $entityInsertions = [];
 private $entityUpdates = [];
 private $extraUpdates = [];
 private $entityDeletions = [];
 private $nonCascadedNewDetectedEntities = [];
 private $collectionDeletions = [];
 private $collectionUpdates = [];
 private $visitedCollections = [];
 private $em;
 private $persisters = [];
 private $collectionPersisters = [];
 private $evm;
 private $listenersInvoker;
 private $identifierFlattener;
 private $orphanRemovals = [];
 private $readOnlyObjects = [];
 private $eagerLoadingEntities = [];
 protected $hasCache = \false;
 private $hydrationCompleteHandler;
 private $reflectionPropertiesGetter;
 public function __construct(EntityManagerInterface $em)
 {
 $this->em = $em;
 $this->evm = $em->getEventManager();
 $this->listenersInvoker = new ListenersInvoker($em);
 $this->hasCache = $em->getConfiguration()->isSecondLevelCacheEnabled();
 $this->identifierFlattener = new IdentifierFlattener($this, $em->getMetadataFactory());
 $this->hydrationCompleteHandler = new HydrationCompleteHandler($this->listenersInvoker, $em);
 $this->reflectionPropertiesGetter = new ReflectionPropertiesGetter(new RuntimeReflectionService());
 }
 public function commit($entity = null)
 {
 if ($entity !== null) {
 Deprecation::triggerIfCalledFromOutside('doctrine/orm', 'https://github.com/doctrine/orm/issues/8459', 'Calling %s() with any arguments to commit specific entities is deprecated and will not be supported in Doctrine ORM 3.0.', __METHOD__);
 }
 $connection = $this->em->getConnection();
 if ($connection instanceof PrimaryReadReplicaConnection) {
 $connection->ensureConnectedToPrimary();
 }
 // Raise preFlush
 if ($this->evm->hasListeners(Events::preFlush)) {
 $this->evm->dispatchEvent(Events::preFlush, new PreFlushEventArgs($this->em));
 }
 // Compute changes done since last commit.
 if ($entity === null) {
 $this->computeChangeSets();
 } elseif (is_object($entity)) {
 $this->computeSingleEntityChangeSet($entity);
 } elseif (is_array($entity)) {
 foreach ($entity as $object) {
 $this->computeSingleEntityChangeSet($object);
 }
 }
 if (!($this->entityInsertions || $this->entityDeletions || $this->entityUpdates || $this->collectionUpdates || $this->collectionDeletions || $this->orphanRemovals)) {
 $this->dispatchOnFlushEvent();
 $this->dispatchPostFlushEvent();
 $this->postCommitCleanup($entity);
 return;
 // Nothing to do.
 }
 $this->assertThatThereAreNoUnintentionallyNonPersistedAssociations();
 if ($this->orphanRemovals) {
 foreach ($this->orphanRemovals as $orphan) {
 $this->remove($orphan);
 }
 }
 $this->dispatchOnFlushEvent();
 // Now we need a commit order to maintain referential integrity
 $commitOrder = $this->getCommitOrder();
 $conn = $this->em->getConnection();
 $conn->beginTransaction();
 try {
 // Collection deletions (deletions of complete collections)
 foreach ($this->collectionDeletions as $collectionToDelete) {
 if (!$collectionToDelete instanceof PersistentCollection) {
 $this->getCollectionPersister($collectionToDelete->getMapping())->delete($collectionToDelete);
 continue;
 }
 // Deferred explicit tracked collections can be removed only when owning relation was persisted
 $owner = $collectionToDelete->getOwner();
 if ($this->em->getClassMetadata(get_class($owner))->isChangeTrackingDeferredImplicit() || $this->isScheduledForDirtyCheck($owner)) {
 $this->getCollectionPersister($collectionToDelete->getMapping())->delete($collectionToDelete);
 }
 }
 if ($this->entityInsertions) {
 foreach ($commitOrder as $class) {
 $this->executeInserts($class);
 }
 }
 if ($this->entityUpdates) {
 foreach ($commitOrder as $class) {
 $this->executeUpdates($class);
 }
 }
 // Extra updates that were requested by persisters.
 if ($this->extraUpdates) {
 $this->executeExtraUpdates();
 }
 // Collection updates (deleteRows, updateRows, insertRows)
 foreach ($this->collectionUpdates as $collectionToUpdate) {
 $this->getCollectionPersister($collectionToUpdate->getMapping())->update($collectionToUpdate);
 }
 // Entity deletions come last and need to be in reverse commit order
 if ($this->entityDeletions) {
 for ($count = count($commitOrder), $i = $count - 1; $i >= 0 && $this->entityDeletions; --$i) {
 $this->executeDeletions($commitOrder[$i]);
 }
 }
 // Commit failed silently
 if ($conn->commit() === \false) {
 $object = is_object($entity) ? $entity : null;
 throw new OptimisticLockException('Commit failed', $object);
 }
 } catch (Throwable $e) {
 $this->em->close();
 if ($conn->isTransactionActive()) {
 $conn->rollBack();
 }
 $this->afterTransactionRolledBack();
 throw $e;
 }
 $this->afterTransactionComplete();
 // Take new snapshots from visited collections
 foreach ($this->visitedCollections as $coll) {
 $coll->takeSnapshot();
 }
 $this->dispatchPostFlushEvent();
 $this->postCommitCleanup($entity);
 }
 private function postCommitCleanup($entity) : void
 {
 $this->entityInsertions = $this->entityUpdates = $this->entityDeletions = $this->extraUpdates = $this->collectionUpdates = $this->nonCascadedNewDetectedEntities = $this->collectionDeletions = $this->visitedCollections = $this->orphanRemovals = [];
 if ($entity === null) {
 $this->entityChangeSets = $this->scheduledForSynchronization = [];
 return;
 }
 $entities = is_object($entity) ? [$entity] : $entity;
 foreach ($entities as $object) {
 $oid = spl_object_id($object);
 $this->clearEntityChangeSet($oid);
 unset($this->scheduledForSynchronization[$this->em->getClassMetadata(get_class($object))->rootEntityName][$oid]);
 }
 }
 private function computeScheduleInsertsChangeSets() : void
 {
 foreach ($this->entityInsertions as $entity) {
 $class = $this->em->getClassMetadata(get_class($entity));
 $this->computeChangeSet($class, $entity);
 }
 }
 private function computeSingleEntityChangeSet($entity) : void
 {
 $state = $this->getEntityState($entity);
 if ($state !== self::STATE_MANAGED && $state !== self::STATE_REMOVED) {
 throw new InvalidArgumentException('Entity has to be managed or scheduled for removal for single computation ' . self::objToStr($entity));
 }
 $class = $this->em->getClassMetadata(get_class($entity));
 if ($state === self::STATE_MANAGED && $class->isChangeTrackingDeferredImplicit()) {
 $this->persist($entity);
 }
 // Compute changes for INSERTed entities first. This must always happen even in this case.
 $this->computeScheduleInsertsChangeSets();
 if ($class->isReadOnly) {
 return;
 }
 // Ignore uninitialized proxy objects
 if ($entity instanceof Proxy && !$entity->__isInitialized()) {
 return;
 }
 // Only MANAGED entities that are NOT SCHEDULED FOR INSERTION OR DELETION are processed here.
 $oid = spl_object_id($entity);
 if (!isset($this->entityInsertions[$oid]) && !isset($this->entityDeletions[$oid]) && isset($this->entityStates[$oid])) {
 $this->computeChangeSet($class, $entity);
 }
 }
 private function executeExtraUpdates() : void
 {
 foreach ($this->extraUpdates as $oid => $update) {
 [$entity, $changeset] = $update;
 $this->entityChangeSets[$oid] = $changeset;
 $this->getEntityPersister(get_class($entity))->update($entity);
 }
 $this->extraUpdates = [];
 }
 public function &getEntityChangeSet($entity)
 {
 $oid = spl_object_id($entity);
 $data = [];
 if (!isset($this->entityChangeSets[$oid])) {
 return $data;
 }
 return $this->entityChangeSets[$oid];
 }
 public function computeChangeSet(ClassMetadata $class, $entity)
 {
 $oid = spl_object_id($entity);
 if (isset($this->readOnlyObjects[$oid])) {
 return;
 }
 if (!$class->isInheritanceTypeNone()) {
 $class = $this->em->getClassMetadata(get_class($entity));
 }
 $invoke = $this->listenersInvoker->getSubscribedSystems($class, Events::preFlush) & ~ListenersInvoker::INVOKE_MANAGER;
 if ($invoke !== ListenersInvoker::INVOKE_NONE) {
 $this->listenersInvoker->invoke($class, Events::preFlush, $entity, new PreFlushEventArgs($this->em), $invoke);
 }
 $actualData = [];
 foreach ($class->reflFields as $name => $refProp) {
 $value = $refProp->getValue($entity);
 if ($class->isCollectionValuedAssociation($name) && $value !== null) {
 if ($value instanceof PersistentCollection) {
 if ($value->getOwner() === $entity) {
 continue;
 }
 $value = new ArrayCollection($value->getValues());
 }
 // If $value is not a Collection then use an ArrayCollection.
 if (!$value instanceof Collection) {
 $value = new ArrayCollection($value);
 }
 $assoc = $class->associationMappings[$name];
 // Inject PersistentCollection
 $value = new PersistentCollection($this->em, $this->em->getClassMetadata($assoc['targetEntity']), $value);
 $value->setOwner($entity, $assoc);
 $value->setDirty(!$value->isEmpty());
 $class->reflFields[$name]->setValue($entity, $value);
 $actualData[$name] = $value;
 continue;
 }
 if ((!$class->isIdentifier($name) || !$class->isIdGeneratorIdentity()) && $name !== $class->versionField) {
 $actualData[$name] = $value;
 }
 }
 if (!isset($this->originalEntityData[$oid])) {
 // Entity is either NEW or MANAGED but not yet fully persisted (only has an id).
 // These result in an INSERT.
 $this->originalEntityData[$oid] = $actualData;
 $changeSet = [];
 foreach ($actualData as $propName => $actualValue) {
 if (!isset($class->associationMappings[$propName])) {
 $changeSet[$propName] = [null, $actualValue];
 continue;
 }
 $assoc = $class->associationMappings[$propName];
 if ($assoc['isOwningSide'] && $assoc['type'] & ClassMetadata::TO_ONE) {
 $changeSet[$propName] = [null, $actualValue];
 }
 }
 $this->entityChangeSets[$oid] = $changeSet;
 } else {
 // Entity is "fully" MANAGED: it was already fully persisted before
 // and we have a copy of the original data
 $originalData = $this->originalEntityData[$oid];
 $isChangeTrackingNotify = $class->isChangeTrackingNotify();
 $changeSet = $isChangeTrackingNotify && isset($this->entityChangeSets[$oid]) ? $this->entityChangeSets[$oid] : [];
 foreach ($actualData as $propName => $actualValue) {
 // skip field, its a partially omitted one!
 if (!(isset($originalData[$propName]) || array_key_exists($propName, $originalData))) {
 continue;
 }
 $orgValue = $originalData[$propName];
 // skip if value haven't changed
 if ($orgValue === $actualValue) {
 continue;
 }
 // if regular field
 if (!isset($class->associationMappings[$propName])) {
 if ($isChangeTrackingNotify) {
 continue;
 }
 $changeSet[$propName] = [$orgValue, $actualValue];
 continue;
 }
 $assoc = $class->associationMappings[$propName];
 // Persistent collection was exchanged with the "originally"
 // created one. This can only mean it was cloned and replaced
 // on another entity.
 if ($actualValue instanceof PersistentCollection) {
 $owner = $actualValue->getOwner();
 if ($owner === null) {
 // cloned
 $actualValue->setOwner($entity, $assoc);
 } elseif ($owner !== $entity) {
 // no clone, we have to fix
 if (!$actualValue->isInitialized()) {
 $actualValue->initialize();
 // we have to do this otherwise the cols share state
 }
 $newValue = clone $actualValue;
 $newValue->setOwner($entity, $assoc);
 $class->reflFields[$propName]->setValue($entity, $newValue);
 }
 }
 if ($orgValue instanceof PersistentCollection) {
 // A PersistentCollection was de-referenced, so delete it.
 $coid = spl_object_id($orgValue);
 if (isset($this->collectionDeletions[$coid])) {
 continue;
 }
 $this->collectionDeletions[$coid] = $orgValue;
 $changeSet[$propName] = $orgValue;
 // Signal changeset, to-many assocs will be ignored.
 continue;
 }
 if ($assoc['type'] & ClassMetadata::TO_ONE) {
 if ($assoc['isOwningSide']) {
 $changeSet[$propName] = [$orgValue, $actualValue];
 }
 if ($orgValue !== null && $assoc['orphanRemoval']) {
 $this->scheduleOrphanRemoval($orgValue);
 }
 }
 }
 if ($changeSet) {
 $this->entityChangeSets[$oid] = $changeSet;
 $this->originalEntityData[$oid] = $actualData;
 $this->entityUpdates[$oid] = $entity;
 }
 }
 // Look for changes in associations of the entity
 foreach ($class->associationMappings as $field => $assoc) {
 $val = $class->reflFields[$field]->getValue($entity);
 if ($val === null) {
 continue;
 }
 $this->computeAssociationChanges($assoc, $val);
 if (!isset($this->entityChangeSets[$oid]) && $assoc['isOwningSide'] && $assoc['type'] === ClassMetadata::MANY_TO_MANY && $val instanceof PersistentCollection && $val->isDirty()) {
 $this->entityChangeSets[$oid] = [];
 $this->originalEntityData[$oid] = $actualData;
 $this->entityUpdates[$oid] = $entity;
 }
 }
 }
 public function computeChangeSets()
 {
 // Compute changes for INSERTed entities first. This must always happen.
 $this->computeScheduleInsertsChangeSets();
 // Compute changes for other MANAGED entities. Change tracking policies take effect here.
 foreach ($this->identityMap as $className => $entities) {
 $class = $this->em->getClassMetadata($className);
 // Skip class if instances are read-only
 if ($class->isReadOnly) {
 continue;
 }
 // If change tracking is explicit or happens through notification, then only compute
 // changes on entities of that type that are explicitly marked for synchronization.
 switch (\true) {
 case $class->isChangeTrackingDeferredImplicit():
 $entitiesToProcess = $entities;
 break;
 case isset($this->scheduledForSynchronization[$className]):
 $entitiesToProcess = $this->scheduledForSynchronization[$className];
 break;
 default:
 $entitiesToProcess = [];
 }
 foreach ($entitiesToProcess as $entity) {
 // Ignore uninitialized proxy objects
 if ($entity instanceof Proxy && !$entity->__isInitialized()) {
 continue;
 }
 // Only MANAGED entities that are NOT SCHEDULED FOR INSERTION OR DELETION are processed here.
 $oid = spl_object_id($entity);
 if (!isset($this->entityInsertions[$oid]) && !isset($this->entityDeletions[$oid]) && isset($this->entityStates[$oid])) {
 $this->computeChangeSet($class, $entity);
 }
 }
 }
 }
 private function computeAssociationChanges(array $assoc, $value) : void
 {
 if ($value instanceof Proxy && !$value->__isInitialized()) {
 return;
 }
 if ($value instanceof PersistentCollection && $value->isDirty()) {
 $coid = spl_object_id($value);
 $this->collectionUpdates[$coid] = $value;
 $this->visitedCollections[$coid] = $value;
 }
 // Look through the entities, and in any of their associations,
 // for transient (new) entities, recursively. ("Persistence by reachability")
 // Unwrap. Uninitialized collections will simply be empty.
 $unwrappedValue = $assoc['type'] & ClassMetadata::TO_ONE ? [$value] : $value->unwrap();
 $targetClass = $this->em->getClassMetadata($assoc['targetEntity']);
 foreach ($unwrappedValue as $key => $entry) {
 if (!$entry instanceof $targetClass->name) {
 throw ORMInvalidArgumentException::invalidAssociation($targetClass, $assoc, $entry);
 }
 $state = $this->getEntityState($entry, self::STATE_NEW);
 if (!$entry instanceof $assoc['targetEntity']) {
 throw UnexpectedAssociationValue::create($assoc['sourceEntity'], $assoc['fieldName'], get_debug_type($entry), $assoc['targetEntity']);
 }
 switch ($state) {
 case self::STATE_NEW:
 if (!$assoc['isCascadePersist']) {
 $this->nonCascadedNewDetectedEntities[spl_object_id($entry)] = [$assoc, $entry];
 break;
 }
 $this->persistNew($targetClass, $entry);
 $this->computeChangeSet($targetClass, $entry);
 break;
 case self::STATE_REMOVED:
 // Consume the $value as array (it's either an array or an ArrayAccess)
 // and remove the element from Collection.
 if ($assoc['type'] & ClassMetadata::TO_MANY) {
 unset($value[$key]);
 }
 break;
 case self::STATE_DETACHED:
 // Can actually not happen right now as we assume STATE_NEW,
 // so the exception will be raised from the DBAL layer (constraint violation).
 throw ORMInvalidArgumentException::detachedEntityFoundThroughRelationship($assoc, $entry);
 default:
 }
 }
 }
 private function persistNew(ClassMetadata $class, $entity) : void
 {
 $oid = spl_object_id($entity);
 $invoke = $this->listenersInvoker->getSubscribedSystems($class, Events::prePersist);
 if ($invoke !== ListenersInvoker::INVOKE_NONE) {
 $this->listenersInvoker->invoke($class, Events::prePersist, $entity, new LifecycleEventArgs($entity, $this->em), $invoke);
 }
 $idGen = $class->idGenerator;
 if (!$idGen->isPostInsertGenerator()) {
 $idValue = $idGen->generateId($this->em, $entity);
 if (!$idGen instanceof AssignedGenerator) {
 $idValue = [$class->getSingleIdentifierFieldName() => $this->convertSingleFieldIdentifierToPHPValue($class, $idValue)];
 $class->setIdentifierValues($entity, $idValue);
 }
 // Some identifiers may be foreign keys to new entities.
 // In this case, we don't have the value yet and should treat it as if we have a post-insert generator
 if (!$this->hasMissingIdsWhichAreForeignKeys($class, $idValue)) {
 $this->entityIdentifiers[$oid] = $idValue;
 }
 }
 $this->entityStates[$oid] = self::STATE_MANAGED;
 $this->scheduleForInsert($entity);
 }
 private function hasMissingIdsWhichAreForeignKeys(ClassMetadata $class, array $idValue) : bool
 {
 foreach ($idValue as $idField => $idFieldValue) {
 if ($idFieldValue === null && isset($class->associationMappings[$idField])) {
 return \true;
 }
 }
 return \false;
 }
 public function recomputeSingleEntityChangeSet(ClassMetadata $class, $entity)
 {
 $oid = spl_object_id($entity);
 if (!isset($this->entityStates[$oid]) || $this->entityStates[$oid] !== self::STATE_MANAGED) {
 throw ORMInvalidArgumentException::entityNotManaged($entity);
 }
 // skip if change tracking is "NOTIFY"
 if ($class->isChangeTrackingNotify()) {
 return;
 }
 if (!$class->isInheritanceTypeNone()) {
 $class = $this->em->getClassMetadata(get_class($entity));
 }
 $actualData = [];
 foreach ($class->reflFields as $name => $refProp) {
 if ((!$class->isIdentifier($name) || !$class->isIdGeneratorIdentity()) && $name !== $class->versionField && !$class->isCollectionValuedAssociation($name)) {
 $actualData[$name] = $refProp->getValue($entity);
 }
 }
 if (!isset($this->originalEntityData[$oid])) {
 throw new RuntimeException('Cannot call recomputeSingleEntityChangeSet before computeChangeSet on an entity.');
 }
 $originalData = $this->originalEntityData[$oid];
 $changeSet = [];
 foreach ($actualData as $propName => $actualValue) {
 $orgValue = $originalData[$propName] ?? null;
 if ($orgValue !== $actualValue) {
 $changeSet[$propName] = [$orgValue, $actualValue];
 }
 }
 if ($changeSet) {
 if (isset($this->entityChangeSets[$oid])) {
 $this->entityChangeSets[$oid] = array_merge($this->entityChangeSets[$oid], $changeSet);
 } elseif (!isset($this->entityInsertions[$oid])) {
 $this->entityChangeSets[$oid] = $changeSet;
 $this->entityUpdates[$oid] = $entity;
 }
 $this->originalEntityData[$oid] = $actualData;
 }
 }
 private function executeInserts(ClassMetadata $class) : void
 {
 $entities = [];
 $className = $class->name;
 $persister = $this->getEntityPersister($className);
 $invoke = $this->listenersInvoker->getSubscribedSystems($class, Events::postPersist);
 $insertionsForClass = [];
 foreach ($this->entityInsertions as $oid => $entity) {
 if ($this->em->getClassMetadata(get_class($entity))->name !== $className) {
 continue;
 }
 $insertionsForClass[$oid] = $entity;
 $persister->addInsert($entity);
 unset($this->entityInsertions[$oid]);
 if ($invoke !== ListenersInvoker::INVOKE_NONE) {
 $entities[] = $entity;
 }
 }
 $postInsertIds = $persister->executeInserts();
 if ($postInsertIds) {
 // Persister returned post-insert IDs
 foreach ($postInsertIds as $postInsertId) {
 $idField = $class->getSingleIdentifierFieldName();
 $idValue = $this->convertSingleFieldIdentifierToPHPValue($class, $postInsertId['generatedId']);
 $entity = $postInsertId['entity'];
 $oid = spl_object_id($entity);
 $class->reflFields[$idField]->setValue($entity, $idValue);
 $this->entityIdentifiers[$oid] = [$idField => $idValue];
 $this->entityStates[$oid] = self::STATE_MANAGED;
 $this->originalEntityData[$oid][$idField] = $idValue;
 $this->addToIdentityMap($entity);
 }
 } else {
 foreach ($insertionsForClass as $oid => $entity) {
 if (!isset($this->entityIdentifiers[$oid])) {
 //entity was not added to identity map because some identifiers are foreign keys to new entities.
 //add it now
 $this->addToEntityIdentifiersAndEntityMap($class, $oid, $entity);
 }
 }
 }
 foreach ($entities as $entity) {
 $this->listenersInvoker->invoke($class, Events::postPersist, $entity, new LifecycleEventArgs($entity, $this->em), $invoke);
 }
 }
 private function addToEntityIdentifiersAndEntityMap(ClassMetadata $class, int $oid, $entity) : void
 {
 $identifier = [];
 foreach ($class->getIdentifierFieldNames() as $idField) {
 $origValue = $class->getFieldValue($entity, $idField);
 $value = null;
 if (isset($class->associationMappings[$idField])) {
 // NOTE: Single Columns as associated identifiers only allowed - this constraint it is enforced.
 $value = $this->getSingleIdentifierValue($origValue);
 }
 $identifier[$idField] = $value ?? $origValue;
 $this->originalEntityData[$oid][$idField] = $origValue;
 }
 $this->entityStates[$oid] = self::STATE_MANAGED;
 $this->entityIdentifiers[$oid] = $identifier;
 $this->addToIdentityMap($entity);
 }
 private function executeUpdates(ClassMetadata $class) : void
 {
 $className = $class->name;
 $persister = $this->getEntityPersister($className);
 $preUpdateInvoke = $this->listenersInvoker->getSubscribedSystems($class, Events::preUpdate);
 $postUpdateInvoke = $this->listenersInvoker->getSubscribedSystems($class, Events::postUpdate);
 foreach ($this->entityUpdates as $oid => $entity) {
 if ($this->em->getClassMetadata(get_class($entity))->name !== $className) {
 continue;
 }
 if ($preUpdateInvoke !== ListenersInvoker::INVOKE_NONE) {
 $this->listenersInvoker->invoke($class, Events::preUpdate, $entity, new PreUpdateEventArgs($entity, $this->em, $this->getEntityChangeSet($entity)), $preUpdateInvoke);
 $this->recomputeSingleEntityChangeSet($class, $entity);
 }
 if (!empty($this->entityChangeSets[$oid])) {
 $persister->update($entity);
 }
 unset($this->entityUpdates[$oid]);
 if ($postUpdateInvoke !== ListenersInvoker::INVOKE_NONE) {
 $this->listenersInvoker->invoke($class, Events::postUpdate, $entity, new LifecycleEventArgs($entity, $this->em), $postUpdateInvoke);
 }
 }
 }
 private function executeDeletions(ClassMetadata $class) : void
 {
 $className = $class->name;
 $persister = $this->getEntityPersister($className);
 $invoke = $this->listenersInvoker->getSubscribedSystems($class, Events::postRemove);
 foreach ($this->entityDeletions as $oid => $entity) {
 if ($this->em->getClassMetadata(get_class($entity))->name !== $className) {
 continue;
 }
 $persister->delete($entity);
 unset($this->entityDeletions[$oid], $this->entityIdentifiers[$oid], $this->originalEntityData[$oid], $this->entityStates[$oid]);
 // Entity with this $oid after deletion treated as NEW, even if the $oid
 // is obtained by a new entity because the old one went out of scope.
 //$this->entityStates[$oid] = self::STATE_NEW;
 if (!$class->isIdentifierNatural()) {
 $class->reflFields[$class->identifier[0]]->setValue($entity, null);
 }
 if ($invoke !== ListenersInvoker::INVOKE_NONE) {
 $this->listenersInvoker->invoke($class, Events::postRemove, $entity, new LifecycleEventArgs($entity, $this->em), $invoke);
 }
 }
 }
 private function getCommitOrder() : array
 {
 $calc = $this->getCommitOrderCalculator();
 // See if there are any new classes in the changeset, that are not in the
 // commit order graph yet (don't have a node).
 // We have to inspect changeSet to be able to correctly build dependencies.
 // It is not possible to use IdentityMap here because post inserted ids
 // are not yet available.
 $newNodes = [];
 foreach (array_merge($this->entityInsertions, $this->entityUpdates, $this->entityDeletions) as $entity) {
 $class = $this->em->getClassMetadata(get_class($entity));
 if ($calc->hasNode($class->name)) {
 continue;
 }
 $calc->addNode($class->name, $class);
 $newNodes[] = $class;
 }
 // Calculate dependencies for new nodes
 while ($class = array_pop($newNodes)) {
 foreach ($class->associationMappings as $assoc) {
 if (!($assoc['isOwningSide'] && $assoc['type'] & ClassMetadata::TO_ONE)) {
 continue;
 }
 $targetClass = $this->em->getClassMetadata($assoc['targetEntity']);
 if (!$calc->hasNode($targetClass->name)) {
 $calc->addNode($targetClass->name, $targetClass);
 $newNodes[] = $targetClass;
 }
 $joinColumns = reset($assoc['joinColumns']);
 $calc->addDependency($targetClass->name, $class->name, (int) empty($joinColumns['nullable']));
 // If the target class has mapped subclasses, these share the same dependency.
 if (!$targetClass->subClasses) {
 continue;
 }
 foreach ($targetClass->subClasses as $subClassName) {
 $targetSubClass = $this->em->getClassMetadata($subClassName);
 if (!$calc->hasNode($subClassName)) {
 $calc->addNode($targetSubClass->name, $targetSubClass);
 $newNodes[] = $targetSubClass;
 }
 $calc->addDependency($targetSubClass->name, $class->name, 1);
 }
 }
 }
 return $calc->sort();
 }
 public function scheduleForInsert($entity)
 {
 $oid = spl_object_id($entity);
 if (isset($this->entityUpdates[$oid])) {
 throw new InvalidArgumentException('Dirty entity can not be scheduled for insertion.');
 }
 if (isset($this->entityDeletions[$oid])) {
 throw ORMInvalidArgumentException::scheduleInsertForRemovedEntity($entity);
 }
 if (isset($this->originalEntityData[$oid]) && !isset($this->entityInsertions[$oid])) {
 throw ORMInvalidArgumentException::scheduleInsertForManagedEntity($entity);
 }
 if (isset($this->entityInsertions[$oid])) {
 throw ORMInvalidArgumentException::scheduleInsertTwice($entity);
 }
 $this->entityInsertions[$oid] = $entity;
 if (isset($this->entityIdentifiers[$oid])) {
 $this->addToIdentityMap($entity);
 }
 if ($entity instanceof NotifyPropertyChanged) {
 $entity->addPropertyChangedListener($this);
 }
 }
 public function isScheduledForInsert($entity)
 {
 return isset($this->entityInsertions[spl_object_id($entity)]);
 }
 public function scheduleForUpdate($entity)
 {
 $oid = spl_object_id($entity);
 if (!isset($this->entityIdentifiers[$oid])) {
 throw ORMInvalidArgumentException::entityHasNoIdentity($entity, 'scheduling for update');
 }
 if (isset($this->entityDeletions[$oid])) {
 throw ORMInvalidArgumentException::entityIsRemoved($entity, 'schedule for update');
 }
 if (!isset($this->entityUpdates[$oid]) && !isset($this->entityInsertions[$oid])) {
 $this->entityUpdates[$oid] = $entity;
 }
 }
 public function scheduleExtraUpdate($entity, array $changeset)
 {
 $oid = spl_object_id($entity);
 $extraUpdate = [$entity, $changeset];
 if (isset($this->extraUpdates[$oid])) {
 [, $changeset2] = $this->extraUpdates[$oid];
 $extraUpdate = [$entity, $changeset + $changeset2];
 }
 $this->extraUpdates[$oid] = $extraUpdate;
 }
 public function isScheduledForUpdate($entity)
 {
 return isset($this->entityUpdates[spl_object_id($entity)]);
 }
 public function isScheduledForDirtyCheck($entity)
 {
 $rootEntityName = $this->em->getClassMetadata(get_class($entity))->rootEntityName;
 return isset($this->scheduledForSynchronization[$rootEntityName][spl_object_id($entity)]);
 }
 public function scheduleForDelete($entity)
 {
 $oid = spl_object_id($entity);
 if (isset($this->entityInsertions[$oid])) {
 if ($this->isInIdentityMap($entity)) {
 $this->removeFromIdentityMap($entity);
 }
 unset($this->entityInsertions[$oid], $this->entityStates[$oid]);
 return;
 // entity has not been persisted yet, so nothing more to do.
 }
 if (!$this->isInIdentityMap($entity)) {
 return;
 }
 $this->removeFromIdentityMap($entity);
 unset($this->entityUpdates[$oid]);
 if (!isset($this->entityDeletions[$oid])) {
 $this->entityDeletions[$oid] = $entity;
 $this->entityStates[$oid] = self::STATE_REMOVED;
 }
 }
 public function isScheduledForDelete($entity)
 {
 return isset($this->entityDeletions[spl_object_id($entity)]);
 }
 public function isEntityScheduled($entity)
 {
 $oid = spl_object_id($entity);
 return isset($this->entityInsertions[$oid]) || isset($this->entityUpdates[$oid]) || isset($this->entityDeletions[$oid]);
 }
 public function addToIdentityMap($entity)
 {
 $classMetadata = $this->em->getClassMetadata(get_class($entity));
 $identifier = $this->entityIdentifiers[spl_object_id($entity)];
 if (empty($identifier) || in_array(null, $identifier, \true)) {
 throw ORMInvalidArgumentException::entityWithoutIdentity($classMetadata->name, $entity);
 }
 $idHash = implode(' ', $identifier);
 $className = $classMetadata->rootEntityName;
 if (isset($this->identityMap[$className][$idHash])) {
 return \false;
 }
 $this->identityMap[$className][$idHash] = $entity;
 return \true;
 }
 public function getEntityState($entity, $assume = null)
 {
 $oid = spl_object_id($entity);
 if (isset($this->entityStates[$oid])) {
 return $this->entityStates[$oid];
 }
 if ($assume !== null) {
 return $assume;
 }
 // State can only be NEW or DETACHED, because MANAGED/REMOVED states are known.
 // Note that you can not remember the NEW or DETACHED state in _entityStates since
 // the UoW does not hold references to such objects and the object hash can be reused.
 // More generally because the state may "change" between NEW/DETACHED without the UoW being aware of it.
 $class = $this->em->getClassMetadata(get_class($entity));
 $id = $class->getIdentifierValues($entity);
 if (!$id) {
 return self::STATE_NEW;
 }
 if ($class->containsForeignIdentifier) {
 $id = $this->identifierFlattener->flattenIdentifier($class, $id);
 }
 switch (\true) {
 case $class->isIdentifierNatural():
 // Check for a version field, if available, to avoid a db lookup.
 if ($class->isVersioned) {
 return $class->getFieldValue($entity, $class->versionField) ? self::STATE_DETACHED : self::STATE_NEW;
 }
 // Last try before db lookup: check the identity map.
 if ($this->tryGetById($id, $class->rootEntityName)) {
 return self::STATE_DETACHED;
 }
 // db lookup
 if ($this->getEntityPersister($class->name)->exists($entity)) {
 return self::STATE_DETACHED;
 }
 return self::STATE_NEW;
 case !$class->idGenerator->isPostInsertGenerator():
 // if we have a pre insert generator we can't be sure that having an id
 // really means that the entity exists. We have to verify this through
 // the last resort: a db lookup
 // Last try before db lookup: check the identity map.
 if ($this->tryGetById($id, $class->rootEntityName)) {
 return self::STATE_DETACHED;
 }
 // db lookup
 if ($this->getEntityPersister($class->name)->exists($entity)) {
 return self::STATE_DETACHED;
 }
 return self::STATE_NEW;
 default:
 return self::STATE_DETACHED;
 }
 }
 public function removeFromIdentityMap($entity)
 {
 $oid = spl_object_id($entity);
 $classMetadata = $this->em->getClassMetadata(get_class($entity));
 $idHash = implode(' ', $this->entityIdentifiers[$oid]);
 if ($idHash === '') {
 throw ORMInvalidArgumentException::entityHasNoIdentity($entity, 'remove from identity map');
 }
 $className = $classMetadata->rootEntityName;
 if (isset($this->identityMap[$className][$idHash])) {
 unset($this->identityMap[$className][$idHash], $this->readOnlyObjects[$oid]);
 //$this->entityStates[$oid] = self::STATE_DETACHED;
 return \true;
 }
 return \false;
 }
 public function getByIdHash($idHash, $rootClassName)
 {
 return $this->identityMap[$rootClassName][$idHash];
 }
 public function tryGetByIdHash($idHash, $rootClassName)
 {
 $stringIdHash = (string) $idHash;
 return $this->identityMap[$rootClassName][$stringIdHash] ?? \false;
 }
 public function isInIdentityMap($entity)
 {
 $oid = spl_object_id($entity);
 if (empty($this->entityIdentifiers[$oid])) {
 return \false;
 }
 $classMetadata = $this->em->getClassMetadata(get_class($entity));
 $idHash = implode(' ', $this->entityIdentifiers[$oid]);
 return isset($this->identityMap[$classMetadata->rootEntityName][$idHash]);
 }
 public function containsIdHash($idHash, $rootClassName)
 {
 return isset($this->identityMap[$rootClassName][$idHash]);
 }
 public function persist($entity)
 {
 $visited = [];
 $this->doPersist($entity, $visited);
 }
 private function doPersist($entity, array &$visited) : void
 {
 $oid = spl_object_id($entity);
 if (isset($visited[$oid])) {
 return;
 // Prevent infinite recursion
 }
 $visited[$oid] = $entity;
 // Mark visited
 $class = $this->em->getClassMetadata(get_class($entity));
 // We assume NEW, so DETACHED entities result in an exception on flush (constraint violation).
 // If we would detect DETACHED here we would throw an exception anyway with the same
 // consequences (not recoverable/programming error), so just assuming NEW here
 // lets us avoid some database lookups for entities with natural identifiers.
 $entityState = $this->getEntityState($entity, self::STATE_NEW);
 switch ($entityState) {
 case self::STATE_MANAGED:
 // Nothing to do, except if policy is "deferred explicit"
 if ($class->isChangeTrackingDeferredExplicit()) {
 $this->scheduleForDirtyCheck($entity);
 }
 break;
 case self::STATE_NEW:
 $this->persistNew($class, $entity);
 break;
 case self::STATE_REMOVED:
 // Entity becomes managed again
 unset($this->entityDeletions[$oid]);
 $this->addToIdentityMap($entity);
 $this->entityStates[$oid] = self::STATE_MANAGED;
 if ($class->isChangeTrackingDeferredExplicit()) {
 $this->scheduleForDirtyCheck($entity);
 }
 break;
 case self::STATE_DETACHED:
 // Can actually not happen right now since we assume STATE_NEW.
 throw ORMInvalidArgumentException::detachedEntityCannot($entity, 'persisted');
 default:
 throw new UnexpectedValueException(sprintf('Unexpected entity state: %s. %s', $entityState, self::objToStr($entity)));
 }
 $this->cascadePersist($entity, $visited);
 }
 public function remove($entity)
 {
 $visited = [];
 $this->doRemove($entity, $visited);
 }
 private function doRemove($entity, array &$visited) : void
 {
 $oid = spl_object_id($entity);
 if (isset($visited[$oid])) {
 return;
 // Prevent infinite recursion
 }
 $visited[$oid] = $entity;
 // mark visited
 // Cascade first, because scheduleForDelete() removes the entity from the identity map, which
 // can cause problems when a lazy proxy has to be initialized for the cascade operation.
 $this->cascadeRemove($entity, $visited);
 $class = $this->em->getClassMetadata(get_class($entity));
 $entityState = $this->getEntityState($entity);
 switch ($entityState) {
 case self::STATE_NEW:
 case self::STATE_REMOVED:
 // nothing to do
 break;
 case self::STATE_MANAGED:
 $invoke = $this->listenersInvoker->getSubscribedSystems($class, Events::preRemove);
 if ($invoke !== ListenersInvoker::INVOKE_NONE) {
 $this->listenersInvoker->invoke($class, Events::preRemove, $entity, new LifecycleEventArgs($entity, $this->em), $invoke);
 }
 $this->scheduleForDelete($entity);
 break;
 case self::STATE_DETACHED:
 throw ORMInvalidArgumentException::detachedEntityCannot($entity, 'removed');
 default:
 throw new UnexpectedValueException(sprintf('Unexpected entity state: %s. %s', $entityState, self::objToStr($entity)));
 }
 }
 public function merge($entity)
 {
 $visited = [];
 return $this->doMerge($entity, $visited);
 }
 private function doMerge($entity, array &$visited, $prevManagedCopy = null, array $assoc = [])
 {
 $oid = spl_object_id($entity);
 if (isset($visited[$oid])) {
 $managedCopy = $visited[$oid];
 if ($prevManagedCopy !== null) {
 $this->updateAssociationWithMergedEntity($entity, $assoc, $prevManagedCopy, $managedCopy);
 }
 return $managedCopy;
 }
 $class = $this->em->getClassMetadata(get_class($entity));
 // First we assume DETACHED, although it can still be NEW but we can avoid
 // an extra db-roundtrip this way. If it is not MANAGED but has an identity,
 // we need to fetch it from the db anyway in order to merge.
 // MANAGED entities are ignored by the merge operation.
 $managedCopy = $entity;
 if ($this->getEntityState($entity, self::STATE_DETACHED) !== self::STATE_MANAGED) {
 // Try to look the entity up in the identity map.
 $id = $class->getIdentifierValues($entity);
 // If there is no ID, it is actually NEW.
 if (!$id) {
 $managedCopy = $this->newInstance($class);
 $this->mergeEntityStateIntoManagedCopy($entity, $managedCopy);
 $this->persistNew($class, $managedCopy);
 } else {
 $flatId = $class->containsForeignIdentifier ? $this->identifierFlattener->flattenIdentifier($class, $id) : $id;
 $managedCopy = $this->tryGetById($flatId, $class->rootEntityName);
 if ($managedCopy) {
 // We have the entity in-memory already, just make sure its not removed.
 if ($this->getEntityState($managedCopy) === self::STATE_REMOVED) {
 throw ORMInvalidArgumentException::entityIsRemoved($managedCopy, 'merge');
 }
 } else {
 // We need to fetch the managed copy in order to merge.
 $managedCopy = $this->em->find($class->name, $flatId);
 }
 if ($managedCopy === null) {
 // If the identifier is ASSIGNED, it is NEW, otherwise an error
 // since the managed entity was not found.
 if (!$class->isIdentifierNatural()) {
 throw EntityNotFoundException::fromClassNameAndIdentifier($class->getName(), $this->identifierFlattener->flattenIdentifier($class, $id));
 }
 $managedCopy = $this->newInstance($class);
 $class->setIdentifierValues($managedCopy, $id);
 $this->mergeEntityStateIntoManagedCopy($entity, $managedCopy);
 $this->persistNew($class, $managedCopy);
 } else {
 $this->ensureVersionMatch($class, $entity, $managedCopy);
 $this->mergeEntityStateIntoManagedCopy($entity, $managedCopy);
 }
 }
 $visited[$oid] = $managedCopy;
 // mark visited
 if ($class->isChangeTrackingDeferredExplicit()) {
 $this->scheduleForDirtyCheck($entity);
 }
 }
 if ($prevManagedCopy !== null) {
 $this->updateAssociationWithMergedEntity($entity, $assoc, $prevManagedCopy, $managedCopy);
 }
 // Mark the managed copy visited as well
 $visited[spl_object_id($managedCopy)] = $managedCopy;
 $this->cascadeMerge($entity, $managedCopy, $visited);
 return $managedCopy;
 }
 private function ensureVersionMatch(ClassMetadata $class, $entity, $managedCopy) : void
 {
 if (!($class->isVersioned && $this->isLoaded($managedCopy) && $this->isLoaded($entity))) {
 return;
 }
 $reflField = $class->reflFields[$class->versionField];
 $managedCopyVersion = $reflField->getValue($managedCopy);
 $entityVersion = $reflField->getValue($entity);
 // Throw exception if versions don't match.
 // phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators.DisallowedEqualOperator
 if ($managedCopyVersion == $entityVersion) {
 return;
 }
 throw OptimisticLockException::lockFailedVersionMismatch($entity, $entityVersion, $managedCopyVersion);
 }
 private function isLoaded($entity) : bool
 {
 return !$entity instanceof Proxy || $entity->__isInitialized();
 }
 private function updateAssociationWithMergedEntity($entity, array $association, $previousManagedCopy, $managedCopy) : void
 {
 $assocField = $association['fieldName'];
 $prevClass = $this->em->getClassMetadata(get_class($previousManagedCopy));
 if ($association['type'] & ClassMetadata::TO_ONE) {
 $prevClass->reflFields[$assocField]->setValue($previousManagedCopy, $managedCopy);
 return;
 }
 $value = $prevClass->reflFields[$assocField]->getValue($previousManagedCopy);
 $value[] = $managedCopy;
 if ($association['type'] === ClassMetadata::ONE_TO_MANY) {
 $class = $this->em->getClassMetadata(get_class($entity));
 $class->reflFields[$association['mappedBy']]->setValue($managedCopy, $previousManagedCopy);
 }
 }
 public function detach($entity)
 {
 $visited = [];
 $this->doDetach($entity, $visited);
 }
 private function doDetach($entity, array &$visited, bool $noCascade = \false) : void
 {
 $oid = spl_object_id($entity);
 if (isset($visited[$oid])) {
 return;
 // Prevent infinite recursion
 }
 $visited[$oid] = $entity;
 // mark visited
 switch ($this->getEntityState($entity, self::STATE_DETACHED)) {
 case self::STATE_MANAGED:
 if ($this->isInIdentityMap($entity)) {
 $this->removeFromIdentityMap($entity);
 }
 unset($this->entityInsertions[$oid], $this->entityUpdates[$oid], $this->entityDeletions[$oid], $this->entityIdentifiers[$oid], $this->entityStates[$oid], $this->originalEntityData[$oid]);
 break;
 case self::STATE_NEW:
 case self::STATE_DETACHED:
 return;
 }
 if (!$noCascade) {
 $this->cascadeDetach($entity, $visited);
 }
 }
 public function refresh($entity)
 {
 $visited = [];
 $this->doRefresh($entity, $visited);
 }
 private function doRefresh($entity, array &$visited) : void
 {
 $oid = spl_object_id($entity);
 if (isset($visited[$oid])) {
 return;
 // Prevent infinite recursion
 }
 $visited[$oid] = $entity;
 // mark visited
 $class = $this->em->getClassMetadata(get_class($entity));
 if ($this->getEntityState($entity) !== self::STATE_MANAGED) {
 throw ORMInvalidArgumentException::entityNotManaged($entity);
 }
 $this->getEntityPersister($class->name)->refresh(array_combine($class->getIdentifierFieldNames(), $this->entityIdentifiers[$oid]), $entity);
 $this->cascadeRefresh($entity, $visited);
 }
 private function cascadeRefresh($entity, array &$visited) : void
 {
 $class = $this->em->getClassMetadata(get_class($entity));
 $associationMappings = array_filter($class->associationMappings, static function ($assoc) {
 return $assoc['isCascadeRefresh'];
 });
 foreach ($associationMappings as $assoc) {
 $relatedEntities = $class->reflFields[$assoc['fieldName']]->getValue($entity);
 switch (\true) {
 case $relatedEntities instanceof PersistentCollection:
 // Unwrap so that foreach() does not initialize
 $relatedEntities = $relatedEntities->unwrap();
 // break; is commented intentionally!
 case $relatedEntities instanceof Collection:
 case is_array($relatedEntities):
 foreach ($relatedEntities as $relatedEntity) {
 $this->doRefresh($relatedEntity, $visited);
 }
 break;
 case $relatedEntities !== null:
 $this->doRefresh($relatedEntities, $visited);
 break;
 default:
 }
 }
 }
 private function cascadeDetach($entity, array &$visited) : void
 {
 $class = $this->em->getClassMetadata(get_class($entity));
 $associationMappings = array_filter($class->associationMappings, static function ($assoc) {
 return $assoc['isCascadeDetach'];
 });
 foreach ($associationMappings as $assoc) {
 $relatedEntities = $class->reflFields[$assoc['fieldName']]->getValue($entity);
 switch (\true) {
 case $relatedEntities instanceof PersistentCollection:
 // Unwrap so that foreach() does not initialize
 $relatedEntities = $relatedEntities->unwrap();
 // break; is commented intentionally!
 case $relatedEntities instanceof Collection:
 case is_array($relatedEntities):
 foreach ($relatedEntities as $relatedEntity) {
 $this->doDetach($relatedEntity, $visited);
 }
 break;
 case $relatedEntities !== null:
 $this->doDetach($relatedEntities, $visited);
 break;
 default:
 }
 }
 }
 private function cascadeMerge($entity, $managedCopy, array &$visited) : void
 {
 $class = $this->em->getClassMetadata(get_class($entity));
 $associationMappings = array_filter($class->associationMappings, static function ($assoc) {
 return $assoc['isCascadeMerge'];
 });
 foreach ($associationMappings as $assoc) {
 $relatedEntities = $class->reflFields[$assoc['fieldName']]->getValue($entity);
 if ($relatedEntities instanceof Collection) {
 if ($relatedEntities === $class->reflFields[$assoc['fieldName']]->getValue($managedCopy)) {
 continue;
 }
 if ($relatedEntities instanceof PersistentCollection) {
 // Unwrap so that foreach() does not initialize
 $relatedEntities = $relatedEntities->unwrap();
 }
 foreach ($relatedEntities as $relatedEntity) {
 $this->doMerge($relatedEntity, $visited, $managedCopy, $assoc);
 }
 } elseif ($relatedEntities !== null) {
 $this->doMerge($relatedEntities, $visited, $managedCopy, $assoc);
 }
 }
 }
 private function cascadePersist($entity, array &$visited) : void
 {
 $class = $this->em->getClassMetadata(get_class($entity));
 $associationMappings = array_filter($class->associationMappings, static function ($assoc) {
 return $assoc['isCascadePersist'];
 });
 foreach ($associationMappings as $assoc) {
 $relatedEntities = $class->reflFields[$assoc['fieldName']]->getValue($entity);
 switch (\true) {
 case $relatedEntities instanceof PersistentCollection:
 // Unwrap so that foreach() does not initialize
 $relatedEntities = $relatedEntities->unwrap();
 // break; is commented intentionally!
 case $relatedEntities instanceof Collection:
 case is_array($relatedEntities):
 if (($assoc['type'] & ClassMetadata::TO_MANY) <= 0) {
 throw ORMInvalidArgumentException::invalidAssociation($this->em->getClassMetadata($assoc['targetEntity']), $assoc, $relatedEntities);
 }
 foreach ($relatedEntities as $relatedEntity) {
 $this->doPersist($relatedEntity, $visited);
 }
 break;
 case $relatedEntities !== null:
 if (!$relatedEntities instanceof $assoc['targetEntity']) {
 throw ORMInvalidArgumentException::invalidAssociation($this->em->getClassMetadata($assoc['targetEntity']), $assoc, $relatedEntities);
 }
 $this->doPersist($relatedEntities, $visited);
 break;
 default:
 }
 }
 }
 private function cascadeRemove($entity, array &$visited) : void
 {
 $class = $this->em->getClassMetadata(get_class($entity));
 $associationMappings = array_filter($class->associationMappings, static function ($assoc) {
 return $assoc['isCascadeRemove'];
 });
 $entitiesToCascade = [];
 foreach ($associationMappings as $assoc) {
 if ($entity instanceof Proxy && !$entity->__isInitialized()) {
 $entity->__load();
 }
 $relatedEntities = $class->reflFields[$assoc['fieldName']]->getValue($entity);
 switch (\true) {
 case $relatedEntities instanceof Collection:
 case is_array($relatedEntities):
 // If its a PersistentCollection initialization is intended! No unwrap!
 foreach ($relatedEntities as $relatedEntity) {
 $entitiesToCascade[] = $relatedEntity;
 }
 break;
 case $relatedEntities !== null:
 $entitiesToCascade[] = $relatedEntities;
 break;
 default:
 }
 }
 foreach ($entitiesToCascade as $relatedEntity) {
 $this->doRemove($relatedEntity, $visited);
 }
 }
 public function lock($entity, int $lockMode, $lockVersion = null) : void
 {
 if ($this->getEntityState($entity, self::STATE_DETACHED) !== self::STATE_MANAGED) {
 throw ORMInvalidArgumentException::entityNotManaged($entity);
 }
 $class = $this->em->getClassMetadata(get_class($entity));
 switch (\true) {
 case $lockMode === LockMode::OPTIMISTIC:
 if (!$class->isVersioned) {
 throw OptimisticLockException::notVersioned($class->name);
 }
 if ($lockVersion === null) {
 return;
 }
 if ($entity instanceof Proxy && !$entity->__isInitialized()) {
 $entity->__load();
 }
 $entityVersion = $class->reflFields[$class->versionField]->getValue($entity);
 // phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators.DisallowedNotEqualOperator
 if ($entityVersion != $lockVersion) {
 throw OptimisticLockException::lockFailedVersionMismatch($entity, $lockVersion, $entityVersion);
 }
 break;
 case $lockMode === LockMode::NONE:
 case $lockMode === LockMode::PESSIMISTIC_READ:
 case $lockMode === LockMode::PESSIMISTIC_WRITE:
 if (!$this->em->getConnection()->isTransactionActive()) {
 throw TransactionRequiredException::transactionRequired();
 }
 $oid = spl_object_id($entity);
 $this->getEntityPersister($class->name)->lock(array_combine($class->getIdentifierFieldNames(), $this->entityIdentifiers[$oid]), $lockMode);
 break;
 default:
 }
 }
 public function getCommitOrderCalculator()
 {
 return new Internal\CommitOrderCalculator();
 }
 public function clear($entityName = null)
 {
 if ($entityName === null) {
 $this->identityMap = $this->entityIdentifiers = $this->originalEntityData = $this->entityChangeSets = $this->entityStates = $this->scheduledForSynchronization = $this->entityInsertions = $this->entityUpdates = $this->entityDeletions = $this->nonCascadedNewDetectedEntities = $this->collectionDeletions = $this->collectionUpdates = $this->extraUpdates = $this->readOnlyObjects = $this->visitedCollections = $this->eagerLoadingEntities = $this->orphanRemovals = [];
 } else {
 Deprecation::triggerIfCalledFromOutside('doctrine/orm', 'https://github.com/doctrine/orm/issues/8460', 'Calling %s() with any arguments to clear specific entities is deprecated and will not be supported in Doctrine ORM 3.0.', __METHOD__);
 $this->clearIdentityMapForEntityName($entityName);
 $this->clearEntityInsertionsForEntityName($entityName);
 }
 if ($this->evm->hasListeners(Events::onClear)) {
 $this->evm->dispatchEvent(Events::onClear, new Event\OnClearEventArgs($this->em, $entityName));
 }
 }
 public function scheduleOrphanRemoval($entity)
 {
 $this->orphanRemovals[spl_object_id($entity)] = $entity;
 }
 public function cancelOrphanRemoval($entity)
 {
 unset($this->orphanRemovals[spl_object_id($entity)]);
 }
 public function scheduleCollectionDeletion(PersistentCollection $coll)
 {
 $coid = spl_object_id($coll);
 // TODO: if $coll is already scheduled for recreation ... what to do?
 // Just remove $coll from the scheduled recreations?
 unset($this->collectionUpdates[$coid]);
 $this->collectionDeletions[$coid] = $coll;
 }
 public function isCollectionScheduledForDeletion(PersistentCollection $coll)
 {
 return isset($this->collectionDeletions[spl_object_id($coll)]);
 }
 private function newInstance(ClassMetadata $class)
 {
 $entity = $class->newInstance();
 if ($entity instanceof ObjectManagerAware) {
 $entity->injectObjectManager($this->em, $class);
 }
 return $entity;
 }
 public function createEntity($className, array $data, &$hints = [])
 {
 $class = $this->em->getClassMetadata($className);
 $id = $this->identifierFlattener->flattenIdentifier($class, $data);
 $idHash = implode(' ', $id);
 if (isset($this->identityMap[$class->rootEntityName][$idHash])) {
 $entity = $this->identityMap[$class->rootEntityName][$idHash];
 $oid = spl_object_id($entity);
 if (isset($hints[Query::HINT_REFRESH], $hints[Query::HINT_REFRESH_ENTITY])) {
 $unmanagedProxy = $hints[Query::HINT_REFRESH_ENTITY];
 if ($unmanagedProxy !== $entity && $unmanagedProxy instanceof Proxy && $this->isIdentifierEquals($unmanagedProxy, $entity)) {
 // DDC-1238 - we have a managed instance, but it isn't the provided one.
 // Therefore we clear its identifier. Also, we must re-fetch metadata since the
 // refreshed object may be anything
 foreach ($class->identifier as $fieldName) {
 $class->reflFields[$fieldName]->setValue($unmanagedProxy, null);
 }
 return $unmanagedProxy;
 }
 }
 if ($entity instanceof Proxy && !$entity->__isInitialized()) {
 $entity->__setInitialized(\true);
 if ($entity instanceof NotifyPropertyChanged) {
 $entity->addPropertyChangedListener($this);
 }
 } else {
 if (!isset($hints[Query::HINT_REFRESH]) || isset($hints[Query::HINT_REFRESH_ENTITY]) && $hints[Query::HINT_REFRESH_ENTITY] !== $entity) {
 return $entity;
 }
 }
 // inject ObjectManager upon refresh.
 if ($entity instanceof ObjectManagerAware) {
 $entity->injectObjectManager($this->em, $class);
 }
 $this->originalEntityData[$oid] = $data;
 } else {
 $entity = $this->newInstance($class);
 $oid = spl_object_id($entity);
 $this->entityIdentifiers[$oid] = $id;
 $this->entityStates[$oid] = self::STATE_MANAGED;
 $this->originalEntityData[$oid] = $data;
 $this->identityMap[$class->rootEntityName][$idHash] = $entity;
 if ($entity instanceof NotifyPropertyChanged) {
 $entity->addPropertyChangedListener($this);
 }
 if (isset($hints[Query::HINT_READ_ONLY])) {
 $this->readOnlyObjects[$oid] = \true;
 }
 }
 foreach ($data as $field => $value) {
 if (isset($class->fieldMappings[$field])) {
 $class->reflFields[$field]->setValue($entity, $value);
 }
 }
 // Loading the entity right here, if its in the eager loading map get rid of it there.
 unset($this->eagerLoadingEntities[$class->rootEntityName][$idHash]);
 if (isset($this->eagerLoadingEntities[$class->rootEntityName]) && !$this->eagerLoadingEntities[$class->rootEntityName]) {
 unset($this->eagerLoadingEntities[$class->rootEntityName]);
 }
 // Properly initialize any unfetched associations, if partial objects are not allowed.
 if (isset($hints[Query::HINT_FORCE_PARTIAL_LOAD])) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8471', 'Partial Objects are deprecated (here entity %s)', $className);
 return $entity;
 }
 foreach ($class->associationMappings as $field => $assoc) {
 // Check if the association is not among the fetch-joined associations already.
 if (isset($hints['fetchAlias'], $hints['fetched'][$hints['fetchAlias']][$field])) {
 continue;
 }
 $targetClass = $this->em->getClassMetadata($assoc['targetEntity']);
 switch (\true) {
 case $assoc['type'] & ClassMetadata::TO_ONE:
 if (!$assoc['isOwningSide']) {
 // use the given entity association
 if (isset($data[$field]) && is_object($data[$field]) && isset($this->entityStates[spl_object_id($data[$field])])) {
 $this->originalEntityData[$oid][$field] = $data[$field];
 $class->reflFields[$field]->setValue($entity, $data[$field]);
 $targetClass->reflFields[$assoc['mappedBy']]->setValue($data[$field], $entity);
 continue 2;
 }
 // Inverse side of x-to-one can never be lazy
 $class->reflFields[$field]->setValue($entity, $this->getEntityPersister($assoc['targetEntity'])->loadOneToOneEntity($assoc, $entity));
 continue 2;
 }
 // use the entity association
 if (isset($data[$field]) && is_object($data[$field]) && isset($this->entityStates[spl_object_id($data[$field])])) {
 $class->reflFields[$field]->setValue($entity, $data[$field]);
 $this->originalEntityData[$oid][$field] = $data[$field];
 break;
 }
 $associatedId = [];
 // TODO: Is this even computed right in all cases of composite keys?
 foreach ($assoc['targetToSourceKeyColumns'] as $targetColumn => $srcColumn) {
 $joinColumnValue = $data[$srcColumn] ?? null;
 if ($joinColumnValue !== null) {
 if ($targetClass->containsForeignIdentifier) {
 $associatedId[$targetClass->getFieldForColumn($targetColumn)] = $joinColumnValue;
 } else {
 $associatedId[$targetClass->fieldNames[$targetColumn]] = $joinColumnValue;
 }
 } elseif ($targetClass->containsForeignIdentifier && in_array($targetClass->getFieldForColumn($targetColumn), $targetClass->identifier, \true)) {
 // the missing key is part of target's entity primary key
 $associatedId = [];
 break;
 }
 }
 if (!$associatedId) {
 // Foreign key is NULL
 $class->reflFields[$field]->setValue($entity, null);
 $this->originalEntityData[$oid][$field] = null;
 break;
 }
 if (!isset($hints['fetchMode'][$class->name][$field])) {
 $hints['fetchMode'][$class->name][$field] = $assoc['fetch'];
 }
 // Foreign key is set
 // Check identity map first
 // FIXME: Can break easily with composite keys if join column values are in
 // wrong order. The correct order is the one in ClassMetadata#identifier.
 $relatedIdHash = implode(' ', $associatedId);
 switch (\true) {
 case isset($this->identityMap[$targetClass->rootEntityName][$relatedIdHash]):
 $newValue = $this->identityMap[$targetClass->rootEntityName][$relatedIdHash];
 // If this is an uninitialized proxy, we are deferring eager loads,
 // this association is marked as eager fetch, and its an uninitialized proxy (wtf!)
 // then we can append this entity for eager loading!
 if ($hints['fetchMode'][$class->name][$field] === ClassMetadata::FETCH_EAGER && isset($hints[self::HINT_DEFEREAGERLOAD]) && !$targetClass->isIdentifierComposite && $newValue instanceof Proxy && $newValue->__isInitialized() === \false) {
 $this->eagerLoadingEntities[$targetClass->rootEntityName][$relatedIdHash] = current($associatedId);
 }
 break;
 case $targetClass->subClasses:
 // If it might be a subtype, it can not be lazy. There isn't even
 // a way to solve this with deferred eager loading, which means putting
 // an entity with subclasses at a *-to-one location is really bad! (performance-wise)
 $newValue = $this->getEntityPersister($assoc['targetEntity'])->loadOneToOneEntity($assoc, $entity, $associatedId);
 break;
 default:
 switch (\true) {
 // We are negating the condition here. Other cases will assume it is valid!
 case $hints['fetchMode'][$class->name][$field] !== ClassMetadata::FETCH_EAGER:
 $newValue = $this->em->getProxyFactory()->getProxy($assoc['targetEntity'], $associatedId);
 break;
 // Deferred eager load only works for single identifier classes
 case isset($hints[self::HINT_DEFEREAGERLOAD]) && !$targetClass->isIdentifierComposite:
 // TODO: Is there a faster approach?
 $this->eagerLoadingEntities[$targetClass->rootEntityName][$relatedIdHash] = current($associatedId);
 $newValue = $this->em->getProxyFactory()->getProxy($assoc['targetEntity'], $associatedId);
 break;
 default:
 // TODO: This is very imperformant, ignore it?
 $newValue = $this->em->find($assoc['targetEntity'], $associatedId);
 break;
 }
 if ($newValue === null) {
 break;
 }
 // PERF: Inlined & optimized code from UnitOfWork#registerManaged()
 $newValueOid = spl_object_id($newValue);
 $this->entityIdentifiers[$newValueOid] = $associatedId;
 $this->identityMap[$targetClass->rootEntityName][$relatedIdHash] = $newValue;
 if ($newValue instanceof NotifyPropertyChanged && (!$newValue instanceof Proxy || $newValue->__isInitialized())) {
 $newValue->addPropertyChangedListener($this);
 }
 $this->entityStates[$newValueOid] = self::STATE_MANAGED;
 // make sure that when an proxy is then finally loaded, $this->originalEntityData is set also!
 break;
 }
 $this->originalEntityData[$oid][$field] = $newValue;
 $class->reflFields[$field]->setValue($entity, $newValue);
 if ($assoc['inversedBy'] && $assoc['type'] & ClassMetadata::ONE_TO_ONE && $newValue !== null) {
 $inverseAssoc = $targetClass->associationMappings[$assoc['inversedBy']];
 $targetClass->reflFields[$inverseAssoc['fieldName']]->setValue($newValue, $entity);
 }
 break;
 default:
 // Ignore if its a cached collection
 if (isset($hints[Query::HINT_CACHE_ENABLED]) && $class->getFieldValue($entity, $field) instanceof PersistentCollection) {
 break;
 }
 // use the given collection
 if (isset($data[$field]) && $data[$field] instanceof PersistentCollection) {
 $data[$field]->setOwner($entity, $assoc);
 $class->reflFields[$field]->setValue($entity, $data[$field]);
 $this->originalEntityData[$oid][$field] = $data[$field];
 break;
 }
 // Inject collection
 $pColl = new PersistentCollection($this->em, $targetClass, new ArrayCollection());
 $pColl->setOwner($entity, $assoc);
 $pColl->setInitialized(\false);
 $reflField = $class->reflFields[$field];
 $reflField->setValue($entity, $pColl);
 if ($assoc['fetch'] === ClassMetadata::FETCH_EAGER) {
 $this->loadCollection($pColl);
 $pColl->takeSnapshot();
 }
 $this->originalEntityData[$oid][$field] = $pColl;
 break;
 }
 }
 // defer invoking of postLoad event to hydration complete step
 $this->hydrationCompleteHandler->deferPostLoadInvoking($class, $entity);
 return $entity;
 }
 public function triggerEagerLoads()
 {
 if (!$this->eagerLoadingEntities) {
 return;
 }
 // avoid infinite recursion
 $eagerLoadingEntities = $this->eagerLoadingEntities;
 $this->eagerLoadingEntities = [];
 foreach ($eagerLoadingEntities as $entityName => $ids) {
 if (!$ids) {
 continue;
 }
 $class = $this->em->getClassMetadata($entityName);
 $this->getEntityPersister($entityName)->loadAll(array_combine($class->identifier, [array_values($ids)]));
 }
 }
 public function loadCollection(PersistentCollection $collection)
 {
 $assoc = $collection->getMapping();
 $persister = $this->getEntityPersister($assoc['targetEntity']);
 switch ($assoc['type']) {
 case ClassMetadata::ONE_TO_MANY:
 $persister->loadOneToManyCollection($assoc, $collection->getOwner(), $collection);
 break;
 case ClassMetadata::MANY_TO_MANY:
 $persister->loadManyToManyCollection($assoc, $collection->getOwner(), $collection);
 break;
 }
 $collection->setInitialized(\true);
 }
 public function getIdentityMap()
 {
 return $this->identityMap;
 }
 public function getOriginalEntityData($entity)
 {
 $oid = spl_object_id($entity);
 return $this->originalEntityData[$oid] ?? [];
 }
 public function setOriginalEntityData($entity, array $data)
 {
 $this->originalEntityData[spl_object_id($entity)] = $data;
 }
 public function setOriginalEntityProperty($oid, $property, $value)
 {
 $this->originalEntityData[$oid][$property] = $value;
 }
 public function getEntityIdentifier($entity)
 {
 if (!isset($this->entityIdentifiers[spl_object_id($entity)])) {
 throw EntityNotFoundException::noIdentifierFound(get_debug_type($entity));
 }
 return $this->entityIdentifiers[spl_object_id($entity)];
 }
 public function getSingleIdentifierValue($entity)
 {
 $class = $this->em->getClassMetadata(get_class($entity));
 if ($class->isIdentifierComposite) {
 throw ORMInvalidArgumentException::invalidCompositeIdentifier();
 }
 $values = $this->isInIdentityMap($entity) ? $this->getEntityIdentifier($entity) : $class->getIdentifierValues($entity);
 return $values[$class->identifier[0]] ?? null;
 }
 public function tryGetById($id, $rootClassName)
 {
 $idHash = implode(' ', (array) $id);
 return $this->identityMap[$rootClassName][$idHash] ?? \false;
 }
 public function scheduleForDirtyCheck($entity)
 {
 $rootClassName = $this->em->getClassMetadata(get_class($entity))->rootEntityName;
 $this->scheduledForSynchronization[$rootClassName][spl_object_id($entity)] = $entity;
 }
 public function hasPendingInsertions()
 {
 return !empty($this->entityInsertions);
 }
 public function size()
 {
 return array_sum(array_map('count', $this->identityMap));
 }
 public function getEntityPersister($entityName)
 {
 if (isset($this->persisters[$entityName])) {
 return $this->persisters[$entityName];
 }
 $class = $this->em->getClassMetadata($entityName);
 switch (\true) {
 case $class->isInheritanceTypeNone():
 $persister = new BasicEntityPersister($this->em, $class);
 break;
 case $class->isInheritanceTypeSingleTable():
 $persister = new SingleTablePersister($this->em, $class);
 break;
 case $class->isInheritanceTypeJoined():
 $persister = new JoinedSubclassPersister($this->em, $class);
 break;
 default:
 throw new RuntimeException('No persister found for entity.');
 }
 if ($this->hasCache && $class->cache !== null) {
 $persister = $this->em->getConfiguration()->getSecondLevelCacheConfiguration()->getCacheFactory()->buildCachedEntityPersister($this->em, $persister, $class);
 }
 $this->persisters[$entityName] = $persister;
 return $this->persisters[$entityName];
 }
 public function getCollectionPersister(array $association)
 {
 $role = isset($association['cache']) ? $association['sourceEntity'] . '::' . $association['fieldName'] : $association['type'];
 if (isset($this->collectionPersisters[$role])) {
 return $this->collectionPersisters[$role];
 }
 $persister = $association['type'] === ClassMetadata::ONE_TO_MANY ? new OneToManyPersister($this->em) : new ManyToManyPersister($this->em);
 if ($this->hasCache && isset($association['cache'])) {
 $persister = $this->em->getConfiguration()->getSecondLevelCacheConfiguration()->getCacheFactory()->buildCachedCollectionPersister($this->em, $persister, $association);
 }
 $this->collectionPersisters[$role] = $persister;
 return $this->collectionPersisters[$role];
 }
 public function registerManaged($entity, array $id, array $data)
 {
 $oid = spl_object_id($entity);
 $this->entityIdentifiers[$oid] = $id;
 $this->entityStates[$oid] = self::STATE_MANAGED;
 $this->originalEntityData[$oid] = $data;
 $this->addToIdentityMap($entity);
 if ($entity instanceof NotifyPropertyChanged && (!$entity instanceof Proxy || $entity->__isInitialized())) {
 $entity->addPropertyChangedListener($this);
 }
 }
 public function clearEntityChangeSet($oid)
 {
 unset($this->entityChangeSets[$oid]);
 }
 public function propertyChanged($sender, $propertyName, $oldValue, $newValue)
 {
 $oid = spl_object_id($sender);
 $class = $this->em->getClassMetadata(get_class($sender));
 $isAssocField = isset($class->associationMappings[$propertyName]);
 if (!$isAssocField && !isset($class->fieldMappings[$propertyName])) {
 return;
 // ignore non-persistent fields
 }
 // Update changeset and mark entity for synchronization
 $this->entityChangeSets[$oid][$propertyName] = [$oldValue, $newValue];
 if (!isset($this->scheduledForSynchronization[$class->rootEntityName][$oid])) {
 $this->scheduleForDirtyCheck($sender);
 }
 }
 public function getScheduledEntityInsertions()
 {
 return $this->entityInsertions;
 }
 public function getScheduledEntityUpdates()
 {
 return $this->entityUpdates;
 }
 public function getScheduledEntityDeletions()
 {
 return $this->entityDeletions;
 }
 public function getScheduledCollectionDeletions()
 {
 return $this->collectionDeletions;
 }
 public function getScheduledCollectionUpdates()
 {
 return $this->collectionUpdates;
 }
 public function initializeObject($obj)
 {
 if ($obj instanceof Proxy) {
 $obj->__load();
 return;
 }
 if ($obj instanceof PersistentCollection) {
 $obj->initialize();
 }
 }
 private static function objToStr($obj) : string
 {
 return method_exists($obj, '__toString') ? (string) $obj : get_debug_type($obj) . '@' . spl_object_id($obj);
 }
 public function markReadOnly($object)
 {
 if (!is_object($object) || !$this->isInIdentityMap($object)) {
 throw ORMInvalidArgumentException::readOnlyRequiresManagedEntity($object);
 }
 $this->readOnlyObjects[spl_object_id($object)] = \true;
 }
 public function isReadOnly($object)
 {
 if (!is_object($object)) {
 throw ORMInvalidArgumentException::readOnlyRequiresManagedEntity($object);
 }
 return isset($this->readOnlyObjects[spl_object_id($object)]);
 }
 private function afterTransactionComplete() : void
 {
 $this->performCallbackOnCachedPersister(static function (CachedPersister $persister) {
 $persister->afterTransactionComplete();
 });
 }
 private function afterTransactionRolledBack() : void
 {
 $this->performCallbackOnCachedPersister(static function (CachedPersister $persister) {
 $persister->afterTransactionRolledBack();
 });
 }
 private function performCallbackOnCachedPersister(callable $callback) : void
 {
 if (!$this->hasCache) {
 return;
 }
 foreach (array_merge($this->persisters, $this->collectionPersisters) as $persister) {
 if ($persister instanceof CachedPersister) {
 $callback($persister);
 }
 }
 }
 private function dispatchOnFlushEvent() : void
 {
 if ($this->evm->hasListeners(Events::onFlush)) {
 $this->evm->dispatchEvent(Events::onFlush, new OnFlushEventArgs($this->em));
 }
 }
 private function dispatchPostFlushEvent() : void
 {
 if ($this->evm->hasListeners(Events::postFlush)) {
 $this->evm->dispatchEvent(Events::postFlush, new PostFlushEventArgs($this->em));
 }
 }
 private function isIdentifierEquals($entity1, $entity2) : bool
 {
 if ($entity1 === $entity2) {
 return \true;
 }
 $class = $this->em->getClassMetadata(get_class($entity1));
 if ($class !== $this->em->getClassMetadata(get_class($entity2))) {
 return \false;
 }
 $oid1 = spl_object_id($entity1);
 $oid2 = spl_object_id($entity2);
 $id1 = $this->entityIdentifiers[$oid1] ?? $this->identifierFlattener->flattenIdentifier($class, $class->getIdentifierValues($entity1));
 $id2 = $this->entityIdentifiers[$oid2] ?? $this->identifierFlattener->flattenIdentifier($class, $class->getIdentifierValues($entity2));
 return $id1 === $id2 || implode(' ', $id1) === implode(' ', $id2);
 }
 private function assertThatThereAreNoUnintentionallyNonPersistedAssociations() : void
 {
 $entitiesNeedingCascadePersist = array_diff_key($this->nonCascadedNewDetectedEntities, $this->entityInsertions);
 $this->nonCascadedNewDetectedEntities = [];
 if ($entitiesNeedingCascadePersist) {
 throw ORMInvalidArgumentException::newEntitiesFoundThroughRelationships(array_values($entitiesNeedingCascadePersist));
 }
 }
 private function mergeEntityStateIntoManagedCopy($entity, $managedCopy) : void
 {
 if (!$this->isLoaded($entity)) {
 return;
 }
 if (!$this->isLoaded($managedCopy)) {
 $managedCopy->__load();
 }
 $class = $this->em->getClassMetadata(get_class($entity));
 foreach ($this->reflectionPropertiesGetter->getProperties($class->name) as $prop) {
 $name = $prop->name;
 $prop->setAccessible(\true);
 if (!isset($class->associationMappings[$name])) {
 if (!$class->isIdentifier($name)) {
 $prop->setValue($managedCopy, $prop->getValue($entity));
 }
 } else {
 $assoc2 = $class->associationMappings[$name];
 if ($assoc2['type'] & ClassMetadata::TO_ONE) {
 $other = $prop->getValue($entity);
 if ($other === null) {
 $prop->setValue($managedCopy, null);
 } else {
 if ($other instanceof Proxy && !$other->__isInitialized()) {
 // do not merge fields marked lazy that have not been fetched.
 continue;
 }
 if (!$assoc2['isCascadeMerge']) {
 if ($this->getEntityState($other) === self::STATE_DETACHED) {
 $targetClass = $this->em->getClassMetadata($assoc2['targetEntity']);
 $relatedId = $targetClass->getIdentifierValues($other);
 if ($targetClass->subClasses) {
 $other = $this->em->find($targetClass->name, $relatedId);
 } else {
 $other = $this->em->getProxyFactory()->getProxy($assoc2['targetEntity'], $relatedId);
 $this->registerManaged($other, $relatedId, []);
 }
 }
 $prop->setValue($managedCopy, $other);
 }
 }
 } else {
 $mergeCol = $prop->getValue($entity);
 if ($mergeCol instanceof PersistentCollection && !$mergeCol->isInitialized()) {
 // do not merge fields marked lazy that have not been fetched.
 // keep the lazy persistent collection of the managed copy.
 continue;
 }
 $managedCol = $prop->getValue($managedCopy);
 if (!$managedCol) {
 $managedCol = new PersistentCollection($this->em, $this->em->getClassMetadata($assoc2['targetEntity']), new ArrayCollection());
 $managedCol->setOwner($managedCopy, $assoc2);
 $prop->setValue($managedCopy, $managedCol);
 }
 if ($assoc2['isCascadeMerge']) {
 $managedCol->initialize();
 // clear and set dirty a managed collection if its not also the same collection to merge from.
 if (!$managedCol->isEmpty() && $managedCol !== $mergeCol) {
 $managedCol->unwrap()->clear();
 $managedCol->setDirty(\true);
 if ($assoc2['isOwningSide'] && $assoc2['type'] === ClassMetadata::MANY_TO_MANY && $class->isChangeTrackingNotify()) {
 $this->scheduleForDirtyCheck($managedCopy);
 }
 }
 }
 }
 }
 if ($class->isChangeTrackingNotify()) {
 // Just treat all properties as changed, there is no other choice.
 $this->propertyChanged($managedCopy, $name, null, $prop->getValue($managedCopy));
 }
 }
 }
 public function hydrationComplete()
 {
 $this->hydrationCompleteHandler->hydrationComplete();
 }
 private function clearIdentityMapForEntityName(string $entityName) : void
 {
 if (!isset($this->identityMap[$entityName])) {
 return;
 }
 $visited = [];
 foreach ($this->identityMap[$entityName] as $entity) {
 $this->doDetach($entity, $visited, \false);
 }
 }
 private function clearEntityInsertionsForEntityName(string $entityName) : void
 {
 foreach ($this->entityInsertions as $hash => $entity) {
 // note: performance optimization - `instanceof` is much faster than a function call
 if ($entity instanceof $entityName && get_class($entity) === $entityName) {
 unset($this->entityInsertions[$hash]);
 }
 }
 }
 private function convertSingleFieldIdentifierToPHPValue(ClassMetadata $class, $identifierValue)
 {
 return $this->em->getConnection()->convertToPHPValue($identifierValue, $class->getTypeOfField($class->getSingleIdentifierFieldName()));
 }
}
