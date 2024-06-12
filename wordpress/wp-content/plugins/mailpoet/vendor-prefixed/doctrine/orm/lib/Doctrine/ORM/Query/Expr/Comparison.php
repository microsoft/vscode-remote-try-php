<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\Expr;
if (!defined('ABSPATH')) exit;
class Comparison
{
 public const EQ = '=';
 public const NEQ = '<>';
 public const LT = '<';
 public const LTE = '<=';
 public const GT = '>';
 public const GTE = '>=';
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
 return $this->leftExpr . ' ' . $this->operator . ' ' . $this->rightExpr;
 }
}
