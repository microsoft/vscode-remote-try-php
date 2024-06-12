<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
use PDO;
final class ColumnCase
{
 public const UPPER = PDO::CASE_UPPER;
 public const LOWER = PDO::CASE_LOWER;
 private function __construct()
 {
 }
}
