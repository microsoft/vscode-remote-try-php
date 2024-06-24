<?php
namespace MailPoetVendor\Twig\Node\Expression\Test;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Error\SyntaxError;
use MailPoetVendor\Twig\Node\Expression\ArrayExpression;
use MailPoetVendor\Twig\Node\Expression\BlockReferenceExpression;
use MailPoetVendor\Twig\Node\Expression\ConstantExpression;
use MailPoetVendor\Twig\Node\Expression\FunctionExpression;
use MailPoetVendor\Twig\Node\Expression\GetAttrExpression;
use MailPoetVendor\Twig\Node\Expression\MethodCallExpression;
use MailPoetVendor\Twig\Node\Expression\NameExpression;
use MailPoetVendor\Twig\Node\Expression\TestExpression;
use MailPoetVendor\Twig\Node\Node;
class DefinedTest extends TestExpression
{
 public function __construct(Node $node, string $name, ?Node $arguments, int $lineno)
 {
 if ($node instanceof NameExpression) {
 $node->setAttribute('is_defined_test', \true);
 } elseif ($node instanceof GetAttrExpression) {
 $node->setAttribute('is_defined_test', \true);
 $this->changeIgnoreStrictCheck($node);
 } elseif ($node instanceof BlockReferenceExpression) {
 $node->setAttribute('is_defined_test', \true);
 } elseif ($node instanceof FunctionExpression && 'constant' === $node->getAttribute('name')) {
 $node->setAttribute('is_defined_test', \true);
 } elseif ($node instanceof ConstantExpression || $node instanceof ArrayExpression) {
 $node = new ConstantExpression(\true, $node->getTemplateLine());
 } elseif ($node instanceof MethodCallExpression) {
 $node->setAttribute('is_defined_test', \true);
 } else {
 throw new SyntaxError('The "defined" test only works with simple variables.', $lineno);
 }
 parent::__construct($node, $name, $arguments, $lineno);
 }
 private function changeIgnoreStrictCheck(GetAttrExpression $node)
 {
 $node->setAttribute('optimizable', \false);
 $node->setAttribute('ignore_strict_check', \true);
 if ($node->getNode('node') instanceof GetAttrExpression) {
 $this->changeIgnoreStrictCheck($node->getNode('node'));
 }
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->subcompile($this->getNode('node'));
 }
}
