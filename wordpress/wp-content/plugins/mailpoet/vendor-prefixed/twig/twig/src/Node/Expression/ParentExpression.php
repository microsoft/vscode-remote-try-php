<?php
namespace MailPoetVendor\Twig\Node\Expression;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class ParentExpression extends AbstractExpression
{
 public function __construct(string $name, int $lineno, ?string $tag = null)
 {
 parent::__construct([], ['output' => \false, 'name' => $name], $lineno, $tag);
 }
 public function compile(Compiler $compiler) : void
 {
 if ($this->getAttribute('output')) {
 $compiler->addDebugInfo($this)->write('yield from $this->yieldParentBlock(')->string($this->getAttribute('name'))->raw(", \$context, \$blocks);\n");
 } else {
 $compiler->raw('$this->renderParentBlock(')->string($this->getAttribute('name'))->raw(', $context, $blocks)');
 }
 }
}
