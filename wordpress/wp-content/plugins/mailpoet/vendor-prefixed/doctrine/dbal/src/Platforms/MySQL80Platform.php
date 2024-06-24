<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\SQL\Builder\SelectSQLBuilder;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
class MySQL80Platform extends MySQL57Platform
{
 protected function getReservedKeywordsClass()
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4510', 'MySQL80Platform::getReservedKeywordsClass() is deprecated,' . ' use MySQL80Platform::createReservedKeywordsList() instead.');
 return Keywords\MySQL80Keywords::class;
 }
 public function createSelectSQLBuilder() : SelectSQLBuilder
 {
 return AbstractPlatform::createSelectSQLBuilder();
 }
}
