<?php
namespace MailPoetVendor\Twig\Sandbox;
if (!defined('ABSPATH')) exit;
final class SecurityNotAllowedFunctionError extends SecurityError
{
 private $functionName;
 public function __construct(string $message, string $functionName)
 {
 parent::__construct($message);
 $this->functionName = $functionName;
 }
 public function getFunctionName() : string
 {
 return $this->functionName;
 }
}
