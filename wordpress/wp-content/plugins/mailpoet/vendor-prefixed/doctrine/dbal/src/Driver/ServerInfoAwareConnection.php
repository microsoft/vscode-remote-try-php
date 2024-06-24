<?php
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
interface ServerInfoAwareConnection extends Connection
{
 public function getServerVersion();
}
