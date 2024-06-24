<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Cache\Cache;
use MailPoetVendor\Doctrine\Common\Cache\Psr6\CacheAdapter;
use MailPoetVendor\Doctrine\Common\Cache\Psr6\DoctrineProvider;
use MailPoetVendor\Doctrine\Common\Collections\ArrayCollection;
use MailPoetVendor\Doctrine\DBAL\Cache\QueryCacheProfile;
use MailPoetVendor\Doctrine\DBAL\LockMode;
use MailPoetVendor\Doctrine\DBAL\Types\Type;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\Internal\Hydration\IterableResult;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\Query\AST\DeleteStatement;
use MailPoetVendor\Doctrine\ORM\Query\AST\SelectStatement;
use MailPoetVendor\Doctrine\ORM\Query\AST\UpdateStatement;
use MailPoetVendor\Doctrine\ORM\Query\Exec\AbstractSqlExecutor;
use MailPoetVendor\Doctrine\ORM\Query\Parameter;
use MailPoetVendor\Doctrine\ORM\Query\ParameterTypeInferer;
use MailPoetVendor\Doctrine\ORM\Query\Parser;
use MailPoetVendor\Doctrine\ORM\Query\ParserResult;
use MailPoetVendor\Doctrine\ORM\Query\QueryException;
use MailPoetVendor\Doctrine\ORM\Query\ResultSetMapping;
use MailPoetVendor\Doctrine\ORM\Utility\HierarchyDiscriminatorResolver;
use MailPoetVendor\Psr\Cache\CacheItemPoolInterface;
use function array_keys;
use function array_values;
use function assert;
use function count;
use function get_debug_type;
use function in_array;
use function is_int;
use function ksort;
use function md5;
use function method_exists;
use function reset;
use function serialize;
use function sha1;
use function stripos;
final class Query extends AbstractQuery
{
 public const STATE_CLEAN = 1;
 public const STATE_DIRTY = 2;
 public const HINT_REFRESH = 'doctrine.refresh';
 public const HINT_CACHE_ENABLED = 'doctrine.cache.enabled';
 public const HINT_CACHE_EVICT = 'doctrine.cache.evict';
 public const HINT_REFRESH_ENTITY = 'doctrine.refresh.entity';
 public const HINT_FORCE_PARTIAL_LOAD = 'doctrine.forcePartialLoad';
 public const HINT_INCLUDE_META_COLUMNS = 'doctrine.includeMetaColumns';
 public const HINT_CUSTOM_TREE_WALKERS = 'doctrine.customTreeWalkers';
 public const HINT_CUSTOM_OUTPUT_WALKER = 'doctrine.customOutputWalker';
 public const HINT_READ_ONLY = 'doctrine.readOnly';
 public const HINT_INTERNAL_ITERATION = 'doctrine.internal.iteration';
 public const HINT_LOCK_MODE = 'doctrine.lockMode';
 private $_state = self::STATE_DIRTY;
 private $parsedTypes = [];
 private $dql = null;
 private $parserResult;
 private $firstResult = 0;
 private $maxResults = null;
 private $queryCache;
 private $expireQueryCache = \false;
 private $queryCacheTTL;
 private $useQueryCache = \true;
 public function getSQL()
 {
 return $this->parse()->getSqlExecutor()->getSqlStatements();
 }
 public function getAST()
 {
 $parser = new Parser($this);
 return $parser->getAST();
 }
 protected function getResultSetMapping()
 {
 // parse query or load from cache
 if ($this->_resultSetMapping === null) {
 $this->_resultSetMapping = $this->parse()->getResultSetMapping();
 }
 return $this->_resultSetMapping;
 }
 private function parse() : ParserResult
 {
 $types = [];
 foreach ($this->parameters as $parameter) {
 $types[$parameter->getName()] = $parameter->getType();
 }
 // Return previous parser result if the query and the filter collection are both clean
 if ($this->_state === self::STATE_CLEAN && $this->parsedTypes === $types && $this->_em->isFiltersStateClean()) {
 return $this->parserResult;
 }
 $this->_state = self::STATE_CLEAN;
 $this->parsedTypes = $types;
 $queryCache = $this->queryCache ?? $this->_em->getConfiguration()->getQueryCache();
 // Check query cache.
 if (!($this->useQueryCache && $queryCache)) {
 $parser = new Parser($this);
 $this->parserResult = $parser->parse();
 return $this->parserResult;
 }
 $cacheItem = $queryCache->getItem($this->getQueryCacheId());
 if (!$this->expireQueryCache && $cacheItem->isHit()) {
 $cached = $cacheItem->get();
 if ($cached instanceof ParserResult) {
 // Cache hit.
 $this->parserResult = $cached;
 return $this->parserResult;
 }
 }
 // Cache miss.
 $parser = new Parser($this);
 $this->parserResult = $parser->parse();
 $queryCache->save($cacheItem->set($this->parserResult)->expiresAfter($this->queryCacheTTL));
 return $this->parserResult;
 }
 protected function _doExecute()
 {
 $executor = $this->parse()->getSqlExecutor();
 if ($this->_queryCacheProfile) {
 $executor->setQueryCacheProfile($this->_queryCacheProfile);
 } else {
 $executor->removeQueryCacheProfile();
 }
 if ($this->_resultSetMapping === null) {
 $this->_resultSetMapping = $this->parserResult->getResultSetMapping();
 }
 // Prepare parameters
 $paramMappings = $this->parserResult->getParameterMappings();
 $paramCount = count($this->parameters);
 $mappingCount = count($paramMappings);
 if ($paramCount > $mappingCount) {
 throw QueryException::tooManyParameters($mappingCount, $paramCount);
 }
 if ($paramCount < $mappingCount) {
 throw QueryException::tooFewParameters($mappingCount, $paramCount);
 }
 // evict all cache for the entity region
 if ($this->hasCache && isset($this->_hints[self::HINT_CACHE_EVICT]) && $this->_hints[self::HINT_CACHE_EVICT]) {
 $this->evictEntityCacheRegion();
 }
 [$sqlParams, $types] = $this->processParameterMappings($paramMappings);
 $this->evictResultSetCache($executor, $sqlParams, $types, $this->_em->getConnection()->getParams());
 return $executor->execute($this->_em->getConnection(), $sqlParams, $types);
 }
 private function evictResultSetCache(AbstractSqlExecutor $executor, array $sqlParams, array $types, array $connectionParams) : void
 {
 if ($this->_queryCacheProfile === null || !$this->getExpireResultCache()) {
 return;
 }
 $cache = method_exists(QueryCacheProfile::class, 'getResultCache') ? $this->_queryCacheProfile->getResultCache() : $this->_queryCacheProfile->getResultCacheDriver();
 assert($cache !== null);
 $statements = (array) $executor->getSqlStatements();
 // Type casted since it can either be a string or an array
 foreach ($statements as $statement) {
 $cacheKeys = $this->_queryCacheProfile->generateCacheKeys($statement, $sqlParams, $types, $connectionParams);
 $cache instanceof CacheItemPoolInterface ? $cache->deleteItem(reset($cacheKeys)) : $cache->delete(reset($cacheKeys));
 }
 }
 private function evictEntityCacheRegion() : void
 {
 $AST = $this->getAST();
 if ($AST instanceof SelectStatement) {
 throw new QueryException('The hint "HINT_CACHE_EVICT" is not valid for select statements.');
 }
 $className = $AST instanceof DeleteStatement ? $AST->deleteClause->abstractSchemaName : $AST->updateClause->abstractSchemaName;
 $this->_em->getCache()->evictEntityRegion($className);
 }
 private function processParameterMappings(array $paramMappings) : array
 {
 $sqlParams = [];
 $types = [];
 foreach ($this->parameters as $parameter) {
 $key = $parameter->getName();
 if (!isset($paramMappings[$key])) {
 throw QueryException::unknownParameter($key);
 }
 [$value, $type] = $this->resolveParameterValue($parameter);
 foreach ($paramMappings[$key] as $position) {
 $types[$position] = $type;
 }
 $sqlPositions = $paramMappings[$key];
 // optimized multi value sql positions away for now,
 // they are not allowed in DQL anyways.
 $value = [$value];
 $countValue = count($value);
 for ($i = 0, $l = count($sqlPositions); $i < $l; $i++) {
 $sqlParams[$sqlPositions[$i]] = $value[$i % $countValue];
 }
 }
 if (count($sqlParams) !== count($types)) {
 throw QueryException::parameterTypeMismatch();
 }
 if ($sqlParams) {
 ksort($sqlParams);
 $sqlParams = array_values($sqlParams);
 ksort($types);
 $types = array_values($types);
 }
 return [$sqlParams, $types];
 }
 private function resolveParameterValue(Parameter $parameter) : array
 {
 if ($parameter->typeWasSpecified()) {
 return [$parameter->getValue(), $parameter->getType()];
 }
 $key = $parameter->getName();
 $originalValue = $parameter->getValue();
 $value = $originalValue;
 $rsm = $this->getResultSetMapping();
 if ($value instanceof ClassMetadata && isset($rsm->metadataParameterMapping[$key])) {
 $value = $value->getMetadataValue($rsm->metadataParameterMapping[$key]);
 }
 if ($value instanceof ClassMetadata && isset($rsm->discriminatorParameters[$key])) {
 $value = array_keys(HierarchyDiscriminatorResolver::resolveDiscriminatorsForClass($value, $this->_em));
 }
 $processedValue = $this->processParameterValue($value);
 return [$processedValue, $originalValue === $processedValue ? $parameter->getType() : ParameterTypeInferer::inferType($processedValue)];
 }
 public function setQueryCacheDriver($queryCache) : self
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9004', '%s is deprecated and will be removed in Doctrine 3.0. Use setQueryCache() instead.', __METHOD__);
 $this->queryCache = $queryCache ? CacheAdapter::wrap($queryCache) : null;
 return $this;
 }
 public function setQueryCache(?CacheItemPoolInterface $queryCache) : self
 {
 $this->queryCache = $queryCache;
 return $this;
 }
 public function useQueryCache($bool) : self
 {
 $this->useQueryCache = $bool;
 return $this;
 }
 public function getQueryCacheDriver() : ?Cache
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9004', '%s is deprecated and will be removed in Doctrine 3.0 without replacement.', __METHOD__);
 $queryCache = $this->queryCache ?? $this->_em->getConfiguration()->getQueryCache();
 return $queryCache ? DoctrineProvider::wrap($queryCache) : null;
 }
 public function setQueryCacheLifetime($timeToLive) : self
 {
 if ($timeToLive !== null) {
 $timeToLive = (int) $timeToLive;
 }
 $this->queryCacheTTL = $timeToLive;
 return $this;
 }
 public function getQueryCacheLifetime() : ?int
 {
 return $this->queryCacheTTL;
 }
 public function expireQueryCache($expire = \true) : self
 {
 $this->expireQueryCache = $expire;
 return $this;
 }
 public function getExpireQueryCache() : bool
 {
 return $this->expireQueryCache;
 }
 public function free() : void
 {
 parent::free();
 $this->dql = null;
 $this->_state = self::STATE_CLEAN;
 }
 public function setDQL($dqlQuery) : self
 {
 if ($dqlQuery === null) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9784', 'Calling %s with null is deprecated and will result in a TypeError in Doctrine 3.0', __METHOD__);
 return $this;
 }
 $this->dql = $dqlQuery;
 $this->_state = self::STATE_DIRTY;
 return $this;
 }
 public function getDQL() : ?string
 {
 return $this->dql;
 }
 public function getState() : int
 {
 return $this->_state;
 }
 public function contains($dql) : bool
 {
 return stripos($this->getDQL(), $dql) !== \false;
 }
 public function setFirstResult($firstResult) : self
 {
 if (!is_int($firstResult)) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9809', 'Calling %s with %s is deprecated and will result in a TypeError in Doctrine 3.0. Pass an integer.', __METHOD__, get_debug_type($firstResult));
 $firstResult = (int) $firstResult;
 }
 $this->firstResult = $firstResult;
 $this->_state = self::STATE_DIRTY;
 return $this;
 }
 public function getFirstResult() : ?int
 {
 return $this->firstResult;
 }
 public function setMaxResults($maxResults) : self
 {
 if ($maxResults !== null) {
 $maxResults = (int) $maxResults;
 }
 $this->maxResults = $maxResults;
 $this->_state = self::STATE_DIRTY;
 return $this;
 }
 public function getMaxResults() : ?int
 {
 return $this->maxResults;
 }
 public function iterate($parameters = null, $hydrationMode = self::HYDRATE_OBJECT) : IterableResult
 {
 $this->setHint(self::HINT_INTERNAL_ITERATION, \true);
 return parent::iterate($parameters, $hydrationMode);
 }
 public function toIterable(iterable $parameters = [], $hydrationMode = self::HYDRATE_OBJECT) : iterable
 {
 $this->setHint(self::HINT_INTERNAL_ITERATION, \true);
 return parent::toIterable($parameters, $hydrationMode);
 }
 public function setHint($name, $value) : self
 {
 $this->_state = self::STATE_DIRTY;
 return parent::setHint($name, $value);
 }
 public function setHydrationMode($hydrationMode) : self
 {
 $this->_state = self::STATE_DIRTY;
 return parent::setHydrationMode($hydrationMode);
 }
 public function setLockMode($lockMode) : self
 {
 if (in_array($lockMode, [LockMode::NONE, LockMode::PESSIMISTIC_READ, LockMode::PESSIMISTIC_WRITE], \true)) {
 if (!$this->_em->getConnection()->isTransactionActive()) {
 throw TransactionRequiredException::transactionRequired();
 }
 }
 $this->setHint(self::HINT_LOCK_MODE, $lockMode);
 return $this;
 }
 public function getLockMode() : ?int
 {
 $lockMode = $this->getHint(self::HINT_LOCK_MODE);
 if ($lockMode === \false) {
 return null;
 }
 return $lockMode;
 }
 protected function getQueryCacheId() : string
 {
 ksort($this->_hints);
 return md5($this->getDQL() . serialize($this->_hints) . '&platform=' . get_debug_type($this->getEntityManager()->getConnection()->getDatabasePlatform()) . ($this->_em->hasFilters() ? $this->_em->getFilters()->getHash() : '') . '&firstResult=' . $this->firstResult . '&maxResult=' . $this->maxResults . '&hydrationMode=' . $this->_hydrationMode . '&types=' . serialize($this->parsedTypes) . 'DOCTRINE_QUERY_CACHE_SALT');
 }
 protected function getHash() : string
 {
 return sha1(parent::getHash() . '-' . $this->firstResult . '-' . $this->maxResults);
 }
 public function __clone()
 {
 parent::__clone();
 $this->_state = self::STATE_DIRTY;
 }
}
