<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Attribute\YieldReady;
use MailPoetVendor\Twig\Compiler;
#[\Twig\Attribute\YieldReady]
class IfNode extends Node
{
 public function __construct(Node $tests, ?Node $else, int $lineno, ?string $tag = null)
 {
 $nodes = ['tests' => $tests];
 if (null !== $else) {
 $nodes['else'] = $else;
 }
 parent::__construct($nodes, [], $lineno, $tag);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->addDebugInfo($this);
 for ($i = 0, $count = \count($this->getNode('tests')); $i < $count; $i += 2) {
 if ($i > 0) {
 $compiler->outdent()->write('} elseif (');
 } else {
 $compiler->write('if (');
 }
 $compiler->subcompile($this->getNode('tests')->getNode((string) $i))->raw(") {\n")->indent();
 // The node might not exists if the content is empty
 if ($this->getNode('tests')->hasNode((string) ($i + 1))) {
 $compiler->subcompile($this->getNode('tests')->getNode((string) ($i + 1)));
 }
 }
 if ($this->hasNode('else')) {
 $compiler->outdent()->write("} else {\n")->indent()->subcompile($this->getNode('else'));
 }
 $compiler->outdent()->write("}\n");
 }
}
