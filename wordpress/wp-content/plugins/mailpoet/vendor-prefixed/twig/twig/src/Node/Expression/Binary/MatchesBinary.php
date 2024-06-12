<?php
namespace MailPoetVendor\Twig\Node\Expression\Binary;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class MatchesBinary extends AbstractBinary
{
 public function compile(Compiler $compiler) : void
 {
 $compiler->raw('preg_match(')->subcompile($this->getNode('right'))->raw(', ')->subcompile($this->getNode('left'))->raw(')');
 }
 public function operator(Compiler $compiler) : Compiler
 {
 return $compiler->raw('');
 }
}
