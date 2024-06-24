<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping\Builder;
if (!defined('ABSPATH')) exit;
class OneToManyAssociationBuilder extends AssociationBuilder
{
 public function setOrderBy(array $fieldNames)
 {
 $this->mapping['orderBy'] = $fieldNames;
 return $this;
 }
 public function setIndexBy($fieldName)
 {
 $this->mapping['indexBy'] = $fieldName;
 return $this;
 }
 public function build()
 {
 $mapping = $this->mapping;
 if ($this->joinColumns) {
 $mapping['joinColumns'] = $this->joinColumns;
 }
 $cm = $this->builder->getClassMetadata();
 $cm->mapOneToMany($mapping);
 return $this->builder;
 }
}
