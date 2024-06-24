<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver\AbstractSQLServerDriver\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\AbstractException;
final class PortWithoutHost extends AbstractException
{
 public static function new() : self
 {
 return new self('Connection port specified without the host');
 }
}
