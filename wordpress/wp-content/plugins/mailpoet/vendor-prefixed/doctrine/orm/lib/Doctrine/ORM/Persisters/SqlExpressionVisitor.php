<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Persisters;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Collections\Expr\Comparison;
use MailPoetVendor\Doctrine\Common\Collections\Expr\CompositeExpression;
use MailPoetVendor\Doctrine\Common\Collections\Expr\ExpressionVisitor;
use MailPoetVendor\Doctrine\Common\Collections\Expr\Value;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\Persisters\Entity\BasicEntityPersister;
use RuntimeException;
use function implode;
use function in_array;
use function is_object;
class SqlExpressionVisitor extends ExpressionVisitor
{
 private $persister;
 private $classMetadata;
 public function __construct(BasicEntityPersister $persister, ClassMetadata $classMetadata)
 {
 $this->persister = $persister;
 $this->classMetadata = $classMetadata;
 }
 public function walkComparison(Comparison $comparison)
 {
 $field = $comparison->getField();
 $value = $comparison->getValue()->getValue();
 // shortcut for walkValue()
 if (isset($this->classMetadata->associationMappings[$field]) && $value !== null && !is_object($value) && !in_array($comparison->getOperator(), [Comparison::IN, Comparison::NIN], \true)) {
 throw MatchingAssociationFieldRequiresObject::fromClassAndAssociation($this->classMetadata->name, $field);
 }
 return $this->persister->getSelectConditionStatementSQL($field, $value, null, $comparison->getOperator());
 }
 public function walkCompositeExpression(CompositeExpression $expr)
 {
 $expressionList = [];
 foreach ($expr->getExpressionList() as $child) {
 $expressionList[] = $this->dispatch($child);
 }
 switch ($expr->getType()) {
 case CompositeExpression::TYPE_AND:
 return '(' . implode(' AND ', $expressionList) . ')';
 case CompositeExpression::TYPE_OR:
 return '(' . implode(' OR ', $expressionList) . ')';
 default:
 throw new RuntimeException('Unknown composite ' . $expr->getType());
 }
 }
 public function walkValue(Value $value)
 {
 return '?';
 }
}
