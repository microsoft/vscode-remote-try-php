<?php
namespace MailPoetVendor\Twig\Node\Expression;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Node;
class FilterExpression extends CallExpression
{
 public function __construct(Node $node, ConstantExpression $filterName, Node $arguments, int $lineno, ?string $tag = null)
 {
 parent::__construct(['node' => $node, 'filter' => $filterName, 'arguments' => $arguments], [], $lineno, $tag);
 }
 public function compile(Compiler $compiler) : void
 {
 $name = $this->getNode('filter')->getAttribute('value');
 $filter = $compiler->getEnvironment()->getFilter($name);
 $this->setAttribute('name', $name);
 $this->setAttribute('type', 'filter');
 $this->setAttribute('needs_charset', $filter->needsCharset());
 $this->setAttribute('needs_environment', $filter->needsEnvironment());
 $this->setAttribute('needs_context', $filter->needsContext());
 $this->setAttribute('arguments', $filter->getArguments());
 $this->setAttribute('callable', $filter->getCallable());
 $this->setAttribute('is_variadic', $filter->isVariadic());
 $this->compileCallable($compiler);
 }
}
