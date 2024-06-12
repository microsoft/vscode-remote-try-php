<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class ArithmeticExpression extends Node
{
 public $simpleArithmeticExpression;
 public $subselect;
 public function isSimpleArithmeticExpression()
 {
 return (bool) $this->simpleArithmeticExpression;
 }
 public function isSubselect()
 {
 return (bool) $this->subselect;
 }
 public function dispatch($walker)
 {
 return $walker->walkArithmeticExpression($this);
 }
}
