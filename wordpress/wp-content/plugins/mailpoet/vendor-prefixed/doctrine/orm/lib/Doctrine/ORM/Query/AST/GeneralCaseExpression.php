<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class GeneralCaseExpression extends Node
{
 public $whenClauses = [];
 public $elseScalarExpression = null;
 public function __construct(array $whenClauses, $elseScalarExpression)
 {
 $this->whenClauses = $whenClauses;
 $this->elseScalarExpression = $elseScalarExpression;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkGeneralCaseExpression($this);
 }
}
