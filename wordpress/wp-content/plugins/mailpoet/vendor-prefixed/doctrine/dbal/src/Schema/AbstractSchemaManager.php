<?php
namespace MailPoetVendor\Doctrine\DBAL\Schema;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Event\SchemaColumnDefinitionEventArgs;
use MailPoetVendor\Doctrine\DBAL\Event\SchemaIndexDefinitionEventArgs;
use MailPoetVendor\Doctrine\DBAL\Events;
use MailPoetVendor\Doctrine\DBAL\Exception;
use MailPoetVendor\Doctrine\DBAL\Exception\DatabaseRequired;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\DBAL\Result;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use Throwable;
use function array_filter;
use function array_intersect;
use function array_map;
use function array_values;
use function assert;
use function call_user_func_array;
use function count;
use function func_get_args;
use function is_callable;
use function is_string;
use function preg_match;
use function str_replace;
use function strtolower;
abstract class AbstractSchemaManager
{
 protected $_conn;
 protected $_platform;
 public function __construct(Connection $connection, AbstractPlatform $platform)
 {
 $this->_conn = $connection;
 $this->_platform = $platform;
 }
 public function getDatabasePlatform()
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5387', 'AbstractSchemaManager::getDatabasePlatform() is deprecated.' . ' Use Connection::getDatabasePlatform() instead.');
 return $this->_platform;
 }
 public function tryMethod()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4897', 'AbstractSchemaManager::tryMethod() is deprecated.');
 $args = func_get_args();
 $method = $args[0];
 unset($args[0]);
 $args = array_values($args);
 $callback = [$this, $method];
 assert(is_callable($callback));
 try {
 return call_user_func_array($callback, $args);
 } catch (Throwable $e) {
 return \false;
 }
 }
 public function listDatabases()
 {
 $sql = $this->_platform->getListDatabasesSQL();
 $databases = $this->_conn->fetchAllAssociative($sql);
 return $this->_getPortableDatabasesList($databases);
 }
 public function listNamespaceNames()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4503', 'AbstractSchemaManager::listNamespaceNames() is deprecated,' . ' use AbstractSchemaManager::listSchemaNames() instead.');
 $sql = $this->_platform->getListNamespacesSQL();
 $namespaces = $this->_conn->fetchAllAssociative($sql);
 return $this->getPortableNamespacesList($namespaces);
 }
 public function listSchemaNames() : array
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function listSequences($database = null)
 {
 if ($database === null) {
 $database = $this->getDatabase(__METHOD__);
 } else {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/5284', 'Passing $database to AbstractSchemaManager::listSequences() is deprecated.');
 }
 $sql = $this->_platform->getListSequencesSQL($database);
 $sequences = $this->_conn->fetchAllAssociative($sql);
 return $this->filterAssetNames($this->_getPortableSequencesList($sequences));
 }
 public function listTableColumns($table, $database = null)
 {
 if ($database === null) {
 $database = $this->getDatabase(__METHOD__);
 } else {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/5284', 'Passing $database to AbstractSchemaManager::listTableColumns() is deprecated.');
 }
 $sql = $this->_platform->getListTableColumnsSQL($table, $database);
 $tableColumns = $this->_conn->fetchAllAssociative($sql);
 return $this->_getPortableTableColumnList($table, $database, $tableColumns);
 }
 protected function doListTableColumns($table, $database = null) : array
 {
 if ($database === null) {
 $database = $this->getDatabase(__METHOD__);
 } else {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/5284', 'Passing $database to AbstractSchemaManager::doListTableColumns() is deprecated.');
 }
 return $this->_getPortableTableColumnList($table, $database, $this->selectTableColumns($database, $this->normalizeName($table))->fetchAllAssociative());
 }
 public function listTableIndexes($table)
 {
 $sql = $this->_platform->getListTableIndexesSQL($table, $this->_conn->getDatabase());
 $tableIndexes = $this->_conn->fetchAllAssociative($sql);
 return $this->_getPortableTableIndexesList($tableIndexes, $table);
 }
 protected function doListTableIndexes($table) : array
 {
 $database = $this->getDatabase(__METHOD__);
 $table = $this->normalizeName($table);
 return $this->_getPortableTableIndexesList($this->selectIndexColumns($database, $table)->fetchAllAssociative(), $table);
 }
 public function tablesExist($names)
 {
 if (is_string($names)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3580', 'The usage of a string $tableNames in AbstractSchemaManager::tablesExist() is deprecated. ' . 'Pass a one-element array instead.');
 }
 $names = array_map('strtolower', (array) $names);
 return count($names) === count(array_intersect($names, array_map('strtolower', $this->listTableNames())));
 }
 public function listTableNames()
 {
 $sql = $this->_platform->getListTablesSQL();
 $tables = $this->_conn->fetchAllAssociative($sql);
 $tableNames = $this->_getPortableTablesList($tables);
 return $this->filterAssetNames($tableNames);
 }
 protected function doListTableNames() : array
 {
 $database = $this->getDatabase(__METHOD__);
 return $this->filterAssetNames($this->_getPortableTablesList($this->selectTableNames($database)->fetchAllAssociative()));
 }
 protected function filterAssetNames($assetNames)
 {
 $filter = $this->_conn->getConfiguration()->getSchemaAssetsFilter();
 if ($filter === null) {
 return $assetNames;
 }
 return array_values(array_filter($assetNames, $filter));
 }
 public function listTables()
 {
 $tableNames = $this->listTableNames();
 $tables = [];
 foreach ($tableNames as $tableName) {
 $tables[] = $this->introspectTable($tableName);
 }
 return $tables;
 }
 protected function doListTables() : array
 {
 $database = $this->getDatabase(__METHOD__);
 $tableColumnsByTable = $this->fetchTableColumnsByTable($database);
 $indexColumnsByTable = $this->fetchIndexColumnsByTable($database);
 $foreignKeyColumnsByTable = $this->fetchForeignKeyColumnsByTable($database);
 $tableOptionsByTable = $this->fetchTableOptionsByTable($database);
 $filter = $this->_conn->getConfiguration()->getSchemaAssetsFilter();
 $tables = [];
 foreach ($tableColumnsByTable as $tableName => $tableColumns) {
 if ($filter !== null && !$filter($tableName)) {
 continue;
 }
 $tables[] = new Table($tableName, $this->_getPortableTableColumnList($tableName, $database, $tableColumns), $this->_getPortableTableIndexesList($indexColumnsByTable[$tableName] ?? [], $tableName), [], $this->_getPortableTableForeignKeysList($foreignKeyColumnsByTable[$tableName] ?? []), $tableOptionsByTable[$tableName] ?? []);
 }
 return $tables;
 }
 public function listTableDetails($name)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5595', '%s is deprecated. Use introspectTable() instead.', __METHOD__);
 $columns = $this->listTableColumns($name);
 $foreignKeys = [];
 if ($this->_platform->supportsForeignKeyConstraints()) {
 $foreignKeys = $this->listTableForeignKeys($name);
 }
 $indexes = $this->listTableIndexes($name);
 return new Table($name, $columns, $indexes, [], $foreignKeys);
 }
 protected function doListTableDetails($name) : Table
 {
 $database = $this->getDatabase(__METHOD__);
 $normalizedName = $this->normalizeName($name);
 $tableOptionsByTable = $this->fetchTableOptionsByTable($database, $normalizedName);
 if ($this->_platform->supportsForeignKeyConstraints()) {
 $foreignKeys = $this->listTableForeignKeys($name);
 } else {
 $foreignKeys = [];
 }
 return new Table($name, $this->listTableColumns($name, $database), $this->listTableIndexes($name), [], $foreignKeys, $tableOptionsByTable[$normalizedName] ?? []);
 }
 protected function normalizeName(string $name) : string
 {
 $identifier = new Identifier($name);
 return $identifier->getName();
 }
 protected function selectTableNames(string $databaseName) : Result
 {
 throw Exception::notSupported(__METHOD__);
 }
 protected function selectTableColumns(string $databaseName, ?string $tableName = null) : Result
 {
 throw Exception::notSupported(__METHOD__);
 }
 protected function selectIndexColumns(string $databaseName, ?string $tableName = null) : Result
 {
 throw Exception::notSupported(__METHOD__);
 }
 protected function selectForeignKeyColumns(string $databaseName, ?string $tableName = null) : Result
 {
 throw Exception::notSupported(__METHOD__);
 }
 protected function fetchTableColumnsByTable(string $databaseName) : array
 {
 return $this->fetchAllAssociativeGrouped($this->selectTableColumns($databaseName));
 }
 protected function fetchIndexColumnsByTable(string $databaseName) : array
 {
 return $this->fetchAllAssociativeGrouped($this->selectIndexColumns($databaseName));
 }
 protected function fetchForeignKeyColumnsByTable(string $databaseName) : array
 {
 if (!$this->_platform->supportsForeignKeyConstraints()) {
 return [];
 }
 return $this->fetchAllAssociativeGrouped($this->selectForeignKeyColumns($databaseName));
 }
 protected function fetchTableOptionsByTable(string $databaseName, ?string $tableName = null) : array
 {
 throw Exception::notSupported(__METHOD__);
 }
 public function introspectTable(string $name) : Table
 {
 $table = $this->listTableDetails($name);
 if ($table->getColumns() === []) {
 throw SchemaException::tableDoesNotExist($name);
 }
 return $table;
 }
 public function listViews()
 {
 $database = $this->_conn->getDatabase();
 $sql = $this->_platform->getListViewsSQL($database);
 $views = $this->_conn->fetchAllAssociative($sql);
 return $this->_getPortableViewsList($views);
 }
 public function listTableForeignKeys($table, $database = null)
 {
 if ($database === null) {
 $database = $this->getDatabase(__METHOD__);
 } else {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/5284', 'Passing $database to AbstractSchemaManager::listTableForeignKeys() is deprecated.');
 }
 $sql = $this->_platform->getListTableForeignKeysSQL($table, $database);
 $tableForeignKeys = $this->_conn->fetchAllAssociative($sql);
 return $this->_getPortableTableForeignKeysList($tableForeignKeys);
 }
 protected function doListTableForeignKeys($table, $database = null) : array
 {
 if ($database === null) {
 $database = $this->getDatabase(__METHOD__);
 } else {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/5284', 'Passing $database to AbstractSchemaManager::listTableForeignKeys() is deprecated.');
 }
 return $this->_getPortableTableForeignKeysList($this->selectForeignKeyColumns($database, $this->normalizeName($table))->fetchAllAssociative());
 }
 public function dropDatabase($database)
 {
 $this->_conn->executeStatement($this->_platform->getDropDatabaseSQL($database));
 }
 public function dropSchema(string $schemaName) : void
 {
 $this->_conn->executeStatement($this->_platform->getDropSchemaSQL($schemaName));
 }
 public function dropTable($name)
 {
 $this->_conn->executeStatement($this->_platform->getDropTableSQL($name));
 }
 public function dropIndex($index, $table)
 {
 if ($index instanceof Index) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4798', 'Passing $index as an Index object to %s is deprecated. Pass it as a quoted name instead.', __METHOD__);
 $index = $index->getQuotedName($this->_platform);
 }
 if ($table instanceof Table) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4798', 'Passing $table as an Table object to %s is deprecated. Pass it as a quoted name instead.', __METHOD__);
 $table = $table->getQuotedName($this->_platform);
 }
 $this->_conn->executeStatement($this->_platform->getDropIndexSQL($index, $table));
 }
 public function dropConstraint(Constraint $constraint, $table)
 {
 if ($table instanceof Table) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4798', 'Passing $table as a Table object to %s is deprecated. Pass it as a quoted name instead.', __METHOD__);
 $table = $table->getQuotedName($this->_platform);
 }
 $this->_conn->executeStatement($this->_platform->getDropConstraintSQL($constraint->getQuotedName($this->_platform), $table));
 }
 public function dropForeignKey($foreignKey, $table)
 {
 if ($foreignKey instanceof ForeignKeyConstraint) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4798', 'Passing $foreignKey as a ForeignKeyConstraint object to %s is deprecated.' . ' Pass it as a quoted name instead.', __METHOD__);
 $foreignKey = $foreignKey->getQuotedName($this->_platform);
 }
 if ($table instanceof Table) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4798', 'Passing $table as a Table object to %s is deprecated. Pass it as a quoted name instead.', __METHOD__);
 $table = $table->getQuotedName($this->_platform);
 }
 $this->_conn->executeStatement($this->_platform->getDropForeignKeySQL($foreignKey, $table));
 }
 public function dropSequence($name)
 {
 $this->_conn->executeStatement($this->_platform->getDropSequenceSQL($name));
 }
 public function dropUniqueConstraint(string $name, string $tableName) : void
 {
 $this->_conn->executeStatement($this->_platform->getDropUniqueConstraintSQL($name, $tableName));
 }
 public function dropView($name)
 {
 $this->_conn->executeStatement($this->_platform->getDropViewSQL($name));
 }
 public function createSchemaObjects(Schema $schema) : void
 {
 $this->_execSql($schema->toSql($this->_platform));
 }
 public function createDatabase($database)
 {
 $this->_conn->executeStatement($this->_platform->getCreateDatabaseSQL($database));
 }
 public function createTable(Table $table)
 {
 $createFlags = AbstractPlatform::CREATE_INDEXES | AbstractPlatform::CREATE_FOREIGNKEYS;
 $this->_execSql($this->_platform->getCreateTableSQL($table, $createFlags));
 }
 public function createSequence($sequence)
 {
 $this->_conn->executeStatement($this->_platform->getCreateSequenceSQL($sequence));
 }
 public function createConstraint(Constraint $constraint, $table)
 {
 $this->_conn->executeStatement($this->_platform->getCreateConstraintSQL($constraint, $table));
 }
 public function createIndex(Index $index, $table)
 {
 $this->_conn->executeStatement($this->_platform->getCreateIndexSQL($index, $table));
 }
 public function createForeignKey(ForeignKeyConstraint $foreignKey, $table)
 {
 $this->_conn->executeStatement($this->_platform->getCreateForeignKeySQL($foreignKey, $table));
 }
 public function createUniqueConstraint(UniqueConstraint $uniqueConstraint, string $tableName) : void
 {
 $this->_conn->executeStatement($this->_platform->getCreateUniqueConstraintSQL($uniqueConstraint, $tableName));
 }
 public function createView(View $view)
 {
 $this->_conn->executeStatement($this->_platform->getCreateViewSQL($view->getQuotedName($this->_platform), $view->getSql()));
 }
 public function dropSchemaObjects(Schema $schema) : void
 {
 $this->_execSql($schema->toDropSql($this->_platform));
 }
 public function dropAndCreateConstraint(Constraint $constraint, $table)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4897', 'AbstractSchemaManager::dropAndCreateConstraint() is deprecated.' . ' Use AbstractSchemaManager::dropIndex() and AbstractSchemaManager::createIndex(),' . ' AbstractSchemaManager::dropForeignKey() and AbstractSchemaManager::createForeignKey()' . ' or AbstractSchemaManager::dropUniqueConstraint()' . ' and AbstractSchemaManager::createUniqueConstraint() instead.');
 $this->tryMethod('dropConstraint', $constraint, $table);
 $this->createConstraint($constraint, $table);
 }
 public function dropAndCreateIndex(Index $index, $table)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4897', 'AbstractSchemaManager::dropAndCreateIndex() is deprecated.' . ' Use AbstractSchemaManager::dropIndex() and AbstractSchemaManager::createIndex() instead.');
 $this->tryMethod('dropIndex', $index->getQuotedName($this->_platform), $table);
 $this->createIndex($index, $table);
 }
 public function dropAndCreateForeignKey(ForeignKeyConstraint $foreignKey, $table)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4897', 'AbstractSchemaManager::dropAndCreateForeignKey() is deprecated.' . ' Use AbstractSchemaManager::dropForeignKey() and AbstractSchemaManager::createForeignKey() instead.');
 $this->tryMethod('dropForeignKey', $foreignKey, $table);
 $this->createForeignKey($foreignKey, $table);
 }
 public function dropAndCreateSequence(Sequence $sequence)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4897', 'AbstractSchemaManager::dropAndCreateSequence() is deprecated.' . ' Use AbstractSchemaManager::dropSequence() and AbstractSchemaManager::createSequence() instead.');
 $this->tryMethod('dropSequence', $sequence->getQuotedName($this->_platform));
 $this->createSequence($sequence);
 }
 public function dropAndCreateTable(Table $table)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4897', 'AbstractSchemaManager::dropAndCreateTable() is deprecated.' . ' Use AbstractSchemaManager::dropTable() and AbstractSchemaManager::createTable() instead.');
 $this->tryMethod('dropTable', $table->getQuotedName($this->_platform));
 $this->createTable($table);
 }
 public function dropAndCreateDatabase($database)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4897', 'AbstractSchemaManager::dropAndCreateDatabase() is deprecated.' . ' Use AbstractSchemaManager::dropDatabase() and AbstractSchemaManager::createDatabase() instead.');
 $this->tryMethod('dropDatabase', $database);
 $this->createDatabase($database);
 }
 public function dropAndCreateView(View $view)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4897', 'AbstractSchemaManager::dropAndCreateView() is deprecated.' . ' Use AbstractSchemaManager::dropView() and AbstractSchemaManager::createView() instead.');
 $this->tryMethod('dropView', $view->getQuotedName($this->_platform));
 $this->createView($view);
 }
 public function alterSchema(SchemaDiff $schemaDiff) : void
 {
 $this->_execSql($this->_platform->getAlterSchemaSQL($schemaDiff));
 }
 public function migrateSchema(Schema $toSchema) : void
 {
 $schemaDiff = $this->createComparator()->compareSchemas($this->introspectSchema(), $toSchema);
 $this->alterSchema($schemaDiff);
 }
 public function alterTable(TableDiff $tableDiff)
 {
 $this->_execSql($this->_platform->getAlterTableSQL($tableDiff));
 }
 public function renameTable($name, $newName)
 {
 $this->_execSql($this->_platform->getRenameTableSQL($name, $newName));
 }
 protected function _getPortableDatabasesList($databases)
 {
 $list = [];
 foreach ($databases as $value) {
 $list[] = $this->_getPortableDatabaseDefinition($value);
 }
 return $list;
 }
 protected function getPortableNamespacesList(array $namespaces)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4503', 'AbstractSchemaManager::getPortableNamespacesList() is deprecated,' . ' use AbstractSchemaManager::listSchemaNames() instead.');
 $namespacesList = [];
 foreach ($namespaces as $namespace) {
 $namespacesList[] = $this->getPortableNamespaceDefinition($namespace);
 }
 return $namespacesList;
 }
 protected function _getPortableDatabaseDefinition($database)
 {
 return $database;
 }
 protected function getPortableNamespaceDefinition(array $namespace)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4503', 'AbstractSchemaManager::getPortableNamespaceDefinition() is deprecated,' . ' use AbstractSchemaManager::listSchemaNames() instead.');
 return $namespace;
 }
 protected function _getPortableSequencesList($sequences)
 {
 $list = [];
 foreach ($sequences as $value) {
 $list[] = $this->_getPortableSequenceDefinition($value);
 }
 return $list;
 }
 protected function _getPortableSequenceDefinition($sequence)
 {
 throw Exception::notSupported('Sequences');
 }
 protected function _getPortableTableColumnList($table, $database, $tableColumns)
 {
 $eventManager = $this->_platform->getEventManager();
 $list = [];
 foreach ($tableColumns as $tableColumn) {
 $column = null;
 $defaultPrevented = \false;
 if ($eventManager !== null && $eventManager->hasListeners(Events::onSchemaColumnDefinition)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/5784', 'Subscribing to %s events is deprecated. Use a custom schema manager instead.', Events::onSchemaColumnDefinition);
 $eventArgs = new SchemaColumnDefinitionEventArgs($tableColumn, $table, $database, $this->_conn);
 $eventManager->dispatchEvent(Events::onSchemaColumnDefinition, $eventArgs);
 $defaultPrevented = $eventArgs->isDefaultPrevented();
 $column = $eventArgs->getColumn();
 }
 if (!$defaultPrevented) {
 $column = $this->_getPortableTableColumnDefinition($tableColumn);
 }
 if ($column === null) {
 continue;
 }
 $name = strtolower($column->getQuotedName($this->_platform));
 $list[$name] = $column;
 }
 return $list;
 }
 protected abstract function _getPortableTableColumnDefinition($tableColumn);
 protected function _getPortableTableIndexesList($tableIndexes, $tableName = null)
 {
 $result = [];
 foreach ($tableIndexes as $tableIndex) {
 $indexName = $keyName = $tableIndex['key_name'];
 if ($tableIndex['primary']) {
 $keyName = 'primary';
 }
 $keyName = strtolower($keyName);
 if (!isset($result[$keyName])) {
 $options = ['lengths' => []];
 if (isset($tableIndex['where'])) {
 $options['where'] = $tableIndex['where'];
 }
 $result[$keyName] = ['name' => $indexName, 'columns' => [], 'unique' => !$tableIndex['non_unique'], 'primary' => $tableIndex['primary'], 'flags' => $tableIndex['flags'] ?? [], 'options' => $options];
 }
 $result[$keyName]['columns'][] = $tableIndex['column_name'];
 $result[$keyName]['options']['lengths'][] = $tableIndex['length'] ?? null;
 }
 $eventManager = $this->_platform->getEventManager();
 $indexes = [];
 foreach ($result as $indexKey => $data) {
 $index = null;
 $defaultPrevented = \false;
 if ($eventManager !== null && $eventManager->hasListeners(Events::onSchemaIndexDefinition)) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/5784', 'Subscribing to %s events is deprecated. Use a custom schema manager instead.', Events::onSchemaColumnDefinition);
 $eventArgs = new SchemaIndexDefinitionEventArgs($data, $tableName, $this->_conn);
 $eventManager->dispatchEvent(Events::onSchemaIndexDefinition, $eventArgs);
 $defaultPrevented = $eventArgs->isDefaultPrevented();
 $index = $eventArgs->getIndex();
 }
 if (!$defaultPrevented) {
 $index = new Index($data['name'], $data['columns'], $data['unique'], $data['primary'], $data['flags'], $data['options']);
 }
 if ($index === null) {
 continue;
 }
 $indexes[$indexKey] = $index;
 }
 return $indexes;
 }
 protected function _getPortableTablesList($tables)
 {
 $list = [];
 foreach ($tables as $value) {
 $list[] = $this->_getPortableTableDefinition($value);
 }
 return $list;
 }
 protected function _getPortableTableDefinition($table)
 {
 return $table;
 }
 protected function _getPortableViewsList($views)
 {
 $list = [];
 foreach ($views as $value) {
 $view = $this->_getPortableViewDefinition($value);
 if ($view === \false) {
 continue;
 }
 $viewName = strtolower($view->getQuotedName($this->_platform));
 $list[$viewName] = $view;
 }
 return $list;
 }
 protected function _getPortableViewDefinition($view)
 {
 return \false;
 }
 protected function _getPortableTableForeignKeysList($tableForeignKeys)
 {
 $list = [];
 foreach ($tableForeignKeys as $value) {
 $list[] = $this->_getPortableTableForeignKeyDefinition($value);
 }
 return $list;
 }
 protected function _getPortableTableForeignKeyDefinition($tableForeignKey)
 {
 return $tableForeignKey;
 }
 protected function _execSql($sql)
 {
 foreach ((array) $sql as $query) {
 $this->_conn->executeStatement($query);
 }
 }
 public function createSchema()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5613', '%s is deprecated. Use introspectSchema() instead.', __METHOD__);
 $schemaNames = [];
 if ($this->_platform->supportsSchemas()) {
 $schemaNames = $this->listNamespaceNames();
 }
 $sequences = [];
 if ($this->_platform->supportsSequences()) {
 $sequences = $this->listSequences();
 }
 $tables = $this->listTables();
 return new Schema($tables, $sequences, $this->createSchemaConfig(), $schemaNames);
 }
 public function introspectSchema() : Schema
 {
 return $this->createSchema();
 }
 public function createSchemaConfig()
 {
 $schemaConfig = new SchemaConfig();
 $schemaConfig->setMaxIdentifierLength($this->_platform->getMaxIdentifierLength());
 $searchPaths = $this->getSchemaSearchPaths();
 if (isset($searchPaths[0])) {
 $schemaConfig->setName($searchPaths[0]);
 }
 $params = $this->_conn->getParams();
 if (!isset($params['defaultTableOptions'])) {
 $params['defaultTableOptions'] = [];
 }
 if (!isset($params['defaultTableOptions']['charset']) && isset($params['charset'])) {
 $params['defaultTableOptions']['charset'] = $params['charset'];
 }
 $schemaConfig->setDefaultTableOptions($params['defaultTableOptions']);
 return $schemaConfig;
 }
 public function getSchemaSearchPaths()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/4821', 'AbstractSchemaManager::getSchemaSearchPaths() is deprecated.');
 $database = $this->_conn->getDatabase();
 if ($database !== null) {
 return [$database];
 }
 return [];
 }
 public function extractDoctrineTypeFromComment($comment, $currentType)
 {
 if ($comment !== null && preg_match('(\\(DC2Type:(((?!\\)).)+)\\))', $comment, $match) === 1) {
 return $match[1];
 }
 return $currentType;
 }
 public function removeDoctrineTypeFromComment($comment, $type)
 {
 if ($comment === null) {
 return null;
 }
 return str_replace('(DC2Type:' . $type . ')', '', $comment);
 }
 private function getDatabase(string $methodName) : string
 {
 $database = $this->_conn->getDatabase();
 if ($database === null) {
 throw DatabaseRequired::new($methodName);
 }
 return $database;
 }
 public function createComparator() : Comparator
 {
 return new Comparator($this->_platform);
 }
 private function fetchAllAssociativeGrouped(Result $result) : array
 {
 $data = [];
 foreach ($result->fetchAllAssociative() as $row) {
 $tableName = $this->_getPortableTableDefinition($row);
 $data[$tableName][] = $row;
 }
 return $data;
 }
}
