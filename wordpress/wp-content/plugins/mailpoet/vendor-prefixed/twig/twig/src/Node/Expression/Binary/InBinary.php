<?php
namespace MailPoetVendor\Twig\Node\Expression\Binary;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class InBinary extends AbstractBinary
{
 public function compile(Compiler $compiler) : void
 {
 $compiler->raw('\\MailPoetVendor\\twig_in_filter(')->subcompile($this->getNode('left'))->raw(', ')->subcompile($this->getNode('right'))->raw(')');
 }
 public function operator(Compiler $compiler) : Compiler
 {
 return $compiler->raw('in');
 }
}
