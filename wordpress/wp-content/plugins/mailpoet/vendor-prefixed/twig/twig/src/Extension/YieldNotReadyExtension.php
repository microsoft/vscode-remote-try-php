<?php
namespace MailPoetVendor\Twig\Extension;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\NodeVisitor\YieldNotReadyNodeVisitor;
final class YieldNotReadyExtension extends AbstractExtension
{
 private $useYield;
 public function __construct(bool $useYield)
 {
 $this->useYield = $useYield;
 }
 public function getNodeVisitors() : array
 {
 return [new YieldNotReadyNodeVisitor($this->useYield)];
 }
}
