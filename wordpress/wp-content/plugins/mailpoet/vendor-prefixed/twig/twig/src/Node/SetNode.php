<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Attribute\YieldReady;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Expression\ConstantExpression;
#[\Twig\Attribute\YieldReady]
class SetNode extends Node implements NodeCaptureInterface
{
 public function __construct(bool $capture, Node $names, Node $values, int $lineno, ?string $tag = null)
 {
 $safe = \false;
 if ($capture) {
 $safe = \true;
 if ($values instanceof TextNode) {
 $values = new ConstantExpression($values->getAttribute('data'), $values->getTemplateLine());
 $capture = \false;
 } else {
 $values = new CaptureNode($values, $values->getTemplateLine());
 }
 }
 parent::__construct(['names' => $names, 'values' => $values], ['capture' => $capture, 'safe' => $safe], $lineno, $tag);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->addDebugInfo($this);
 if (\count($this->getNode('names')) > 1) {
 $compiler->write('[');
 foreach ($this->getNode('names') as $idx => $node) {
 if ($idx) {
 $compiler->raw(', ');
 }
 $compiler->subcompile($node);
 }
 $compiler->raw(']');
 } else {
 $compiler->subcompile($this->getNode('names'), \false);
 }
 $compiler->raw(' = ');
 if ($this->getAttribute('capture')) {
 $compiler->subcompile($this->getNode('values'));
 } else {
 if (\count($this->getNode('names')) > 1) {
 $compiler->write('[');
 foreach ($this->getNode('values') as $idx => $value) {
 if ($idx) {
 $compiler->raw(', ');
 }
 $compiler->subcompile($value);
 }
 $compiler->raw(']');
 } else {
 if ($this->getAttribute('safe')) {
 $compiler->raw("('' === \$tmp = ")->subcompile($this->getNode('values'))->raw(") ? '' : new Markup(\$tmp, \$this->env->getCharset())");
 } else {
 $compiler->subcompile($this->getNode('values'));
 }
 }
 $compiler->raw(';');
 }
 $compiler->raw("\n");
 }
}
