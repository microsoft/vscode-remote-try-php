<?php
namespace MailPoetVendor\Twig\Node\Expression\Test;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Expression\TestExpression;
class DivisiblebyTest extends TestExpression
{
 public function compile(Compiler $compiler) : void
 {
 $compiler->raw('(0 == ')->subcompile($this->getNode('node'))->raw(' % ')->subcompile($this->getNode('arguments')->getNode(0))->raw(')');
 }
}
