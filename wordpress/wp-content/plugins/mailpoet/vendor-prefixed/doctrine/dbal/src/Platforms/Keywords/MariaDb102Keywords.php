<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms\Keywords;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
final class MariaDb102Keywords extends MariaDBKeywords
{
 public function getName() : string
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/5433', 'MariaDb102Keywords::getName() is deprecated.');
 return 'MariaDb102';
 }
}
