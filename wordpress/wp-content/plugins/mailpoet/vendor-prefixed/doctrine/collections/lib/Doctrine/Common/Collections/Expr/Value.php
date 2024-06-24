<?php
namespace MailPoetVendor\Doctrine\Common\Collections\Expr;
if (!defined('ABSPATH')) exit;
class Value implements Expression
{
 private $value;
 public function __construct($value)
 {
 $this->value = $value;
 }
 public function getValue()
 {
 return $this->value;
 }
 public function visit(ExpressionVisitor $visitor)
 {
 return $visitor->walkValue($this);
 }
}
