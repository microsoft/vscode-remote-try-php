<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class GroupByClause extends Node
{
 public $groupByItems = [];
 public function __construct(array $groupByItems)
 {
 $this->groupByItems = $groupByItems;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkGroupByClause($this);
 }
}
