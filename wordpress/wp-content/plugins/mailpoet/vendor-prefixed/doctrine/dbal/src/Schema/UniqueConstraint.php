<?php
namespace MailPoetVendor\Doctrine\DBAL\Schema;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use function array_keys;
use function array_map;
use function strtolower;
class UniqueConstraint extends AbstractAsset implements Constraint
{
 protected $columns = [];
 protected $flags = [];
 private array $options;
 public function __construct(string $name, array $columns, array $flags = [], array $options = [])
 {
 $this->_setName($name);
 $this->options = $options;
 foreach ($columns as $column) {
 $this->addColumn($column);
 }
 foreach ($flags as $flag) {
 $this->addFlag($flag);
 }
 }
 public function getColumns()
 {
 return array_keys($this->columns);
 }
 public function getQuotedColumns(AbstractPlatform $platform)
 {
 $columns = [];
 foreach ($this->columns as $column) {
 $columns[] = $column->getQuotedName($platform);
 }
 return $columns;
 }
 public function getUnquotedColumns() : array
 {
 return array_map([$this, 'trimQuotes'], $this->getColumns());
 }
 public function getFlags() : array
 {
 return array_keys($this->flags);
 }
 public function addFlag(string $flag) : UniqueConstraint
 {
 $this->flags[strtolower($flag)] = \true;
 return $this;
 }
 public function hasFlag(string $flag) : bool
 {
 return isset($this->flags[strtolower($flag)]);
 }
 public function removeFlag(string $flag) : void
 {
 unset($this->flags[strtolower($flag)]);
 }
 public function hasOption(string $name) : bool
 {
 return isset($this->options[strtolower($name)]);
 }
 public function getOption(string $name)
 {
 return $this->options[strtolower($name)];
 }
 public function getOptions() : array
 {
 return $this->options;
 }
 protected function addColumn(string $column) : void
 {
 $this->columns[$column] = new Identifier($column);
 }
}
