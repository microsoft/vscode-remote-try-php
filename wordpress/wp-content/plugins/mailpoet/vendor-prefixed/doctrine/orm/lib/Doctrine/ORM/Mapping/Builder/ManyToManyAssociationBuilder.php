<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping\Builder;
if (!defined('ABSPATH')) exit;
class ManyToManyAssociationBuilder extends OneToManyAssociationBuilder
{
 private $joinTableName;
 private $inverseJoinColumns = [];
 public function setJoinTable($name)
 {
 $this->joinTableName = $name;
 return $this;
 }
 public function addInverseJoinColumn($columnName, $referencedColumnName, $nullable = \true, $unique = \false, $onDelete = null, $columnDef = null)
 {
 $this->inverseJoinColumns[] = ['name' => $columnName, 'referencedColumnName' => $referencedColumnName, 'nullable' => $nullable, 'unique' => $unique, 'onDelete' => $onDelete, 'columnDefinition' => $columnDef];
 return $this;
 }
 public function build()
 {
 $mapping = $this->mapping;
 $mapping['joinTable'] = [];
 if ($this->joinColumns) {
 $mapping['joinTable']['joinColumns'] = $this->joinColumns;
 }
 if ($this->inverseJoinColumns) {
 $mapping['joinTable']['inverseJoinColumns'] = $this->inverseJoinColumns;
 }
 if ($this->joinTableName) {
 $mapping['joinTable']['name'] = $this->joinTableName;
 }
 $cm = $this->builder->getClassMetadata();
 $cm->mapManyToMany($mapping);
 return $this->builder;
 }
}
