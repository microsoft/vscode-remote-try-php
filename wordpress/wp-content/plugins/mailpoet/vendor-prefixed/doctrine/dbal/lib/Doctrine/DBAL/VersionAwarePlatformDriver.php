<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
interface VersionAwarePlatformDriver
{
 public function createDatabasePlatformForVersion($version);
}
