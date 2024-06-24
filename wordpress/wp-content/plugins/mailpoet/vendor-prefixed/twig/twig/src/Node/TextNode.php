<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Attribute\YieldReady;
use MailPoetVendor\Twig\Compiler;
#[\Twig\Attribute\YieldReady]
class TextNode extends Node implements NodeOutputInterface
{
 public function __construct(string $data, int $lineno)
 {
 parent::__construct([], ['data' => $data], $lineno);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->addDebugInfo($this);
 $compiler->write('yield ')->string($this->getAttribute('data'))->raw(";\n");
 }
}
