<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\AbstractQuery;
use Generator;
use function array_diff;
use function array_keys;
class TreeWalkerChain implements TreeWalker
{
 private $walkers = [];
 private $query;
 private $parserResult;
 private $queryComponents;
 public function getQueryComponents()
 {
 return $this->queryComponents;
 }
 public function setQueryComponent($dqlAlias, array $queryComponent)
 {
 $requiredKeys = ['metadata', 'parent', 'relation', 'map', 'nestingLevel', 'token'];
 if (array_diff($requiredKeys, array_keys($queryComponent))) {
 throw QueryException::invalidQueryComponent($dqlAlias);
 }
 $this->queryComponents[$dqlAlias] = $queryComponent;
 }
 public function __construct($query, $parserResult, array $queryComponents)
 {
 $this->query = $query;
 $this->parserResult = $parserResult;
 $this->queryComponents = $queryComponents;
 }
 public function addTreeWalker($walkerClass)
 {
 $this->walkers[] = $walkerClass;
 }
 public function walkSelectStatement(AST\SelectStatement $AST)
 {
 foreach ($this->getWalkers() as $walker) {
 $walker->walkSelectStatement($AST);
 $this->queryComponents = $walker->getQueryComponents();
 }
 }
 public function walkSelectClause($selectClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkSelectClause($selectClause);
 }
 }
 public function walkFromClause($fromClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkFromClause($fromClause);
 }
 }
 public function walkFunction($function)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkFunction($function);
 }
 }
 public function walkOrderByClause($orderByClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkOrderByClause($orderByClause);
 }
 }
 public function walkOrderByItem($orderByItem)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkOrderByItem($orderByItem);
 }
 }
 public function walkHavingClause($havingClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkHavingClause($havingClause);
 }
 }
 public function walkJoin($join)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkJoin($join);
 }
 }
 public function walkSelectExpression($selectExpression)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkSelectExpression($selectExpression);
 }
 }
 public function walkQuantifiedExpression($qExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkQuantifiedExpression($qExpr);
 }
 }
 public function walkSubselect($subselect)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkSubselect($subselect);
 }
 }
 public function walkSubselectFromClause($subselectFromClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkSubselectFromClause($subselectFromClause);
 }
 }
 public function walkSimpleSelectClause($simpleSelectClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkSimpleSelectClause($simpleSelectClause);
 }
 }
 public function walkSimpleSelectExpression($simpleSelectExpression)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkSimpleSelectExpression($simpleSelectExpression);
 }
 }
 public function walkAggregateExpression($aggExpression)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkAggregateExpression($aggExpression);
 }
 }
 public function walkGroupByClause($groupByClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkGroupByClause($groupByClause);
 }
 }
 public function walkGroupByItem($groupByItem)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkGroupByItem($groupByItem);
 }
 }
 public function walkUpdateStatement(AST\UpdateStatement $AST)
 {
 foreach ($this->getWalkers() as $walker) {
 $walker->walkUpdateStatement($AST);
 }
 }
 public function walkDeleteStatement(AST\DeleteStatement $AST)
 {
 foreach ($this->getWalkers() as $walker) {
 $walker->walkDeleteStatement($AST);
 }
 }
 public function walkDeleteClause(AST\DeleteClause $deleteClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkDeleteClause($deleteClause);
 }
 }
 public function walkUpdateClause($updateClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkUpdateClause($updateClause);
 }
 }
 public function walkUpdateItem($updateItem)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkUpdateItem($updateItem);
 }
 }
 public function walkWhereClause($whereClause)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkWhereClause($whereClause);
 }
 }
 public function walkConditionalExpression($condExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkConditionalExpression($condExpr);
 }
 }
 public function walkConditionalTerm($condTerm)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkConditionalTerm($condTerm);
 }
 }
 public function walkConditionalFactor($factor)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkConditionalFactor($factor);
 }
 }
 public function walkConditionalPrimary($condPrimary)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkConditionalPrimary($condPrimary);
 }
 }
 public function walkExistsExpression($existsExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkExistsExpression($existsExpr);
 }
 }
 public function walkCollectionMemberExpression($collMemberExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkCollectionMemberExpression($collMemberExpr);
 }
 }
 public function walkEmptyCollectionComparisonExpression($emptyCollCompExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkEmptyCollectionComparisonExpression($emptyCollCompExpr);
 }
 }
 public function walkNullComparisonExpression($nullCompExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkNullComparisonExpression($nullCompExpr);
 }
 }
 public function walkInExpression($inExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkInExpression($inExpr);
 }
 }
 public function walkInstanceOfExpression($instanceOfExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkInstanceOfExpression($instanceOfExpr);
 }
 }
 public function walkLiteral($literal)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkLiteral($literal);
 }
 }
 public function walkBetweenExpression($betweenExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkBetweenExpression($betweenExpr);
 }
 }
 public function walkLikeExpression($likeExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkLikeExpression($likeExpr);
 }
 }
 public function walkStateFieldPathExpression($stateFieldPathExpression)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkStateFieldPathExpression($stateFieldPathExpression);
 }
 }
 public function walkComparisonExpression($compExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkComparisonExpression($compExpr);
 }
 }
 public function walkInputParameter($inputParam)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkInputParameter($inputParam);
 }
 }
 public function walkArithmeticExpression($arithmeticExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkArithmeticExpression($arithmeticExpr);
 }
 }
 public function walkArithmeticTerm($term)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkArithmeticTerm($term);
 }
 }
 public function walkStringPrimary($stringPrimary)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkStringPrimary($stringPrimary);
 }
 }
 public function walkArithmeticFactor($factor)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkArithmeticFactor($factor);
 }
 }
 public function walkSimpleArithmeticExpression($simpleArithmeticExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkSimpleArithmeticExpression($simpleArithmeticExpr);
 }
 }
 public function walkPathExpression($pathExpr)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkPathExpression($pathExpr);
 }
 }
 public function walkResultVariable($resultVariable)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 foreach ($this->getWalkers() as $walker) {
 $walker->walkResultVariable($resultVariable);
 }
 }
 public function getExecutor($AST)
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/pull/9551', 'Method "%s" is deprecated and will be removed in ORM 3.0 without replacement.', __METHOD__);
 return null;
 }
 private function getWalkers() : Generator
 {
 foreach ($this->walkers as $walkerClass) {
 (yield new $walkerClass($this->query, $this->parserResult, $this->queryComponents));
 }
 }
}
