<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST;
if (!defined('ABSPATH')) exit;
class InListExpression extends InExpression
{
 public $literals;
 public function __construct(ArithmeticExpression $expression, array $literals, bool $not = \false)
 {
 $this->literals = $literals;
 $this->not = $not;
 parent::__construct($expression);
 }
}
