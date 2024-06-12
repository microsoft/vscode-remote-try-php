<?php
namespace MailPoetVendor\Twig\Node\Expression\Test;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Expression\TestExpression;
class ConstantTest extends TestExpression
{
 public function compile(Compiler $compiler) : void
 {
 $compiler->raw('(')->subcompile($this->getNode('node'))->raw(' === constant(');
 if ($this->getNode('arguments')->hasNode(1)) {
 $compiler->raw('get_class(')->subcompile($this->getNode('arguments')->getNode(1))->raw(')."::".');
 }
 $compiler->subcompile($this->getNode('arguments')->getNode(0))->raw('))');
 }
}
