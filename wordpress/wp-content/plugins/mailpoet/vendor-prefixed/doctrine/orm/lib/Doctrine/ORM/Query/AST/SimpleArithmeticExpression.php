<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class SimpleArithmeticExpression extends Node
{
 public $arithmeticTerms = [];
 public function __construct(array $arithmeticTerms)
 {
 $this->arithmeticTerms = $arithmeticTerms;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkSimpleArithmeticExpression($this);
 }
}
