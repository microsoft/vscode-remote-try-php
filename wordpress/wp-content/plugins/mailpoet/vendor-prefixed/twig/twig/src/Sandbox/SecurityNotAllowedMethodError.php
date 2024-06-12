<?php
namespace MailPoetVendor\Twig\Sandbox;
if (!defined('ABSPATH')) exit;
final class SecurityNotAllowedMethodError extends SecurityError
{
 private $className;
 private $methodName;
 public function __construct(string $message, string $className, string $methodName)
 {
 parent::__construct($message);
 $this->className = $className;
 $this->methodName = $methodName;
 }
 public function getClassName() : string
 {
 return $this->className;
 }
 public function getMethodName()
 {
 return $this->methodName;
 }
}
