<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
use BadMethodCallException;
use DateTimeInterface;
use MailPoetVendor\Doctrine\Common\EventManager;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\LockMode;
use MailPoetVendor\Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use MailPoetVendor\Doctrine\ORM\Proxy\ProxyFactory;
use MailPoetVendor\Doctrine\ORM\Query\Expr;
use MailPoetVendor\Doctrine\ORM\Query\FilterCollection;
use MailPoetVendor\Doctrine\ORM\Query\ResultSetMapping;
use MailPoetVendor\Doctrine\Persistence\ObjectManager;
interface EntityManagerInterface extends ObjectManager
{
 public function getRepository($className);
 public function getCache();
 public function getConnection();
 public function getExpressionBuilder();
 public function beginTransaction();
 public function transactional($func);
 // public function wrapInTransaction(callable $func);
 public function commit();
 public function rollback();
 public function createQuery($dql = '');
 public function createNamedQuery($name);
 public function createNativeQuery($sql, ResultSetMapping $rsm);
 public function createNamedNativeQuery($name);
 public function createQueryBuilder();
 public function getReference($entityName, $id);
 public function getPartialReference($entityName, $identifier);
 public function close();
 public function copy($entity, $deep = \false);
 public function lock($entity, $lockMode, $lockVersion = null);
 public function getEventManager();
 public function getConfiguration();
 public function isOpen();
 public function getUnitOfWork();
 public function getHydrator($hydrationMode);
 public function newHydrator($hydrationMode);
 public function getProxyFactory();
 public function getFilters();
 public function isFiltersStateClean();
 public function hasFilters();
 public function getClassMetadata($className);
}
