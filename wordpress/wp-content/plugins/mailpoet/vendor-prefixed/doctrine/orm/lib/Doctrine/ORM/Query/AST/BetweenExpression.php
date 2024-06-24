<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class BetweenExpression extends Node
{
 public $expression;
 public $leftBetweenExpression;
 public $rightBetweenExpression;
 public $not;
 public function __construct($expr, $leftExpr, $rightExpr, bool $not = \false)
 {
 $this->expression = $expr;
 $this->leftBetweenExpression = $leftExpr;
 $this->rightBetweenExpression = $rightExpr;
 $this->not = $not;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkBetweenExpression($this);
 }
}
