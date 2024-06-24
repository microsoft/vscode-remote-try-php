<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping\Builder;
if (!defined('ABSPATH')) exit;
use BackedEnum;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadataInfo;
use function get_class;
class ClassMetadataBuilder
{
 private $cm;
 public function __construct(ClassMetadataInfo $cm)
 {
 if (!$cm instanceof ClassMetadata) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/249', 'Passing an instance of %s to %s is deprecated, please pass a ClassMetadata instance instead.', get_class($cm), __METHOD__, ClassMetadata::class);
 }
 $this->cm = $cm;
 }
 public function getClassMetadata()
 {
 return $this->cm;
 }
 public function setMappedSuperClass()
 {
 $this->cm->isMappedSuperclass = \true;
 $this->cm->isEmbeddedClass = \false;
 return $this;
 }
 public function setEmbeddable()
 {
 $this->cm->isEmbeddedClass = \true;
 $this->cm->isMappedSuperclass = \false;
 return $this;
 }
 public function addEmbedded($fieldName, $class, $columnPrefix = null)
 {
 $this->cm->mapEmbedded(['fieldName' => $fieldName, 'class' => $class, 'columnPrefix' => $columnPrefix]);
 return $this;
 }
 public function setCustomRepositoryClass($repositoryClassName)
 {
 $this->cm->setCustomRepositoryClass($repositoryClassName);
 return $this;
 }
 public function setReadOnly()
 {
 $this->cm->markReadOnly();
 return $this;
 }
 public function setTable($name)
 {
 $this->cm->setPrimaryTable(['name' => $name]);
 return $this;
 }
 public function addIndex(array $columns, $name)
 {
 if (!isset($this->cm->table['indexes'])) {
 $this->cm->table['indexes'] = [];
 }
 $this->cm->table['indexes'][$name] = ['columns' => $columns];
 return $this;
 }
 public function addUniqueConstraint(array $columns, $name)
 {
 if (!isset($this->cm->table['uniqueConstraints'])) {
 $this->cm->table['uniqueConstraints'] = [];
 }
 $this->cm->table['uniqueConstraints'][$name] = ['columns' => $columns];
 return $this;
 }
 public function addNamedQuery($name, $dqlQuery)
 {
 $this->cm->addNamedQuery(['name' => $name, 'query' => $dqlQuery]);
 return $this;
 }
 public function setJoinedTableInheritance()
 {
 $this->cm->setInheritanceType(ClassMetadata::INHERITANCE_TYPE_JOINED);
 return $this;
 }
 public function setSingleTableInheritance()
 {
 $this->cm->setInheritanceType(ClassMetadata::INHERITANCE_TYPE_SINGLE_TABLE);
 return $this;
 }
 public function setDiscriminatorColumn($name, $type = 'string', $length = 255, ?string $columnDefinition = null, ?string $enumType = null)
 {
 $this->cm->setDiscriminatorColumn(['name' => $name, 'type' => $type, 'length' => $length, 'columnDefinition' => $columnDefinition, 'enumType' => $enumType]);
 return $this;
 }
 public function addDiscriminatorMapClass($name, $class)
 {
 $this->cm->addDiscriminatorMapClass($name, $class);
 return $this;
 }
 public function setChangeTrackingPolicyDeferredExplicit()
 {
 $this->cm->setChangeTrackingPolicy(ClassMetadata::CHANGETRACKING_DEFERRED_EXPLICIT);
 return $this;
 }
 public function setChangeTrackingPolicyNotify()
 {
 $this->cm->setChangeTrackingPolicy(ClassMetadata::CHANGETRACKING_NOTIFY);
 return $this;
 }
 public function addLifecycleEvent($methodName, $event)
 {
 $this->cm->addLifecycleCallback($methodName, $event);
 return $this;
 }
 public function addField($name, $type, array $mapping = [])
 {
 $mapping['fieldName'] = $name;
 $mapping['type'] = $type;
 $this->cm->mapField($mapping);
 return $this;
 }
 public function createField($name, $type)
 {
 return new FieldBuilder($this, ['fieldName' => $name, 'type' => $type]);
 }
 public function createEmbedded($fieldName, $class)
 {
 return new EmbeddedBuilder($this, ['fieldName' => $fieldName, 'class' => $class, 'columnPrefix' => null]);
 }
 public function addManyToOne($name, $targetEntity, $inversedBy = null)
 {
 $builder = $this->createManyToOne($name, $targetEntity);
 if ($inversedBy) {
 $builder->inversedBy($inversedBy);
 }
 return $builder->build();
 }
 public function createManyToOne($name, $targetEntity)
 {
 return new AssociationBuilder($this, ['fieldName' => $name, 'targetEntity' => $targetEntity], ClassMetadata::MANY_TO_ONE);
 }
 public function createOneToOne($name, $targetEntity)
 {
 return new AssociationBuilder($this, ['fieldName' => $name, 'targetEntity' => $targetEntity], ClassMetadata::ONE_TO_ONE);
 }
 public function addInverseOneToOne($name, $targetEntity, $mappedBy)
 {
 $builder = $this->createOneToOne($name, $targetEntity);
 $builder->mappedBy($mappedBy);
 return $builder->build();
 }
 public function addOwningOneToOne($name, $targetEntity, $inversedBy = null)
 {
 $builder = $this->createOneToOne($name, $targetEntity);
 if ($inversedBy) {
 $builder->inversedBy($inversedBy);
 }
 return $builder->build();
 }
 public function createManyToMany($name, $targetEntity)
 {
 return new ManyToManyAssociationBuilder($this, ['fieldName' => $name, 'targetEntity' => $targetEntity], ClassMetadata::MANY_TO_MANY);
 }
 public function addOwningManyToMany($name, $targetEntity, $inversedBy = null)
 {
 $builder = $this->createManyToMany($name, $targetEntity);
 if ($inversedBy) {
 $builder->inversedBy($inversedBy);
 }
 return $builder->build();
 }
 public function addInverseManyToMany($name, $targetEntity, $mappedBy)
 {
 $builder = $this->createManyToMany($name, $targetEntity);
 $builder->mappedBy($mappedBy);
 return $builder->build();
 }
 public function createOneToMany($name, $targetEntity)
 {
 return new OneToManyAssociationBuilder($this, ['fieldName' => $name, 'targetEntity' => $targetEntity], ClassMetadata::ONE_TO_MANY);
 }
 public function addOneToMany($name, $targetEntity, $mappedBy)
 {
 $builder = $this->createOneToMany($name, $targetEntity);
 $builder->mappedBy($mappedBy);
 return $builder->build();
 }
}
