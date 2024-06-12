<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class IdentificationVariableDeclaration extends Node
{
 public $rangeVariableDeclaration = null;
 public $indexBy = null;
 public $joins = [];
 public function __construct($rangeVariableDecl, $indexBy, array $joins)
 {
 $this->rangeVariableDeclaration = $rangeVariableDecl;
 $this->indexBy = $indexBy;
 $this->joins = $joins;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkIdentificationVariableDeclaration($this);
 }
}
