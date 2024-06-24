<?php
namespace MailPoetVendor\Doctrine\Common\Collections\Expr;
if (!defined('ABSPATH')) exit;
use RuntimeException;
use function get_class;
abstract class ExpressionVisitor
{
 public abstract function walkComparison(Comparison $comparison);
 public abstract function walkValue(Value $value);
 public abstract function walkCompositeExpression(CompositeExpression $expr);
 public function dispatch(Expression $expr)
 {
 switch (\true) {
 case $expr instanceof Comparison:
 return $this->walkComparison($expr);
 case $expr instanceof Value:
 return $this->walkValue($expr);
 case $expr instanceof CompositeExpression:
 return $this->walkCompositeExpression($expr);
 default:
 throw new RuntimeException('Unknown Expression ' . get_class($expr));
 }
 }
}
