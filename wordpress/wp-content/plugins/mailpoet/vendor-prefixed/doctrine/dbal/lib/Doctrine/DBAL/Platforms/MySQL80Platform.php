<?php
namespace MailPoetVendor\Doctrine\DBAL\Platforms;
if (!defined('ABSPATH')) exit;
class MySQL80Platform extends MySQL57Platform
{
 protected function getReservedKeywordsClass()
 {
 return Keywords\MySQL80Keywords::class;
 }
}
