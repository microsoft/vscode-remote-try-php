<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Platforms;
if (!defined('ABSPATH')) exit;
final class TrimMode
{
 public const UNSPECIFIED = 0;
 public const LEADING = 1;
 public const TRAILING = 2;
 public const BOTH = 3;
 private function __construct()
 {
 }
}
