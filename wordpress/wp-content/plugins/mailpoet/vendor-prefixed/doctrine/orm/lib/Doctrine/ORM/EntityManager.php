<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
use BadMethodCallException;
use MailPoetVendor\Doctrine\Common\Cache\Psr6\CacheAdapter;
use MailPoetVendor\Doctrine\Common\EventManager;
use MailPoetVendor\Doctrine\Common\Util\ClassUtils;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\DriverManager;
use MailPoetVendor\Doctrine\DBAL\LockMode;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\Exception\EntityManagerClosed;
use MailPoetVendor\Doctrine\ORM\Exception\InvalidHydrationMode;
use MailPoetVendor\Doctrine\ORM\Exception\MismatchedEventManager;
use MailPoetVendor\Doctrine\ORM\Exception\MissingIdentifierField;
use MailPoetVendor\Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use MailPoetVendor\Doctrine\ORM\Exception\UnrecognizedIdentifierFields;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadataFactory;
use MailPoetVendor\Doctrine\ORM\Proxy\ProxyFactory;
use MailPoetVendor\Doctrine\ORM\Query\Expr;
use MailPoetVendor\Doctrine\ORM\Query\FilterCollection;
use MailPoetVendor\Doctrine\ORM\Query\ResultSetMapping;
use MailPoetVendor\Doctrine\ORM\Repository\RepositoryFactory;
use MailPoetVendor\Doctrine\Persistence\Mapping\MappingException;
use MailPoetVendor\Doctrine\Persistence\ObjectRepository;
use InvalidArgumentException;
use Throwable;
use function array_keys;
use function call_user_func;
use function get_debug_type;
use function gettype;
use function is_array;
use function is_callable;
use function is_object;
use function is_string;
use function ltrim;
use function sprintf;
class EntityManager implements EntityManagerInterface
{
 private $config;
 private $conn;
 private $metadataFactory;
 private $unitOfWork;
 private $eventManager;
 private $proxyFactory;
 private $repositoryFactory;
 private $expressionBuilder;
 private $closed = \false;
 private $filterCollection;
 private $cache;
 protected function __construct(Connection $conn, Configuration $config, EventManager $eventManager)
 {
 $this->conn = $conn;
 $this->config = $config;
 $this->eventManager = $eventManager;
 $metadataFactoryClassName = $config->getClassMetadataFactoryName();
 $this->metadataFactory = new $metadataFactoryClassName();
 $this->metadataFactory->setEntityManager($this);
 $this->configureMetadataCache();
 $this->repositoryFactory = $config->getRepositoryFactory();
 $this->unitOfWork = new UnitOfWork($this);
 $this->proxyFactory = new ProxyFactory($this, $config->getProxyDir(), $config->getProxyNamespace(), $config->getAutoGenerateProxyClasses());
 if ($config->isSecondLevelCacheEnabled()) {
 $cacheConfig = $config->getSecondLevelCacheConfiguration();
 $cacheFactory = $cacheConfig->getCacheFactory();
 $this->cache = $cacheFactory->createCache($this);
 }
 }
 public function getConnection()
 {
 return $this->conn;
 }
 public function getMetadataFactory()
 {
 return $this->metadataFactory;
 }
 public function getExpressionBuilder()
 {
 if ($this->expressionBuilder === null) {
 $this->expressionBuilder = new Query\Expr();
 }
 return $this->expressionBuilder;
 }
 public function beginTransaction()
 {
 $this->conn->beginTransaction();
 }
 public function getCache()
 {
 return $this->cache;
 }
 public function transactional($func)
 {
 if (!is_callable($func)) {
 throw new InvalidArgumentException('Expected argument of type "callable", got "' . gettype($func) . '"');
 }
 $this->conn->beginTransaction();
 try {
 $return = call_user_func($func, $this);
 $this->flush();
 $this->conn->commit();
 return $return ?: \true;
 } catch (Throwable $e) {
 $this->close();
 $this->conn->rollBack();
 throw $e;
 }
 }
 public function wrapInTransaction(callable $func)
 {
 $this->conn->beginTransaction();
 try {
 $return = $func($this);
 $this->flush();
 $this->conn->commit();
 return $return;
 } catch (Throwable $e) {
 $this->close();
 $this->conn->rollBack();
 throw $e;
 }
 }
 public function commit()
 {
 $this->conn->commit();
 }
 public function rollback()
 {
 $this->conn->rollBack();
 }
 public function getClassMetadata($className)
 {
 return $this->metadataFactory->getMetadataFor($className);
 }
 public function createQuery($dql = '')
 {
 $query = new Query($this);
 if (!empty($dql)) {
 $query->setDQL($dql);
 }
 return $query;
 }
 public function createNamedQuery($name)
 {
 return $this->createQuery($this->config->getNamedQuery($name));
 }
 public function createNativeQuery($sql, ResultSetMapping $rsm)
 {
 $query = new NativeQuery($this);
 $query->setSQL($sql);
 $query->setResultSetMapping($rsm);
 return $query;
 }
 public function createNamedNativeQuery($name)
 {
 [$sql, $rsm] = $this->config->getNamedNativeQuery($name);
 return $this->createNativeQuery($sql, $rsm);
 }
 public function createQueryBuilder()
 {
 return new QueryBuilder($this);
 }
 public function flush($entity = null)
 {
 if ($entity !== null) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8459', 'Calling %s() with any arguments to flush specific entities is deprecated and will not be supported in Doctrine ORM 3.0.', __METHOD__);
 }
 $this->errorIfClosed();
 $this->unitOfWork->commit($entity);
 }
 public function find($className, $id, $lockMode = null, $lockVersion = null)
 {
 $class = $this->metadataFactory->getMetadataFor(ltrim($className, '\\'));
 if ($lockMode !== null) {
 $this->checkLockRequirements($lockMode, $class);
 }
 if (!is_array($id)) {
 if ($class->isIdentifierComposite) {
 throw ORMInvalidArgumentException::invalidCompositeIdentifier();
 }
 $id = [$class->identifier[0] => $id];
 }
 foreach ($id as $i => $value) {
 if (is_object($value) && $this->metadataFactory->hasMetadataFor(ClassUtils::getClass($value))) {
 $id[$i] = $this->unitOfWork->getSingleIdentifierValue($value);
 if ($id[$i] === null) {
 throw ORMInvalidArgumentException::invalidIdentifierBindingEntity();
 }
 }
 }
 $sortedId = [];
 foreach ($class->identifier as $identifier) {
 if (!isset($id[$identifier])) {
 throw MissingIdentifierField::fromFieldAndClass($identifier, $class->name);
 }
 $sortedId[$identifier] = $id[$identifier];
 unset($id[$identifier]);
 }
 if ($id) {
 throw UnrecognizedIdentifierFields::fromClassAndFieldNames($class->name, array_keys($id));
 }
 $unitOfWork = $this->getUnitOfWork();
 $entity = $unitOfWork->tryGetById($sortedId, $class->rootEntityName);
 // Check identity map first
 if ($entity !== \false) {
 if (!$entity instanceof $class->name) {
 return null;
 }
 switch (\true) {
 case $lockMode === LockMode::OPTIMISTIC:
 $this->lock($entity, $lockMode, $lockVersion);
 break;
 case $lockMode === LockMode::NONE:
 case $lockMode === LockMode::PESSIMISTIC_READ:
 case $lockMode === LockMode::PESSIMISTIC_WRITE:
 $persister = $unitOfWork->getEntityPersister($class->name);
 $persister->refresh($sortedId, $entity, $lockMode);
 break;
 }
 return $entity;
 // Hit!
 }
 $persister = $unitOfWork->getEntityPersister($class->name);
 switch (\true) {
 case $lockMode === LockMode::OPTIMISTIC:
 $entity = $persister->load($sortedId);
 if ($entity !== null) {
 $unitOfWork->lock($entity, $lockMode, $lockVersion);
 }
 return $entity;
 case $lockMode === LockMode::PESSIMISTIC_READ:
 case $lockMode === LockMode::PESSIMISTIC_WRITE:
 return $persister->load($sortedId, null, null, [], $lockMode);
 default:
 return $persister->loadById($sortedId);
 }
 }
 public function getReference($entityName, $id)
 {
 $class = $this->metadataFactory->getMetadataFor(ltrim($entityName, '\\'));
 if (!is_array($id)) {
 $id = [$class->identifier[0] => $id];
 }
 $sortedId = [];
 foreach ($class->identifier as $identifier) {
 if (!isset($id[$identifier])) {
 throw MissingIdentifierField::fromFieldAndClass($identifier, $class->name);
 }
 $sortedId[$identifier] = $id[$identifier];
 unset($id[$identifier]);
 }
 if ($id) {
 throw UnrecognizedIdentifierFields::fromClassAndFieldNames($class->name, array_keys($id));
 }
 $entity = $this->unitOfWork->tryGetById($sortedId, $class->rootEntityName);
 // Check identity map first, if its already in there just return it.
 if ($entity !== \false) {
 return $entity instanceof $class->name ? $entity : null;
 }
 if ($class->subClasses) {
 return $this->find($entityName, $sortedId);
 }
 $entity = $this->proxyFactory->getProxy($class->name, $sortedId);
 $this->unitOfWork->registerManaged($entity, $sortedId, []);
 return $entity;
 }
 public function getPartialReference($entityName, $identifier)
 {
 $class = $this->metadataFactory->getMetadataFor(ltrim($entityName, '\\'));
 $entity = $this->unitOfWork->tryGetById($identifier, $class->rootEntityName);
 // Check identity map first, if its already in there just return it.
 if ($entity !== \false) {
 return $entity instanceof $class->name ? $entity : null;
 }
 if (!is_array($identifier)) {
 $identifier = [$class->identifier[0] => $identifier];
 }
 $entity = $class->newInstance();
 $class->setIdentifierValues($entity, $identifier);
 $this->unitOfWork->registerManaged($entity, $identifier, []);
 $this->unitOfWork->markReadOnly($entity);
 return $entity;
 }
 public function clear($entityName = null)
 {
 if ($entityName !== null && !is_string($entityName)) {
 throw ORMInvalidArgumentException::invalidEntityName($entityName);
 }
 if ($entityName !== null) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8460', 'Calling %s() with any arguments to clear specific entities is deprecated and will not be supported in Doctrine ORM 3.0.', __METHOD__);
 }
 $this->unitOfWork->clear($entityName === null ? null : $this->metadataFactory->getMetadataFor($entityName)->getName());
 }
 public function close()
 {
 $this->clear();
 $this->closed = \true;
 }
 public function persist($entity)
 {
 if (!is_object($entity)) {
 throw ORMInvalidArgumentException::invalidObject('EntityManager#persist()', $entity);
 }
 $this->errorIfClosed();
 $this->unitOfWork->persist($entity);
 }
 public function remove($entity)
 {
 if (!is_object($entity)) {
 throw ORMInvalidArgumentException::invalidObject('EntityManager#remove()', $entity);
 }
 $this->errorIfClosed();
 $this->unitOfWork->remove($entity);
 }
 public function refresh($entity)
 {
 if (!is_object($entity)) {
 throw ORMInvalidArgumentException::invalidObject('EntityManager#refresh()', $entity);
 }
 $this->errorIfClosed();
 $this->unitOfWork->refresh($entity);
 }
 public function detach($entity)
 {
 if (!is_object($entity)) {
 throw ORMInvalidArgumentException::invalidObject('EntityManager#detach()', $entity);
 }
 $this->unitOfWork->detach($entity);
 }
 public function merge($entity)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8461', 'Method %s() is deprecated and will be removed in Doctrine ORM 3.0.', __METHOD__);
 if (!is_object($entity)) {
 throw ORMInvalidArgumentException::invalidObject('EntityManager#merge()', $entity);
 }
 $this->errorIfClosed();
 return $this->unitOfWork->merge($entity);
 }
 public function copy($entity, $deep = \false)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8462', 'Method %s() is deprecated and will be removed in Doctrine ORM 3.0.', __METHOD__);
 throw new BadMethodCallException('Not implemented.');
 }
 public function lock($entity, $lockMode, $lockVersion = null)
 {
 $this->unitOfWork->lock($entity, $lockMode, $lockVersion);
 }
 public function getRepository($entityName)
 {
 return $this->repositoryFactory->getRepository($this, $entityName);
 }
 public function contains($entity)
 {
 return $this->unitOfWork->isScheduledForInsert($entity) || $this->unitOfWork->isInIdentityMap($entity) && !$this->unitOfWork->isScheduledForDelete($entity);
 }
 public function getEventManager()
 {
 return $this->eventManager;
 }
 public function getConfiguration()
 {
 return $this->config;
 }
 private function errorIfClosed() : void
 {
 if ($this->closed) {
 throw EntityManagerClosed::create();
 }
 }
 public function isOpen()
 {
 return !$this->closed;
 }
 public function getUnitOfWork()
 {
 return $this->unitOfWork;
 }
 public function getHydrator($hydrationMode)
 {
 return $this->newHydrator($hydrationMode);
 }
 public function newHydrator($hydrationMode)
 {
 switch ($hydrationMode) {
 case Query::HYDRATE_OBJECT:
 return new Internal\Hydration\ObjectHydrator($this);
 case Query::HYDRATE_ARRAY:
 return new Internal\Hydration\ArrayHydrator($this);
 case Query::HYDRATE_SCALAR:
 return new Internal\Hydration\ScalarHydrator($this);
 case Query::HYDRATE_SINGLE_SCALAR:
 return new Internal\Hydration\SingleScalarHydrator($this);
 case Query::HYDRATE_SIMPLEOBJECT:
 return new Internal\Hydration\SimpleObjectHydrator($this);
 case Query::HYDRATE_SCALAR_COLUMN:
 return new Internal\Hydration\ScalarColumnHydrator($this);
 default:
 $class = $this->config->getCustomHydrationMode($hydrationMode);
 if ($class !== null) {
 return new $class($this);
 }
 }
 throw InvalidHydrationMode::fromMode((string) $hydrationMode);
 }
 public function getProxyFactory()
 {
 return $this->proxyFactory;
 }
 public function initializeObject($obj)
 {
 $this->unitOfWork->initializeObject($obj);
 }
 public static function create($connection, Configuration $config, ?EventManager $eventManager = null)
 {
 if (!$config->getMetadataDriverImpl()) {
 throw MissingMappingDriverImplementation::create();
 }
 $connection = static::createConnection($connection, $config, $eventManager);
 return new EntityManager($connection, $config, $connection->getEventManager());
 }
 protected static function createConnection($connection, Configuration $config, ?EventManager $eventManager = null)
 {
 if (is_array($connection)) {
 return DriverManager::getConnection($connection, $config, $eventManager ?: new EventManager());
 }
 if (!$connection instanceof Connection) {
 throw new InvalidArgumentException(sprintf('Invalid $connection argument of type %s given%s.', get_debug_type($connection), is_object($connection) ? '' : ': "' . $connection . '"'));
 }
 if ($eventManager !== null && $connection->getEventManager() !== $eventManager) {
 throw MismatchedEventManager::create();
 }
 return $connection;
 }
 public function getFilters()
 {
 if ($this->filterCollection === null) {
 $this->filterCollection = new FilterCollection($this);
 }
 return $this->filterCollection;
 }
 public function isFiltersStateClean()
 {
 return $this->filterCollection === null || $this->filterCollection->isClean();
 }
 public function hasFilters()
 {
 return $this->filterCollection !== null;
 }
 private function checkLockRequirements(int $lockMode, ClassMetadata $class) : void
 {
 switch ($lockMode) {
 case LockMode::OPTIMISTIC:
 if (!$class->isVersioned) {
 throw OptimisticLockException::notVersioned($class->name);
 }
 break;
 case LockMode::PESSIMISTIC_READ:
 case LockMode::PESSIMISTIC_WRITE:
 if (!$this->getConnection()->isTransactionActive()) {
 throw TransactionRequiredException::transactionRequired();
 }
 }
 }
 private function configureMetadataCache() : void
 {
 $metadataCache = $this->config->getMetadataCache();
 if (!$metadataCache) {
 $this->configureLegacyMetadataCache();
 return;
 }
 $this->metadataFactory->setCache($metadataCache);
 }
 private function configureLegacyMetadataCache() : void
 {
 $metadataCache = $this->config->getMetadataCacheImpl();
 if (!$metadataCache) {
 return;
 }
 // Wrap doctrine/cache to provide PSR-6 interface
 $this->metadataFactory->setCache(CacheAdapter::wrap($metadataCache));
 }
}
