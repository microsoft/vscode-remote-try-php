<?php
namespace MailPoetVendor\Twig\Extension;
if (!defined('ABSPATH')) exit;
abstract class AbstractExtension implements ExtensionInterface
{
 public function getTokenParsers()
 {
 return [];
 }
 public function getNodeVisitors()
 {
 return [];
 }
 public function getFilters()
 {
 return [];
 }
 public function getTests()
 {
 return [];
 }
 public function getFunctions()
 {
 return [];
 }
 public function getOperators()
 {
 return [];
 }
}
