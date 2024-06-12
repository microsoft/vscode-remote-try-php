<?php
namespace MailPoetVendor\Doctrine\DBAL\ForwardCompatibility;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL;
interface DriverResultStatement extends DBAL\Driver\ResultStatement, DBAL\Result
{
}
