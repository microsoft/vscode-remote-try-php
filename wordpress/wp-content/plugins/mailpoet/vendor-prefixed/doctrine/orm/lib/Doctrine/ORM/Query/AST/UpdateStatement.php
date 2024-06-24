<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class UpdateStatement extends Node
{
 public $updateClause;
 public $whereClause;
 public function __construct($updateClause)
 {
 $this->updateClause = $updateClause;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkUpdateStatement($this);
 }
}
