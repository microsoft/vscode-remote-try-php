<?php
namespace MailPoetVendor\Doctrine\Persistence;
if (!defined('ABSPATH')) exit;
interface ConnectionRegistry
{
 public function getDefaultConnectionName();
 public function getConnection($name = null);
 public function getConnections();
 public function getConnectionNames();
}
