<?php
namespace MailPoetVendor\Doctrine\DBAL\Portability;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\Middleware\AbstractStatementMiddleware;
use MailPoetVendor\Doctrine\DBAL\Driver\Result as ResultInterface;
use MailPoetVendor\Doctrine\DBAL\Driver\Statement as DriverStatement;
final class Statement extends AbstractStatementMiddleware
{
 private Converter $converter;
 public function __construct(DriverStatement $stmt, Converter $converter)
 {
 parent::__construct($stmt);
 $this->converter = $converter;
 }
 public function execute($params = null) : ResultInterface
 {
 return new Result(parent::execute($params), $this->converter);
 }
}
