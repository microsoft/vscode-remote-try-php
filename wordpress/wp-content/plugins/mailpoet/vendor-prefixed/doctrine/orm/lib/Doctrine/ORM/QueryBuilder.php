<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Collections\ArrayCollection;
use MailPoetVendor\Doctrine\Common\Collections\Criteria;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\Query\Expr;
use MailPoetVendor\Doctrine\ORM\Query\Parameter;
use MailPoetVendor\Doctrine\ORM\Query\QueryExpressionVisitor;
use InvalidArgumentException;
use RuntimeException;
use function array_keys;
use function array_merge;
use function array_unshift;
use function assert;
use function func_get_args;
use function func_num_args;
use function implode;
use function in_array;
use function is_array;
use function is_numeric;
use function is_object;
use function is_string;
use function key;
use function reset;
use function sprintf;
use function str_starts_with;
use function strpos;
use function strrpos;
use function substr;
class QueryBuilder
{
 public const SELECT = 0;
 public const DELETE = 1;
 public const UPDATE = 2;
 public const STATE_DIRTY = 0;
 public const STATE_CLEAN = 1;
 private $_em;
 private $_dqlParts = ['distinct' => \false, 'select' => [], 'from' => [], 'join' => [], 'set' => [], 'where' => null, 'groupBy' => [], 'having' => null, 'orderBy' => []];
 private $_type = self::SELECT;
 private $_state = self::STATE_CLEAN;
 private $_dql;
 private $parameters;
 private $_firstResult = 0;
 private $_maxResults = null;
 private $joinRootAliases = [];
 protected $cacheable = \false;
 protected $cacheRegion;
 protected $cacheMode;
 protected $lifetime = 0;
 public function __construct(EntityManagerInterface $em)
 {
 $this->_em = $em;
 $this->parameters = new ArrayCollection();
 }
 public function expr()
 {
 return $this->_em->getExpressionBuilder();
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
 public function getType()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/orm/pull/9945', 'Relying on the type of the query being built is deprecated.' . ' If necessary, track the type of the query being built outside of the builder.');
 return $this->_type;
 }
 public function getEntityManager()
 {
 return $this->_em;
 }
 public function getState()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/orm/pull/9945', 'Relying on the query builder state is deprecated as it is an internal concern.');
 return $this->_state;
 }
 public function getDQL()
 {
 if ($this->_dql !== null && $this->_state === self::STATE_CLEAN) {
 return $this->_dql;
 }
 switch ($this->_type) {
 case self::DELETE:
 $dql = $this->getDQLForDelete();
 break;
 case self::UPDATE:
 $dql = $this->getDQLForUpdate();
 break;
 case self::SELECT:
 default:
 $dql = $this->getDQLForSelect();
 break;
 }
 $this->_state = self::STATE_CLEAN;
 $this->_dql = $dql;
 return $dql;
 }
 public function getQuery()
 {
 $parameters = clone $this->parameters;
 $query = $this->_em->createQuery($this->getDQL())->setParameters($parameters)->setFirstResult($this->_firstResult)->setMaxResults($this->_maxResults);
 if ($this->lifetime) {
 $query->setLifetime($this->lifetime);
 }
 if ($this->cacheMode) {
 $query->setCacheMode($this->cacheMode);
 }
 if ($this->cacheable) {
 $query->setCacheable($this->cacheable);
 }
 if ($this->cacheRegion) {
 $query->setCacheRegion($this->cacheRegion);
 }
 return $query;
 }
 private function findRootAlias(string $alias, string $parentAlias) : string
 {
 if (in_array($parentAlias, $this->getRootAliases(), \true)) {
 $rootAlias = $parentAlias;
 } elseif (isset($this->joinRootAliases[$parentAlias])) {
 $rootAlias = $this->joinRootAliases[$parentAlias];
 } else {
 // Should never happen with correct joining order. Might be
 // thoughtful to throw exception instead.
 $rootAlias = $this->getRootAlias();
 }
 $this->joinRootAliases[$alias] = $rootAlias;
 return $rootAlias;
 }
 public function getRootAlias()
 {
 $aliases = $this->getRootAliases();
 if (!isset($aliases[0])) {
 throw new RuntimeException('No alias was set before invoking getRootAlias().');
 }
 return $aliases[0];
 }
 public function getRootAliases()
 {
 $aliases = [];
 foreach ($this->_dqlParts['from'] as &$fromClause) {
 if (is_string($fromClause)) {
 $spacePos = strrpos($fromClause, ' ');
 $from = substr($fromClause, 0, $spacePos);
 $alias = substr($fromClause, $spacePos + 1);
 $fromClause = new Query\Expr\From($from, $alias);
 }
 $aliases[] = $fromClause->getAlias();
 }
 return $aliases;
 }
 public function getAllAliases()
 {
 return array_merge($this->getRootAliases(), array_keys($this->joinRootAliases));
 }
 public function getRootEntities()
 {
 $entities = [];
 foreach ($this->_dqlParts['from'] as &$fromClause) {
 if (is_string($fromClause)) {
 $spacePos = strrpos($fromClause, ' ');
 $from = substr($fromClause, 0, $spacePos);
 $alias = substr($fromClause, $spacePos + 1);
 $fromClause = new Query\Expr\From($from, $alias);
 }
 $entities[] = $fromClause->getFrom();
 }
 return $entities;
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
 public function setParameters($parameters)
 {
 // BC compatibility with 2.3-
 if (is_array($parameters)) {
 $parameterCollection = new ArrayCollection();
 foreach ($parameters as $key => $value) {
 $parameter = new Parameter($key, $value);
 $parameterCollection->add($parameter);
 }
 $parameters = $parameterCollection;
 }
 $this->parameters = $parameters;
 return $this;
 }
 public function getParameters()
 {
 return $this->parameters;
 }
 public function getParameter($key)
 {
 $key = Parameter::normalizeName($key);
 $filteredParameters = $this->parameters->filter(static function (Parameter $parameter) use($key) : bool {
 $parameterName = $parameter->getName();
 return $key === $parameterName;
 });
 return !$filteredParameters->isEmpty() ? $filteredParameters->first() : null;
 }
 public function setFirstResult($firstResult)
 {
 $this->_firstResult = (int) $firstResult;
 return $this;
 }
 public function getFirstResult()
 {
 return $this->_firstResult;
 }
 public function setMaxResults($maxResults)
 {
 if ($maxResults !== null) {
 $maxResults = (int) $maxResults;
 }
 $this->_maxResults = $maxResults;
 return $this;
 }
 public function getMaxResults()
 {
 return $this->_maxResults;
 }
 public function add($dqlPartName, $dqlPart, $append = \false)
 {
 if ($append && ($dqlPartName === 'where' || $dqlPartName === 'having')) {
 throw new InvalidArgumentException("Using \$append = true does not have an effect with 'where' or 'having' " . 'parts. See QueryBuilder#andWhere() for an example for correct usage.');
 }
 $isMultiple = is_array($this->_dqlParts[$dqlPartName]) && !($dqlPartName === 'join' && !$append);
 // Allow adding any part retrieved from self::getDQLParts().
 if (is_array($dqlPart) && $dqlPartName !== 'join') {
 $dqlPart = reset($dqlPart);
 }
 // This is introduced for backwards compatibility reasons.
 // TODO: Remove for 3.0
 if ($dqlPartName === 'join') {
 $newDqlPart = [];
 foreach ($dqlPart as $k => $v) {
 $k = is_numeric($k) ? $this->getRootAlias() : $k;
 $newDqlPart[$k] = $v;
 }
 $dqlPart = $newDqlPart;
 }
 if ($append && $isMultiple) {
 if (is_array($dqlPart)) {
 $key = key($dqlPart);
 $this->_dqlParts[$dqlPartName][$key][] = $dqlPart[$key];
 } else {
 $this->_dqlParts[$dqlPartName][] = $dqlPart;
 }
 } else {
 $this->_dqlParts[$dqlPartName] = $isMultiple ? [$dqlPart] : $dqlPart;
 }
 $this->_state = self::STATE_DIRTY;
 return $this;
 }
 public function select($select = null)
 {
 $this->_type = self::SELECT;
 if (empty($select)) {
 return $this;
 }
 $selects = is_array($select) ? $select : func_get_args();
 return $this->add('select', new Expr\Select($selects), \false);
 }
 public function distinct($flag = \true)
 {
 $this->_dqlParts['distinct'] = (bool) $flag;
 return $this;
 }
 public function addSelect($select = null)
 {
 $this->_type = self::SELECT;
 if (empty($select)) {
 return $this;
 }
 $selects = is_array($select) ? $select : func_get_args();
 return $this->add('select', new Expr\Select($selects), \true);
 }
 public function delete($delete = null, $alias = null)
 {
 $this->_type = self::DELETE;
 if (!$delete) {
 return $this;
 }
 if (!$alias) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/9733', 'Omitting the alias is deprecated and will throw an exception in Doctrine 3.0.');
 }
 return $this->add('from', new Expr\From($delete, $alias));
 }
 public function update($update = null, $alias = null)
 {
 $this->_type = self::UPDATE;
 if (!$update) {
 return $this;
 }
 if (!$alias) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/9733', 'Omitting the alias is deprecated and will throw an exception in Doctrine 3.0.');
 }
 return $this->add('from', new Expr\From($update, $alias));
 }
 public function from($from, $alias, $indexBy = null)
 {
 return $this->add('from', new Expr\From($from, $alias, $indexBy), \true);
 }
 public function indexBy($alias, $indexBy)
 {
 $rootAliases = $this->getRootAliases();
 if (!in_array($alias, $rootAliases, \true)) {
 throw new Query\QueryException(sprintf('Specified root alias %s must be set before invoking indexBy().', $alias));
 }
 foreach ($this->_dqlParts['from'] as &$fromClause) {
 assert($fromClause instanceof Expr\From);
 if ($fromClause->getAlias() !== $alias) {
 continue;
 }
 $fromClause = new Expr\From($fromClause->getFrom(), $fromClause->getAlias(), $indexBy);
 }
 return $this;
 }
 public function join($join, $alias, $conditionType = null, $condition = null, $indexBy = null)
 {
 return $this->innerJoin($join, $alias, $conditionType, $condition, $indexBy);
 }
 public function innerJoin($join, $alias, $conditionType = null, $condition = null, $indexBy = null)
 {
 $parentAlias = substr($join, 0, (int) strpos($join, '.'));
 $rootAlias = $this->findRootAlias($alias, $parentAlias);
 $join = new Expr\Join(Expr\Join::INNER_JOIN, $join, $alias, $conditionType, $condition, $indexBy);
 return $this->add('join', [$rootAlias => $join], \true);
 }
 public function leftJoin($join, $alias, $conditionType = null, $condition = null, $indexBy = null)
 {
 $parentAlias = substr($join, 0, (int) strpos($join, '.'));
 $rootAlias = $this->findRootAlias($alias, $parentAlias);
 $join = new Expr\Join(Expr\Join::LEFT_JOIN, $join, $alias, $conditionType, $condition, $indexBy);
 return $this->add('join', [$rootAlias => $join], \true);
 }
 public function set($key, $value)
 {
 return $this->add('set', new Expr\Comparison($key, Expr\Comparison::EQ, $value), \true);
 }
 public function where($predicates)
 {
 if (!(func_num_args() === 1 && $predicates instanceof Expr\Composite)) {
 $predicates = new Expr\Andx(func_get_args());
 }
 return $this->add('where', $predicates);
 }
 public function andWhere()
 {
 $args = func_get_args();
 $where = $this->getDQLPart('where');
 if ($where instanceof Expr\Andx) {
 $where->addMultiple($args);
 } else {
 array_unshift($args, $where);
 $where = new Expr\Andx($args);
 }
 return $this->add('where', $where);
 }
 public function orWhere()
 {
 $args = func_get_args();
 $where = $this->getDQLPart('where');
 if ($where instanceof Expr\Orx) {
 $where->addMultiple($args);
 } else {
 array_unshift($args, $where);
 $where = new Expr\Orx($args);
 }
 return $this->add('where', $where);
 }
 public function groupBy($groupBy)
 {
 return $this->add('groupBy', new Expr\GroupBy(func_get_args()));
 }
 public function addGroupBy($groupBy)
 {
 return $this->add('groupBy', new Expr\GroupBy(func_get_args()), \true);
 }
 public function having($having)
 {
 if (!(func_num_args() === 1 && ($having instanceof Expr\Andx || $having instanceof Expr\Orx))) {
 $having = new Expr\Andx(func_get_args());
 }
 return $this->add('having', $having);
 }
 public function andHaving($having)
 {
 $args = func_get_args();
 $having = $this->getDQLPart('having');
 if ($having instanceof Expr\Andx) {
 $having->addMultiple($args);
 } else {
 array_unshift($args, $having);
 $having = new Expr\Andx($args);
 }
 return $this->add('having', $having);
 }
 public function orHaving($having)
 {
 $args = func_get_args();
 $having = $this->getDQLPart('having');
 if ($having instanceof Expr\Orx) {
 $having->addMultiple($args);
 } else {
 array_unshift($args, $having);
 $having = new Expr\Orx($args);
 }
 return $this->add('having', $having);
 }
 public function orderBy($sort, $order = null)
 {
 $orderBy = $sort instanceof Expr\OrderBy ? $sort : new Expr\OrderBy($sort, $order);
 return $this->add('orderBy', $orderBy);
 }
 public function addOrderBy($sort, $order = null)
 {
 $orderBy = $sort instanceof Expr\OrderBy ? $sort : new Expr\OrderBy($sort, $order);
 return $this->add('orderBy', $orderBy, \true);
 }
 public function addCriteria(Criteria $criteria)
 {
 $allAliases = $this->getAllAliases();
 if (!isset($allAliases[0])) {
 throw new Query\QueryException('No aliases are set before invoking addCriteria().');
 }
 $visitor = new QueryExpressionVisitor($this->getAllAliases());
 $whereExpression = $criteria->getWhereExpression();
 if ($whereExpression) {
 $this->andWhere($visitor->dispatch($whereExpression));
 foreach ($visitor->getParameters() as $parameter) {
 $this->parameters->add($parameter);
 }
 }
 if ($criteria->getOrderings()) {
 foreach ($criteria->getOrderings() as $sort => $order) {
 $hasValidAlias = \false;
 foreach ($allAliases as $alias) {
 if (str_starts_with($sort . '.', $alias . '.')) {
 $hasValidAlias = \true;
 break;
 }
 }
 if (!$hasValidAlias) {
 $sort = $allAliases[0] . '.' . $sort;
 }
 $this->addOrderBy($sort, $order);
 }
 }
 // Overwrite limits only if they was set in criteria
 $firstResult = $criteria->getFirstResult();
 if ($firstResult > 0) {
 $this->setFirstResult($firstResult);
 }
 $maxResults = $criteria->getMaxResults();
 if ($maxResults !== null) {
 $this->setMaxResults($maxResults);
 }
 return $this;
 }
 public function getDQLPart($queryPartName)
 {
 return $this->_dqlParts[$queryPartName];
 }
 public function getDQLParts()
 {
 return $this->_dqlParts;
 }
 private function getDQLForDelete() : string
 {
 return 'DELETE' . $this->getReducedDQLQueryPart('from', ['pre' => ' ', 'separator' => ', ']) . $this->getReducedDQLQueryPart('where', ['pre' => ' WHERE ']) . $this->getReducedDQLQueryPart('orderBy', ['pre' => ' ORDER BY ', 'separator' => ', ']);
 }
 private function getDQLForUpdate() : string
 {
 return 'UPDATE' . $this->getReducedDQLQueryPart('from', ['pre' => ' ', 'separator' => ', ']) . $this->getReducedDQLQueryPart('set', ['pre' => ' SET ', 'separator' => ', ']) . $this->getReducedDQLQueryPart('where', ['pre' => ' WHERE ']) . $this->getReducedDQLQueryPart('orderBy', ['pre' => ' ORDER BY ', 'separator' => ', ']);
 }
 private function getDQLForSelect() : string
 {
 $dql = 'SELECT' . ($this->_dqlParts['distinct'] === \true ? ' DISTINCT' : '') . $this->getReducedDQLQueryPart('select', ['pre' => ' ', 'separator' => ', ']);
 $fromParts = $this->getDQLPart('from');
 $joinParts = $this->getDQLPart('join');
 $fromClauses = [];
 // Loop through all FROM clauses
 if (!empty($fromParts)) {
 $dql .= ' FROM ';
 foreach ($fromParts as $from) {
 $fromClause = (string) $from;
 if ($from instanceof Expr\From && isset($joinParts[$from->getAlias()])) {
 foreach ($joinParts[$from->getAlias()] as $join) {
 $fromClause .= ' ' . (string) $join;
 }
 }
 $fromClauses[] = $fromClause;
 }
 }
 $dql .= implode(', ', $fromClauses) . $this->getReducedDQLQueryPart('where', ['pre' => ' WHERE ']) . $this->getReducedDQLQueryPart('groupBy', ['pre' => ' GROUP BY ', 'separator' => ', ']) . $this->getReducedDQLQueryPart('having', ['pre' => ' HAVING ']) . $this->getReducedDQLQueryPart('orderBy', ['pre' => ' ORDER BY ', 'separator' => ', ']);
 return $dql;
 }
 private function getReducedDQLQueryPart(string $queryPartName, array $options = []) : string
 {
 $queryPart = $this->getDQLPart($queryPartName);
 if (empty($queryPart)) {
 return $options['empty'] ?? '';
 }
 return ($options['pre'] ?? '') . (is_array($queryPart) ? implode($options['separator'], $queryPart) : $queryPart) . ($options['post'] ?? '');
 }
 public function resetDQLParts($parts = null)
 {
 if ($parts === null) {
 $parts = array_keys($this->_dqlParts);
 }
 foreach ($parts as $part) {
 $this->resetDQLPart($part);
 }
 return $this;
 }
 public function resetDQLPart($part)
 {
 $this->_dqlParts[$part] = is_array($this->_dqlParts[$part]) ? [] : null;
 $this->_state = self::STATE_DIRTY;
 return $this;
 }
 public function __toString()
 {
 return $this->getDQL();
 }
 public function __clone()
 {
 foreach ($this->_dqlParts as $part => $elements) {
 if (is_array($this->_dqlParts[$part])) {
 foreach ($this->_dqlParts[$part] as $idx => $element) {
 if (is_object($element)) {
 $this->_dqlParts[$part][$idx] = clone $element;
 }
 }
 } elseif (is_object($elements)) {
 $this->_dqlParts[$part] = clone $elements;
 }
 }
 $parameters = [];
 foreach ($this->parameters as $parameter) {
 $parameters[] = clone $parameter;
 }
 $this->parameters = new ArrayCollection($parameters);
 }
}
