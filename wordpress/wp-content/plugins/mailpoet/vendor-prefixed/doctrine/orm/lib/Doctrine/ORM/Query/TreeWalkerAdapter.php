<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\AbstractQuery;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use function array_diff;
use function array_keys;
abstract class TreeWalkerAdapter implements TreeWalker
{
 private $_query;
 private $_parserResult;
 private $_queryComponents;
 public function __construct($query, $parserResult, array $queryComponents)
 {
 $this->_query = $query;
 $this->_parserResult = $parserResult;
 $this->_queryComponents = $queryComponents;
 }
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
 protected function _getQueryComponents()
 {
 return $this->_queryComponents;
 }
 protected function _getQuery()
 {
 return $this->_query;
 }
 protected function _getParserResult()
 {
 return $this->_parserResult;
 }
 public function walkSelectStatement(AST\SelectStatement $AST)
 {
 }
 public function walkSelectClause($selectClause)
 {
 }
 public function walkFromClause($fromClause)
 {
 }
 public function walkFunction($function)
 {
 }
 public function walkOrderByClause($orderByClause)
 {
 }
 public function walkOrderByItem($orderByItem)
 {
 }
 public function walkHavingClause($havingClause)
 {
 }
 public function walkJoin($join)
 {
 }
 public function walkSelectExpression($selectExpression)
 {
 }
 public function walkQuantifiedExpression($qExpr)
 {
 }
 public function walkSubselect($subselect)
 {
 }
 public function walkSubselectFromClause($subselectFromClause)
 {
 }
 public function walkSimpleSelectClause($simpleSelectClause)
 {
 }
 public function walkSimpleSelectExpression($simpleSelectExpression)
 {
 }
 public function walkAggregateExpression($aggExpression)
 {
 }
 public function walkGroupByClause($groupByClause)
 {
 }
 public function walkGroupByItem($groupByItem)
 {
 }
 public function walkUpdateStatement(AST\UpdateStatement $AST)
 {
 }
 public function walkDeleteStatement(AST\DeleteStatement $AST)
 {
 }
 public function walkDeleteClause(AST\DeleteClause $deleteClause)
 {
 }
 public function walkUpdateClause($updateClause)
 {
 }
 public function walkUpdateItem($updateItem)
 {
 }
 public function walkWhereClause($whereClause)
 {
 }
 public function walkConditionalExpression($condExpr)
 {
 }
 public function walkConditionalTerm($condTerm)
 {
 }
 public function walkConditionalFactor($factor)
 {
 }
 public function walkConditionalPrimary($primary)
 {
 }
 public function walkExistsExpression($existsExpr)
 {
 }
 public function walkCollectionMemberExpression($collMemberExpr)
 {
 }
 public function walkEmptyCollectionComparisonExpression($emptyCollCompExpr)
 {
 }
 public function walkNullComparisonExpression($nullCompExpr)
 {
 }
 public function walkInExpression($inExpr)
 {
 }
 public function walkInstanceOfExpression($instanceOfExpr)
 {
 }
 public function walkLiteral($literal)
 {
 }
 public function walkBetweenExpression($betweenExpr)
 {
 }
 public function walkLikeExpression($likeExpr)
 {
 }
 public function walkStateFieldPathExpression($stateFieldPathExpression)
 {
 }
 public function walkComparisonExpression($compExpr)
 {
 }
 public function walkInputParameter($inputParam)
 {
 }
 public function walkArithmeticExpression($arithmeticExpr)
 {
 }
 public function walkArithmeticTerm($term)
 {
 }
 public function walkStringPrimary($stringPrimary)
 {
 }
 public function walkArithmeticFactor($factor)
 {
 }
 public function walkSimpleArithmeticExpression($simpleArithmeticExpr)
 {
 }
 public function walkPathExpression($pathExpr)
 {
 }
 public function walkResultVariable($resultVariable)
 {
 }
 public function getExecutor($AST)
 {
 }
}
