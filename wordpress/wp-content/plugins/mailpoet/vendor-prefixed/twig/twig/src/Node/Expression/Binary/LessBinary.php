<?php
namespace MailPoetVendor\Twig\Node\Expression\Binary;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class LessBinary extends AbstractBinary
{
 public function compile(Compiler $compiler) : void
 {
 if (\PHP_VERSION_ID >= 80000) {
 parent::compile($compiler);
 return;
 }
 $compiler->raw('(-1 === CoreExtension::compare(')->subcompile($this->getNode('left'))->raw(', ')->subcompile($this->getNode('right'))->raw('))');
 }
 public function operator(Compiler $compiler) : Compiler
 {
 return $compiler->raw('<');
 }
}
