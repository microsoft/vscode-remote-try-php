<?php
namespace MailPoetVendor\Twig\Node\Expression\Binary;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class FloorDivBinary extends AbstractBinary
{
 public function compile(Compiler $compiler) : void
 {
 $compiler->raw('(int) floor(');
 parent::compile($compiler);
 $compiler->raw(')');
 }
 public function operator(Compiler $compiler) : Compiler
 {
 return $compiler->raw('/');
 }
}
