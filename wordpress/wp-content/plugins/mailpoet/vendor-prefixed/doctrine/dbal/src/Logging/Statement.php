<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Logging;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\Middleware\AbstractStatementMiddleware;
use MailPoetVendor\Doctrine\DBAL\Driver\Result as ResultInterface;
use MailPoetVendor\Doctrine\DBAL\Driver\Statement as StatementInterface;
use MailPoetVendor\Doctrine\DBAL\ParameterType;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Psr\Log\LoggerInterface;
use function array_slice;
use function func_get_args;
use function func_num_args;
final class Statement extends AbstractStatementMiddleware
{
 private LoggerInterface $logger;
 private string $sql;
 private array $params = [];
 private array $types = [];
 public function __construct(StatementInterface $statement, LoggerInterface $logger, string $sql)
 {
 parent::__construct($statement);
 $this->logger = $logger;
 $this->sql = $sql;
 }
 public function bindParam($param, &$variable, $type = ParameterType::STRING, $length = null)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5563', '%s is deprecated. Use bindValue() instead.', __METHOD__);
 if (func_num_args() < 3) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5558', 'Not passing $type to Statement::bindParam() is deprecated.' . ' Pass the type corresponding to the parameter being bound.');
 }
 $this->params[$param] =& $variable;
 $this->types[$param] = $type;
 return parent::bindParam($param, $variable, $type, ...array_slice(func_get_args(), 3));
 }
 public function bindValue($param, $value, $type = ParameterType::STRING)
 {
 if (func_num_args() < 3) {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5558', 'Not passing $type to Statement::bindValue() is deprecated.' . ' Pass the type corresponding to the parameter being bound.');
 }
 $this->params[$param] = $value;
 $this->types[$param] = $type;
 return parent::bindValue($param, $value, $type);
 }
 public function execute($params = null) : ResultInterface
 {
 $this->logger->debug('Executing statement: {sql} (parameters: {params}, types: {types})', ['sql' => $this->sql, 'params' => $params ?? $this->params, 'types' => $this->types]);
 return parent::execute($params);
 }
}
