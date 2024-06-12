<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class ArithmeticFactor extends Node
{
 public $arithmeticPrimary;
 public $sign;
 public function __construct($arithmeticPrimary, $sign = null)
 {
 $this->arithmeticPrimary = $arithmeticPrimary;
 $this->sign = $sign;
 }
 public function isPositiveSigned()
 {
 return $this->sign === \true;
 }
 public function isNegativeSigned()
 {
 return $this->sign === \false;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkArithmeticFactor($this);
 }
}
