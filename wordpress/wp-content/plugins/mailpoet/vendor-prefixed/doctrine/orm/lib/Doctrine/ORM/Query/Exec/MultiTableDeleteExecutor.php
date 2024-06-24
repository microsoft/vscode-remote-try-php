<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\Exec;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Connections\PrimaryReadReplicaConnection;
use MailPoetVendor\Doctrine\DBAL\Types\Type;
use MailPoetVendor\Doctrine\ORM\Query\AST;
use MailPoetVendor\Doctrine\ORM\Query\AST\DeleteStatement;
use MailPoetVendor\Doctrine\ORM\Query\SqlWalker;
use MailPoetVendor\Doctrine\ORM\Utility\PersisterHelper;
use Throwable;
use function array_merge;
use function array_reverse;
use function implode;
class MultiTableDeleteExecutor extends AbstractSqlExecutor
{
 private $_createTempTableSql;
 private $_dropTempTableSql;
 private $_insertSql;
 public function __construct(AST\Node $AST, $sqlWalker)
 {
 $em = $sqlWalker->getEntityManager();
 $conn = $em->getConnection();
 $platform = $conn->getDatabasePlatform();
 $quoteStrategy = $em->getConfiguration()->getQuoteStrategy();
 if ($conn instanceof PrimaryReadReplicaConnection) {
 $conn->ensureConnectedToPrimary();
 }
 $primaryClass = $em->getClassMetadata($AST->deleteClause->abstractSchemaName);
 $primaryDqlAlias = $AST->deleteClause->aliasIdentificationVariable;
 $rootClass = $em->getClassMetadata($primaryClass->rootEntityName);
 $tempTable = $platform->getTemporaryTableName($rootClass->getTemporaryIdTableName());
 $idColumnNames = $rootClass->getIdentifierColumnNames();
 $idColumnList = implode(', ', $idColumnNames);
 // 1. Create an INSERT INTO temptable ... SELECT identifiers WHERE $AST->getWhereClause()
 $sqlWalker->setSQLTableAlias($primaryClass->getTableName(), 't0', $primaryDqlAlias);
 $this->_insertSql = 'INSERT INTO ' . $tempTable . ' (' . $idColumnList . ')' . ' SELECT t0.' . implode(', t0.', $idColumnNames);
 $rangeDecl = new AST\RangeVariableDeclaration($primaryClass->name, $primaryDqlAlias);
 $fromClause = new AST\FromClause([new AST\IdentificationVariableDeclaration($rangeDecl, null, [])]);
 $this->_insertSql .= $sqlWalker->walkFromClause($fromClause);
 // Append WHERE clause, if there is one.
 if ($AST->whereClause) {
 $this->_insertSql .= $sqlWalker->walkWhereClause($AST->whereClause);
 }
 // 2. Create ID subselect statement used in DELETE ... WHERE ... IN (subselect)
 $idSubselect = 'SELECT ' . $idColumnList . ' FROM ' . $tempTable;
 // 3. Create and store DELETE statements
 $classNames = array_merge($primaryClass->parentClasses, [$primaryClass->name], $primaryClass->subClasses);
 foreach (array_reverse($classNames) as $className) {
 $tableName = $quoteStrategy->getTableName($em->getClassMetadata($className), $platform);
 $this->_sqlStatements[] = 'DELETE FROM ' . $tableName . ' WHERE (' . $idColumnList . ') IN (' . $idSubselect . ')';
 }
 // 4. Store DDL for temporary identifier table.
 $columnDefinitions = [];
 foreach ($idColumnNames as $idColumnName) {
 $columnDefinitions[$idColumnName] = ['notnull' => \true, 'type' => Type::getType(PersisterHelper::getTypeOfColumn($idColumnName, $rootClass, $em))];
 }
 $this->_createTempTableSql = $platform->getCreateTemporaryTableSnippetSQL() . ' ' . $tempTable . ' (' . $platform->getColumnDeclarationListSQL($columnDefinitions) . ', PRIMARY KEY(' . implode(',', $idColumnNames) . '))';
 $this->_dropTempTableSql = $platform->getDropTemporaryTableSQL($tempTable);
 }
 public function execute(Connection $conn, array $params, array $types)
 {
 // Create temporary id table
 $conn->executeStatement($this->_createTempTableSql);
 try {
 // Insert identifiers
 $numDeleted = $conn->executeStatement($this->_insertSql, $params, $types);
 // Execute DELETE statements
 foreach ($this->_sqlStatements as $sql) {
 $conn->executeStatement($sql);
 }
 } catch (Throwable $exception) {
 // FAILURE! Drop temporary table to avoid possible collisions
 $conn->executeStatement($this->_dropTempTableSql);
 // Re-throw exception
 throw $exception;
 }
 // Drop temporary table
 $conn->executeStatement($this->_dropTempTableSql);
 return $numDeleted;
 }
}
