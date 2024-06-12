<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class SandboxNode extends Node
{
 public function __construct(Node $body, int $lineno, string $tag = null)
 {
 parent::__construct(['body' => $body], [], $lineno, $tag);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->addDebugInfo($this)->write("if (!\$alreadySandboxed = \$this->sandbox->isSandboxed()) {\n")->indent()->write("\$this->sandbox->enableSandbox();\n")->outdent()->write("}\n")->write("try {\n")->indent()->subcompile($this->getNode('body'))->outdent()->write("} finally {\n")->indent()->write("if (!\$alreadySandboxed) {\n")->indent()->write("\$this->sandbox->disableSandbox();\n")->outdent()->write("}\n")->outdent()->write("}\n");
 }
}
