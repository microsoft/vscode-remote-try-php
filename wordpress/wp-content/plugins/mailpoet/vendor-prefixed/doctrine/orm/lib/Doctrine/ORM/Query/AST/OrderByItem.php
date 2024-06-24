<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
use function strtoupper;
class OrderByItem extends Node
{
 public $expression;
 public $type;
 public function __construct($expression)
 {
 $this->expression = $expression;
 }
 public function isAsc()
 {
 return strtoupper($this->type) === 'ASC';
 }
 public function isDesc()
 {
 return strtoupper($this->type) === 'DESC';
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkOrderByItem($this);
 }
}
