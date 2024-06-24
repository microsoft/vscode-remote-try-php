<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Collections\ArrayCollection;
use MailPoetVendor\Doctrine\Common\Collections\Expr\Comparison;
use MailPoetVendor\Doctrine\Common\Collections\Expr\CompositeExpression;
use MailPoetVendor\Doctrine\Common\Collections\Expr\ExpressionVisitor;
use MailPoetVendor\Doctrine\Common\Collections\Expr\Value;
use RuntimeException;
use function count;
use function str_replace;
use function str_starts_with;
class QueryExpressionVisitor extends ExpressionVisitor
{
 private static $operatorMap = [Comparison::GT => Expr\Comparison::GT, Comparison::GTE => Expr\Comparison::GTE, Comparison::LT => Expr\Comparison::LT, Comparison::LTE => Expr\Comparison::LTE];
 private $queryAliases;
 private $expr;
 private $parameters = [];
 public function __construct($queryAliases)
 {
 $this->queryAliases = $queryAliases;
 $this->expr = new Expr();
 }
 public function getParameters()
 {
 return new ArrayCollection($this->parameters);
 }
 public function clearParameters()
 {
 $this->parameters = [];
 }
 private static function convertComparisonOperator($criteriaOperator)
 {
 return self::$operatorMap[$criteriaOperator] ?? null;
 }
 public function walkCompositeExpression(CompositeExpression $expr)
 {
 $expressionList = [];
 foreach ($expr->getExpressionList() as $child) {
 $expressionList[] = $this->dispatch($child);
 }
 switch ($expr->getType()) {
 case CompositeExpression::TYPE_AND:
 return new Expr\Andx($expressionList);
 case CompositeExpression::TYPE_OR:
 return new Expr\Orx($expressionList);
 default:
 throw new RuntimeException('Unknown composite ' . $expr->getType());
 }
 }
 public function walkComparison(Comparison $comparison)
 {
 if (!isset($this->queryAliases[0])) {
 throw new QueryException('No aliases are set before invoking walkComparison().');
 }
 $field = $this->queryAliases[0] . '.' . $comparison->getField();
 foreach ($this->queryAliases as $alias) {
 if (str_starts_with($comparison->getField() . '.', $alias . '.')) {
 $field = $comparison->getField();
 break;
 }
 }
 $parameterName = str_replace('.', '_', $comparison->getField());
 foreach ($this->parameters as $parameter) {
 if ($parameter->getName() === $parameterName) {
 $parameterName .= '_' . count($this->parameters);
 break;
 }
 }
 $parameter = new Parameter($parameterName, $this->walkValue($comparison->getValue()));
 $placeholder = ':' . $parameterName;
 switch ($comparison->getOperator()) {
 case Comparison::IN:
 $this->parameters[] = $parameter;
 return $this->expr->in($field, $placeholder);
 case Comparison::NIN:
 $this->parameters[] = $parameter;
 return $this->expr->notIn($field, $placeholder);
 case Comparison::EQ:
 case Comparison::IS:
 if ($this->walkValue($comparison->getValue()) === null) {
 return $this->expr->isNull($field);
 }
 $this->parameters[] = $parameter;
 return $this->expr->eq($field, $placeholder);
 case Comparison::NEQ:
 if ($this->walkValue($comparison->getValue()) === null) {
 return $this->expr->isNotNull($field);
 }
 $this->parameters[] = $parameter;
 return $this->expr->neq($field, $placeholder);
 case Comparison::CONTAINS:
 $parameter->setValue('%' . $parameter->getValue() . '%', $parameter->getType());
 $this->parameters[] = $parameter;
 return $this->expr->like($field, $placeholder);
 case Comparison::MEMBER_OF:
 return $this->expr->isMemberOf($comparison->getField(), $comparison->getValue()->getValue());
 case Comparison::STARTS_WITH:
 $parameter->setValue($parameter->getValue() . '%', $parameter->getType());
 $this->parameters[] = $parameter;
 return $this->expr->like($field, $placeholder);
 case Comparison::ENDS_WITH:
 $parameter->setValue('%' . $parameter->getValue(), $parameter->getType());
 $this->parameters[] = $parameter;
 return $this->expr->like($field, $placeholder);
 default:
 $operator = self::convertComparisonOperator($comparison->getOperator());
 if ($operator) {
 $this->parameters[] = $parameter;
 return new Expr\Comparison($field, $operator, $placeholder);
 }
 throw new RuntimeException('Unknown comparison operator: ' . $comparison->getOperator());
 }
 }
 public function walkValue(Value $value)
 {
 return $value->getValue();
 }
}
