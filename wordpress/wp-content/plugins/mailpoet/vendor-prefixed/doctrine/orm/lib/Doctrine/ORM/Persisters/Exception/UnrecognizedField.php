<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Persisters\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Exception\PersisterException;
use function sprintf;
final class UnrecognizedField extends PersisterException
{
 public static function byName(string $field) : self
 {
 return new self(sprintf('Unrecognized field: %s', $field));
 }
}
