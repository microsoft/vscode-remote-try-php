<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class ArithmeticTerm extends Node
{
 public $arithmeticFactors;
 public function __construct(array $arithmeticFactors)
 {
 $this->arithmeticFactors = $arithmeticFactors;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkArithmeticTerm($this);
 }
}
