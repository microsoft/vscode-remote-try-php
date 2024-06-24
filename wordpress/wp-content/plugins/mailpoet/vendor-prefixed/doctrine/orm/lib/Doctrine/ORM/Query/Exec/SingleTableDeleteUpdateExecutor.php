<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\Exec;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Connections\PrimaryReadReplicaConnection;
use MailPoetVendor\Doctrine\ORM\Query\AST;
use MailPoetVendor\Doctrine\ORM\Query\SqlWalker;
class SingleTableDeleteUpdateExecutor extends AbstractSqlExecutor
{
 public function __construct(AST\Node $AST, $sqlWalker)
 {
 if ($AST instanceof AST\UpdateStatement) {
 $this->_sqlStatements = $sqlWalker->walkUpdateStatement($AST);
 } elseif ($AST instanceof AST\DeleteStatement) {
 $this->_sqlStatements = $sqlWalker->walkDeleteStatement($AST);
 }
 }
 public function execute(Connection $conn, array $params, array $types)
 {
 if ($conn instanceof PrimaryReadReplicaConnection) {
 $conn->ensureConnectedToPrimary();
 }
 return $conn->executeStatement($this->_sqlStatements, $params, $types);
 }
}
