<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
class Variable
{
 private $name;
 public function __construct(string $name)
 {
 $this->name = $name;
 }
 public function __toString()
 {
 return $this->name;
 }
}
