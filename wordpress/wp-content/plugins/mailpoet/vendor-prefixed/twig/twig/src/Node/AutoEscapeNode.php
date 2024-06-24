<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Attribute\YieldReady;
use MailPoetVendor\Twig\Compiler;
#[\Twig\Attribute\YieldReady]
class AutoEscapeNode extends Node
{
 public function __construct($value, Node $body, int $lineno, string $tag = 'autoescape')
 {
 parent::__construct(['body' => $body], ['value' => $value], $lineno, $tag);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->subcompile($this->getNode('body'));
 }
}
