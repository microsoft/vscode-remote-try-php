<?php
namespace MailPoetVendor\Twig\Node\Expression;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class AssignNameExpression extends NameExpression
{
 public function compile(Compiler $compiler) : void
 {
 $compiler->raw('$context[')->string($this->getAttribute('name'))->raw(']');
 }
}
