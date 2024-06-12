<?php
namespace MailPoetVendor\Twig\Node\Expression\Unary;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class NotUnary extends AbstractUnary
{
 public function operator(Compiler $compiler) : Compiler
 {
 return $compiler->raw('!');
 }
}
