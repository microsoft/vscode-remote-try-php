<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Attribute\YieldReady;
use MailPoetVendor\Twig\Compiler;
#[\Twig\Attribute\YieldReady]
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
