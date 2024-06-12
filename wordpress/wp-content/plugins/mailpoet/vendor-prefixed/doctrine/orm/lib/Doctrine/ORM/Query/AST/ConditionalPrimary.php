<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class ConditionalPrimary extends Node
{
 public $simpleConditionalExpression;
 public $conditionalExpression;
 public function isSimpleConditionalExpression()
 {
 return (bool) $this->simpleConditionalExpression;
 }
 public function isConditionalExpression()
 {
 return (bool) $this->conditionalExpression;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkConditionalPrimary($this);
 }
}
