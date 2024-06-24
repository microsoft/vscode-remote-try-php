<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Loader;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadata;
use MailPoetVendor\Symfony\Component\Yaml\Exception\ParseException;
use MailPoetVendor\Symfony\Component\Yaml\Parser as YamlParser;
use MailPoetVendor\Symfony\Component\Yaml\Yaml;
class YamlFileLoader extends FileLoader
{
 protected $classes = null;
 private $yamlParser;
 public function loadClassMetadata(ClassMetadata $metadata)
 {
 if (null === $this->classes) {
 $this->loadClassesFromYaml();
 }
 if (isset($this->classes[$metadata->getClassName()])) {
 $classDescription = $this->classes[$metadata->getClassName()];
 $this->loadClassMetadataFromYaml($metadata, $classDescription);
 return \true;
 }
 return \false;
 }
 public function getMappedClasses()
 {
 if (null === $this->classes) {
 $this->loadClassesFromYaml();
 }
 return \array_keys($this->classes);
 }
 protected function parseNodes(array $nodes)
 {
 $values = [];
 foreach ($nodes as $name => $childNodes) {
 if (\is_numeric($name) && \is_array($childNodes) && 1 === \count($childNodes)) {
 $options = \current($childNodes);
 if (\is_array($options)) {
 $options = $this->parseNodes($options);
 }
 $values[] = $this->newConstraint(\key($childNodes), $options);
 } else {
 if (\is_array($childNodes)) {
 $childNodes = $this->parseNodes($childNodes);
 }
 $values[$name] = $childNodes;
 }
 }
 return $values;
 }
 private function parseFile(string $path) : array
 {
 try {
 $classes = $this->yamlParser->parseFile($path, Yaml::PARSE_CONSTANT);
 } catch (ParseException $e) {
 throw new \InvalidArgumentException(\sprintf('The file "%s" does not contain valid YAML: ', $path) . $e->getMessage(), 0, $e);
 }
 // empty file
 if (null === $classes) {
 return [];
 }
 // not an array
 if (!\is_array($classes)) {
 throw new \InvalidArgumentException(\sprintf('The file "%s" must contain a YAML array.', $this->file));
 }
 return $classes;
 }
 private function loadClassesFromYaml()
 {
 if (null === $this->yamlParser) {
 $this->yamlParser = new YamlParser();
 }
 $this->classes = $this->parseFile($this->file);
 if (isset($this->classes['namespaces'])) {
 foreach ($this->classes['namespaces'] as $alias => $namespace) {
 $this->addNamespaceAlias($alias, $namespace);
 }
 unset($this->classes['namespaces']);
 }
 }
 private function loadClassMetadataFromYaml(ClassMetadata $metadata, array $classDescription)
 {
 if (isset($classDescription['group_sequence_provider'])) {
 $metadata->setGroupSequenceProvider((bool) $classDescription['group_sequence_provider']);
 }
 if (isset($classDescription['group_sequence'])) {
 $metadata->setGroupSequence($classDescription['group_sequence']);
 }
 if (isset($classDescription['constraints']) && \is_array($classDescription['constraints'])) {
 foreach ($this->parseNodes($classDescription['constraints']) as $constraint) {
 $metadata->addConstraint($constraint);
 }
 }
 if (isset($classDescription['properties']) && \is_array($classDescription['properties'])) {
 foreach ($classDescription['properties'] as $property => $constraints) {
 if (null !== $constraints) {
 foreach ($this->parseNodes($constraints) as $constraint) {
 $metadata->addPropertyConstraint($property, $constraint);
 }
 }
 }
 }
 if (isset($classDescription['getters']) && \is_array($classDescription['getters'])) {
 foreach ($classDescription['getters'] as $getter => $constraints) {
 if (null !== $constraints) {
 foreach ($this->parseNodes($constraints) as $constraint) {
 $metadata->addGetterConstraint($getter, $constraint);
 }
 }
 }
 }
 }
}
