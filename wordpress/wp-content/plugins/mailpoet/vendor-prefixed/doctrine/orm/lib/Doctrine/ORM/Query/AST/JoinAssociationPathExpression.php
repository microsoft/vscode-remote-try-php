<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class JoinAssociationPathExpression extends Node
{
 public $identificationVariable;
 public $associationField;
 public function __construct($identificationVariable, $associationField)
 {
 $this->identificationVariable = $identificationVariable;
 $this->associationField = $associationField;
 }
 public function dispatch($sqlWalker)
 {
 return $sqlWalker->walkPathExpression($this);
 }
}
