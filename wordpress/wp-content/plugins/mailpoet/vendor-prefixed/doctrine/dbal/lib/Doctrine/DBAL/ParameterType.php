<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
use PDO;
final class ParameterType
{
 public const NULL = PDO::PARAM_NULL;
 public const INTEGER = PDO::PARAM_INT;
 public const STRING = PDO::PARAM_STR;
 public const LARGE_OBJECT = PDO::PARAM_LOB;
 public const BOOLEAN = PDO::PARAM_BOOL;
 public const BINARY = 16;
 public const ASCII = 17;
 private function __construct()
 {
 }
}
