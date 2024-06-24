<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Attribute\YieldReady;
use MailPoetVendor\Twig\Compiler;
#[\Twig\Attribute\YieldReady]
class CaptureNode extends Node
{
 public function __construct(Node $body, int $lineno, ?string $tag = null)
 {
 parent::__construct(['body' => $body], ['raw' => \false], $lineno, $tag);
 }
 public function compile(Compiler $compiler) : void
 {
 $useYield = $compiler->getEnvironment()->useYield();
 if (!$this->getAttribute('raw')) {
 $compiler->raw("('' === \$tmp = ");
 }
 $compiler->raw($useYield ? "implode('', iterator_to_array(" : '\\MailPoetVendor\\Twig\\Extension\\CoreExtension::captureOutput(')->raw("(function () use (&\$context, \$macros, \$blocks) {\n")->indent()->subcompile($this->getNode('body'))->write("return; yield '';\n")->outdent()->write('})()');
 if ($useYield) {
 $compiler->raw(', false))');
 } else {
 $compiler->raw(')');
 }
 if (!$this->getAttribute('raw')) {
 $compiler->raw(") ? '' : new Markup(\$tmp, \$this->env->getCharset());");
 } else {
 $compiler->raw(';');
 }
 }
}
