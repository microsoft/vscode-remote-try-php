<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class InstanceOfExpression extends Node
{
 public $not;
 public $identificationVariable;
 public $value;
 public function __construct($identVariable)
 {
 $this->identificationVariable = $identVariable;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkInstanceOfExpression($this);
 }
}
