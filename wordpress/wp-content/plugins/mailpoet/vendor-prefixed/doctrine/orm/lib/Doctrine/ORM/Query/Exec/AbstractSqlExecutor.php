<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\Exec;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Cache\QueryCacheProfile;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Result;
use MailPoetVendor\Doctrine\DBAL\Types\Type;
abstract class AbstractSqlExecutor
{
 protected $_sqlStatements;
 protected $queryCacheProfile;
 public function getSqlStatements()
 {
 return $this->_sqlStatements;
 }
 public function setQueryCacheProfile(QueryCacheProfile $qcp)
 {
 $this->queryCacheProfile = $qcp;
 }
 public function removeQueryCacheProfile()
 {
 $this->queryCacheProfile = null;
 }
 public abstract function execute(Connection $conn, array $params, array $types);
}
