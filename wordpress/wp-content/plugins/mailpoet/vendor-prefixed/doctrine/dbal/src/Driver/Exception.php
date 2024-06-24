<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver;
if (!defined('ABSPATH')) exit;
use Throwable;
interface Exception extends Throwable
{
 public function getSQLState();
}
