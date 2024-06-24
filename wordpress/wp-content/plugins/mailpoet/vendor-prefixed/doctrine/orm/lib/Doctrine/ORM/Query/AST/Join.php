<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class Join extends Node
{
 public const JOIN_TYPE_LEFT = 1;
 public const JOIN_TYPE_LEFTOUTER = 2;
 public const JOIN_TYPE_INNER = 3;
 public $joinType = self::JOIN_TYPE_INNER;
 public $joinAssociationDeclaration = null;
 public $conditionalExpression = null;
 public function __construct($joinType, $joinAssociationDeclaration)
 {
 $this->joinType = $joinType;
 $this->joinAssociationDeclaration = $joinAssociationDeclaration;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkJoin($this);
 }
}
