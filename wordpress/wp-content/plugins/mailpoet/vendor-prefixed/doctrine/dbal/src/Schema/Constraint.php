<?php
namespace MailPoetVendor\Doctrine\DBAL\Schema;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
interface Constraint
{
 public function getName();
 public function getQuotedName(AbstractPlatform $platform);
 public function getColumns();
 public function getQuotedColumns(AbstractPlatform $platform);
}
