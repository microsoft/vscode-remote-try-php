<?php
namespace MailPoetVendor\Twig\Node\Expression\Binary;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Expression\AbstractExpression;
use MailPoetVendor\Twig\Node\Node;
abstract class AbstractBinary extends AbstractExpression
{
 public function __construct(Node $left, Node $right, int $lineno)
 {
 parent::__construct(['left' => $left, 'right' => $right], [], $lineno);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->raw('(')->subcompile($this->getNode('left'))->raw(' ');
 $this->operator($compiler);
 $compiler->raw(' ')->subcompile($this->getNode('right'))->raw(')');
 }
 public abstract function operator(Compiler $compiler) : Compiler;
}
