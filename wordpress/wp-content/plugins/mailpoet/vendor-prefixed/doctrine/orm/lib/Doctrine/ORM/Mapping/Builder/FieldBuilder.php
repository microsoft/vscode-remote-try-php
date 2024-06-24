<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping\Builder;
if (!defined('ABSPATH')) exit;
use function constant;
class FieldBuilder
{
 private $builder;
 private $mapping;
 private $version;
 private $generatedValue;
 private $sequenceDef;
 private $customIdGenerator;
 public function __construct(ClassMetadataBuilder $builder, array $mapping)
 {
 $this->builder = $builder;
 $this->mapping = $mapping;
 }
 public function length($length)
 {
 $this->mapping['length'] = $length;
 return $this;
 }
 public function nullable($flag = \true)
 {
 $this->mapping['nullable'] = (bool) $flag;
 return $this;
 }
 public function unique($flag = \true)
 {
 $this->mapping['unique'] = (bool) $flag;
 return $this;
 }
 public function columnName($name)
 {
 $this->mapping['columnName'] = $name;
 return $this;
 }
 public function precision($p)
 {
 $this->mapping['precision'] = $p;
 return $this;
 }
 public function insertable(bool $flag = \true) : self
 {
 if (!$flag) {
 $this->mapping['notInsertable'] = \true;
 }
 return $this;
 }
 public function updatable(bool $flag = \true) : self
 {
 if (!$flag) {
 $this->mapping['notUpdatable'] = \true;
 }
 return $this;
 }
 public function scale($s)
 {
 $this->mapping['scale'] = $s;
 return $this;
 }
 public function isPrimaryKey()
 {
 return $this->makePrimaryKey();
 }
 public function makePrimaryKey()
 {
 $this->mapping['id'] = \true;
 return $this;
 }
 public function option($name, $value)
 {
 $this->mapping['options'][$name] = $value;
 return $this;
 }
 public function generatedValue($strategy = 'AUTO')
 {
 $this->generatedValue = $strategy;
 return $this;
 }
 public function isVersionField()
 {
 $this->version = \true;
 return $this;
 }
 public function setSequenceGenerator($sequenceName, $allocationSize = 1, $initialValue = 1)
 {
 $this->sequenceDef = ['sequenceName' => $sequenceName, 'allocationSize' => $allocationSize, 'initialValue' => $initialValue];
 return $this;
 }
 public function columnDefinition($def)
 {
 $this->mapping['columnDefinition'] = $def;
 return $this;
 }
 public function setCustomIdGenerator($customIdGenerator)
 {
 $this->customIdGenerator = (string) $customIdGenerator;
 return $this;
 }
 public function build()
 {
 $cm = $this->builder->getClassMetadata();
 if ($this->generatedValue) {
 $cm->setIdGeneratorType(constant('MailPoetVendor\\Doctrine\\ORM\\Mapping\\ClassMetadata::GENERATOR_TYPE_' . $this->generatedValue));
 }
 if ($this->version) {
 $cm->setVersionMapping($this->mapping);
 }
 $cm->mapField($this->mapping);
 if ($this->sequenceDef) {
 $cm->setSequenceGeneratorDefinition($this->sequenceDef);
 }
 if ($this->customIdGenerator) {
 $cm->setCustomGeneratorDefinition(['class' => $this->customIdGenerator]);
 }
 return $this->builder;
 }
}
