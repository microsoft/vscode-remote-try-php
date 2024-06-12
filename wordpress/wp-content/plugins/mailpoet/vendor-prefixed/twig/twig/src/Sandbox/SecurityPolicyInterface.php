<?php
namespace MailPoetVendor\Twig\Sandbox;
if (!defined('ABSPATH')) exit;
interface SecurityPolicyInterface
{
 public function checkSecurity($tags, $filters, $functions) : void;
 public function checkMethodAllowed($obj, $method) : void;
 public function checkPropertyAllowed($obj, $property) : void;
}
