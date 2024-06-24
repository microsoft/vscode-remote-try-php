<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Persisters\Entity;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Types\Type;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use function sprintf;
abstract class AbstractEntityInheritancePersister extends BasicEntityPersister
{
 protected function prepareInsertData($entity)
 {
 $data = parent::prepareInsertData($entity);
 // Populate the discriminator column
 $discColumn = $this->class->getDiscriminatorColumn();
 $this->columnTypes[$discColumn['name']] = $discColumn['type'];
 $data[$this->getDiscriminatorColumnTableName()][$discColumn['name']] = $this->class->discriminatorValue;
 return $data;
 }
 protected abstract function getDiscriminatorColumnTableName();
 protected function getSelectColumnSQL($field, ClassMetadata $class, $alias = 'r')
 {
 $tableAlias = $alias === 'r' ? '' : $alias;
 $fieldMapping = $class->fieldMappings[$field];
 $columnAlias = $this->getSQLColumnAlias($fieldMapping['columnName']);
 $sql = sprintf('%s.%s', $this->getSQLTableAlias($class->name, $tableAlias), $this->quoteStrategy->getColumnName($field, $class, $this->platform));
 $this->currentPersisterContext->rsm->addFieldResult($alias, $columnAlias, $field, $class->name);
 if (isset($fieldMapping['requireSQLConversion'])) {
 $type = Type::getType($fieldMapping['type']);
 $sql = $type->convertToPHPValueSQL($sql, $this->platform);
 }
 return $sql . ' AS ' . $columnAlias;
 }
 protected function getSelectJoinColumnSQL($tableAlias, $joinColumnName, $quotedColumnName, $type)
 {
 $columnAlias = $this->getSQLColumnAlias($joinColumnName);
 $this->currentPersisterContext->rsm->addMetaResult('r', $columnAlias, $joinColumnName, \false, $type);
 return $tableAlias . '.' . $quotedColumnName . ' AS ' . $columnAlias;
 }
}
