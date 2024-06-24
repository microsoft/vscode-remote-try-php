<?php
namespace MailPoetVendor\Doctrine\DBAL\Exception;
if (!defined('ABSPATH')) exit;
use InvalidArgumentException;
class MalformedDsnException extends InvalidArgumentException
{
 public static function new() : self
 {
 return new self('Malformed database connection URL');
 }
}
