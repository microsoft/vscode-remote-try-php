<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class SimpleSelectClause extends Node
{
 public $isDistinct = \false;
 public $simpleSelectExpression;
 public function __construct($simpleSelectExpression, $isDistinct)
 {
 $this->simpleSelectExpression = $simpleSelectExpression;
 $this->isDistinct = $isDistinct;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkSimpleSelectClause($this);
 }
}
