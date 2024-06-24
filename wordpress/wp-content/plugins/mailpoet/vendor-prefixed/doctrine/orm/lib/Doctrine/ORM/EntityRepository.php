<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
use BadMethodCallException;
use MailPoetVendor\Doctrine\Common\Collections\AbstractLazyCollection;
use MailPoetVendor\Doctrine\Common\Collections\Criteria;
use MailPoetVendor\Doctrine\Common\Collections\Selectable;
use MailPoetVendor\Doctrine\Common\Persistence\PersistentObject;
use MailPoetVendor\Doctrine\DBAL\LockMode;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\Inflector\Inflector;
use MailPoetVendor\Doctrine\Inflector\InflectorFactory;
use MailPoetVendor\Doctrine\ORM\Exception\NotSupported;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\Query\ResultSetMappingBuilder;
use MailPoetVendor\Doctrine\ORM\Repository\Exception\InvalidMagicMethodCall;
use MailPoetVendor\Doctrine\Persistence\ObjectRepository;
use function array_slice;
use function class_exists;
use function lcfirst;
use function sprintf;
use function str_starts_with;
use function substr;
class EntityRepository implements ObjectRepository, Selectable
{
 protected $_entityName;
 protected $_em;
 protected $_class;
 private static $inflector;
 public function __construct(EntityManagerInterface $em, ClassMetadata $class)
 {
 $this->_entityName = $class->name;
 $this->_em = $em;
 $this->_class = $class;
 }
 public function createQueryBuilder($alias, $indexBy = null)
 {
 return $this->_em->createQueryBuilder()->select($alias)->from($this->_entityName, $alias, $indexBy);
 }
 public function createResultSetMappingBuilder($alias)
 {
 $rsm = new ResultSetMappingBuilder($this->_em, ResultSetMappingBuilder::COLUMN_RENAMING_INCREMENT);
 $rsm->addRootEntityFromClassMetadata($this->_entityName, $alias);
 return $rsm;
 }
 public function createNamedQuery($queryName)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8592', 'Named Queries are deprecated, here "%s" on entity %s. Move the query logic into EntityRepository', $queryName, $this->_class->name);
 return $this->_em->createQuery($this->_class->getNamedQuery($queryName));
 }
 public function createNativeNamedQuery($queryName)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8592', 'Named Native Queries are deprecated, here "%s" on entity %s. Move the query logic into EntityRepository', $queryName, $this->_class->name);
 $queryMapping = $this->_class->getNamedNativeQuery($queryName);
 $rsm = new Query\ResultSetMappingBuilder($this->_em);
 $rsm->addNamedNativeQueryMapping($this->_class, $queryMapping);
 return $this->_em->createNativeQuery($queryMapping['query'], $rsm);
 }
 public function clear()
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8460', 'Calling %s() is deprecated and will not be supported in Doctrine ORM 3.0.', __METHOD__);
 if (!class_exists(PersistentObject::class)) {
 throw NotSupported::createForPersistence3(sprintf('Partial clearing of entities for class %s', $this->_class->rootEntityName));
 }
 $this->_em->clear($this->_class->rootEntityName);
 }
 public function find($id, $lockMode = null, $lockVersion = null)
 {
 return $this->_em->find($this->_entityName, $id, $lockMode, $lockVersion);
 }
 public function findAll()
 {
 return $this->findBy([]);
 }
 public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 {
 $persister = $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName);
 return $persister->loadAll($criteria, $orderBy, $limit, $offset);
 }
 public function findOneBy(array $criteria, ?array $orderBy = null)
 {
 $persister = $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName);
 return $persister->load($criteria, null, null, [], null, 1, $orderBy);
 }
 public function count(array $criteria)
 {
 return $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName)->count($criteria);
 }
 public function __call($method, $arguments)
 {
 if (str_starts_with($method, 'findBy')) {
 return $this->resolveMagicCall('findBy', substr($method, 6), $arguments);
 }
 if (str_starts_with($method, 'findOneBy')) {
 return $this->resolveMagicCall('findOneBy', substr($method, 9), $arguments);
 }
 if (str_starts_with($method, 'countBy')) {
 return $this->resolveMagicCall('count', substr($method, 7), $arguments);
 }
 throw new BadMethodCallException(sprintf('Undefined method "%s". The method name must start with ' . 'either findBy, findOneBy or countBy!', $method));
 }
 protected function getEntityName()
 {
 return $this->_entityName;
 }
 public function getClassName()
 {
 return $this->getEntityName();
 }
 protected function getEntityManager()
 {
 return $this->_em;
 }
 protected function getClassMetadata()
 {
 return $this->_class;
 }
 public function matching(Criteria $criteria)
 {
 $persister = $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName);
 return new LazyCriteriaCollection($persister, $criteria);
 }
 private function resolveMagicCall(string $method, string $by, array $arguments)
 {
 if (!$arguments) {
 throw InvalidMagicMethodCall::onMissingParameter($method . $by);
 }
 if (self::$inflector === null) {
 self::$inflector = InflectorFactory::create()->build();
 }
 $fieldName = lcfirst(self::$inflector->classify($by));
 if (!($this->_class->hasField($fieldName) || $this->_class->hasAssociation($fieldName))) {
 throw InvalidMagicMethodCall::becauseFieldNotFoundIn($this->_entityName, $fieldName, $method . $by);
 }
 return $this->{$method}([$fieldName => $arguments[0]], ...array_slice($arguments, 1));
 }
}
