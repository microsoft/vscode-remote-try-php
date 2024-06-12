<?php
namespace MailPoetVendor\Twig\Sandbox;
if (!defined('ABSPATH')) exit;
final class SecurityNotAllowedFilterError extends SecurityError
{
 private $filterName;
 public function __construct(string $message, string $functionName)
 {
 parent::__construct($message);
 $this->filterName = $functionName;
 }
 public function getFilterName() : string
 {
 return $this->filterName;
 }
}
