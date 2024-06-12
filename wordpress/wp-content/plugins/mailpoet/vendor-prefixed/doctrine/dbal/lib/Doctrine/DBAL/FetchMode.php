<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
use PDO;
final class FetchMode
{
 public const ASSOCIATIVE = PDO::FETCH_ASSOC;
 public const NUMERIC = PDO::FETCH_NUM;
 public const MIXED = PDO::FETCH_BOTH;
 public const STANDARD_OBJECT = PDO::FETCH_OBJ;
 public const COLUMN = PDO::FETCH_COLUMN;
 public const CUSTOM_OBJECT = PDO::FETCH_CLASS;
 private function __construct()
 {
 }
}
