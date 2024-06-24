<?php
namespace MailPoetVendor\Twig\Node\Expression\Test;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Expression\TestExpression;
class NullTest extends TestExpression
{
 public function compile(Compiler $compiler) : void
 {
 $compiler->raw('(null === ')->subcompile($this->getNode('node'))->raw(')');
 }
}
