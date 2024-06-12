<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\Exec\AbstractSqlExecutor;
class ParserResult
{
 private $_sqlExecutor;
 private $_resultSetMapping;
 private $_parameterMappings = [];
 public function __construct()
 {
 $this->_resultSetMapping = new ResultSetMapping();
 }
 public function getResultSetMapping()
 {
 return $this->_resultSetMapping;
 }
 public function setResultSetMapping(ResultSetMapping $rsm)
 {
 $this->_resultSetMapping = $rsm;
 }
 public function setSqlExecutor($executor)
 {
 $this->_sqlExecutor = $executor;
 }
 public function getSqlExecutor()
 {
 return $this->_sqlExecutor;
 }
 public function addParameterMapping($dqlPosition, $sqlPosition)
 {
 $this->_parameterMappings[$dqlPosition][] = $sqlPosition;
 }
 public function getParameterMappings()
 {
 return $this->_parameterMappings;
 }
 public function getSqlParameterPositions($dqlPosition)
 {
 return $this->_parameterMappings[$dqlPosition];
 }
}
