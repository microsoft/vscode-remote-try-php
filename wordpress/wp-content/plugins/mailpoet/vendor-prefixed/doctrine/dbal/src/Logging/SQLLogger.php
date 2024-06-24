<?php
namespace MailPoetVendor\Doctrine\DBAL\Logging;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Types\Type;
interface SQLLogger
{
 public function startQuery($sql, ?array $params = null, ?array $types = null);
 public function stopQuery();
}
