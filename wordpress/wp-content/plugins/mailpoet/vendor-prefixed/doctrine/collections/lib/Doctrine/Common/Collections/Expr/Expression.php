<?php
namespace MailPoetVendor\Doctrine\Common\Collections\Expr;
if (!defined('ABSPATH')) exit;
interface Expression
{
 public function visit(ExpressionVisitor $visitor);
}
