<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
use ReturnTypeWillChange;
use function func_get_args;
use const PHP_VERSION_ID;
if (PHP_VERSION_ID >= 80000) {
 trait PDOStatementImplementations
 {
 #[\ReturnTypeWillChange]
 public function setFetchMode($mode, ...$args)
 {
 return $this->doSetFetchMode($mode, ...$args);
 }
 #[\ReturnTypeWillChange]
 public function fetchAll($mode = null, ...$args)
 {
 return $this->doFetchAll($mode, ...$args);
 }
 }
} else {
 trait PDOStatementImplementations
 {
 public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null) : bool
 {
 return $this->doSetFetchMode(...func_get_args());
 }
 public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null)
 {
 return $this->doFetchAll(...func_get_args());
 }
 }
}
