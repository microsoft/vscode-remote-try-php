<?php
namespace MailPoetVendor\Doctrine\DBAL\ForwardCompatibility;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL;
interface DriverStatement extends DBAL\Driver\Statement, DBAL\Result
{
}
