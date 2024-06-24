<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class JoinVariableDeclaration extends Node
{
 public $join;
 public $indexBy;
 public function __construct($join, $indexBy)
 {
 $this->join = $join;
 $this->indexBy = $indexBy;
 }
 public function dispatch($walker)
 {
 return $walker->walkJoinVariableDeclaration($this);
 }
}
