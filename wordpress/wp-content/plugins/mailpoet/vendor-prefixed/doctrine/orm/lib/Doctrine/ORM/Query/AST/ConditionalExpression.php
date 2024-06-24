<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class ConditionalExpression extends Node
{
 public $conditionalTerms = [];
 public function __construct(array $conditionalTerms)
 {
 $this->conditionalTerms = $conditionalTerms;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkConditionalExpression($this);
 }
}
