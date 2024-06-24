<?php
namespace MailPoetVendor\Doctrine\DBAL\Query;
if (!defined('ABSPATH')) exit;
final class SelectQuery
{
 private bool $distinct;
 private array $columns;
 private array $from;
 private ?string $where;
 private array $groupBy;
 private ?string $having;
 private array $orderBy;
 private Limit $limit;
 private ?ForUpdate $forUpdate;
 public function __construct(bool $distinct, array $columns, array $from, ?string $where, array $groupBy, ?string $having, array $orderBy, Limit $limit, ?ForUpdate $forUpdate)
 {
 $this->distinct = $distinct;
 $this->columns = $columns;
 $this->from = $from;
 $this->where = $where;
 $this->groupBy = $groupBy;
 $this->having = $having;
 $this->orderBy = $orderBy;
 $this->limit = $limit;
 $this->forUpdate = $forUpdate;
 }
 public function isDistinct() : bool
 {
 return $this->distinct;
 }
 public function getColumns() : array
 {
 return $this->columns;
 }
 public function getFrom() : array
 {
 return $this->from;
 }
 public function getWhere() : ?string
 {
 return $this->where;
 }
 public function getGroupBy() : array
 {
 return $this->groupBy;
 }
 public function getHaving() : ?string
 {
 return $this->having;
 }
 public function getOrderBy() : array
 {
 return $this->orderBy;
 }
 public function getLimit() : Limit
 {
 return $this->limit;
 }
 public function getForUpdate() : ?ForUpdate
 {
 return $this->forUpdate;
 }
}
