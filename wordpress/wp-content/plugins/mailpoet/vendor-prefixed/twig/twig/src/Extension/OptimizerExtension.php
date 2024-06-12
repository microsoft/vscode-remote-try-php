<?php
namespace MailPoetVendor\Twig\Extension;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\NodeVisitor\OptimizerNodeVisitor;
final class OptimizerExtension extends AbstractExtension
{
 private $optimizers;
 public function __construct(int $optimizers = -1)
 {
 $this->optimizers = $optimizers;
 }
 public function getNodeVisitors() : array
 {
 return [new OptimizerNodeVisitor($this->optimizers)];
 }
}
