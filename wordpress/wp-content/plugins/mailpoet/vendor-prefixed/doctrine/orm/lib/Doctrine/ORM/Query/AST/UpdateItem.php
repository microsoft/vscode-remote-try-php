<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class UpdateItem extends Node
{
 public $pathExpression;
 public $newValue;
 public function __construct($pathExpression, $newValue)
 {
 $this->pathExpression = $pathExpression;
 $this->newValue = $newValue;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkUpdateItem($this);
 }
}
