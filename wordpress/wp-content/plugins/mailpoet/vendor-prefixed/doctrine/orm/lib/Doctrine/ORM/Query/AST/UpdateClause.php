<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class UpdateClause extends Node
{
 public $abstractSchemaName;
 public $aliasIdentificationVariable;
 public $updateItems = [];
 public function __construct($abstractSchemaName, array $updateItems)
 {
 $this->abstractSchemaName = $abstractSchemaName;
 $this->updateItems = $updateItems;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkUpdateClause($this);
 }
}
