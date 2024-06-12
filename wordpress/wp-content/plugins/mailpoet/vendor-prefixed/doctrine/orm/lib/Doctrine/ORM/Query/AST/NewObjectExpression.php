<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class NewObjectExpression extends Node
{
 public $className;
 public $args;
 public function __construct($className, array $args)
 {
 $this->className = $className;
 $this->args = $args;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkNewObject($this);
 }
}
