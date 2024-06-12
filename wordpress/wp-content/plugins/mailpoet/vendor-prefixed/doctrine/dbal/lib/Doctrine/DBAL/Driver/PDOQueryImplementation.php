<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
use PDOStatement;
use ReturnTypeWillChange;
use function func_get_args;
use const PHP_VERSION_ID;
if (PHP_VERSION_ID >= 80000) {
 trait PDOQueryImplementation
 {
 #[\ReturnTypeWillChange]
 public function query(?string $query = null, ?int $fetchMode = null, mixed ...$fetchModeArgs)
 {
 return $this->doQuery($query, $fetchMode, ...$fetchModeArgs);
 }
 }
} else {
 trait PDOQueryImplementation
 {
 public function query()
 {
 return $this->doQuery(...func_get_args());
 }
 }
}
