<?php
namespace MailPoetVendor\Doctrine\DBAL\Query;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Cache\QueryCacheProfile;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Exception;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
use MailPoetVendor\Doctrine\DBAL\Query\Expression\CompositeExpression;
use MailPoetVendor\Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use MailPoetVendor\Doctrine\DBAL\Query\ForUpdate\ConflictResolutionMode;
use MailPoetVendor\Doctrine\DBAL\Result;
use MailPoetVendor\Doctrine\DBAL\Statement;
use MailPoetVendor\Doctrine\DBAL\Types\Type;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function array_key_exists;
use function array_keys;
use function array_unshift;
use function count;
use function func_get_arg;
use function func_get_args;
use function func_num_args;
use function implode;
use function is_array;
use function is_object;
use function key;
use function method_exists;
use function strtoupper;
use function substr;
use function ucfirst;
class QueryBuilder
{
 public const SELECT = 0;
 public const DELETE = 1;
 public const UPDATE = 2;
 public const INSERT = 3;
 public const STATE_DIRTY = 0;
 public const STATE_CLEAN = 1;
 private Connection $connection;
 private const SQL_PARTS_DEFAULTS = ['select' => [], 'distinct' => \false, 'from' => [], 'join' => [], 'set' => [], 'where' => null, 'groupBy' => [], 'having' => null, 'orderBy' => [], 'values' => [], 'for_update' => null];
 private array $sqlParts = self::SQL_PARTS_DEFAULTS;
 private ?string $sql = null;
 private $params = [];
 private array $paramTypes = [];
 private int $type = self::SELECT;
 private int $state = self::STATE_CLEAN;
 private int $firstResult = 0;
 private ?int $maxResults = null;
 private int $boundCounter = 0;
 private ?QueryCacheProfile $resultCacheProfile = null;
 public function __construct(Connection $connection)
 {
 $this->connection = $connection;
 }
 public function expr()
 {
 return $this->connection->getExpressionBuilder();
 }
 public function getType()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5551', 'Relying on the type of the query being built is deprecated.' . ' If necessary, track the type of the query being built outside of the builder.');
 return $this->type;
 }
 public function getConnection()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5780', '%s is deprecated. Use the connection used to instantiate the builder instead.', __METHOD__);
 return $this->connection;
 }
 public function getState()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5551', 'Relying on the query builder state is deprecated as it is an internal concern.');
 return $this->state;
 }
 public function fetchAssociative()
 {
 return $this->executeQuery()->fetchAssociative();
 }
 public function fetchNumeric()
 {
 return $this->executeQuery()->fetchNumeric();
 }
 public function fetchOne()
 {
 return $this->executeQuery()->fetchOne();
 }
 public function fetchAllNumeric() : array
 {
 return $this->executeQuery()->fetchAllNumeric();
 }
 public function fetchAllAssociative() : array
 {
 return $this->executeQuery()->fetchAllAssociative();
 }
 public function fetchAllKeyValue() : array
 {
 return $this->executeQuery()->fetchAllKeyValue();
 }
 public function fetchAllAssociativeIndexed() : array
 {
 return $this->executeQuery()->fetchAllAssociativeIndexed();
 }
 public function fetchFirstColumn() : array
 {
 return $this->executeQuery()->fetchFirstColumn();
 }
 public function executeQuery() : Result
 {
 return $this->connection->executeQuery($this->getSQL(), $this->params, $this->paramTypes, $this->resultCacheProfile);
 }
 public function executeStatement() : int
 {
 return $this->connection->executeStatement($this->getSQL(), $this->params, $this->paramTypes);
 }
 public function execute()
 {
 if ($this->type === self::SELECT) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4578', 'QueryBuilder::execute() is deprecated, use QueryBuilder::executeQuery() for SQL queries instead.');
 return $this->executeQuery();
 }
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4578', 'QueryBuilder::execute() is deprecated, use QueryBuilder::executeStatement() for SQL statements instead.');
 return $this->connection->executeStatement($this->getSQL(), $this->params, $this->paramTypes);
 }
 public function getSQL()
 {
 if ($this->sql !== null && $this->state === self::STATE_CLEAN) {
 return $this->sql;
 }
 switch ($this->type) {
 case self::INSERT:
 $sql = $this->getSQLForInsert();
 break;
 case self::DELETE:
 $sql = $this->getSQLForDelete();
 break;
 case self::UPDATE:
 $sql = $this->getSQLForUpdate();
 break;
 case self::SELECT:
 $sql = $this->getSQLForSelect();
 break;
 }
 $this->state = self::STATE_CLEAN;
 $this->sql = $sql;
 return $sql;
 }
 public function setParameter($key, $value, $type = ParameterType::STRING)
 {
 if ($type !== null) {
 $this->paramTypes[$key] = $type;
 } else {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5550', 'Using NULL as prepared statement parameter type is deprecated.' . 'Omit or use ParameterType::STRING instead');
 }
 $this->params[$key] = $value;
 return $this;
 }
 public function setParameters(array $params, array $types = [])
 {
 $this->paramTypes = $types;
 $this->params = $params;
 return $this;
 }
 public function getParameters()
 {
 return $this->params;
 }
 public function getParameter($key)
 {
 return $this->params[$key] ?? null;
 }
 public function getParameterTypes()
 {
 return $this->paramTypes;
 }
 public function getParameterType($key)
 {
 return $this->paramTypes[$key] ?? ParameterType::STRING;
 }
 public function setFirstResult($firstResult)
 {
 $this->state = self::STATE_DIRTY;
 $this->firstResult = $firstResult;
 return $this;
 }
 public function getFirstResult()
 {
 return $this->firstResult;
 }
 public function setMaxResults($maxResults)
 {
 $this->state = self::STATE_DIRTY;
 $this->maxResults = $maxResults;
 return $this;
 }
 public function getMaxResults()
 {
 return $this->maxResults;
 }
 public function forUpdate(int $conflictResolutionMode = ConflictResolutionMode::ORDINARY) : self
 {
 $this->state = self::STATE_DIRTY;
 $this->sqlParts['for_update'] = new ForUpdate($conflictResolutionMode);
 return $this;
 }
 public function add($sqlPartName, $sqlPart, $append = \false)
 {
 $isArray = is_array($sqlPart);
 $isMultiple = is_array($this->sqlParts[$sqlPartName]);
 if ($isMultiple && !$isArray) {
 $sqlPart = [$sqlPart];
 }
 $this->state = self::STATE_DIRTY;
 if ($append) {
 if ($sqlPartName === 'orderBy' || $sqlPartName === 'groupBy' || $sqlPartName === 'select' || $sqlPartName === 'set') {
 foreach ($sqlPart as $part) {
 $this->sqlParts[$sqlPartName][] = $part;
 }
 } elseif ($isArray && is_array($sqlPart[key($sqlPart)])) {
 $key = key($sqlPart);
 $this->sqlParts[$sqlPartName][$key][] = $sqlPart[$key];
 } elseif ($isMultiple) {
 $this->sqlParts[$sqlPartName][] = $sqlPart;
 } else {
 $this->sqlParts[$sqlPartName] = $sqlPart;
 }
 return $this;
 }
 $this->sqlParts[$sqlPartName] = $sqlPart;
 return $this;
 }
 public function select($select = null)
 {
 $this->type = self::SELECT;
 if ($select === null) {
 return $this;
 }
 if (is_array($select)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3837', 'Passing an array for the first argument to QueryBuilder::select() is deprecated, ' . 'pass each value as an individual variadic argument instead.');
 }
 $selects = is_array($select) ? $select : func_get_args();
 return $this->add('select', $selects);
 }
 public function distinct() : self
 {
 $this->sqlParts['distinct'] = func_num_args() < 1 || func_get_arg(0);
 $this->state = self::STATE_DIRTY;
 return $this;
 }
 public function addSelect($select = null)
 {
 $this->type = self::SELECT;
 if ($select === null) {
 return $this;
 }
 if (is_array($select)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3837', 'Passing an array for the first argument to QueryBuilder::addSelect() is deprecated, ' . 'pass each value as an individual variadic argument instead.');
 }
 $selects = is_array($select) ? $select : func_get_args();
 return $this->add('select', $selects, \true);
 }
 public function delete($delete = null, $alias = null)
 {
 $this->type = self::DELETE;
 if ($delete === null) {
 return $this;
 }
 return $this->add('from', ['table' => $delete, 'alias' => $alias]);
 }
 public function update($update = null, $alias = null)
 {
 $this->type = self::UPDATE;
 if ($update === null) {
 return $this;
 }
 return $this->add('from', ['table' => $update, 'alias' => $alias]);
 }
 public function insert($insert = null)
 {
 $this->type = self::INSERT;
 if ($insert === null) {
 return $this;
 }
 return $this->add('from', ['table' => $insert]);
 }
 public function from($from, $alias = null)
 {
 return $this->add('from', ['table' => $from, 'alias' => $alias], \true);
 }
 public function join($fromAlias, $join, $alias, $condition = null)
 {
 return $this->innerJoin($fromAlias, $join, $alias, $condition);
 }
 public function innerJoin($fromAlias, $join, $alias, $condition = null)
 {
 return $this->add('join', [$fromAlias => ['joinType' => 'inner', 'joinTable' => $join, 'joinAlias' => $alias, 'joinCondition' => $condition]], \true);
 }
 public function leftJoin($fromAlias, $join, $alias, $condition = null)
 {
 return $this->add('join', [$fromAlias => ['joinType' => 'left', 'joinTable' => $join, 'joinAlias' => $alias, 'joinCondition' => $condition]], \true);
 }
 public function rightJoin($fromAlias, $join, $alias, $condition = null)
 {
 return $this->add('join', [$fromAlias => ['joinType' => 'right', 'joinTable' => $join, 'joinAlias' => $alias, 'joinCondition' => $condition]], \true);
 }
 public function set($key, $value)
 {
 return $this->add('set', $key . ' = ' . $value, \true);
 }
 public function where($predicates)
 {
 if (!(func_num_args() === 1 && $predicates instanceof CompositeExpression)) {
 $predicates = CompositeExpression::and(...func_get_args());
 }
 return $this->add('where', $predicates);
 }
 public function andWhere($where)
 {
 $args = func_get_args();
 $where = $this->getQueryPart('where');
 if ($where instanceof CompositeExpression && $where->getType() === CompositeExpression::TYPE_AND) {
 $where = $where->with(...$args);
 } else {
 array_unshift($args, $where);
 $where = CompositeExpression::and(...$args);
 }
 return $this->add('where', $where, \true);
 }
 public function orWhere($where)
 {
 $args = func_get_args();
 $where = $this->getQueryPart('where');
 if ($where instanceof CompositeExpression && $where->getType() === CompositeExpression::TYPE_OR) {
 $where = $where->with(...$args);
 } else {
 array_unshift($args, $where);
 $where = CompositeExpression::or(...$args);
 }
 return $this->add('where', $where, \true);
 }
 public function groupBy($groupBy)
 {
 if (is_array($groupBy) && count($groupBy) === 0) {
 return $this;
 }
 if (is_array($groupBy)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3837', 'Passing an array for the first argument to QueryBuilder::groupBy() is deprecated, ' . 'pass each value as an individual variadic argument instead.');
 }
 $groupBy = is_array($groupBy) ? $groupBy : func_get_args();
 return $this->add('groupBy', $groupBy, \false);
 }
 public function addGroupBy($groupBy)
 {
 if (is_array($groupBy) && count($groupBy) === 0) {
 return $this;
 }
 if (is_array($groupBy)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3837', 'Passing an array for the first argument to QueryBuilder::addGroupBy() is deprecated, ' . 'pass each value as an individual variadic argument instead.');
 }
 $groupBy = is_array($groupBy) ? $groupBy : func_get_args();
 return $this->add('groupBy', $groupBy, \true);
 }
 public function setValue($column, $value)
 {
 $this->sqlParts['values'][$column] = $value;
 return $this;
 }
 public function values(array $values)
 {
 return $this->add('values', $values);
 }
 public function having($having)
 {
 if (!(func_num_args() === 1 && $having instanceof CompositeExpression)) {
 $having = CompositeExpression::and(...func_get_args());
 }
 return $this->add('having', $having);
 }
 public function andHaving($having)
 {
 $args = func_get_args();
 $having = $this->getQueryPart('having');
 if ($having instanceof CompositeExpression && $having->getType() === CompositeExpression::TYPE_AND) {
 $having = $having->with(...$args);
 } else {
 array_unshift($args, $having);
 $having = CompositeExpression::and(...$args);
 }
 return $this->add('having', $having);
 }
 public function orHaving($having)
 {
 $args = func_get_args();
 $having = $this->getQueryPart('having');
 if ($having instanceof CompositeExpression && $having->getType() === CompositeExpression::TYPE_OR) {
 $having = $having->with(...$args);
 } else {
 array_unshift($args, $having);
 $having = CompositeExpression::or(...$args);
 }
 return $this->add('having', $having);
 }
 public function orderBy($sort, $order = null)
 {
 return $this->add('orderBy', $sort . ' ' . ($order ?? 'ASC'), \false);
 }
 public function addOrderBy($sort, $order = null)
 {
 return $this->add('orderBy', $sort . ' ' . ($order ?? 'ASC'), \true);
 }
 public function getQueryPart($queryPartName)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/6179', 'Getting query parts is deprecated as they are implementation details.');
 return $this->sqlParts[$queryPartName];
 }
 public function getQueryParts()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/6179', 'Getting query parts is deprecated as they are implementation details.');
 return $this->sqlParts;
 }
 public function resetQueryParts($queryPartNames = null)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/6193', '%s() is deprecated, instead use dedicated reset methods for the parts that shall be reset.', __METHOD__);
 $queryPartNames ??= array_keys($this->sqlParts);
 foreach ($queryPartNames as $queryPartName) {
 $this->sqlParts[$queryPartName] = self::SQL_PARTS_DEFAULTS[$queryPartName];
 }
 $this->state = self::STATE_DIRTY;
 return $this;
 }
 public function resetQueryPart($queryPartName)
 {
 if ($queryPartName === 'distinct') {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/6193', 'Calling %s() with "distinct" is deprecated, call distinct(false) instead.', __METHOD__);
 return $this->distinct(\false);
 }
 $newMethodName = 'reset' . ucfirst($queryPartName);
 if (array_key_exists($queryPartName, self::SQL_PARTS_DEFAULTS) && method_exists($this, $newMethodName)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/6193', 'Calling %s() with "%s" is deprecated, call %s() instead.', __METHOD__, $queryPartName, $newMethodName);
 return $this->{$newMethodName}();
 }
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/6193', 'Calling %s() with "%s" is deprecated without replacement.', __METHOD__, $queryPartName, $newMethodName);
 $this->sqlParts[$queryPartName] = self::SQL_PARTS_DEFAULTS[$queryPartName];
 $this->state = self::STATE_DIRTY;
 return $this;
 }
 public function resetWhere() : self
 {
 $this->sqlParts['where'] = self::SQL_PARTS_DEFAULTS['where'];
 $this->state = self::STATE_DIRTY;
 return $this;
 }
 public function resetGroupBy() : self
 {
 $this->sqlParts['groupBy'] = self::SQL_PARTS_DEFAULTS['groupBy'];
 $this->state = self::STATE_DIRTY;
 return $this;
 }
 public function resetHaving() : self
 {
 $this->sqlParts['having'] = self::SQL_PARTS_DEFAULTS['having'];
 $this->state = self::STATE_DIRTY;
 return $this;
 }
 public function resetOrderBy() : self
 {
 $this->sqlParts['orderBy'] = self::SQL_PARTS_DEFAULTS['orderBy'];
 $this->state = self::STATE_DIRTY;
 return $this;
 }
 private function getSQLForSelect() : string
 {
 return $this->connection->getDatabasePlatform()->createSelectSQLBuilder()->buildSQL(new SelectQuery($this->sqlParts['distinct'], $this->sqlParts['select'], $this->getFromClauses(), $this->sqlParts['where'], $this->sqlParts['groupBy'], $this->sqlParts['having'], $this->sqlParts['orderBy'], new Limit($this->maxResults, $this->firstResult), $this->sqlParts['for_update']));
 }
 private function getFromClauses() : array
 {
 $fromClauses = [];
 $knownAliases = [];
 // Loop through all FROM clauses
 foreach ($this->sqlParts['from'] as $from) {
 if ($from['alias'] === null) {
 $tableSql = $from['table'];
 $tableReference = $from['table'];
 } else {
 $tableSql = $from['table'] . ' ' . $from['alias'];
 $tableReference = $from['alias'];
 }
 $knownAliases[$tableReference] = \true;
 $fromClauses[$tableReference] = $tableSql . $this->getSQLForJoins($tableReference, $knownAliases);
 }
 $this->verifyAllAliasesAreKnown($knownAliases);
 return $fromClauses;
 }
 private function verifyAllAliasesAreKnown(array $knownAliases) : void
 {
 foreach ($this->sqlParts['join'] as $fromAlias => $joins) {
 if (!isset($knownAliases[$fromAlias])) {
 throw QueryException::unknownAlias($fromAlias, array_keys($knownAliases));
 }
 }
 }
 private function getSQLForInsert() : string
 {
 return 'INSERT INTO ' . $this->sqlParts['from']['table'] . ' (' . implode(', ', array_keys($this->sqlParts['values'])) . ')' . ' VALUES(' . implode(', ', $this->sqlParts['values']) . ')';
 }
 private function getSQLForUpdate() : string
 {
 $table = $this->sqlParts['from']['table'] . ($this->sqlParts['from']['alias'] ? ' ' . $this->sqlParts['from']['alias'] : '');
 return 'UPDATE ' . $table . ' SET ' . implode(', ', $this->sqlParts['set']) . ($this->sqlParts['where'] !== null ? ' WHERE ' . (string) $this->sqlParts['where'] : '');
 }
 private function getSQLForDelete() : string
 {
 $table = $this->sqlParts['from']['table'] . ($this->sqlParts['from']['alias'] ? ' ' . $this->sqlParts['from']['alias'] : '');
 return 'DELETE FROM ' . $table . ($this->sqlParts['where'] !== null ? ' WHERE ' . (string) $this->sqlParts['where'] : '');
 }
 public function __toString()
 {
 return $this->getSQL();
 }
 public function createNamedParameter($value, $type = ParameterType::STRING, $placeHolder = null)
 {
 if ($placeHolder === null) {
 $this->boundCounter++;
 $placeHolder = ':dcValue' . $this->boundCounter;
 }
 $this->setParameter(substr($placeHolder, 1), $value, $type);
 return $placeHolder;
 }
 public function createPositionalParameter($value, $type = ParameterType::STRING)
 {
 $this->setParameter($this->boundCounter, $value, $type);
 $this->boundCounter++;
 return '?';
 }
 private function getSQLForJoins($fromAlias, array &$knownAliases) : string
 {
 $sql = '';
 if (isset($this->sqlParts['join'][$fromAlias])) {
 foreach ($this->sqlParts['join'][$fromAlias] as $join) {
 if (array_key_exists($join['joinAlias'], $knownAliases)) {
 throw QueryException::nonUniqueAlias((string) $join['joinAlias'], array_keys($knownAliases));
 }
 $sql .= ' ' . strtoupper($join['joinType']) . ' JOIN ' . $join['joinTable'] . ' ' . $join['joinAlias'];
 if ($join['joinCondition'] !== null) {
 $sql .= ' ON ' . $join['joinCondition'];
 }
 $knownAliases[$join['joinAlias']] = \true;
 }
 foreach ($this->sqlParts['join'][$fromAlias] as $join) {
 $sql .= $this->getSQLForJoins($join['joinAlias'], $knownAliases);
 }
 }
 return $sql;
 }
 public function __clone()
 {
 foreach ($this->sqlParts as $part => $elements) {
 if (is_array($this->sqlParts[$part])) {
 foreach ($this->sqlParts[$part] as $idx => $element) {
 if (!is_object($element)) {
 continue;
 }
 $this->sqlParts[$part][$idx] = clone $element;
 }
 } elseif (is_object($elements)) {
 $this->sqlParts[$part] = clone $elements;
 }
 }
 foreach ($this->params as $name => $param) {
 if (!is_object($param)) {
 continue;
 }
 $this->params[$name] = clone $param;
 }
 }
 public function enableResultCache(QueryCacheProfile $cacheProfile) : self
 {
 $this->resultCacheProfile = $cacheProfile;
 return $this;
 }
 public function disableResultCache() : self
 {
 $this->resultCacheProfile = null;
 return $this;
 }
}
