<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class AggregateExpression extends Node
{
 public $functionName;
 public $pathExpression;
 public $isDistinct = \false;
 public function __construct($functionName, $pathExpression, $isDistinct)
 {
 $this->functionName = $functionName;
 $this->pathExpression = $pathExpression;
 $this->isDistinct = $isDistinct;
 }
 public function dispatch($walker)
 {
 return $walker->walkAggregateExpression($this);
 }
}
