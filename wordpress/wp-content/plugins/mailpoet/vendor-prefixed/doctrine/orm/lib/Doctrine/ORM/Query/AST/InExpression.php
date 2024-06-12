<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class InExpression extends Node
{
 public $not;
 public $expression;
 public $literals = [];
 public $subselect;
 public function __construct($expression)
 {
 $this->expression = $expression;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkInExpression($this);
 }
}
