<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class OrderByClause extends Node
{
 public $orderByItems = [];
 public function __construct(array $orderByItems)
 {
 $this->orderByItems = $orderByItems;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkOrderByClause($this);
 }
}
