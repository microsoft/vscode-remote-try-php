<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Attribute\YieldReady;
use MailPoetVendor\Twig\Compiler;
#[\Twig\Attribute\YieldReady]
class CheckSecurityCallNode extends Node
{
 public function compile(Compiler $compiler)
 {
 $compiler->write("\$this->sandbox = \$this->env->getExtension(SandboxExtension::class);\n")->write("\$this->checkSecurity();\n");
 }
}
