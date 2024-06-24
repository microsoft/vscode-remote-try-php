<?php
namespace MailPoetVendor\Doctrine\Common\Collections\Expr;
if (!defined('ABSPATH')) exit;
class Comparison implements Expression
{
 public const EQ = '=';
 public const NEQ = '<>';
 public const LT = '<';
 public const LTE = '<=';
 public const GT = '>';
 public const GTE = '>=';
 public const IS = '=';
 // no difference with EQ
 public const IN = 'IN';
 public const NIN = 'NIN';
 public const CONTAINS = 'CONTAINS';
 public const MEMBER_OF = 'MEMBER_OF';
 public const STARTS_WITH = 'STARTS_WITH';
 public const ENDS_WITH = 'ENDS_WITH';
 private $field;
 private $op;
 private $value;
 public function __construct($field, $operator, $value)
 {
 if (!$value instanceof Value) {
 $value = new Value($value);
 }
 $this->field = $field;
 $this->op = $operator;
 $this->value = $value;
 }
 public function getField()
 {
 return $this->field;
 }
 public function getValue()
 {
 return $this->value;
 }
 public function getOperator()
 {
 return $this->op;
 }
 public function visit(ExpressionVisitor $visitor)
 {
 return $visitor->walkComparison($this);
 }
}
