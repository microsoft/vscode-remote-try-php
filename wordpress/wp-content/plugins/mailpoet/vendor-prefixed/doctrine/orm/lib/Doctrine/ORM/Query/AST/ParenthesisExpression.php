<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class ParenthesisExpression extends Node
{
 public $expression;
 public function __construct(Node $expression)
 {
 $this->expression = $expression;
 }
 public function dispatch($walker)
 {
 return $walker->walkParenthesisExpression($this);
 }
}
