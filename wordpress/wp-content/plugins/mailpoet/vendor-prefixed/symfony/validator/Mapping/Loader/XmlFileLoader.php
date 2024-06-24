<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Loader;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Config\Util\XmlUtils;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\MappingException;
use MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadata;
class XmlFileLoader extends FileLoader
{
 protected $classes;
 public function loadClassMetadata(ClassMetadata $metadata)
 {
 if (null === $this->classes) {
 $this->loadClassesFromXml();
 }
 if (isset($this->classes[$metadata->getClassName()])) {
 $classDescription = $this->classes[$metadata->getClassName()];
 $this->loadClassMetadataFromXml($metadata, $classDescription);
 return \true;
 }
 return \false;
 }
 public function getMappedClasses()
 {
 if (null === $this->classes) {
 $this->loadClassesFromXml();
 }
 return \array_keys($this->classes);
 }
 protected function parseConstraints(\SimpleXMLElement $nodes)
 {
 $constraints = [];
 foreach ($nodes as $node) {
 if (\count($node) > 0) {
 if (\count($node->value) > 0) {
 $options = $this->parseValues($node->value);
 } elseif (\count($node->constraint) > 0) {
 $options = $this->parseConstraints($node->constraint);
 } elseif (\count($node->option) > 0) {
 $options = $this->parseOptions($node->option);
 } else {
 $options = [];
 }
 } elseif ('' !== (string) $node) {
 $options = XmlUtils::phpize(\trim($node));
 } else {
 $options = null;
 }
 $constraints[] = $this->newConstraint((string) $node['name'], $options);
 }
 return $constraints;
 }
 protected function parseValues(\SimpleXMLElement $nodes)
 {
 $values = [];
 foreach ($nodes as $node) {
 if (\count($node) > 0) {
 if (\count($node->value) > 0) {
 $value = $this->parseValues($node->value);
 } elseif (\count($node->constraint) > 0) {
 $value = $this->parseConstraints($node->constraint);
 } else {
 $value = [];
 }
 } else {
 $value = \trim($node);
 }
 if (isset($node['key'])) {
 $values[(string) $node['key']] = $value;
 } else {
 $values[] = $value;
 }
 }
 return $values;
 }
 protected function parseOptions(\SimpleXMLElement $nodes)
 {
 $options = [];
 foreach ($nodes as $node) {
 if (\count($node) > 0) {
 if (\count($node->value) > 0) {
 $value = $this->parseValues($node->value);
 } elseif (\count($node->constraint) > 0) {
 $value = $this->parseConstraints($node->constraint);
 } else {
 $value = [];
 }
 } else {
 $value = XmlUtils::phpize($node);
 if (\is_string($value)) {
 $value = \trim($value);
 }
 }
 $options[(string) $node['name']] = $value;
 }
 return $options;
 }
 protected function parseFile(string $path)
 {
 try {
 $dom = XmlUtils::loadFile($path, __DIR__ . '/schema/dic/constraint-mapping/constraint-mapping-1.0.xsd');
 } catch (\Exception $e) {
 throw new MappingException($e->getMessage(), $e->getCode(), $e);
 }
 return \simplexml_import_dom($dom);
 }
 private function loadClassesFromXml()
 {
 // This method may throw an exception. Do not modify the class'
 // state before it completes
 $xml = $this->parseFile($this->file);
 $this->classes = [];
 foreach ($xml->namespace as $namespace) {
 $this->addNamespaceAlias((string) $namespace['prefix'], \trim((string) $namespace));
 }
 foreach ($xml->class as $class) {
 $this->classes[(string) $class['name']] = $class;
 }
 }
 private function loadClassMetadataFromXml(ClassMetadata $metadata, \SimpleXMLElement $classDescription)
 {
 if (\count($classDescription->{'group-sequence-provider'}) > 0) {
 $metadata->setGroupSequenceProvider(\true);
 }
 foreach ($classDescription->{'group-sequence'} as $groupSequence) {
 if (\count($groupSequence->value) > 0) {
 $metadata->setGroupSequence($this->parseValues($groupSequence[0]->value));
 }
 }
 foreach ($this->parseConstraints($classDescription->constraint) as $constraint) {
 $metadata->addConstraint($constraint);
 }
 foreach ($classDescription->property as $property) {
 foreach ($this->parseConstraints($property->constraint) as $constraint) {
 $metadata->addPropertyConstraint((string) $property['name'], $constraint);
 }
 }
 foreach ($classDescription->getter as $getter) {
 foreach ($this->parseConstraints($getter->constraint) as $constraint) {
 $metadata->addGetterConstraint((string) $getter['property'], $constraint);
 }
 }
 }
}
