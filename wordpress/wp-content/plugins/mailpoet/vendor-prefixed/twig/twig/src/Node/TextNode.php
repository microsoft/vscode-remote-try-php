<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class TextNode extends Node implements NodeOutputInterface
{
 public function __construct(string $data, int $lineno)
 {
 parent::__construct([], ['data' => $data], $lineno);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->addDebugInfo($this)->write('echo ')->string($this->getAttribute('data'))->raw(";\n");
 }
}
