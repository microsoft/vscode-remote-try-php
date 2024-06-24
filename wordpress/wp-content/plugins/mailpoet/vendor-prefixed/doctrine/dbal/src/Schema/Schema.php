<?php
namespace MailPoetVendor\Doctrine\DBAL\Schema;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Exception;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\DBAL\Schema\Visitor\NamespaceVisitor;
use MailPoetVendor\Doctrine\DBAL\Schema\Visitor\Visitor;
use MailPoetVendor\Doctrine\DBAL\SQL\Builder\CreateSchemaObjectsSQLBuilder;
use MailPoetVendor\Doctrine\DBAL\SQL\Builder\DropSchemaObjectsSQLBuilder;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function array_keys;
use function strpos;
use function strtolower;
class Schema extends AbstractAsset
{
 private array $namespaces = [];
 protected $_tables = [];
 protected $_sequences = [];
 protected $_schemaConfig;
 public function __construct(array $tables = [], array $sequences = [], ?SchemaConfig $schemaConfig = null, array $namespaces = [])
 {
 $schemaConfig ??= new SchemaConfig();
 $this->_schemaConfig = $schemaConfig;
 $this->_setName($schemaConfig->getName() ?? 'public');
 foreach ($namespaces as $namespace) {
 $this->createNamespace($namespace);
 }
 foreach ($tables as $table) {
 $this->_addTable($table);
 }
 foreach ($sequences as $sequence) {
 $this->_addSequence($sequence);
 }
 }
 public function hasExplicitForeignKeyIndexes()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4822', 'Schema::hasExplicitForeignKeyIndexes() is deprecated.');
 return $this->_schemaConfig->hasExplicitForeignKeyIndexes();
 }
 protected function _addTable(Table $table)
 {
 $namespaceName = $table->getNamespaceName();
 $tableName = $this->normalizeName($table);
 if (isset($this->_tables[$tableName])) {
 throw SchemaException::tableAlreadyExists($tableName);
 }
 if ($namespaceName !== null && !$table->isInDefaultNamespace($this->getName()) && !$this->hasNamespace($namespaceName)) {
 $this->createNamespace($namespaceName);
 }
 $this->_tables[$tableName] = $table;
 $table->setSchemaConfig($this->_schemaConfig);
 }
 protected function _addSequence(Sequence $sequence)
 {
 $namespaceName = $sequence->getNamespaceName();
 $seqName = $this->normalizeName($sequence);
 if (isset($this->_sequences[$seqName])) {
 throw SchemaException::sequenceAlreadyExists($seqName);
 }
 if ($namespaceName !== null && !$sequence->isInDefaultNamespace($this->getName()) && !$this->hasNamespace($namespaceName)) {
 $this->createNamespace($namespaceName);
 }
 $this->_sequences[$seqName] = $sequence;
 }
 public function getNamespaces()
 {
 return $this->namespaces;
 }
 public function getTables()
 {
 return $this->_tables;
 }
 public function getTable($name)
 {
 $name = $this->getFullQualifiedAssetName($name);
 if (!isset($this->_tables[$name])) {
 throw SchemaException::tableDoesNotExist($name);
 }
 return $this->_tables[$name];
 }
 private function getFullQualifiedAssetName($name) : string
 {
 $name = $this->getUnquotedAssetName($name);
 if (strpos($name, '.') === \false) {
 $name = $this->getName() . '.' . $name;
 }
 return strtolower($name);
 }
 private function normalizeName(AbstractAsset $asset) : string
 {
 return $asset->getFullQualifiedName($this->getName());
 }
 private function getUnquotedAssetName($assetName) : string
 {
 if ($this->isIdentifierQuoted($assetName)) {
 return $this->trimQuotes($assetName);
 }
 return $assetName;
 }
 public function hasNamespace($name)
 {
 $name = strtolower($this->getUnquotedAssetName($name));
 return isset($this->namespaces[$name]);
 }
 public function hasTable($name)
 {
 $name = $this->getFullQualifiedAssetName($name);
 return isset($this->_tables[$name]);
 }
 public function getTableNames()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4800', 'Schema::getTableNames() is deprecated.' . ' Use Schema::getTables() and Table::getName() instead.', __METHOD__);
 return array_keys($this->_tables);
 }
 public function hasSequence($name)
 {
 $name = $this->getFullQualifiedAssetName($name);
 return isset($this->_sequences[$name]);
 }
 public function getSequence($name)
 {
 $name = $this->getFullQualifiedAssetName($name);
 if (!$this->hasSequence($name)) {
 throw SchemaException::sequenceDoesNotExist($name);
 }
 return $this->_sequences[$name];
 }
 public function getSequences()
 {
 return $this->_sequences;
 }
 public function createNamespace($name)
 {
 $unquotedName = strtolower($this->getUnquotedAssetName($name));
 if (isset($this->namespaces[$unquotedName])) {
 throw SchemaException::namespaceAlreadyExists($unquotedName);
 }
 $this->namespaces[$unquotedName] = $name;
 return $this;
 }
 public function createTable($name)
 {
 $table = new Table($name);
 $this->_addTable($table);
 foreach ($this->_schemaConfig->getDefaultTableOptions() as $option => $value) {
 $table->addOption($option, $value);
 }
 return $table;
 }
 public function renameTable($oldName, $newName)
 {
 $table = $this->getTable($oldName);
 $table->_setName($newName);
 $this->dropTable($oldName);
 $this->_addTable($table);
 return $this;
 }
 public function dropTable($name)
 {
 $name = $this->getFullQualifiedAssetName($name);
 $this->getTable($name);
 unset($this->_tables[$name]);
 return $this;
 }
 public function createSequence($name, $allocationSize = 1, $initialValue = 1)
 {
 $seq = new Sequence($name, $allocationSize, $initialValue);
 $this->_addSequence($seq);
 return $seq;
 }
 public function dropSequence($name)
 {
 $name = $this->getFullQualifiedAssetName($name);
 unset($this->_sequences[$name]);
 return $this;
 }
 public function toSql(AbstractPlatform $platform)
 {
 $builder = new CreateSchemaObjectsSQLBuilder($platform);
 return $builder->buildSQL($this);
 }
 public function toDropSql(AbstractPlatform $platform)
 {
 $builder = new DropSchemaObjectsSQLBuilder($platform);
 return $builder->buildSQL($this);
 }
 public function getMigrateToSql(Schema $toSchema, AbstractPlatform $platform)
 {
 $schemaDiff = (new Comparator())->compareSchemas($this, $toSchema);
 return $schemaDiff->toSql($platform);
 }
 public function getMigrateFromSql(Schema $fromSchema, AbstractPlatform $platform)
 {
 $schemaDiff = (new Comparator())->compareSchemas($fromSchema, $this);
 return $schemaDiff->toSql($platform);
 }
 public function visit(Visitor $visitor)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5435', 'Schema::visit() is deprecated.');
 $visitor->acceptSchema($this);
 if ($visitor instanceof NamespaceVisitor) {
 foreach ($this->namespaces as $namespace) {
 $visitor->acceptNamespace($namespace);
 }
 }
 foreach ($this->_tables as $table) {
 $table->visit($visitor);
 }
 foreach ($this->_sequences as $sequence) {
 $sequence->visit($visitor);
 }
 }
 public function __clone()
 {
 foreach ($this->_tables as $k => $table) {
 $this->_tables[$k] = clone $table;
 }
 foreach ($this->_sequences as $k => $sequence) {
 $this->_sequences[$k] = clone $sequence;
 }
 }
}
