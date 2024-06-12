<?php
namespace MailPoetVendor\Twig\Node\Expression;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class ConditionalExpression extends AbstractExpression
{
 public function __construct(AbstractExpression $expr1, AbstractExpression $expr2, AbstractExpression $expr3, int $lineno)
 {
 parent::__construct(['expr1' => $expr1, 'expr2' => $expr2, 'expr3' => $expr3], [], $lineno);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->raw('((')->subcompile($this->getNode('expr1'))->raw(') ? (')->subcompile($this->getNode('expr2'))->raw(') : (')->subcompile($this->getNode('expr3'))->raw('))');
 }
}
