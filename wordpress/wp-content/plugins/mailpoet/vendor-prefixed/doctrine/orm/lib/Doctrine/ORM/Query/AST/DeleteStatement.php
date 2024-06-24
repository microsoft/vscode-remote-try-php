<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class DeleteStatement extends Node
{
 public $deleteClause;
 public $whereClause;
 public function __construct($deleteClause)
 {
 $this->deleteClause = $deleteClause;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkDeleteStatement($this);
 }
}
