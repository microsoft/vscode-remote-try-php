<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\AbstractQuery;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use LogicException;
use function array_diff;
use function array_keys;
use function debug_backtrace;
use function is_a;
use function sprintf;
use const DEBUG_BACKTRACE_IGNORE_ARGS;
abstract class TreeWalkerAdapter implements TreeWalker
{
 private $query;
 private $parserResult;
 private $queryComponents;
 public function __construct($query, $parserResult, array $queryComponents)
 {
 $this->query = $query;
 $this->parserResult = $parserResult;
 $this->queryComponents = $queryComponents;
 }
 public function getQueryComponents()
 {
 return $this->queryComponents;
 }
 public function setQueryComponent($dqlAlias, array $queryComponent)
 {
 $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
 if (!isset($trace[1]['class']) || !is_a($trace[1]['class'], self::class, \true)) {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method %s will be protected in 3.0. Calling it publicly is deprecated.', __METHOD__);
 }
 $requiredKeys = ['metadata', 'parent', 'relation', 'map', 'nestingLevel', 'token'];
 if (array_diff($requiredKeys, array_keys($queryComponent))) {
 throw QueryException::invalidQueryComponent($dqlAlias);
 }
 $this->queryComponents[$dqlAlias] = $queryComponent;
 }
 protected function _getQueryComponents()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method %s is deprecated, call getQueryComponents() instead.', __METHOD__);
 return $this->queryComponents;
 }
 protected function _getQuery()
 {
 return $this->query;
 }
 protected function _getParserResult()
 {
 return $this->parserResult;
 }
 public function walkSelectStatement(AST\SelectStatement $AST)
 {
 }
 public function walkSelectClause($selectClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkFromClause($fromClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkFunction($function)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkOrderByClause($orderByClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkOrderByItem($orderByItem)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkHavingClause($havingClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkJoin($join)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkSelectExpression($selectExpression)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkQuantifiedExpression($qExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkSubselect($subselect)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkSubselectFromClause($subselectFromClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkSimpleSelectClause($simpleSelectClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkSimpleSelectExpression($simpleSelectExpression)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkAggregateExpression($aggExpression)
 {
 }
 public function walkGroupByClause($groupByClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkGroupByItem($groupByItem)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkUpdateStatement(AST\UpdateStatement $AST)
 {
 }
 public function walkDeleteStatement(AST\DeleteStatement $AST)
 {
 }
 public function walkDeleteClause(AST\DeleteClause $deleteClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkUpdateClause($updateClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkUpdateItem($updateItem)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkWhereClause($whereClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkConditionalExpression($condExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkConditionalTerm($condTerm)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkConditionalFactor($factor)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkConditionalPrimary($primary)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkExistsExpression($existsExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkCollectionMemberExpression($collMemberExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkEmptyCollectionComparisonExpression($emptyCollCompExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkNullComparisonExpression($nullCompExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkInExpression($inExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkInstanceOfExpression($instanceOfExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkLiteral($literal)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkBetweenExpression($betweenExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkLikeExpression($likeExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkStateFieldPathExpression($stateFieldPathExpression)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkComparisonExpression($compExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkInputParameter($inputParam)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkArithmeticExpression($arithmeticExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkArithmeticTerm($term)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkStringPrimary($stringPrimary)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkArithmeticFactor($factor)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkSimpleArithmeticExpression($simpleArithmeticExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkPathExpression($pathExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function walkResultVariable($resultVariable)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 }
 public function getExecutor($AST)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 return null;
 }
 protected final function getMetadataForDqlAlias(string $dqlAlias) : ClassMetadata
 {
 $metadata = $this->_getQueryComponents()[$dqlAlias]['metadata'] ?? null;
 if ($metadata === null) {
 throw new LogicException(sprintf('No metadata for DQL alias: %s', $dqlAlias));
 }
 return $metadata;
 }
}
