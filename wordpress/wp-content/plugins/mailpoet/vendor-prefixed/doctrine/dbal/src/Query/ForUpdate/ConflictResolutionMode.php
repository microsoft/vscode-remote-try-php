<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Query\ForUpdate;
if (!defined('ABSPATH')) exit;
final class ConflictResolutionMode
{
 public const ORDINARY = 0;
 public const SKIP_LOCKED = 1;
 private function __construct()
 {
 }
}
