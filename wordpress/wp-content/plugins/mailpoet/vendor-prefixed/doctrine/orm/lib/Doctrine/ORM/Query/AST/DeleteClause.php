<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class DeleteClause extends Node
{
 public $abstractSchemaName;
 public $aliasIdentificationVariable;
 public function __construct($abstractSchemaName)
 {
 $this->abstractSchemaName = $abstractSchemaName;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkDeleteClause($this);
 }
}
