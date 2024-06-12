<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Persisters\Entity;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Collections\Criteria;
use MailPoetVendor\Doctrine\DBAL\LockMode;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\Mapping\MappingException;
use MailPoetVendor\Doctrine\ORM\PersistentCollection;
use MailPoetVendor\Doctrine\ORM\Query\ResultSetMapping;
interface EntityPersister
{
 public function getClassMetadata();
 public function getResultSetMapping();
 public function getInserts();
 public function getInsertSQL();
 public function getSelectSQL($criteria, $assoc = null, $lockMode = null, $limit = null, $offset = null, ?array $orderBy = null);
 public function getCountSQL($criteria = []);
 public function expandParameters($criteria);
 public function expandCriteriaParameters(Criteria $criteria);
 public function getSelectConditionStatementSQL($field, $value, $assoc = null, $comparison = null);
 public function addInsert($entity);
 public function executeInserts();
 public function update($entity);
 public function delete($entity);
 public function count($criteria = []);
 public function getOwningTable($fieldName);
 public function load(array $criteria, $entity = null, $assoc = null, array $hints = [], $lockMode = null, $limit = null, ?array $orderBy = null);
 public function loadById(array $identifier, $entity = null);
 public function loadOneToOneEntity(array $assoc, $sourceEntity, array $identifier = []);
 public function refresh(array $id, $entity, $lockMode = null);
 public function loadCriteria(Criteria $criteria);
 public function loadAll(array $criteria = [], ?array $orderBy = null, $limit = null, $offset = null);
 public function getManyToManyCollection(array $assoc, $sourceEntity, $offset = null, $limit = null);
 public function loadManyToManyCollection(array $assoc, $sourceEntity, PersistentCollection $collection);
 public function loadOneToManyCollection(array $assoc, $sourceEntity, PersistentCollection $collection);
 public function lock(array $criteria, $lockMode);
 public function getOneToManyCollection(array $assoc, $sourceEntity, $offset = null, $limit = null);
 public function exists($entity, ?Criteria $extraConditions = null);
}
