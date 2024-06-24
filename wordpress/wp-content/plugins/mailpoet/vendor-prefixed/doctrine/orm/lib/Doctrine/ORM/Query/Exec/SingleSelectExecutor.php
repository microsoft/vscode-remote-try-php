<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\Exec;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Result;
use MailPoetVendor\Doctrine\ORM\Query\AST\SelectStatement;
use MailPoetVendor\Doctrine\ORM\Query\SqlWalker;
class SingleSelectExecutor extends AbstractSqlExecutor
{
 public function __construct(SelectStatement $AST, SqlWalker $sqlWalker)
 {
 $this->_sqlStatements = $sqlWalker->walkSelectStatement($AST);
 }
 public function execute(Connection $conn, array $params, array $types)
 {
 return $conn->executeQuery($this->_sqlStatements, $params, $types, $this->queryCacheProfile);
 }
}
