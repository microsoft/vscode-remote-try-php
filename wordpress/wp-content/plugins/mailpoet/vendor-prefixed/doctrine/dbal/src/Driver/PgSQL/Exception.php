<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver\PgSQL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\AbstractException;
use MailPoetVendor\PgSql\Result as PgSqlResult;
use function pg_result_error_field;
use const PGSQL_DIAG_MESSAGE_PRIMARY;
use const PGSQL_DIAG_SQLSTATE;
final class Exception extends AbstractException
{
 public static function fromResult($result) : self
 {
 $sqlstate = pg_result_error_field($result, PGSQL_DIAG_SQLSTATE);
 if ($sqlstate === \false) {
 $sqlstate = null;
 }
 return new self((string) pg_result_error_field($result, PGSQL_DIAG_MESSAGE_PRIMARY), $sqlstate);
 }
}
