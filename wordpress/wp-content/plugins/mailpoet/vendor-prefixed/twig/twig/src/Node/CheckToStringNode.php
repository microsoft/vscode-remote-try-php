<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Expression\AbstractExpression;
class CheckToStringNode extends AbstractExpression
{
 public function __construct(AbstractExpression $expr)
 {
 parent::__construct(['expr' => $expr], [], $expr->getTemplateLine(), $expr->getNodeTag());
 }
 public function compile(Compiler $compiler) : void
 {
 $expr = $this->getNode('expr');
 $compiler->raw('$this->sandbox->ensureToStringAllowed(')->subcompile($expr)->raw(', ')->repr($expr->getTemplateLine())->raw(', $this->source)');
 }
}
