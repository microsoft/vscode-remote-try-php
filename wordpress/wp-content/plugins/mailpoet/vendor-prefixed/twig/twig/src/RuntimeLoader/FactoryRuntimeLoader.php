<?php
namespace MailPoetVendor\Twig\RuntimeLoader;
if (!defined('ABSPATH')) exit;
class FactoryRuntimeLoader implements RuntimeLoaderInterface
{
 private $map;
 public function __construct(array $map = [])
 {
 $this->map = $map;
 }
 public function load(string $class)
 {
 if (!isset($this->map[$class])) {
 return null;
 }
 $runtimeFactory = $this->map[$class];
 return $runtimeFactory();
 }
}
