<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class SimpleWhenClause extends Node
{
 public $caseScalarExpression = null;
 public $thenScalarExpression = null;
 public function __construct($caseScalarExpression, $thenScalarExpression)
 {
 $this->caseScalarExpression = $caseScalarExpression;
 $this->thenScalarExpression = $thenScalarExpression;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkWhenClauseExpression($this);
 }
}
