<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class JoinClassPathExpression extends Node
{
 public $abstractSchemaName;
 public $aliasIdentificationVariable;
 public function __construct($abstractSchemaName, $aliasIdentificationVar)
 {
 $this->abstractSchemaName = $abstractSchemaName;
 $this->aliasIdentificationVariable = $aliasIdentificationVar;
 }
 public function dispatch($walker)
 {
 return $walker->walkJoinPathExpression($this);
 }
}
