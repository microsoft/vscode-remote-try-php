<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class ConditionalFactor extends Node
{
 public $not = \false;
 public $conditionalPrimary;
 public function __construct($conditionalPrimary)
 {
 $this->conditionalPrimary = $conditionalPrimary;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkConditionalFactor($this);
 }
}
