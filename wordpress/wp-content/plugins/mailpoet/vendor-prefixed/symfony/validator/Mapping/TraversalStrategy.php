<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping;
if (!defined('ABSPATH')) exit;
class TraversalStrategy
{
 public const IMPLICIT = 1;
 public const NONE = 2;
 public const TRAVERSE = 4;
 private function __construct()
 {
 }
}
