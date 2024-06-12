<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Expression\AbstractExpression;
class DoNode extends Node
{
 public function __construct(AbstractExpression $expr, int $lineno, string $tag = null)
 {
 parent::__construct(['expr' => $expr], [], $lineno, $tag);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->addDebugInfo($this)->write('')->subcompile($this->getNode('expr'))->raw(";\n");
 }
}
