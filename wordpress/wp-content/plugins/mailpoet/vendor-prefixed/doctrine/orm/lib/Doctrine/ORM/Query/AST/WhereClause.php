<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class WhereClause extends Node
{
 public $conditionalExpression;
 public function __construct($conditionalExpression)
 {
 $this->conditionalExpression = $conditionalExpression;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkWhereClause($this);
 }
}
