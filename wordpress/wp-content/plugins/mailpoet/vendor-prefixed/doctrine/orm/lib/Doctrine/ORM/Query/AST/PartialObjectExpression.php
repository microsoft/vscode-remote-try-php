<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class PartialObjectExpression extends Node
{
 public $identificationVariable;
 public $partialFieldSet;
 public function __construct($identificationVariable, array $partialFieldSet)
 {
 $this->identificationVariable = $identificationVariable;
 $this->partialFieldSet = $partialFieldSet;
 }
}
