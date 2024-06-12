<?php
namespace MailPoetVendor\Twig\Node\Expression;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Node;
class ArrowFunctionExpression extends AbstractExpression
{
 public function __construct(AbstractExpression $expr, Node $names, $lineno, $tag = null)
 {
 parent::__construct(['expr' => $expr, 'names' => $names], [], $lineno, $tag);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->addDebugInfo($this)->raw('function (');
 foreach ($this->getNode('names') as $i => $name) {
 if ($i) {
 $compiler->raw(', ');
 }
 $compiler->raw('$__')->raw($name->getAttribute('name'))->raw('__');
 }
 $compiler->raw(') use ($context, $macros) { ');
 foreach ($this->getNode('names') as $name) {
 $compiler->raw('$context["')->raw($name->getAttribute('name'))->raw('"] = $__')->raw($name->getAttribute('name'))->raw('__; ');
 }
 $compiler->raw('return ')->subcompile($this->getNode('expr'))->raw('; }');
 }
}
