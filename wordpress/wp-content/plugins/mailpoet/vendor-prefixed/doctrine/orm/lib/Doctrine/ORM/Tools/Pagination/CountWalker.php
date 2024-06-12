<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Tools\Pagination;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\AST\AggregateExpression;
use MailPoetVendor\Doctrine\ORM\Query\AST\PathExpression;
use MailPoetVendor\Doctrine\ORM\Query\AST\SelectExpression;
use MailPoetVendor\Doctrine\ORM\Query\AST\SelectStatement;
use MailPoetVendor\Doctrine\ORM\Query\TreeWalkerAdapter;
use RuntimeException;
use function count;
use function reset;
class CountWalker extends TreeWalkerAdapter
{
 public const HINT_DISTINCT = 'doctrine_paginator.distinct';
 public function walkSelectStatement(SelectStatement $AST)
 {
 if ($AST->havingClause) {
 throw new RuntimeException('Cannot count query that uses a HAVING clause. Use the output walkers for pagination');
 }
 $queryComponents = $this->_getQueryComponents();
 // Get the root entity and alias from the AST fromClause
 $from = $AST->fromClause->identificationVariableDeclarations;
 if (count($from) > 1) {
 throw new RuntimeException('Cannot count query which selects two FROM components, cannot make distinction');
 }
 $fromRoot = reset($from);
 $rootAlias = $fromRoot->rangeVariableDeclaration->aliasIdentificationVariable;
 $rootClass = $queryComponents[$rootAlias]['metadata'];
 $identifierFieldName = $rootClass->getSingleIdentifierFieldName();
 $pathType = PathExpression::TYPE_STATE_FIELD;
 if (isset($rootClass->associationMappings[$identifierFieldName])) {
 $pathType = PathExpression::TYPE_SINGLE_VALUED_ASSOCIATION;
 }
 $pathExpression = new PathExpression(PathExpression::TYPE_STATE_FIELD | PathExpression::TYPE_SINGLE_VALUED_ASSOCIATION, $rootAlias, $identifierFieldName);
 $pathExpression->type = $pathType;
 $distinct = $this->_getQuery()->getHint(self::HINT_DISTINCT);
 $AST->selectClause->selectExpressions = [new SelectExpression(new AggregateExpression('count', $pathExpression, $distinct), null)];
 // ORDER BY is not needed, only increases query execution through unnecessary sorting.
 $AST->orderByClause = null;
 }
}
