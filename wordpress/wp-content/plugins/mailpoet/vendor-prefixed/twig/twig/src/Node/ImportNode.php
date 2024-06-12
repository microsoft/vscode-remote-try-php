<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Expression\AbstractExpression;
use MailPoetVendor\Twig\Node\Expression\NameExpression;
class ImportNode extends Node
{
 public function __construct(AbstractExpression $expr, AbstractExpression $var, int $lineno, string $tag = null, bool $global = \true)
 {
 parent::__construct(['expr' => $expr, 'var' => $var], ['global' => $global], $lineno, $tag);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->addDebugInfo($this)->write('$macros[')->repr($this->getNode('var')->getAttribute('name'))->raw('] = ');
 if ($this->getAttribute('global')) {
 $compiler->raw('$this->macros[')->repr($this->getNode('var')->getAttribute('name'))->raw('] = ');
 }
 if ($this->getNode('expr') instanceof NameExpression && '_self' === $this->getNode('expr')->getAttribute('name')) {
 $compiler->raw('$this');
 } else {
 $compiler->raw('$this->loadTemplate(')->subcompile($this->getNode('expr'))->raw(', ')->repr($this->getTemplateName())->raw(', ')->repr($this->getTemplateLine())->raw(')->unwrap()');
 }
 $compiler->raw(";\n");
 }
}
