<?php
namespace MailPoetVendor\Twig\NodeVisitor;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Node\Node;
abstract class AbstractNodeVisitor implements NodeVisitorInterface
{
 public final function enterNode(Node $node, Environment $env) : Node
 {
 return $this->doEnterNode($node, $env);
 }
 public final function leaveNode(Node $node, Environment $env) : ?Node
 {
 return $this->doLeaveNode($node, $env);
 }
 protected abstract function doEnterNode(Node $node, Environment $env);
 protected abstract function doLeaveNode(Node $node, Environment $env);
}
