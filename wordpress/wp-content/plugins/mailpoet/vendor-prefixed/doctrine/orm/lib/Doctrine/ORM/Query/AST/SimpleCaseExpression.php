<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class SimpleCaseExpression extends Node
{
 public $caseOperand = null;
 public $simpleWhenClauses = [];
 public $elseScalarExpression = null;
 public function __construct($caseOperand, array $simpleWhenClauses, $elseScalarExpression)
 {
 $this->caseOperand = $caseOperand;
 $this->simpleWhenClauses = $simpleWhenClauses;
 $this->elseScalarExpression = $elseScalarExpression;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkSimpleCaseExpression($this);
 }
}
