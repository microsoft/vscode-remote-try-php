<?php
namespace MailPoetVendor\Doctrine\DBAL\SQL\Builder;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Exception;
use MailPoetVendor\Doctrine\DBAL\Query\SelectQuery;
interface SelectSQLBuilder
{
 public function buildSQL(SelectQuery $query) : string;
}
