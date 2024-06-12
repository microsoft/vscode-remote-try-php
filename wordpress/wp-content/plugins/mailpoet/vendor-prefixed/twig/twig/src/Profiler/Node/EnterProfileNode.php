<?php
namespace MailPoetVendor\Twig\Profiler\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Node;
class EnterProfileNode extends Node
{
 public function __construct(string $extensionName, string $type, string $name, string $varName)
 {
 parent::__construct([], ['extension_name' => $extensionName, 'name' => $name, 'type' => $type, 'var_name' => $varName]);
 }
 public function compile(Compiler $compiler) : void
 {
 $compiler->write(\sprintf('$%s = $this->extensions[', $this->getAttribute('var_name')))->repr($this->getAttribute('extension_name'))->raw("];\n")->write(\sprintf('$%s->enter($%s = new \\MailPoetVendor\\Twig\\Profiler\\Profile($this->getTemplateName(), ', $this->getAttribute('var_name'), $this->getAttribute('var_name') . '_prof'))->repr($this->getAttribute('type'))->raw(', ')->repr($this->getAttribute('name'))->raw("));\n\n");
 }
}
