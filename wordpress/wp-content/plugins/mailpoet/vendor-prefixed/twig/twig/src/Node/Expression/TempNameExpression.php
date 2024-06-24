<?php
namespace MailPoetVendor\Twig\Node\Expression;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class TempNameExpression extends AbstractExpression
{
 public function __construct(string $name, int $lineno)
 {
 parent::__construct([], ['name' => $name], $lineno);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->raw('$_')->raw($this->getAttribute('name'))->raw('_');
 }
}
