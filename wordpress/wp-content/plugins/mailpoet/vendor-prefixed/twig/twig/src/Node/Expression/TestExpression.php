<?php
namespace MailPoetVendor\Twig\Node\Expression;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Node;
class TestExpression extends CallExpression
{
 public function __construct(Node $node, string $name, ?Node $arguments, int $lineno)
 {
 $nodes = ['node' => $node];
 if (null !== $arguments) {
 $nodes['arguments'] = $arguments;
 }
 parent::__construct($nodes, ['name' => $name], $lineno);
 }
 public function compile(Compiler $compiler) : void
 {
 $name = $this->getAttribute('name');
 $test = $compiler->getEnvironment()->getTest($name);
 $this->setAttribute('name', $name);
 $this->setAttribute('type', 'test');
 $this->setAttribute('arguments', $test->getArguments());
 $this->setAttribute('callable', $test->getCallable());
 $this->setAttribute('is_variadic', $test->isVariadic());
 $this->compileCallable($compiler);
 }
}
