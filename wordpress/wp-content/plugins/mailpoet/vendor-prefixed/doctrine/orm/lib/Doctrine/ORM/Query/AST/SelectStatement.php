<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class SelectStatement extends Node
{
 public $selectClause;
 public $fromClause;
 public $whereClause;
 public $groupByClause;
 public $havingClause;
 public $orderByClause;
 public function __construct($selectClause, $fromClause)
 {
 $this->selectClause = $selectClause;
 $this->fromClause = $fromClause;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkSelectStatement($this);
 }
}
