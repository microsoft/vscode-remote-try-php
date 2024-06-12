<?php
namespace MailPoetVendor\Twig\Node\Expression;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Expression\Binary\AndBinary;
use MailPoetVendor\Twig\Node\Expression\Test\DefinedTest;
use MailPoetVendor\Twig\Node\Expression\Test\NullTest;
use MailPoetVendor\Twig\Node\Expression\Unary\NotUnary;
use MailPoetVendor\Twig\Node\Node;
class NullCoalesceExpression extends ConditionalExpression
{
 public function __construct(Node $left, Node $right, int $lineno)
 {
 $test = new DefinedTest(clone $left, 'defined', new Node(), $left->getTemplateLine());
 // for "block()", we don't need the null test as the return value is always a string
 if (!$left instanceof BlockReferenceExpression) {
 $test = new AndBinary($test, new NotUnary(new NullTest($left, 'null', new Node(), $left->getTemplateLine()), $left->getTemplateLine()), $left->getTemplateLine());
 }
 parent::__construct($test, $left, $right, $lineno);
 }
 public function compile(Compiler $compiler) : void
 {
 if ($this->getNode('expr2') instanceof NameExpression) {
 $this->getNode('expr2')->setAttribute('always_defined', \true);
 $compiler->raw('((')->subcompile($this->getNode('expr2'))->raw(') ?? (')->subcompile($this->getNode('expr3'))->raw('))');
 } else {
 parent::compile($compiler);
 }
 }
}
