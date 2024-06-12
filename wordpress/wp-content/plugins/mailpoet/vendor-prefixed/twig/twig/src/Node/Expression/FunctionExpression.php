<?php
namespace MailPoetVendor\Twig\Node\Expression;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Node;
class FunctionExpression extends CallExpression
{
 public function __construct(string $name, Node $arguments, int $lineno)
 {
 parent::__construct(['arguments' => $arguments], ['name' => $name, 'is_defined_test' => \false], $lineno);
 }
 public function compile(Compiler $compiler)
 {
 $name = $this->getAttribute('name');
 $function = $compiler->getEnvironment()->getFunction($name);
 $this->setAttribute('name', $name);
 $this->setAttribute('type', 'function');
 $this->setAttribute('needs_environment', $function->needsEnvironment());
 $this->setAttribute('needs_context', $function->needsContext());
 $this->setAttribute('arguments', $function->getArguments());
 $callable = $function->getCallable();
 if ('constant' === $name && $this->getAttribute('is_defined_test')) {
 $callable = '\\MailPoetVendor\\twig_constant_is_defined';
 }
 $this->setAttribute('callable', $callable);
 $this->setAttribute('is_variadic', $function->isVariadic());
 $this->compileCallable($compiler);
 }
}
