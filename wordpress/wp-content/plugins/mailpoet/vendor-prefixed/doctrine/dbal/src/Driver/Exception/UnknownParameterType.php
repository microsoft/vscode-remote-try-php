<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\DBAL\Driver\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Driver\AbstractException;
use function sprintf;
final class UnknownParameterType extends AbstractException
{
 public static function new($type) : self
 {
 return new self(sprintf('Unknown parameter type, %d given.', $type));
 }
}
