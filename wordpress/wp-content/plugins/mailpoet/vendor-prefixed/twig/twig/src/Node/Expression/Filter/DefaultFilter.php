<?php
namespace MailPoetVendor\Twig\Node\Expression\Filter;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Expression\ConditionalExpression;
use MailPoetVendor\Twig\Node\Expression\ConstantExpression;
use MailPoetVendor\Twig\Node\Expression\FilterExpression;
use MailPoetVendor\Twig\Node\Expression\GetAttrExpression;
use MailPoetVendor\Twig\Node\Expression\NameExpression;
use MailPoetVendor\Twig\Node\Expression\Test\DefinedTest;
use MailPoetVendor\Twig\Node\Node;
class DefaultFilter extends FilterExpression
{
 public function __construct(Node $node, ConstantExpression $filterName, Node $arguments, int $lineno, string $tag = null)
 {
 $default = new FilterExpression($node, new ConstantExpression('default', $node->getTemplateLine()), $arguments, $node->getTemplateLine());
 if ('default' === $filterName->getAttribute('value') && ($node instanceof NameExpression || $node instanceof GetAttrExpression)) {
 $test = new DefinedTest(clone $node, 'defined', new Node(), $node->getTemplateLine());
 $false = \count($arguments) ? $arguments->getNode(0) : new ConstantExpression('', $node->getTemplateLine());
 $node = new ConditionalExpression($test, $default, $false, $node->getTemplateLine());
 } else {
 $node = $default;
 }
 parent::__construct($node, $filterName, $arguments, $lineno, $tag);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->subcompile($this->getNode('node'));
 }
}
