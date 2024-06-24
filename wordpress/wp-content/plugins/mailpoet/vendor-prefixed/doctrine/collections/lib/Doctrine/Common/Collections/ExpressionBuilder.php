<?php
namespace MailPoetVendor\Doctrine\Common\Collections;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Collections\Expr\Comparison;
use MailPoetVendor\Doctrine\Common\Collections\Expr\CompositeExpression;
use MailPoetVendor\Doctrine\Common\Collections\Expr\Value;
use function func_get_args;
class ExpressionBuilder
{
 public function andX($x = null)
 {
 return new CompositeExpression(CompositeExpression::TYPE_AND, func_get_args());
 }
 public function orX($x = null)
 {
 return new CompositeExpression(CompositeExpression::TYPE_OR, func_get_args());
 }
 public function eq($field, $value)
 {
 return new Comparison($field, Comparison::EQ, new Value($value));
 }
 public function gt($field, $value)
 {
 return new Comparison($field, Comparison::GT, new Value($value));
 }
 public function lt($field, $value)
 {
 return new Comparison($field, Comparison::LT, new Value($value));
 }
 public function gte($field, $value)
 {
 return new Comparison($field, Comparison::GTE, new Value($value));
 }
 public function lte($field, $value)
 {
 return new Comparison($field, Comparison::LTE, new Value($value));
 }
 public function neq($field, $value)
 {
 return new Comparison($field, Comparison::NEQ, new Value($value));
 }
 public function isNull($field)
 {
 return new Comparison($field, Comparison::EQ, new Value(null));
 }
 public function in($field, array $values)
 {
 return new Comparison($field, Comparison::IN, new Value($values));
 }
 public function notIn($field, array $values)
 {
 return new Comparison($field, Comparison::NIN, new Value($values));
 }
 public function contains($field, $value)
 {
 return new Comparison($field, Comparison::CONTAINS, new Value($value));
 }
 public function memberOf($field, $value)
 {
 return new Comparison($field, Comparison::MEMBER_OF, new Value($value));
 }
 public function startsWith($field, $value)
 {
 return new Comparison($field, Comparison::STARTS_WITH, new Value($value));
 }
 public function endsWith($field, $value)
 {
 return new Comparison($field, Comparison::ENDS_WITH, new Value($value));
 }
}
