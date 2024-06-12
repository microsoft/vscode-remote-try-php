<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\Expr;
if (!defined('ABSPATH')) exit;
use function strtoupper;
class Join
{
 public const INNER_JOIN = 'INNER';
 public const LEFT_JOIN = 'LEFT';
 public const ON = 'ON';
 public const WITH = 'WITH';
 protected $joinType;
 protected $join;
 protected $alias;
 protected $conditionType;
 protected $condition;
 protected $indexBy;
 public function __construct($joinType, $join, $alias = null, $conditionType = null, $condition = null, $indexBy = null)
 {
 $this->joinType = $joinType;
 $this->join = $join;
 $this->alias = $alias;
 $this->conditionType = $conditionType;
 $this->condition = $condition;
 $this->indexBy = $indexBy;
 }
 public function getJoinType()
 {
 return $this->joinType;
 }
 public function getJoin()
 {
 return $this->join;
 }
 public function getAlias()
 {
 return $this->alias;
 }
 public function getConditionType()
 {
 return $this->conditionType;
 }
 public function getCondition()
 {
 return $this->condition;
 }
 public function getIndexBy()
 {
 return $this->indexBy;
 }
 public function __toString()
 {
 return strtoupper($this->joinType) . ' JOIN ' . $this->join . ($this->alias ? ' ' . $this->alias : '') . ($this->indexBy ? ' INDEX BY ' . $this->indexBy : '') . ($this->condition ? ' ' . strtoupper($this->conditionType) . ' ' . $this->condition : '');
 }
}
