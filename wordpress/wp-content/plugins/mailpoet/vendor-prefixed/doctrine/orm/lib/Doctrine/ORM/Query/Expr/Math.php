<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\Expr;
if (!defined('ABSPATH')) exit;
class Math
{
 protected $leftExpr;
 protected $operator;
 protected $rightExpr;
 public function __construct($leftExpr, $operator, $rightExpr)
 {
 $this->leftExpr = $leftExpr;
 $this->operator = $operator;
 $this->rightExpr = $rightExpr;
 }
 public function getLeftExpr()
 {
 return $this->leftExpr;
 }
 public function getOperator()
 {
 return $this->operator;
 }
 public function getRightExpr()
 {
 return $this->rightExpr;
 }
 public function __toString()
 {
 // Adjusting Left Expression
 $leftExpr = (string) $this->leftExpr;
 if ($this->leftExpr instanceof Math) {
 $leftExpr = '(' . $leftExpr . ')';
 }
 // Adjusting Right Expression
 $rightExpr = (string) $this->rightExpr;
 if ($this->rightExpr instanceof Math) {
 $rightExpr = '(' . $rightExpr . ')';
 }
 return $leftExpr . ' ' . $this->operator . ' ' . $rightExpr;
 }
}
