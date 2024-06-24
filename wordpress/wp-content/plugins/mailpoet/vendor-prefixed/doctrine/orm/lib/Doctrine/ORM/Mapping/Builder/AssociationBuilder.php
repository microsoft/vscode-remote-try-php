<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping\Builder;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use InvalidArgumentException;
class AssociationBuilder
{
 protected $builder;
 protected $mapping;
 protected $joinColumns;
 protected $type;
 public function __construct(ClassMetadataBuilder $builder, array $mapping, $type)
 {
 $this->builder = $builder;
 $this->mapping = $mapping;
 $this->type = $type;
 }
 public function mappedBy($fieldName)
 {
 $this->mapping['mappedBy'] = $fieldName;
 return $this;
 }
 public function inversedBy($fieldName)
 {
 $this->mapping['inversedBy'] = $fieldName;
 return $this;
 }
 public function cascadeAll()
 {
 $this->mapping['cascade'] = ['ALL'];
 return $this;
 }
 public function cascadePersist()
 {
 $this->mapping['cascade'][] = 'persist';
 return $this;
 }
 public function cascadeRemove()
 {
 $this->mapping['cascade'][] = 'remove';
 return $this;
 }
 public function cascadeMerge()
 {
 $this->mapping['cascade'][] = 'merge';
 return $this;
 }
 public function cascadeDetach()
 {
 $this->mapping['cascade'][] = 'detach';
 return $this;
 }
 public function cascadeRefresh()
 {
 $this->mapping['cascade'][] = 'refresh';
 return $this;
 }
 public function fetchExtraLazy()
 {
 $this->mapping['fetch'] = ClassMetadata::FETCH_EXTRA_LAZY;
 return $this;
 }
 public function fetchEager()
 {
 $this->mapping['fetch'] = ClassMetadata::FETCH_EAGER;
 return $this;
 }
 public function fetchLazy()
 {
 $this->mapping['fetch'] = ClassMetadata::FETCH_LAZY;
 return $this;
 }
 public function addJoinColumn($columnName, $referencedColumnName, $nullable = \true, $unique = \false, $onDelete = null, $columnDef = null)
 {
 $this->joinColumns[] = ['name' => $columnName, 'referencedColumnName' => $referencedColumnName, 'nullable' => $nullable, 'unique' => $unique, 'onDelete' => $onDelete, 'columnDefinition' => $columnDef];
 return $this;
 }
 public function makePrimaryKey()
 {
 $this->mapping['id'] = \true;
 return $this;
 }
 public function orphanRemoval()
 {
 $this->mapping['orphanRemoval'] = \true;
 return $this;
 }
 public function build()
 {
 $mapping = $this->mapping;
 if ($this->joinColumns) {
 $mapping['joinColumns'] = $this->joinColumns;
 }
 $cm = $this->builder->getClassMetadata();
 if ($this->type === ClassMetadata::MANY_TO_ONE) {
 $cm->mapManyToOne($mapping);
 } elseif ($this->type === ClassMetadata::ONE_TO_ONE) {
 $cm->mapOneToOne($mapping);
 } else {
 throw new InvalidArgumentException('Type should be a ToOne Association here');
 }
 return $this->builder;
 }
}
