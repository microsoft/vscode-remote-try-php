<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
use BackedEnum;
use Countable;
use MailPoetVendor\Doctrine\Common\Cache\Psr6\CacheAdapter;
use MailPoetVendor\Doctrine\Common\Cache\Psr6\DoctrineProvider;
use MailPoetVendor\Doctrine\Common\Collections\ArrayCollection;
use MailPoetVendor\Doctrine\Common\Collections\Collection;
use MailPoetVendor\Doctrine\Common\Util\ClassUtils;
use MailPoetVendor\Doctrine\DBAL\Cache\QueryCacheProfile;
use MailPoetVendor\Doctrine\DBAL\Result;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\Cache\Exception\InvalidResultCacheDriver;
use MailPoetVendor\Doctrine\ORM\Cache\Logging\CacheLogger;
use MailPoetVendor\Doctrine\ORM\Cache\QueryCacheKey;
use MailPoetVendor\Doctrine\ORM\Cache\TimestampCacheKey;
use MailPoetVendor\Doctrine\ORM\Internal\Hydration\IterableResult;
use MailPoetVendor\Doctrine\ORM\Mapping\MappingException as ORMMappingException;
use MailPoetVendor\Doctrine\ORM\Query\Parameter;
use MailPoetVendor\Doctrine\ORM\Query\QueryException;
use MailPoetVendor\Doctrine\ORM\Query\ResultSetMapping;
use MailPoetVendor\Doctrine\Persistence\Mapping\MappingException;
use LogicException;
use MailPoetVendor\Psr\Cache\CacheItemPoolInterface;
use Traversable;
use function array_map;
use function array_shift;
use function assert;
use function count;
use function func_num_args;
use function in_array;
use function is_array;
use function is_numeric;
use function is_object;
use function is_scalar;
use function is_string;
use function iterator_count;
use function iterator_to_array;
use function ksort;
use function method_exists;
use function reset;
use function serialize;
use function sha1;
abstract class AbstractQuery
{
 public const HYDRATE_OBJECT = 1;
 public const HYDRATE_ARRAY = 2;
 public const HYDRATE_SCALAR = 3;
 public const HYDRATE_SINGLE_SCALAR = 4;
 public const HYDRATE_SIMPLEOBJECT = 5;
 public const HYDRATE_SCALAR_COLUMN = 6;
 protected $parameters;
 protected $_resultSetMapping;
 protected $_em;
 protected $_hints = [];
 protected $_hydrationMode = self::HYDRATE_OBJECT;
 protected $_queryCacheProfile;
 protected $_expireResultCache = \false;
 protected $_hydrationCacheProfile;
 protected $cacheable = \false;
 protected $hasCache = \false;
 protected $cacheRegion;
 protected $cacheMode;
 protected $cacheLogger;
 protected $lifetime = 0;
 public function __construct(EntityManagerInterface $em)
 {
 $this->_em = $em;
 $this->parameters = new ArrayCollection();
 $this->_hints = $em->getConfiguration()->getDefaultQueryHints();
 $this->hasCache = $this->_em->getConfiguration()->isSecondLevelCacheEnabled();
 if ($this->hasCache) {
 $this->cacheLogger = $em->getConfiguration()->getSecondLevelCacheConfiguration()->getCacheLogger();
 }
 }
 public function setCacheable($cacheable)
 {
 $this->cacheable = (bool) $cacheable;
 return $this;
 }
 public function isCacheable()
 {
 return $this->cacheable;
 }
 public function setCacheRegion($cacheRegion)
 {
 $this->cacheRegion = (string) $cacheRegion;
 return $this;
 }
 public function getCacheRegion()
 {
 return $this->cacheRegion;
 }
 protected function isCacheEnabled()
 {
 return $this->cacheable && $this->hasCache;
 }
 public function getLifetime()
 {
 return $this->lifetime;
 }
 public function setLifetime($lifetime)
 {
 $this->lifetime = (int) $lifetime;
 return $this;
 }
 public function getCacheMode()
 {
 return $this->cacheMode;
 }
 public function setCacheMode($cacheMode)
 {
 $this->cacheMode = (int) $cacheMode;
 return $this;
 }
 public abstract function getSQL();
 public function getEntityManager()
 {
 return $this->_em;
 }
 public function free()
 {
 $this->parameters = new ArrayCollection();
 $this->_hints = $this->_em->getConfiguration()->getDefaultQueryHints();
 }
 public function getParameters()
 {
 return $this->parameters;
 }
 public function getParameter($key)
 {
 $key = Query\Parameter::normalizeName($key);
 $filteredParameters = $this->parameters->filter(static function (Query\Parameter $parameter) use($key) : bool {
 $parameterName = $parameter->getName();
 return $key === $parameterName;
 });
 return !$filteredParameters->isEmpty() ? $filteredParameters->first() : null;
 }
 public function setParameters($parameters)
 {
 if (is_array($parameters)) {
 $parameterCollection = new ArrayCollection();
 foreach ($parameters as $key => $value) {
 $parameterCollection->add(new Parameter($key, $value));
 }
 $parameters = $parameterCollection;
 }
 $this->parameters = $parameters;
 return $this;
 }
 public function setParameter($key, $value, $type = null)
 {
 $existingParameter = $this->getParameter($key);
 if ($existingParameter !== null) {
 $existingParameter->setValue($value, $type);
 return $this;
 }
 $this->parameters->add(new Parameter($key, $value, $type));
 return $this;
 }
 public function processParameterValue($value)
 {
 if (is_scalar($value)) {
 return $value;
 }
 if ($value instanceof Collection) {
 $value = iterator_to_array($value);
 }
 if (is_array($value)) {
 $value = $this->processArrayParameterValue($value);
 return $value;
 }
 if ($value instanceof Mapping\ClassMetadata) {
 return $value->name;
 }
 if ($value instanceof BackedEnum) {
 return $value->value;
 }
 if (!is_object($value)) {
 return $value;
 }
 try {
 $class = ClassUtils::getClass($value);
 $value = $this->_em->getUnitOfWork()->getSingleIdentifierValue($value);
 if ($value === null) {
 throw ORMInvalidArgumentException::invalidIdentifierBindingEntity($class);
 }
 } catch (MappingException|ORMMappingException $e) {
 $value = $this->potentiallyProcessIterable($value);
 }
 return $value;
 }
 private function potentiallyProcessIterable($value)
 {
 if ($value instanceof Traversable) {
 $value = iterator_to_array($value);
 $value = $this->processArrayParameterValue($value);
 }
 return $value;
 }
 private function processArrayParameterValue(array $value) : array
 {
 foreach ($value as $key => $paramValue) {
 $paramValue = $this->processParameterValue($paramValue);
 $value[$key] = is_array($paramValue) ? reset($paramValue) : $paramValue;
 }
 return $value;
 }
 public function setResultSetMapping(Query\ResultSetMapping $rsm)
 {
 $this->translateNamespaces($rsm);
 $this->_resultSetMapping = $rsm;
 return $this;
 }
 protected function getResultSetMapping()
 {
 return $this->_resultSetMapping;
 }
 private function translateNamespaces(Query\ResultSetMapping $rsm) : void
 {
 $translate = function ($alias) : string {
 return $this->_em->getClassMetadata($alias)->getName();
 };
 $rsm->aliasMap = array_map($translate, $rsm->aliasMap);
 $rsm->declaringClasses = array_map($translate, $rsm->declaringClasses);
 }
 public function setHydrationCacheProfile(?QueryCacheProfile $profile = null)
 {
 if ($profile === null) {
 if (func_num_args() < 1) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9791', 'Calling %s without arguments is deprecated, pass null instead.', __METHOD__);
 }
 $this->_hydrationCacheProfile = null;
 return $this;
 }
 // DBAL 2
 if (!method_exists(QueryCacheProfile::class, 'setResultCache')) {
 if (!$profile->getResultCacheDriver()) {
 $defaultHydrationCacheImpl = $this->_em->getConfiguration()->getHydrationCache();
 if ($defaultHydrationCacheImpl) {
 $profile = $profile->setResultCacheDriver(DoctrineProvider::wrap($defaultHydrationCacheImpl));
 }
 }
 } elseif (!$profile->getResultCache()) {
 $defaultHydrationCacheImpl = $this->_em->getConfiguration()->getHydrationCache();
 if ($defaultHydrationCacheImpl) {
 $profile = $profile->setResultCache($defaultHydrationCacheImpl);
 }
 }
 $this->_hydrationCacheProfile = $profile;
 return $this;
 }
 public function getHydrationCacheProfile()
 {
 return $this->_hydrationCacheProfile;
 }
 public function setResultCacheProfile(?QueryCacheProfile $profile = null)
 {
 if ($profile === null) {
 if (func_num_args() < 1) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9791', 'Calling %s without arguments is deprecated, pass null instead.', __METHOD__);
 }
 $this->_queryCacheProfile = null;
 return $this;
 }
 // DBAL 2
 if (!method_exists(QueryCacheProfile::class, 'setResultCache')) {
 if (!$profile->getResultCacheDriver()) {
 $defaultResultCacheDriver = $this->_em->getConfiguration()->getResultCache();
 if ($defaultResultCacheDriver) {
 $profile = $profile->setResultCacheDriver(DoctrineProvider::wrap($defaultResultCacheDriver));
 }
 }
 } elseif (!$profile->getResultCache()) {
 $defaultResultCache = $this->_em->getConfiguration()->getResultCache();
 if ($defaultResultCache) {
 $profile = $profile->setResultCache($defaultResultCache);
 }
 }
 $this->_queryCacheProfile = $profile;
 return $this;
 }
 public function setResultCacheDriver($resultCacheDriver = null)
 {
 if ($resultCacheDriver !== null && !$resultCacheDriver instanceof \MailPoetVendor\Doctrine\Common\Cache\Cache) {
 throw InvalidResultCacheDriver::create();
 }
 return $this->setResultCache($resultCacheDriver ? CacheAdapter::wrap($resultCacheDriver) : null);
 }
 public function setResultCache(?CacheItemPoolInterface $resultCache = null)
 {
 if ($resultCache === null) {
 if (func_num_args() < 1) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9791', 'Calling %s without arguments is deprecated, pass null instead.', __METHOD__);
 }
 if ($this->_queryCacheProfile) {
 $this->_queryCacheProfile = new QueryCacheProfile($this->_queryCacheProfile->getLifetime(), $this->_queryCacheProfile->getCacheKey());
 }
 return $this;
 }
 // DBAL 2
 if (!method_exists(QueryCacheProfile::class, 'setResultCache')) {
 $resultCacheDriver = DoctrineProvider::wrap($resultCache);
 $this->_queryCacheProfile = $this->_queryCacheProfile ? $this->_queryCacheProfile->setResultCacheDriver($resultCacheDriver) : new QueryCacheProfile(0, null, $resultCacheDriver);
 return $this;
 }
 $this->_queryCacheProfile = $this->_queryCacheProfile ? $this->_queryCacheProfile->setResultCache($resultCache) : new QueryCacheProfile(0, null, $resultCache);
 return $this;
 }
 public function getResultCacheDriver()
 {
 if ($this->_queryCacheProfile && $this->_queryCacheProfile->getResultCacheDriver()) {
 return $this->_queryCacheProfile->getResultCacheDriver();
 }
 return $this->_em->getConfiguration()->getResultCacheImpl();
 }
 public function useResultCache($useCache, $lifetime = null, $resultCacheId = null)
 {
 return $useCache ? $this->enableResultCache($lifetime, $resultCacheId) : $this->disableResultCache();
 }
 public function enableResultCache(?int $lifetime = null, ?string $resultCacheId = null) : self
 {
 $this->setResultCacheLifetime($lifetime);
 $this->setResultCacheId($resultCacheId);
 return $this;
 }
 public function disableResultCache() : self
 {
 $this->_queryCacheProfile = null;
 return $this;
 }
 public function setResultCacheLifetime($lifetime)
 {
 $lifetime = (int) $lifetime;
 if ($this->_queryCacheProfile) {
 $this->_queryCacheProfile = $this->_queryCacheProfile->setLifetime($lifetime);
 return $this;
 }
 $this->_queryCacheProfile = new QueryCacheProfile($lifetime);
 $cache = $this->_em->getConfiguration()->getResultCache();
 if (!$cache) {
 return $this;
 }
 // Compatibility for DBAL 2
 if (!method_exists($this->_queryCacheProfile, 'setResultCache')) {
 $this->_queryCacheProfile = $this->_queryCacheProfile->setResultCacheDriver(DoctrineProvider::wrap($cache));
 return $this;
 }
 $this->_queryCacheProfile = $this->_queryCacheProfile->setResultCache($cache);
 return $this;
 }
 public function getResultCacheLifetime()
 {
 return $this->_queryCacheProfile ? $this->_queryCacheProfile->getLifetime() : 0;
 }
 public function expireResultCache($expire = \true)
 {
 $this->_expireResultCache = $expire;
 return $this;
 }
 public function getExpireResultCache()
 {
 return $this->_expireResultCache;
 }
 public function getQueryCacheProfile()
 {
 return $this->_queryCacheProfile;
 }
 public function setFetchMode($class, $assocName, $fetchMode)
 {
 if (!in_array($fetchMode, [Mapping\ClassMetadata::FETCH_EAGER, Mapping\ClassMetadata::FETCH_LAZY], \true)) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9777', 'Calling %s() with something else than ClassMetadata::FETCH_EAGER or ClassMetadata::FETCH_LAZY is deprecated.', __METHOD__);
 $fetchMode = Mapping\ClassMetadata::FETCH_LAZY;
 }
 $this->_hints['fetchMode'][$class][$assocName] = $fetchMode;
 return $this;
 }
 public function setHydrationMode($hydrationMode)
 {
 $this->_hydrationMode = $hydrationMode;
 return $this;
 }
 public function getHydrationMode()
 {
 return $this->_hydrationMode;
 }
 public function getResult($hydrationMode = self::HYDRATE_OBJECT)
 {
 return $this->execute(null, $hydrationMode);
 }
 public function getArrayResult()
 {
 return $this->execute(null, self::HYDRATE_ARRAY);
 }
 public function getSingleColumnResult()
 {
 return $this->execute(null, self::HYDRATE_SCALAR_COLUMN);
 }
 public function getScalarResult()
 {
 return $this->execute(null, self::HYDRATE_SCALAR);
 }
 public function getOneOrNullResult($hydrationMode = null)
 {
 try {
 $result = $this->execute(null, $hydrationMode);
 } catch (NoResultException $e) {
 return null;
 }
 if ($this->_hydrationMode !== self::HYDRATE_SINGLE_SCALAR && !$result) {
 return null;
 }
 if (!is_array($result)) {
 return $result;
 }
 if (count($result) > 1) {
 throw new NonUniqueResultException();
 }
 return array_shift($result);
 }
 public function getSingleResult($hydrationMode = null)
 {
 $result = $this->execute(null, $hydrationMode);
 if ($this->_hydrationMode !== self::HYDRATE_SINGLE_SCALAR && !$result) {
 throw new NoResultException();
 }
 if (!is_array($result)) {
 return $result;
 }
 if (count($result) > 1) {
 throw new NonUniqueResultException();
 }
 return array_shift($result);
 }
 public function getSingleScalarResult()
 {
 return $this->getSingleResult(self::HYDRATE_SINGLE_SCALAR);
 }
 public function setHint($name, $value)
 {
 $this->_hints[$name] = $value;
 return $this;
 }
 public function getHint($name)
 {
 return $this->_hints[$name] ?? \false;
 }
 public function hasHint($name)
 {
 return isset($this->_hints[$name]);
 }
 public function getHints()
 {
 return $this->_hints;
 }
 public function iterate($parameters = null, $hydrationMode = null)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8463', 'Method %s() is deprecated and will be removed in Doctrine ORM 3.0. Use toIterable() instead.', __METHOD__);
 if ($hydrationMode !== null) {
 $this->setHydrationMode($hydrationMode);
 }
 if (!empty($parameters)) {
 $this->setParameters($parameters);
 }
 $rsm = $this->getResultSetMapping();
 if ($rsm === null) {
 throw new LogicException('Uninitialized result set mapping.');
 }
 $stmt = $this->_doExecute();
 return $this->_em->newHydrator($this->_hydrationMode)->iterate($stmt, $rsm, $this->_hints);
 }
 public function toIterable(iterable $parameters = [], $hydrationMode = null) : iterable
 {
 if ($hydrationMode !== null) {
 $this->setHydrationMode($hydrationMode);
 }
 if ($this->isCountable($parameters) && count($parameters) !== 0 || $parameters instanceof Traversable && iterator_count($parameters) !== 0) {
 $this->setParameters($parameters);
 }
 $rsm = $this->getResultSetMapping();
 if ($rsm === null) {
 throw new LogicException('Uninitialized result set mapping.');
 }
 if ($rsm->isMixed && count($rsm->scalarMappings) > 0) {
 throw QueryException::iterateWithMixedResultNotAllowed();
 }
 $stmt = $this->_doExecute();
 return $this->_em->newHydrator($this->_hydrationMode)->toIterable($stmt, $rsm, $this->_hints);
 }
 public function execute($parameters = null, $hydrationMode = null)
 {
 if ($this->cacheable && $this->isCacheEnabled()) {
 return $this->executeUsingQueryCache($parameters, $hydrationMode);
 }
 return $this->executeIgnoreQueryCache($parameters, $hydrationMode);
 }
 private function executeIgnoreQueryCache($parameters = null, $hydrationMode = null)
 {
 if ($hydrationMode !== null) {
 $this->setHydrationMode($hydrationMode);
 }
 if (!empty($parameters)) {
 $this->setParameters($parameters);
 }
 $setCacheEntry = static function ($data) : void {
 };
 if ($this->_hydrationCacheProfile !== null) {
 [$cacheKey, $realCacheKey] = $this->getHydrationCacheId();
 $cache = $this->getHydrationCache();
 $cacheItem = $cache->getItem($cacheKey);
 $result = $cacheItem->isHit() ? $cacheItem->get() : [];
 if (isset($result[$realCacheKey])) {
 return $result[$realCacheKey];
 }
 if (!$result) {
 $result = [];
 }
 $setCacheEntry = static function ($data) use($cache, $result, $cacheItem, $realCacheKey) : void {
 $cache->save($cacheItem->set($result + [$realCacheKey => $data]));
 };
 }
 $stmt = $this->_doExecute();
 if (is_numeric($stmt)) {
 $setCacheEntry($stmt);
 return $stmt;
 }
 $rsm = $this->getResultSetMapping();
 if ($rsm === null) {
 throw new LogicException('Uninitialized result set mapping.');
 }
 $data = $this->_em->newHydrator($this->_hydrationMode)->hydrateAll($stmt, $rsm, $this->_hints);
 $setCacheEntry($data);
 return $data;
 }
 private function getHydrationCache() : CacheItemPoolInterface
 {
 assert($this->_hydrationCacheProfile !== null);
 // Support for DBAL 2
 if (!method_exists($this->_hydrationCacheProfile, 'getResultCache')) {
 $cacheDriver = $this->_hydrationCacheProfile->getResultCacheDriver();
 assert($cacheDriver !== null);
 return CacheAdapter::wrap($cacheDriver);
 }
 $cache = $this->_hydrationCacheProfile->getResultCache();
 assert($cache !== null);
 return $cache;
 }
 private function executeUsingQueryCache($parameters = null, $hydrationMode = null)
 {
 $rsm = $this->getResultSetMapping();
 if ($rsm === null) {
 throw new LogicException('Uninitialized result set mapping.');
 }
 $queryCache = $this->_em->getCache()->getQueryCache($this->cacheRegion);
 $queryKey = new QueryCacheKey($this->getHash(), $this->lifetime, $this->cacheMode ?: Cache::MODE_NORMAL, $this->getTimestampKey());
 $result = $queryCache->get($queryKey, $rsm, $this->_hints);
 if ($result !== null) {
 if ($this->cacheLogger) {
 $this->cacheLogger->queryCacheHit($queryCache->getRegion()->getName(), $queryKey);
 }
 return $result;
 }
 $result = $this->executeIgnoreQueryCache($parameters, $hydrationMode);
 $cached = $queryCache->put($queryKey, $rsm, $result, $this->_hints);
 if ($this->cacheLogger) {
 $this->cacheLogger->queryCacheMiss($queryCache->getRegion()->getName(), $queryKey);
 if ($cached) {
 $this->cacheLogger->queryCachePut($queryCache->getRegion()->getName(), $queryKey);
 }
 }
 return $result;
 }
 private function getTimestampKey() : ?TimestampCacheKey
 {
 assert($this->_resultSetMapping !== null);
 $entityName = reset($this->_resultSetMapping->aliasMap);
 if (empty($entityName)) {
 return null;
 }
 $metadata = $this->_em->getClassMetadata($entityName);
 return new Cache\TimestampCacheKey($metadata->rootEntityName);
 }
 protected function getHydrationCacheId()
 {
 $parameters = [];
 $types = [];
 foreach ($this->getParameters() as $parameter) {
 $parameters[$parameter->getName()] = $this->processParameterValue($parameter->getValue());
 $types[$parameter->getName()] = $parameter->getType();
 }
 $sql = $this->getSQL();
 assert(is_string($sql));
 $queryCacheProfile = $this->getHydrationCacheProfile();
 $hints = $this->getHints();
 $hints['hydrationMode'] = $this->getHydrationMode();
 ksort($hints);
 assert($queryCacheProfile !== null);
 return $queryCacheProfile->generateCacheKeys($sql, $parameters, $types, $hints);
 }
 public function setResultCacheId($id)
 {
 if (!$this->_queryCacheProfile) {
 return $this->setResultCacheProfile(new QueryCacheProfile(0, $id));
 }
 $this->_queryCacheProfile = $this->_queryCacheProfile->setCacheKey($id);
 return $this;
 }
 public function getResultCacheId()
 {
 return $this->_queryCacheProfile ? $this->_queryCacheProfile->getCacheKey() : null;
 }
 protected abstract function _doExecute();
 public function __clone()
 {
 $this->parameters = new ArrayCollection();
 $this->_hints = [];
 $this->_hints = $this->_em->getConfiguration()->getDefaultQueryHints();
 }
 protected function getHash()
 {
 $query = $this->getSQL();
 assert(is_string($query));
 $hints = $this->getHints();
 $params = array_map(function (Parameter $parameter) {
 $value = $parameter->getValue();
 // Small optimization
 // Does not invoke processParameterValue for scalar value
 if (is_scalar($value)) {
 return $value;
 }
 return $this->processParameterValue($value);
 }, $this->parameters->getValues());
 ksort($hints);
 return sha1($query . '-' . serialize($params) . '-' . serialize($hints));
 }
 private function isCountable(iterable $subject) : bool
 {
 return $subject instanceof Countable || is_array($subject);
 }
}
