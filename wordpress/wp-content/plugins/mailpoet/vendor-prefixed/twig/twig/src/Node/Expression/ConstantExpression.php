<?php
namespace MailPoetVendor\Twig\Node\Expression;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class ConstantExpression extends AbstractExpression
{
 public function __construct($value, int $lineno)
 {
 parent::__construct([], ['value' => $value], $lineno);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->repr($this->getAttribute('value'));
 }
}
