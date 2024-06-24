<?php
namespace MailPoetVendor\Twig\NodeVisitor;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Node\Node;
interface NodeVisitorInterface
{
 public function enterNode(Node $node, Environment $env) : Node;
 public function leaveNode(Node $node, Environment $env) : ?Node;
 public function getPriority();
}
