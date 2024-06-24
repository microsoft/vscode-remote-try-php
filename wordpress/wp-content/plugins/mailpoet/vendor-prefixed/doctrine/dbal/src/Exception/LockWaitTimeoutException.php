<?php
namespace MailPoetVendor\Doctrine\DBAL\Exception;
if (!defined('ABSPATH')) exit;
class LockWaitTimeoutException extends ServerException implements RetryableException
{
}
