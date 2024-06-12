<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class FlushNode extends Node
{
 public function __construct(int $lineno, string $tag)
 {
 parent::__construct([], [], $lineno, $tag);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->addDebugInfo($this)->write("flush();\n");
 }
}
