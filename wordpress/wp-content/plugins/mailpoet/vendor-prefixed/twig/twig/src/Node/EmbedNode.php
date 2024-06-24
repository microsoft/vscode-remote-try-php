<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Attribute\YieldReady;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Expression\AbstractExpression;
use MailPoetVendor\Twig\Node\Expression\ConstantExpression;
#[\Twig\Attribute\YieldReady]
class EmbedNode extends IncludeNode
{
 // we don't inject the module to avoid node visitors to traverse it twice (as it will be already visited in the main module)
 public function __construct(string $name, int $index, ?AbstractExpression $variables, bool $only, bool $ignoreMissing, int $lineno, ?string $tag = null)
 {
 parent::__construct(new ConstantExpression('not_used', $lineno), $variables, $only, $ignoreMissing, $lineno, $tag);
 $this->setAttribute('name', $name);
 $this->setAttribute('index', $index);
 }
 protected function addGetTemplate(Compiler $compiler) : void
 {
 $compiler->write('$this->loadTemplate(')->string($this->getAttribute('name'))->raw(', ')->repr($this->getTemplateName())->raw(', ')->repr($this->getTemplateLine())->raw(', ')->string($this->getAttribute('index'))->raw(')');
 }
}
