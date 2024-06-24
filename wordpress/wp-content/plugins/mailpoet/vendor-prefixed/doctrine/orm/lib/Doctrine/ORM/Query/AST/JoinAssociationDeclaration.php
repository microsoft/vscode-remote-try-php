<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class JoinAssociationDeclaration extends Node
{
 public $joinAssociationPathExpression;
 public $aliasIdentificationVariable;
 public $indexBy;
 public function __construct($joinAssociationPathExpression, $aliasIdentificationVariable, $indexBy)
 {
 $this->joinAssociationPathExpression = $joinAssociationPathExpression;
 $this->aliasIdentificationVariable = $aliasIdentificationVariable;
 $this->indexBy = $indexBy;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkJoinAssociationDeclaration($this);
 }
}
