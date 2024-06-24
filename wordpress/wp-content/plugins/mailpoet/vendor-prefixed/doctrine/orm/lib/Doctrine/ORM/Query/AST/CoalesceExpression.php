<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class CoalesceExpression extends Node
{
 public $scalarExpressions = [];
 public function __construct(array $scalarExpressions)
 {
 $this->scalarExpressions = $scalarExpressions;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkCoalesceExpression($this);
 }
}
