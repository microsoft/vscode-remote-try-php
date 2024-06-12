<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class WhenClause extends Node
{
 public $caseConditionExpression = null;
 public $thenScalarExpression = null;
 public function __construct($caseConditionExpression, $thenScalarExpression)
 {
 $this->caseConditionExpression = $caseConditionExpression;
 $this->thenScalarExpression = $thenScalarExpression;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkWhenClauseExpression($this);
 }
}
