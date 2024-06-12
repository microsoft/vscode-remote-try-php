<?php
namespace MailPoetVendor\Doctrine\DBAL\Exception;
if (!defined('ABSPATH')) exit;
class DeadlockException extends ServerException implements RetryableException
{
}
