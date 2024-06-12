<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class IndexBy extends Node
{
 public $singleValuedPathExpression = null;
 public $simpleStateFieldPathExpression = null;
 public function __construct(PathExpression $singleValuedPathExpression)
 {
 $this->singleValuedPathExpression = $this->simpleStateFieldPathExpression = $singleValuedPathExpression;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkIndexBy($this);
 }
}
