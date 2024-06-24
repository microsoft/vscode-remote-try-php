<?php
namespace MailPoetVendor\Doctrine\DBAL;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
interface VersionAwarePlatformDriver extends Driver
{
 public function createDatabasePlatformForVersion($version);
}
