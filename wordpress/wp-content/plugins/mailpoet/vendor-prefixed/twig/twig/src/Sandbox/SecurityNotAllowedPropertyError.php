<?php
namespace MailPoetVendor\Twig\Sandbox;
if (!defined('ABSPATH')) exit;
final class SecurityNotAllowedPropertyError extends SecurityError
{
 private $className;
 private $propertyName;
 public function __construct(string $message, string $className, string $propertyName)
 {
 parent::__construct($message);
 $this->className = $className;
 $this->propertyName = $propertyName;
 }
 public function getClassName() : string
 {
 return $this->className;
 }
 public function getPropertyName()
 {
 return $this->propertyName;
 }
}
