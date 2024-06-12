<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class SubselectIdentificationVariableDeclaration
{
 public $associationPathExpression;
 public $aliasIdentificationVariable;
 public function __construct($associationPathExpression, $aliasIdentificationVariable)
 {
 $this->associationPathExpression = $associationPathExpression;
 $this->aliasIdentificationVariable = $aliasIdentificationVariable;
 }
}
