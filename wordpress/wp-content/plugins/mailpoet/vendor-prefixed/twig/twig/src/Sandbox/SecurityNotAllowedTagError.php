<?php
namespace MailPoetVendor\Twig\Sandbox;
if (!defined('ABSPATH')) exit;
final class SecurityNotAllowedTagError extends SecurityError
{
 private $tagName;
 public function __construct(string $message, string $tagName)
 {
 parent::__construct($message);
 $this->tagName = $tagName;
 }
 public function getTagName() : string
 {
 return $this->tagName;
 }
}
