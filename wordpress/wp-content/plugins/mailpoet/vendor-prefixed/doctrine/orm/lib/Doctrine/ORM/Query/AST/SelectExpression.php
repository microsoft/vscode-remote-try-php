<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class SelectExpression extends Node
{
 public $expression;
 public $fieldIdentificationVariable;
 public $hiddenAliasResultVariable;
 public function __construct($expression, $fieldIdentificationVariable, $hiddenAliasResultVariable = \false)
 {
 $this->expression = $expression;
 $this->fieldIdentificationVariable = $fieldIdentificationVariable;
 $this->hiddenAliasResultVariable = $hiddenAliasResultVariable;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkSelectExpression($this);
 }
}
