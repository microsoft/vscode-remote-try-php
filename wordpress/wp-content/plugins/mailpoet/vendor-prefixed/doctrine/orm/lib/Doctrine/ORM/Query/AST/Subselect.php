<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class Subselect extends Node
{
 public $simpleSelectClause;
 public $subselectFromClause;
 public $whereClause;
 public $groupByClause;
 public $havingClause;
 public $orderByClause;
 public function __construct($simpleSelectClause, $subselectFromClause)
 {
 $this->simpleSelectClause = $simpleSelectClause;
 $this->subselectFromClause = $subselectFromClause;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkSubselect($this);
 }
}
