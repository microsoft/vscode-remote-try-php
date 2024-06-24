<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class SimpleSelectExpression extends Node
{
 public $expression;
 public $fieldIdentificationVariable;
 public function __construct($expression)
 {
 $this->expression = $expression;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkSimpleSelectExpression($this);
 }
}
