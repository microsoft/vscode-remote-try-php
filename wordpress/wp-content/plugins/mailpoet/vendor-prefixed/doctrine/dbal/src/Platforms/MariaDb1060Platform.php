<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\SQL\Builder\SelectSQLBuilder;
class MariaDb1060Platform extends MariaDb1052Platform
{
 public function createSelectSQLBuilder() : SelectSQLBuilder
 {
 return AbstractPlatform::createSelectSQLBuilder();
 }
}
