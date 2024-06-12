<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Exception;
use function array_search;
use function in_array;
final class TypeRegistry
{
 private $instances;
 public function __construct(array $instances = [])
 {
 $this->instances = $instances;
 }
 public function get(string $name) : Type
 {
 if (!isset($this->instances[$name])) {
 throw Exception::unknownColumnType($name);
 }
 return $this->instances[$name];
 }
 public function lookupName(Type $type) : string
 {
 $name = $this->findTypeName($type);
 if ($name === null) {
 throw Exception::typeNotRegistered($type);
 }
 return $name;
 }
 public function has(string $name) : bool
 {
 return isset($this->instances[$name]);
 }
 public function register(string $name, Type $type) : void
 {
 if (isset($this->instances[$name])) {
 throw Exception::typeExists($name);
 }
 if ($this->findTypeName($type) !== null) {
 throw Exception::typeAlreadyRegistered($type);
 }
 $this->instances[$name] = $type;
 }
 public function override(string $name, Type $type) : void
 {
 if (!isset($this->instances[$name])) {
 throw Exception::typeNotFound($name);
 }
 if (!in_array($this->findTypeName($type), [$name, null], \true)) {
 throw Exception::typeAlreadyRegistered($type);
 }
 $this->instances[$name] = $type;
 }
 public function getMap() : array
 {
 return $this->instances;
 }
 private function findTypeName(Type $type) : ?string
 {
 $name = array_search($type, $this->instances, \true);
 if ($name === \false) {
 return null;
 }
 return $name;
 }
}
