<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\AST\Functions\FunctionNode;
class LikeExpression extends Node
{
 public $not = \false;
 public $stringExpression;
 public $stringPattern;
 public $escapeChar;
 public function __construct($stringExpression, $stringPattern, $escapeChar = null, bool $not = \false)
 {
 $this->stringExpression = $stringExpression;
 $this->stringPattern = $stringPattern;
 $this->escapeChar = $escapeChar;
 $this->not = $not;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkLikeExpression($this);
 }
}
