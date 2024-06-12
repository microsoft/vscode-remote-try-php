<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
class Parameter
{
 private $id;
 public function __construct(string $id)
 {
 $this->id = $id;
 }
 public function __toString()
 {
 return $this->id;
 }
}
