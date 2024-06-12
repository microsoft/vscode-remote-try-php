<?php
namespace MailPoetVendor\Doctrine\DBAL\Exception;
if (!defined('ABSPATH')) exit;
use Throwable;
interface RetryableException extends Throwable
{
}
