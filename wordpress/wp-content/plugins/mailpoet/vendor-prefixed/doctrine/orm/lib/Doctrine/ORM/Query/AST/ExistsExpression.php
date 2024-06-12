<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class ExistsExpression extends Node
{
 public $not;
 public $subselect;
 public function __construct($subselect)
 {
 $this->subselect = $subselect;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkExistsExpression($this);
 }
}
