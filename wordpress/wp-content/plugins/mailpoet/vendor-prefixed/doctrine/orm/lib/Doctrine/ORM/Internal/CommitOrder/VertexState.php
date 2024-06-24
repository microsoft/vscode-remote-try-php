<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Internal\CommitOrder;
if (!defined('ABSPATH')) exit;
final class VertexState
{
 public const NOT_VISITED = 0;
 public const IN_PROGRESS = 1;
 public const VISITED = 2;
 private function __construct()
 {
 }
}
