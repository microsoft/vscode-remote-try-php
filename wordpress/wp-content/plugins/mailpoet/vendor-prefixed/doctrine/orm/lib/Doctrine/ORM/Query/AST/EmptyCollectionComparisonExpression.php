<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class EmptyCollectionComparisonExpression extends Node
{
 public $expression;
 public $not;
 public function __construct($expression, bool $not = \false)
 {
 $this->expression = $expression;
 $this->not = $not;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkEmptyCollectionComparisonExpression($this);
 }
}
