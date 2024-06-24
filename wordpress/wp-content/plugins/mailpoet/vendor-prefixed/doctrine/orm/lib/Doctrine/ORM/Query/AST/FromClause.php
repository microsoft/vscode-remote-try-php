<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class FromClause extends Node
{
 public $identificationVariableDeclarations = [];
 public function __construct(array $identificationVariableDeclarations)
 {
 $this->identificationVariableDeclarations = $identificationVariableDeclarations;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkFromClause($this);
 }
}
