<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use function array_diff;
use function array_keys;
class TreeWalkerChain implements TreeWalker
{
 private $_walkers;
 private $_queryComponents;
 public function getQueryComponents()
 {
 return $this->_queryComponents;
 }
 public function setQueryComponent($dqlAlias, array $queryComponent)
 {
 $requiredKeys = ['metadata', 'parent', 'relation', 'map', 'nestingLevel', 'token'];
 if (array_diff($requiredKeys, array_keys($queryComponent))) {
 throw QueryException::invalidQueryComponent($dqlAlias);
 }
 $this->_queryComponents[$dqlAlias] = $queryComponent;
 }
 public function __construct($query, $parserResult, array $queryComponents)
 {
 $this->_queryComponents = $queryComponents;
 $this->_walkers = new TreeWalkerChainIterator($this, $query, $parserResult);
 }
 public function addTreeWalker($walkerClass)
 {
 $this->_walkers[] = $walkerClass;
 }
 public function walkSelectStatement(AST\SelectStatement $AST)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkSelectStatement($AST);
 $this->_queryComponents = $walker->getQueryComponents();
 }
 }
 public function walkSelectClause($selectClause)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkSelectClause($selectClause);
 }
 }
 public function walkFromClause($fromClause)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkFromClause($fromClause);
 }
 }
 public function walkFunction($function)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkFunction($function);
 }
 }
 public function walkOrderByClause($orderByClause)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkOrderByClause($orderByClause);
 }
 }
 public function walkOrderByItem($orderByItem)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkOrderByItem($orderByItem);
 }
 }
 public function walkHavingClause($havingClause)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkHavingClause($havingClause);
 }
 }
 public function walkJoin($join)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkJoin($join);
 }
 }
 public function walkSelectExpression($selectExpression)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkSelectExpression($selectExpression);
 }
 }
 public function walkQuantifiedExpression($qExpr)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkQuantifiedExpression($qExpr);
 }
 }
 public function walkSubselect($subselect)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkSubselect($subselect);
 }
 }
 public function walkSubselectFromClause($subselectFromClause)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkSubselectFromClause($subselectFromClause);
 }
 }
 public function walkSimpleSelectClause($simpleSelectClause)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkSimpleSelectClause($simpleSelectClause);
 }
 }
 public function walkSimpleSelectExpression($simpleSelectExpression)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkSimpleSelectExpression($simpleSelectExpression);
 }
 }
 public function walkAggregateExpression($aggExpression)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkAggregateExpression($aggExpression);
 }
 }
 public function walkGroupByClause($groupByClause)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkGroupByClause($groupByClause);
 }
 }
 public function walkGroupByItem($groupByItem)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkGroupByItem($groupByItem);
 }
 }
 public function walkUpdateStatement(AST\UpdateStatement $AST)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkUpdateStatement($AST);
 }
 }
 public function walkDeleteStatement(AST\DeleteStatement $AST)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkDeleteStatement($AST);
 }
 }
 public function walkDeleteClause(AST\DeleteClause $deleteClause)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkDeleteClause($deleteClause);
 }
 }
 public function walkUpdateClause($updateClause)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkUpdateClause($updateClause);
 }
 }
 public function walkUpdateItem($updateItem)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkUpdateItem($updateItem);
 }
 }
 public function walkWhereClause($whereClause)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkWhereClause($whereClause);
 }
 }
 public function walkConditionalExpression($condExpr)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkConditionalExpression($condExpr);
 }
 }
 public function walkConditionalTerm($condTerm)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkConditionalTerm($condTerm);
 }
 }
 public function walkConditionalFactor($factor)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkConditionalFactor($factor);
 }
 }
 public function walkConditionalPrimary($condPrimary)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkConditionalPrimary($condPrimary);
 }
 }
 public function walkExistsExpression($existsExpr)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkExistsExpression($existsExpr);
 }
 }
 public function walkCollectionMemberExpression($collMemberExpr)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkCollectionMemberExpression($collMemberExpr);
 }
 }
 public function walkEmptyCollectionComparisonExpression($emptyCollCompExpr)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkEmptyCollectionComparisonExpression($emptyCollCompExpr);
 }
 }
 public function walkNullComparisonExpression($nullCompExpr)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkNullComparisonExpression($nullCompExpr);
 }
 }
 public function walkInExpression($inExpr)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkInExpression($inExpr);
 }
 }
 public function walkInstanceOfExpression($instanceOfExpr)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkInstanceOfExpression($instanceOfExpr);
 }
 }
 public function walkLiteral($literal)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkLiteral($literal);
 }
 }
 public function walkBetweenExpression($betweenExpr)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkBetweenExpression($betweenExpr);
 }
 }
 public function walkLikeExpression($likeExpr)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkLikeExpression($likeExpr);
 }
 }
 public function walkStateFieldPathExpression($stateFieldPathExpression)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkStateFieldPathExpression($stateFieldPathExpression);
 }
 }
 public function walkComparisonExpression($compExpr)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkComparisonExpression($compExpr);
 }
 }
 public function walkInputParameter($inputParam)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkInputParameter($inputParam);
 }
 }
 public function walkArithmeticExpression($arithmeticExpr)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkArithmeticExpression($arithmeticExpr);
 }
 }
 public function walkArithmeticTerm($term)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkArithmeticTerm($term);
 }
 }
 public function walkStringPrimary($stringPrimary)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkStringPrimary($stringPrimary);
 }
 }
 public function walkArithmeticFactor($factor)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkArithmeticFactor($factor);
 }
 }
 public function walkSimpleArithmeticExpression($simpleArithmeticExpr)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkSimpleArithmeticExpression($simpleArithmeticExpr);
 }
 }
 public function walkPathExpression($pathExpr)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkPathExpression($pathExpr);
 }
 }
 public function walkResultVariable($resultVariable)
 {
 foreach ($this->_walkers as $walker) {
 $walker->walkResultVariable($resultVariable);
 }
 }
 public function getExecutor($AST)
 {
 }
}
