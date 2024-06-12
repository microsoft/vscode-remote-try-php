<?php
namespace MailPoetVendor\Doctrine\Common\Cache\Psr6;
if (!defined('ABSPATH')) exit;
use InvalidArgumentException;
use MailPoetVendor\Psr\Cache\InvalidArgumentException as PsrInvalidArgumentException;
final class InvalidArgument extends InvalidArgumentException implements PsrInvalidArgumentException
{
}
