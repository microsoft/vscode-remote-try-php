<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Persisters;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Collections\Expr\Comparison;
use MailPoetVendor\Doctrine\Common\Collections\Expr\CompositeExpression;
use MailPoetVendor\Doctrine\Common\Collections\Expr\ExpressionVisitor;
use MailPoetVendor\Doctrine\Common\Collections\Expr\Value;
class SqlValueVisitor extends ExpressionVisitor
{
 private $values = [];
 private $types = [];
 public function walkComparison(Comparison $comparison)
 {
 $value = $this->getValueFromComparison($comparison);
 $field = $comparison->getField();
 $operator = $comparison->getOperator();
 if (($operator === Comparison::EQ || $operator === Comparison::IS) && $value === null) {
 return;
 } elseif ($operator === Comparison::NEQ && $value === null) {
 return;
 }
 $this->values[] = $value;
 $this->types[] = [$field, $value, $operator];
 }
 public function walkCompositeExpression(CompositeExpression $expr)
 {
 foreach ($expr->getExpressionList() as $child) {
 $this->dispatch($child);
 }
 }
 public function walkValue(Value $value)
 {
 return;
 }
 public function getParamsAndTypes()
 {
 return [$this->values, $this->types];
 }
 protected function getValueFromComparison(Comparison $comparison)
 {
 $value = $comparison->getValue()->getValue();
 switch ($comparison->getOperator()) {
 case Comparison::CONTAINS:
 return '%' . $value . '%';
 case Comparison::STARTS_WITH:
 return $value . '%';
 case Comparison::ENDS_WITH:
 return '%' . $value;
 default:
 return $value;
 }
 }
}
