<?php
namespace MailPoetVendor\Doctrine\Common\Collections\Expr;
if (!defined('ABSPATH')) exit;
use RuntimeException;
class CompositeExpression implements Expression
{
 public const TYPE_AND = 'AND';
 public const TYPE_OR = 'OR';
 private $type;
 private $expressions = [];
 public function __construct($type, array $expressions)
 {
 $this->type = $type;
 foreach ($expressions as $expr) {
 if ($expr instanceof Value) {
 throw new RuntimeException('Values are not supported expressions as children of and/or expressions.');
 }
 if (!$expr instanceof Expression) {
 throw new RuntimeException('No expression given to CompositeExpression.');
 }
 $this->expressions[] = $expr;
 }
 }
 public function getExpressionList()
 {
 return $this->expressions;
 }
 public function getType()
 {
 return $this->type;
 }
 public function visit(ExpressionVisitor $visitor)
 {
 return $visitor->walkCompositeExpression($this);
 }
}
