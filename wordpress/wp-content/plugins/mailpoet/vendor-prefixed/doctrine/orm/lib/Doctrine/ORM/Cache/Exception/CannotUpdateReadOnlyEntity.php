<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Exception;
if (!defined('ABSPATH')) exit;
use function sprintf;
class CannotUpdateReadOnlyEntity extends CacheException
{
 public static function fromEntity(string $entityName) : self
 {
 return new self(sprintf('Cannot update a readonly entity "%s"', $entityName));
 }
}
