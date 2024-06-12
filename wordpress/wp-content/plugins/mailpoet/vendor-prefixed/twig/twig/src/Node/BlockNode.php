<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class BlockNode extends Node
{
 public function __construct(string $name, Node $body, int $lineno, string $tag = null)
 {
 parent::__construct(['body' => $body], ['name' => $name], $lineno, $tag);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->addDebugInfo($this)->write(\sprintf("public function block_%s(\$context, array \$blocks = [])\n", $this->getAttribute('name')), "{\n")->indent()->write("\$macros = \$this->macros;\n");
 $compiler->subcompile($this->getNode('body'))->outdent()->write("}\n\n");
 }
}
