<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\ORM\Exception\ORMException;
use function get_debug_type;
use function sprintf;
final class CannotGenerateIds extends ORMException
{
 public static function withPlatform(AbstractPlatform $platform) : self
 {
 return new self(sprintf('Platform %s does not support generating identifiers', get_debug_type($platform)));
 }
}
