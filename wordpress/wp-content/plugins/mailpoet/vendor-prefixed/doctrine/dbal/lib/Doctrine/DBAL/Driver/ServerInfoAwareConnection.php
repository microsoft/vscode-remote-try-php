<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
interface ServerInfoAwareConnection
{
 public function getServerVersion();
 public function requiresQueryForServerVersion();
}
