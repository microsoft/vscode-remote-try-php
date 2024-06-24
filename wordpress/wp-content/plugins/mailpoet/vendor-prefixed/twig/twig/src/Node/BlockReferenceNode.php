<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Attribute\YieldReady;
use MailPoetVendor\Twig\Compiler;
#[\Twig\Attribute\YieldReady]
class BlockReferenceNode extends Node implements NodeOutputInterface
{
 public function __construct(string $name, int $lineno, ?string $tag = null)
 {
 parent::__construct([], ['name' => $name], $lineno, $tag);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->addDebugInfo($this)->write(\sprintf("yield from \$this->unwrap()->yieldBlock('%s', \$context, \$blocks);\n", $this->getAttribute('name')));
 }
}
