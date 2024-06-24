<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class ComparisonExpression extends Node
{
 public $leftExpression;
 public $rightExpression;
 public $operator;
 public function __construct($leftExpr, $operator, $rightExpr)
 {
 $this->leftExpression = $leftExpr;
 $this->rightExpression = $rightExpr;
 $this->operator = $operator;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkComparisonExpression($this);
 }
}
