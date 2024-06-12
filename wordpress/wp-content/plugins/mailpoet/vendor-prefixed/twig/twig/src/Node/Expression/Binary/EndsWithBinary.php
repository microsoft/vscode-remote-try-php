<?php
namespace MailPoetVendor\Twig\Node\Expression\Binary;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class EndsWithBinary extends AbstractBinary
{
 public function compile(Compiler $compiler) : void
 {
 $left = $compiler->getVarName();
 $right = $compiler->getVarName();
 $compiler->raw(\sprintf('(is_string($%s = ', $left))->subcompile($this->getNode('left'))->raw(\sprintf(') && is_string($%s = ', $right))->subcompile($this->getNode('right'))->raw(\sprintf(') && (\'\' === $%2$s || $%2$s === substr($%1$s, -strlen($%2$s))))', $left, $right));
 }
 public function operator(Compiler $compiler) : Compiler
 {
 return $compiler->raw('');
 }
}
