<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver\Middleware;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\Result;
use MailPoetVendor\Doctrine\DBAL\Driver\Statement;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function func_num_args;
abstract class AbstractStatementMiddleware implements Statement
{
 private Statement $wrappedStatement;
 public function __construct(Statement $wrappedStatement)
 {
 $this->wrappedStatement = $wrappedStatement;
 }
 public function bindValue($param, $value, $type = ParameterType::STRING)
 {
 if (func_num_args() < 3) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5558', 'Not passing $type to Statement::bindValue() is deprecated.' . ' Pass the type corresponding to the parameter being bound.');
 }
 return $this->wrappedStatement->bindValue($param, $value, $type);
 }
 public function bindParam($param, &$variable, $type = ParameterType::STRING, $length = null)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5563', '%s is deprecated. Use bindValue() instead.', __METHOD__);
 if (func_num_args() < 3) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5558', 'Not passing $type to Statement::bindParam() is deprecated.' . ' Pass the type corresponding to the parameter being bound.');
 }
 return $this->wrappedStatement->bindParam($param, $variable, $type, $length);
 }
 public function execute($params = null) : Result
 {
 return $this->wrappedStatement->execute($params);
 }
}
