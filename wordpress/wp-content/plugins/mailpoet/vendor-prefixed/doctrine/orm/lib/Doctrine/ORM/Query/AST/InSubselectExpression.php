<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class InSubselectExpression extends InExpression
{
 public $subselect;
 public function __construct(ArithmeticExpression $expression, Subselect $subselect, bool $not = \false)
 {
 $this->subselect = $subselect;
 $this->not = $not;
 parent::__construct($expression);
 }
}
