<?php
namespace MailPoetVendor\Twig\Node\Expression\Test;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Expression\TestExpression;
class SameasTest extends TestExpression
{
 public function compile(Compiler $compiler) : void
 {
 $compiler->raw('(')->subcompile($this->getNode('node'))->raw(' === ')->subcompile($this->getNode('arguments')->getNode(0))->raw(')');
 }
}
