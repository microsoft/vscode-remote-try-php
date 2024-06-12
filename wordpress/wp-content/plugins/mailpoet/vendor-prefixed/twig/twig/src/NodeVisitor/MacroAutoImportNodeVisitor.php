<?php
namespace MailPoetVendor\Twig\NodeVisitor;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Node\Expression\AssignNameExpression;
use MailPoetVendor\Twig\Node\Expression\ConstantExpression;
use MailPoetVendor\Twig\Node\Expression\GetAttrExpression;
use MailPoetVendor\Twig\Node\Expression\MethodCallExpression;
use MailPoetVendor\Twig\Node\Expression\NameExpression;
use MailPoetVendor\Twig\Node\ImportNode;
use MailPoetVendor\Twig\Node\ModuleNode;
use MailPoetVendor\Twig\Node\Node;
final class MacroAutoImportNodeVisitor implements NodeVisitorInterface
{
 private $inAModule = \false;
 private $hasMacroCalls = \false;
 public function enterNode(Node $node, Environment $env) : Node
 {
 if ($node instanceof ModuleNode) {
 $this->inAModule = \true;
 $this->hasMacroCalls = \false;
 }
 return $node;
 }
 public function leaveNode(Node $node, Environment $env) : Node
 {
 if ($node instanceof ModuleNode) {
 $this->inAModule = \false;
 if ($this->hasMacroCalls) {
 $node->getNode('constructor_end')->setNode('_auto_macro_import', new ImportNode(new NameExpression('_self', 0), new AssignNameExpression('_self', 0), 0, 'import', \true));
 }
 } elseif ($this->inAModule) {
 if ($node instanceof GetAttrExpression && $node->getNode('node') instanceof NameExpression && '_self' === $node->getNode('node')->getAttribute('name') && $node->getNode('attribute') instanceof ConstantExpression) {
 $this->hasMacroCalls = \true;
 $name = $node->getNode('attribute')->getAttribute('value');
 $node = new MethodCallExpression($node->getNode('node'), 'macro_' . $name, $node->getNode('arguments'), $node->getTemplateLine());
 $node->setAttribute('safe', \true);
 }
 }
 return $node;
 }
 public function getPriority() : int
 {
 // we must be ran before auto-escaping
 return -10;
 }
}
