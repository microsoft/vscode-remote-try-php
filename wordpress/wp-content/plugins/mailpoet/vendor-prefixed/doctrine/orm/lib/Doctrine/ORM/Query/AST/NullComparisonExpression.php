<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class NullComparisonExpression extends Node
{
 public $not;
 public $expression;
 public function __construct($expression)
 {
 $this->expression = $expression;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkNullComparisonExpression($this);
 }
}
