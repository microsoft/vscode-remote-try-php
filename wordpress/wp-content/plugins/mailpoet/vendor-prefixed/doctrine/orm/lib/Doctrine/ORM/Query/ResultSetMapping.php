<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query;
if (!defined('ABSPATH')) exit;
use function array_merge;
use function count;
class ResultSetMapping
{
 public $isMixed = \false;
 public $isSelect = \true;
 public $aliasMap = [];
 public $relationMap = [];
 public $parentAliasMap = [];
 public $fieldMappings = [];
 public $scalarMappings = [];
 public $enumMappings = [];
 public $typeMappings = [];
 public $entityMappings = [];
 public $metaMappings = [];
 public $columnOwnerMap = [];
 public $discriminatorColumns = [];
 public $indexByMap = [];
 public $declaringClasses = [];
 public $isIdentifierColumn = [];
 public $newObjectMappings = [];
 public $metadataParameterMapping = [];
 public $discriminatorParameters = [];
 public function addEntityResult($class, $alias, $resultAlias = null)
 {
 $this->aliasMap[$alias] = $class;
 $this->entityMappings[$alias] = $resultAlias;
 if ($resultAlias !== null) {
 $this->isMixed = \true;
 }
 return $this;
 }
 public function setDiscriminatorColumn($alias, $discrColumn)
 {
 $this->discriminatorColumns[$alias] = $discrColumn;
 $this->columnOwnerMap[$discrColumn] = $alias;
 return $this;
 }
 public function addIndexBy($alias, $fieldName)
 {
 $found = \false;
 foreach (array_merge($this->metaMappings, $this->fieldMappings) as $columnName => $columnFieldName) {
 if (!($columnFieldName === $fieldName && $this->columnOwnerMap[$columnName] === $alias)) {
 continue;
 }
 $this->addIndexByColumn($alias, $columnName);
 $found = \true;
 break;
 }
 return $this;
 }
 public function addIndexByScalar($resultColumnName)
 {
 $this->indexByMap['scalars'] = $resultColumnName;
 return $this;
 }
 public function addIndexByColumn($alias, $resultColumnName)
 {
 $this->indexByMap[$alias] = $resultColumnName;
 return $this;
 }
 public function hasIndexBy($alias)
 {
 return isset($this->indexByMap[$alias]);
 }
 public function isFieldResult($columnName)
 {
 return isset($this->fieldMappings[$columnName]);
 }
 public function addFieldResult($alias, $columnName, $fieldName, $declaringClass = null)
 {
 // column name (in result set) => field name
 $this->fieldMappings[$columnName] = $fieldName;
 // column name => alias of owner
 $this->columnOwnerMap[$columnName] = $alias;
 // field name => class name of declaring class
 $this->declaringClasses[$columnName] = $declaringClass ?: $this->aliasMap[$alias];
 if (!$this->isMixed && $this->scalarMappings) {
 $this->isMixed = \true;
 }
 return $this;
 }
 public function addJoinedEntityResult($class, $alias, $parentAlias, $relation)
 {
 $this->aliasMap[$alias] = $class;
 $this->parentAliasMap[$alias] = $parentAlias;
 $this->relationMap[$alias] = $relation;
 return $this;
 }
 public function addScalarResult($columnName, $alias, $type = 'string')
 {
 $this->scalarMappings[$columnName] = $alias;
 $this->typeMappings[$columnName] = $type;
 if (!$this->isMixed && $this->fieldMappings) {
 $this->isMixed = \true;
 }
 return $this;
 }
 public function addEnumResult($columnName, $enumType)
 {
 $this->enumMappings[$columnName] = $enumType;
 return $this;
 }
 public function addMetadataParameterMapping($parameter, $attribute)
 {
 $this->metadataParameterMapping[$parameter] = $attribute;
 }
 public function isScalarResult($columnName)
 {
 return isset($this->scalarMappings[$columnName]);
 }
 public function getClassName($alias)
 {
 return $this->aliasMap[$alias];
 }
 public function getScalarAlias($columnName)
 {
 return $this->scalarMappings[$columnName];
 }
 public function getDeclaringClass($columnName)
 {
 return $this->declaringClasses[$columnName];
 }
 public function getRelation($alias)
 {
 return $this->relationMap[$alias];
 }
 public function isRelation($alias)
 {
 return isset($this->relationMap[$alias]);
 }
 public function getEntityAlias($columnName)
 {
 return $this->columnOwnerMap[$columnName];
 }
 public function getParentAlias($alias)
 {
 return $this->parentAliasMap[$alias];
 }
 public function hasParentAlias($alias)
 {
 return isset($this->parentAliasMap[$alias]);
 }
 public function getFieldName($columnName)
 {
 return $this->fieldMappings[$columnName];
 }
 public function getAliasMap()
 {
 return $this->aliasMap;
 }
 public function getEntityResultCount()
 {
 return count($this->aliasMap);
 }
 public function isMixedResult()
 {
 return $this->isMixed;
 }
 public function addMetaResult($alias, $columnName, $fieldName, $isIdentifierColumn = \false, $type = null)
 {
 $this->metaMappings[$columnName] = $fieldName;
 $this->columnOwnerMap[$columnName] = $alias;
 if ($isIdentifierColumn) {
 $this->isIdentifierColumn[$alias][$columnName] = \true;
 }
 if ($type) {
 $this->typeMappings[$columnName] = $type;
 }
 return $this;
 }
}
