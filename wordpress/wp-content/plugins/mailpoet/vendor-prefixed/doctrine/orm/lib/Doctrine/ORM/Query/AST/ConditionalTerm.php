<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class ConditionalTerm extends Node
{
 public $conditionalFactors = [];
 public function __construct(array $conditionalFactors)
 {
 $this->conditionalFactors = $conditionalFactors;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkConditionalTerm($this);
 }
}
