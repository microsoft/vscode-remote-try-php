<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Types;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Exception;
use function spl_object_id;
final class TypeRegistry
{
 private array $instances;
 private array $instancesReverseIndex;
 public function __construct(array $instances = [])
 {
 $this->instances = [];
 $this->instancesReverseIndex = [];
 foreach ($instances as $name => $type) {
 $this->register($name, $type);
 }
 }
 public function get(string $name) : Type
 {
 $type = $this->instances[$name] ?? null;
 if ($type === null) {
 throw Exception::unknownColumnType($name);
 }
 return $type;
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
 $this->instancesReverseIndex[spl_object_id($type)] = $name;
 }
 public function override(string $name, Type $type) : void
 {
 $origType = $this->instances[$name] ?? null;
 if ($origType === null) {
 throw Exception::typeNotFound($name);
 }
 if (($this->findTypeName($type) ?? $name) !== $name) {
 throw Exception::typeAlreadyRegistered($type);
 }
 unset($this->instancesReverseIndex[spl_object_id($origType)]);
 $this->instances[$name] = $type;
 $this->instancesReverseIndex[spl_object_id($type)] = $name;
 }
 public function getMap() : array
 {
 return $this->instances;
 }
 private function findTypeName(Type $type) : ?string
 {
 return $this->instancesReverseIndex[spl_object_id($type)] ?? null;
 }
}
