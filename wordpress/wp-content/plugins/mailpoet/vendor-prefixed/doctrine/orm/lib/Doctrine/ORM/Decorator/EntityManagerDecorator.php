<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Decorator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\EntityRepository;
use MailPoetVendor\Doctrine\ORM\Query\ResultSetMapping;
use MailPoetVendor\Doctrine\Persistence\ObjectManagerDecorator;
use function func_get_arg;
use function func_num_args;
use function get_debug_type;
use function method_exists;
use function sprintf;
use function trigger_error;
use const E_USER_NOTICE;
abstract class EntityManagerDecorator extends ObjectManagerDecorator implements EntityManagerInterface
{
 public function __construct(EntityManagerInterface $wrapped)
 {
 $this->wrapped = $wrapped;
 }
 public function getConnection()
 {
 return $this->wrapped->getConnection();
 }
 public function getExpressionBuilder()
 {
 return $this->wrapped->getExpressionBuilder();
 }
 public function getRepository($className)
 {
 return $this->wrapped->getRepository($className);
 }
 public function getClassMetadata($className)
 {
 return $this->wrapped->getClassMetadata($className);
 }
 public function beginTransaction()
 {
 $this->wrapped->beginTransaction();
 }
 public function transactional($func)
 {
 return $this->wrapped->transactional($func);
 }
 public function wrapInTransaction(callable $func)
 {
 if (!method_exists($this->wrapped, 'wrapInTransaction')) {
 trigger_error(sprintf('Calling `transactional()` instead of `wrapInTransaction()` which is not implemented on %s', get_debug_type($this->wrapped)), E_USER_NOTICE);
 return $this->wrapped->transactional($func);
 }
 return $this->wrapped->wrapInTransaction($func);
 }
 public function commit()
 {
 $this->wrapped->commit();
 }
 public function rollback()
 {
 $this->wrapped->rollback();
 }
 public function createQuery($dql = '')
 {
 return $this->wrapped->createQuery($dql);
 }
 public function createNamedQuery($name)
 {
 return $this->wrapped->createNamedQuery($name);
 }
 public function createNativeQuery($sql, ResultSetMapping $rsm)
 {
 return $this->wrapped->createNativeQuery($sql, $rsm);
 }
 public function createNamedNativeQuery($name)
 {
 return $this->wrapped->createNamedNativeQuery($name);
 }
 public function createQueryBuilder()
 {
 return $this->wrapped->createQueryBuilder();
 }
 public function getReference($entityName, $id)
 {
 return $this->wrapped->getReference($entityName, $id);
 }
 public function getPartialReference($entityName, $identifier)
 {
 return $this->wrapped->getPartialReference($entityName, $identifier);
 }
 public function close()
 {
 $this->wrapped->close();
 }
 public function copy($entity, $deep = \false)
 {
 return $this->wrapped->copy($entity, $deep);
 }
 public function lock($entity, $lockMode, $lockVersion = null)
 {
 $this->wrapped->lock($entity, $lockMode, $lockVersion);
 }
 public function find($className, $id, $lockMode = null, $lockVersion = null)
 {
 return $this->wrapped->find($className, $id, $lockMode, $lockVersion);
 }
 public function flush($entity = null)
 {
 $this->wrapped->flush($entity);
 }
 public function refresh($object)
 {
 $lockMode = null;
 if (func_num_args() > 1) {
 $lockMode = func_get_arg(1);
 }
 $this->wrapped->refresh($object, $lockMode);
 }
 public function getEventManager()
 {
 return $this->wrapped->getEventManager();
 }
 public function getConfiguration()
 {
 return $this->wrapped->getConfiguration();
 }
 public function isOpen()
 {
 return $this->wrapped->isOpen();
 }
 public function getUnitOfWork()
 {
 return $this->wrapped->getUnitOfWork();
 }
 public function getHydrator($hydrationMode)
 {
 return $this->wrapped->getHydrator($hydrationMode);
 }
 public function newHydrator($hydrationMode)
 {
 return $this->wrapped->newHydrator($hydrationMode);
 }
 public function getProxyFactory()
 {
 return $this->wrapped->getProxyFactory();
 }
 public function getFilters()
 {
 return $this->wrapped->getFilters();
 }
 public function isFiltersStateClean()
 {
 return $this->wrapped->isFiltersStateClean();
 }
 public function hasFilters()
 {
 return $this->wrapped->hasFilters();
 }
 public function getCache()
 {
 return $this->wrapped->getCache();
 }
}
