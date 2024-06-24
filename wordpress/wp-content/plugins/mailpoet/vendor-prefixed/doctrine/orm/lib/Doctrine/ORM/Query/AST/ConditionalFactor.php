<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class ConditionalFactor extends Node
{
 public $not = \false;
 public $conditionalPrimary;
 public function __construct($conditionalPrimary, bool $not = \false)
 {
 $this->conditionalPrimary = $conditionalPrimary;
 $this->not = $not;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkConditionalFactor($this);
 }
}
