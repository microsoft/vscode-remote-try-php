<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class RangeVariableDeclaration extends Node
{
 public $abstractSchemaName;
 public $aliasIdentificationVariable;
 public $isRoot;
 public function __construct($abstractSchemaName, $aliasIdentificationVar, $isRoot = \true)
 {
 $this->abstractSchemaName = $abstractSchemaName;
 $this->aliasIdentificationVariable = $aliasIdentificationVar;
 $this->isRoot = $isRoot;
 }
 public function dispatch($walker)
 {
 return $walker->walkRangeVariableDeclaration($this);
 }
}
