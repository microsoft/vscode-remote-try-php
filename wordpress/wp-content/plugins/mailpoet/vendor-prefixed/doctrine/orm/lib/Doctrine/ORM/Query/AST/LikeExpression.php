<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class LikeExpression extends Node
{
 public $not;
 public $stringExpression;
 public $stringPattern;
 public $escapeChar;
 public function __construct($stringExpression, $stringPattern, $escapeChar = null)
 {
 $this->stringExpression = $stringExpression;
 $this->stringPattern = $stringPattern;
 $this->escapeChar = $escapeChar;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkLikeExpression($this);
 }
}
